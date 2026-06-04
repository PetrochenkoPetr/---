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
    echo [!] Python не найден. Скачиваю из интернета...
    echo.
    powershell -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://www.python.org/ftp/python/3.12.7/python-3.12.7-amd64.exe' -OutFile '%TEMP%\python-installer.exe'"
    
    if exist "%TEMP%\python-installer.exe" (
        echo Устанавливаю Python (без участия пользователя)...
        "%TEMP%\python-installer.exe" /quiet InstallAllUsers=0 PrependPath=1 Include_test=0
        del "%TEMP%\python-installer.exe" 2>nul
        echo.
        echo Python установлен. Перезапустите этот скрипт.
        pause
        exit /b
    ) else (
        echo [ОШИБКА] Не удалось скачать Python
        echo Скачай вручную: https://www.python.org/downloads/
        pause
        exit /b
    )
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
