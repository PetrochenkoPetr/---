@echo off
chcp 65001 >nul
title Установка XAMPP

echo ========================================
echo    УСТАНОВКА XAMPP
echo ========================================
echo.

REM Проверяем, установлен ли уже XAMPP
if exist "C:\xampp\xampp-control.exe" (
    echo [OK] XAMPP уже установлен: C:\xampp
    echo.
    start "" "C:\xampp\xampp-control.exe"
    echo Запускаю XAMPP Control Panel...
    echo Нажми "Start" напротив MySQL и Apache
    pause
    exit /b
)

echo [!] XAMPP не найден. Скачиваю из интернета...
echo.

REM Создаём временную папку
if not exist "%TEMP%\xampp_install" mkdir "%TEMP%\xampp_install"

REM Скачиваем XAMPP через PowerShell
echo Скачивание XAMPP (~170 МБ). Подождите...
powershell -Command "[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri 'https://sourceforge.net/projects/xampp/files/XAMPP%20Windows/8.2.12/xampp-windows-x64-8.2.12-0-VS16-installer.exe/download' -OutFile '%TEMP%\xampp_install\xampp-installer.exe'"

if not exist "%TEMP%\xampp_install\xampp-installer.exe" (
    echo.
    echo [ОШИБКА] Не удалось скачать XAMPP
    echo Проверьте подключение к интернету
    echo.
    echo Скачайте вручную: https://www.apachefriends.org/
    pause
    exit /b
)

echo.
echo Скачивание завершено!
echo Устанавливаю XAMPP (без участия пользователя)...
echo Это займёт 2-3 минуты...
echo.

REM Тихая установка XAMPP
"%TEMP%\xampp_install\xampp-installer.exe" --unattendedmodeui none --mode unattended --prefix C:\xampp

REM Проверяем результат
if exist "C:\xampp\xampp-control.exe" (
    echo.
    echo ========================================
    echo    XAMPP УСПЕШНО УСТАНОВЛЕН!
    echo ========================================
    echo.
    echo Запускаю XAMPP Control Panel...
    start "" "C:\xampp\xampp-control.exe"
    echo.
    echo Нажми "Start" напротив MySQL и Apache
    echo Затем запусти start.bat
) else (
    echo.
    echo [ОШИБКА] Установка не удалась
    echo Попробуйте установить вручную
)

REM Удаляем временные файлы
rd /s /q "%TEMP%\xampp_install" 2>nul

echo.
pause
