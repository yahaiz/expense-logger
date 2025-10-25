@echo off
REM ExpenseLogger Desktop Launcher
REM This script starts the PHP development server and opens the app in browser

echo Starting ExpenseLogger...
echo.

REM Check if PHP is available
php --version >nul 2>&1
if %errorlevel% neq 0 (
    echo ERROR: PHP is not installed or not in PATH
    echo Please install PHP and add it to your system PATH
    pause
    exit /b 1
)

REM Get the directory where this script is located
set "SCRIPT_DIR=%~dp0"

REM Start PHP built-in server in the background
start /B php -S localhost:8000 -t "%SCRIPT_DIR%"

REM Wait a moment for server to start
timeout /t 2 /nobreak >nul

REM Open the app in default browser
start http://localhost:8000

echo ExpenseLogger is now running at http://localhost:8000
echo Press Ctrl+C to stop the server
echo.

REM Keep the server running
php -S localhost:8000 -t "%SCRIPT_DIR%"