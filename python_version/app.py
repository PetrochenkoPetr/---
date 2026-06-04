import os
import sqlite3
import hashlib
import secrets
from datetime import datetime
from functools import wraps
from flask import (Flask, render_template, request, redirect, url_for,
                   session, flash, g)

app = Flask(__name__)
app.secret_key = secrets.token_hex(32)
DATABASE = os.path.join(os.path.dirname(__file__), 'uchus_rf.db')

ADMIN_LOGIN = 'Admin26'
ADMIN_PASS = 'Demo20'


def get_db():
    if 'db' not in g:
        g.db = sqlite3.connect(DATABASE)
        g.db.row_factory = sqlite3.Row
        g.db.execute("PRAGMA journal_mode=WAL")
    return g.db


@app.teardown_appcontext
def close_db(exception):
    db = g.pop('db', None)
    if db:
        db.close()


def hash_password(password):
    return hashlib.sha256(password.encode()).hexdigest()


def init_db():
    db = sqlite3.connect(DATABASE)
    db.executescript('''
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            login TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            full_name TEXT NOT NULL,
            phone TEXT NOT NULL,
            email TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime'))
        );
        CREATE TABLE IF NOT EXISTS applications (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            course_type TEXT NOT NULL,
            start_date TEXT NOT NULL,
            payment_method TEXT NOT NULL,
            status TEXT NOT NULL DEFAULT 'Новая',
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            updated_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
        );
        CREATE TABLE IF NOT EXISTS reviews (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            application_id INTEGER NOT NULL,
            text TEXT NOT NULL,
            created_at TEXT NOT NULL DEFAULT (datetime('now','localtime')),
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
        );
    ''')
    db.commit()
    db.close()


def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return redirect(url_for('login'))
        return f(*args, **kwargs)
    return decorated


def validate_login(login):
    if len(login) < 6:
        return 'Логин должен содержать минимум 6 символов'
    if not login.isalnum():
        return 'Логин должен содержать только латинские буквы и цифры'
    return None


def validate_password(password):
    if len(password) < 8:
        return 'Пароль должен содержать минимум 8 символов'
    return None


@app.route('/')
def index():
    if 'user_id' in session:
        return redirect(url_for('profile'))
    return render_template('index.html')


@app.route('/register', methods=['GET', 'POST'])
def register():
    if 'user_id' in session:
        return redirect(url_for('profile'))

    errors = {}
    old = {}

    if request.method == 'POST':
        login = request.form.get('login', '').strip()
        password = request.form.get('password', '')
        full_name = request.form.get('full_name', '').strip()
        phone = request.form.get('phone', '').strip()
        email = request.form.get('email', '').strip()
        old = {'login': login, 'full_name': full_name, 'phone': phone, 'email': email}

        err_login = validate_login(login)
        err_pass = validate_password(password)
        if err_login:
            errors['login'] = err_login
        if err_pass:
            errors['password'] = err_pass
        if not full_name:
            errors['full_name'] = 'Укажите ФИО'
        if not phone:
            errors['phone'] = 'Укажите телефон'
        if not email:
            errors['email'] = 'Укажите e-mail'

        if not errors:
            db = get_db()
            try:
                db.execute(
                    'INSERT INTO users (login, password, full_name, phone, email) VALUES (?, ?, ?, ?, ?)',
                    (login, hash_password(password), full_name, phone, email)
                )
                db.commit()
                flash('Регистрация прошла успешно! Войдите в систему.', 'success')
                return redirect(url_for('login'))
            except sqlite3.IntegrityError:
                errors['login'] = 'Логин уже занят'

    return render_template('register.html', errors=errors, old=old)


@app.route('/login', methods=['GET', 'POST'])
def login():
    if 'user_id' in session:
        return redirect(url_for('profile'))

    error = ''

    if request.method == 'POST':
        login_val = request.form.get('login', '').strip()
        password = request.form.get('password', '')

        if login_val == ADMIN_LOGIN and password == ADMIN_PASS:
            session['admin'] = True
            return redirect(url_for('admin_panel'))

        db = get_db()
        user = db.execute('SELECT * FROM users WHERE login = ?', (login_val,)).fetchone()
        if user and user['password'] == hash_password(password):
            session['user_id'] = user['id']
            return redirect(url_for('profile'))
        else:
            error = 'Неверный логин или пароль'

    flash_msg = ''
    if '_flashes' in session:
        for category, message in session.get('_flashes', []):
            if category == 'success':
                flash_msg = message

    return render_template('login.html', error=error, flash_msg=flash_msg)


@app.route('/logout')
def logout():
    session.clear()
    return redirect(url_for('login'))


