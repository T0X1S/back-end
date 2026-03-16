@echo off
set "PHP=D:\ProgramsFiles\OSPanel\modules\PHP-8.4\PHP\php.exe"
set "DIR=%~dp0"
set "DIR=%DIR:~0,-1%"
cd /d "%DIR%"

echo Checking PHP...
if not exist "%PHP%" (
    echo PHP not found: %PHP%
    echo Edit this file and set PHP path in line 2.
    pause
    exit /b 1
)
"%PHP%" -v
echo.

echo Downloading Composer installer...
"%PHP%" -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
if errorlevel 1 (
    echo Download failed. Check internet connection.
    pause
    exit /b 1
)

echo Installing composer.phar...
"%PHP%" composer-setup.php --install-dir="%DIR%" --filename=composer.phar
del composer-setup.php 2>nul

if not exist "%DIR%\composer.phar" (
    echo Failed to create composer.phar
    pause
    exit /b 1
)

echo.
echo Installing project dependencies...
"%PHP%" composer.phar install --no-interaction
echo.
echo Done.
pause
