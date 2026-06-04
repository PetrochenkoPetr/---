@echo off
chcp 65001 >nul
title Учусь.РФ - Запуск проекта

echo ========================================
echo    ПРОЕКТ "УЧУСЬ.РФ"
echo ========================================
echo.

REM Проверяем XAMPP
if not exist "C:\xampp\xampp-control.exe" (
    echo [!] XAMPP не найден!
    echo Запускаю установку...
    echo.
    call "установка_xampp.bat"
    exit /b
)

echo [OK] XAMPP найден
echo.

REM Запускаем XAMPP если не запущен
tasklist /FI "IMAGENAME eq mysqld.exe" 2>NUL | find /I "mysqld.exe" >NUL
if errorlevel 1 (
    echo Запускаю MySQL и Apache...
    start "" "C:\xampp\xampp-control.exe"
    timeout /t 5 /nobreak >nul
)

REM Восстанавливаем БД если нужно
echo Проверяю базу данных...
C:\xampp\mysql\bin\mysql.exe -u root -e "USE uchus_rf" 2>nul
if errorlevel 1 (
    echo БД не найдена. Восстанавливаю из dump.sql...
    C:\xampp\mysql\bin\mysql.exe -u root < "database\dump.sql" 2>nul
    if errorlevel 1 (
        echo Пробую через PHP...
        C:\xampp\php\php.exe database\import.php
    )
    echo [OK] База данных создана
) else (
    echo [OK] База данных существует
)

echo.
echo Запускаю веб-сервер...
echo.
echo ========================================
echo    ОТКРОЙ В БРАУЗЕРЕ:
echo    http://127.0.0.1:8080
echo ========================================
echo    Админ: Admin26 / Demo20
echo ========================================
echo    Для остановки нажми Ctrl+C
echo ========================================
echo.

C:\xampp\php\php.exe -S 127.0.0.1:8080 -t "%~dp0"