@app.route('/profile', methods=['GET', 'POST'])
@login_required
def profile():
    db = get_db()
    user = db.execute('SELECT * FROM users WHERE id = ?', (session['user_id'],)).fetchone()
    apps = db.execute('SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC',
                      (session['user_id'],)).fetchall()
    reviews = db.execute('SELECT r.*, a.course_type FROM reviews r JOIN applications a ON r.application_id = a.id WHERE r.user_id = ? ORDER BY r.created_at DESC',
                         (session['user_id'],)).fetchall()
    reviewed_ids = [r['application_id'] for r in reviews]

    if request.method == 'POST' and 'review_text' in request.form and 'app_id' in request.form:
        app_id = int(request.form['app_id'])
        text = request.form['review_text'].strip()
        if text:
            db.execute('INSERT INTO reviews (user_id, application_id, text) VALUES (?, ?, ?)',
                       (session['user_id'], app_id, text))
            db.commit()
            return redirect(url_for('profile'))

    return render_template('profile.html', user=user, apps=apps, reviews=reviews, reviewed_ids=reviewed_ids)


@app.route('/application', methods=['GET', 'POST'])
@login_required
def application_form():
    course_types = ['Повышение квалификации', 'Профессиональная переподготовка',
                    'Охрана труда', 'Пожарная безопасность', 'Первая помощь']
    payment_methods = ['Наличные', 'Банковская карта', 'Банковский перевод', 'Электронные деньги']

    success = ''
    errors = []

    if request.method == 'POST':
        course_type = request.form.get('course_type', '')
        start_date = request.form.get('start_date', '')
        payment_method = request.form.get('payment_method', '')

        if course_type not in course_types:
            errors.append('Выберите курс')
        if not start_date or len(start_date) != 10:
            errors.append('Укажите дату в формате ДД.ММ.ГГГГ')
        if payment_method not in payment_methods:
            errors.append('Выберите способ оплаты')

        if not errors:
            parts = start_date.split('.')
            mysql_date = f'{parts[2]}-{parts[1]}-{parts[0]}'
            db = get_db()
            db.execute(
                'INSERT INTO applications (user_id, course_type, start_date, payment_method) VALUES (?, ?, ?, ?)',
                (session['user_id'], course_type, mysql_date, payment_method)
            )
            db.commit()
            success = 'Заявка успешно отправлена!'

    return render_template('application.html', course_types=course_types,
                           payment_methods=payment_methods, success=success, errors=errors)


@app.route('/admin', methods=['GET', 'POST'])
def admin_panel():
    if 'admin' not in session:
        error = ''
        if request.method == 'POST':
            login_val = request.form.get('login', '')
            password = request.form.get('password', '')
            if login_val == ADMIN_LOGIN and password == ADMIN_PASS:
                session['admin'] = True
                return redirect(url_for('admin_panel'))
            error = 'Неверные учётные данные'
        return render_template('admin_login.html', error=error)

    if request.method == 'POST' and 'change_status' in request.form:
        app_id = int(request.form['app_id'])
        status = request.form['status']
        db = get_db()
        db.execute('UPDATE applications SET status = ?, updated_at = datetime("now","localtime") WHERE id = ?',
                   (status, app_id))
        db.commit()
        return redirect(url_for('admin_panel',
                                page=request.args.get('page', 1),
                                filter=request.args.get('filter', ''),
                                sort=request.args.get('sort', 'created_at'),
                                order=request.args.get('order', 'DESC')))

    page = max(1, int(request.args.get('page', 1)))
    limit = 5
    offset = (page - 1) * limit
    status_filter = request.args.get('filter', '')
    sort_by = request.args.get('sort', 'created_at')
    sort_order = request.args.get('order', 'DESC')

    db = get_db()

    if status_filter:
        total = db.execute('SELECT COUNT(*) FROM applications WHERE status = ?',
                           (status_filter,)).fetchone()[0]
        apps = db.execute(
            f'SELECT a.*, u.login, u.full_name, u.phone, u.email '
            f'FROM applications a JOIN users u ON a.user_id = u.id '
            f'WHERE a.status = ? ORDER BY a.{sort_by} {sort_order} LIMIT ? OFFSET ?',
            (status_filter, limit, offset)).fetchall()
    else:
        total = db.execute('SELECT COUNT(*) FROM applications').fetchone()[0]
        apps = db.execute(
            f'SELECT a.*, u.login, u.full_name, u.phone, u.email '
            f'FROM applications a JOIN users u ON a.user_id = u.id '
            f'ORDER BY a.{sort_by} {sort_order} LIMIT ? OFFSET ?',
            (limit, offset)).fetchall()

    total_pages = max(1, -(-total // limit))

    return render_template('admin.html', apps=apps, page=page,
                           total_pages=total_pages, status_filter=status_filter,
                           sort_by=sort_by, sort_order=sort_order)


if __name__ == '__main__':
    init_db()
    print('=' * 44)
    print('   ПРОЕКТ "УЧУСЬ.РФ" (Python)')
    print('=' * 44)
    print('   http://127.0.0.1:5000')
    print('   Админ: Admin26 / Demo20')
    print('=' * 44)
    app.run(host='127.0.0.1', port=5000, debug=True)
