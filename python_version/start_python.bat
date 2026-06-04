@echo off
chcp 65001 >nul
title Учусь.РФ (Python)

echo ========================================
echo    ПРОЕКТ "УЧУСЬ.РФ" (Python версия)
echo ========================================
echo.

REM Проверяем Python
python --version >nul 2>&1
if errorlevel 1 (
    echo [ОШИБКА] Python не установлен!
    echo Скачай с https://www.python.org/downloads/
    pause
    exit /b
)

echo [OK] Python найден
echo.

REM Устанавливаем Flask
pip install flask >nul 2>&1

echo Запускаю сервер...
echo.
echo ========================================
echo    ОТКРОЙ В БРАУЗЕРЕ:
echo    http://127.0.0.1:5000
echo ========================================
echo    Админ: Admin26 / Demo20
echo ========================================
echo.

python app.py
