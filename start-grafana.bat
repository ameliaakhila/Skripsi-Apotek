@echo off
title Grafana Server
echo ========================================
echo   Starting Grafana 13.1.2...
echo   Access at: http://localhost:3001
echo   Default login: admin / admin
echo ========================================
echo.
cd /d "C:\Users\ASUS\Downloads\grafana-13.1.2"
"bin\grafana.exe" server --homepath "C:\Users\ASUS\Downloads\grafana-13.1.2" --http-port 3001
pause