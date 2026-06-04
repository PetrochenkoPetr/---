@echo off
chcp 65001 >nul
title Запуск через PHP portable

echo ========================================
echo    УСТАНОВКА PHP PORTABLE
echo ========================================
echo.

if exist "C:\php\php.exe" (
    echo [OK] PHP уже есть: C:\php
    goto start_server
)

echo Скачиваю PHP (25 МБ)...
powershell -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://windows.php.net/downloads/releases/php-8.2.12-nts-Win32-vs16-x64.zip' -OutFile '%TEMP%\php.zip'"

if not exist "%TEMP%\php.zip" (
    echo [ОШИБКА] Не удалось скачать PHP
    pause
    exit /b
)

echo Распаковываю...
mkdir C:\php 2>nul
powershell -Command "Expand-Archive -Path '%TEMP%\php.zip' -DestinationPath 'C:\php' -Force"
del "%TEMP%\php.zip" 2>nul

if not exist "C:\php\php.exe" (
    echo [ОШИБКА] Не удалось распаковать PHP
    pause
    exit /b
)

echo [OK] PHP установлен
echo.

:start_server
echo ========================================
echo    ЗАПУСК СЕРВЕРА
echo ========================================
echo    http://127.0.0.1:8080
echo    Админ: Admin26 / Demo20
echo ========================================
echo.

REM Восстанавливаем БД если нужно
C:\php\php.exe database\import.php 2>nul

C:\php\php.exe -S 127.0.0.1:8080 -t "%~dp0"
