@echo off
title MAX PIVOTEKA - Verejny Web
color 0A

echo ====================================================
echo      MAX PIVOTEKA - SPOUSTENI VEREJNEHO WEBU
echo ====================================================
echo.
echo [1] Ujisti se, ze mas zapnuty Laragon a spustenou MySQL!
echo.

set "DOC_ROOT=%~dp0"
if "%DOC_ROOT:~-1%"=="\" set "DOC_ROOT=%DOC_ROOT:~0,-1%"

echo [2] Spoustim lokalni PHP webovy server (port 8000)...
start "PHP Server (Port 8000)" cmd /k "C:\laragon\bin\php\php-8.3.30-Win32-vs16-x64\php.exe -S 127.0.0.1:8000 -t "%DOC_ROOT%""

timeout /t 2 >nul

echo [3] Vytvarim verejny tunel pro sdileni...
start "Verejny Odkaz pro Sdileni" cmd /k "echo Vytvarim verejny odkaz pro sdileni... & ssh -R 80:127.0.0.1:8000 -o StrictHostKeyChecking=no serveo.net"

echo.
echo ====================================================
echo   VSE JE SPUSTENO!
echo.
echo   V okynku "Verejny Odkaz pro Sdileni" najdes 
echo   odkaz ve tvaru: https://xxxxx.serveousercontent.com
echo.
echo   Tento odkaz staci zkopirovat a poslat komukoliv!
echo ====================================================
echo.
pause
