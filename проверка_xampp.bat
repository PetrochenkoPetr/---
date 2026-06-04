@echo off
chcp 65001 >nul
title Проверка XAMPP

echo ========================================
echo    ПРОВЕРКА XAMPP
echo ========================================
echo.

REM Проверяем XAMPP
if exist "C:\xampp\xampp-control.exe" (
    echo [OK] XAMPP найден: C:\xampp
    echo.
    echo Запускаю XAMPP Control Panel...
    start "" "C:\xampp\xampp-control.exe"
    echo.
    echo ========================================
    echo    ИНСТРУКЦИЯ:
    echo ========================================
    echo  1. Нажми "Start" напротив MySQL
    echo  2. Нажми "Start" напротив Apache
    echo  3. Затем запусти start.bat
    echo ========================================
    pause
    exit /b
)

if exist "D:\xampp\xampp-control.exe" (
    echo [OK] XAMPP найден: D:\xampp
    start "" "D:\xampp\xampp-control.exe"
    pause
    exit /b
)

echo [!] XAMPP НЕ НАЙДЕН!
echo.
echo ========================================
echo    ВАРИАНТЫ УСТАНОВКИ:
echo ========================================
echo.
echo  1. Скачай XAMPP:
echo     https://www.apachefriends.org/
echo.
echo  2. Установи в C:\xampp
echo.
echo  3. Запусти этот скрипт снова
echo ========================================
echo.
echo  Или возьми XAMPP portable на флешке
echo ========================================
pause
