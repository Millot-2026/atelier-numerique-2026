@echo off
setlocal enabledelayedexpansion
title DEV NOMADE — Verification Export Nuxit

REM ================================================================
REM  DEV NOMADE — Verification visuelle de l'export Nuxit
REM  Version : 1.3 — Port : 8765
REM  Fermer cette fenetre (ou Ctrl+C) pour arreter le serveur.
REM ================================================================

REM -- 1. Resolution dynamique des chemins -------------------------
SET "SCRIPT_DIR=%~dp0"
SET "USB_ROOT=%~dp0..\.."
SET "PHP_EXE=%USB_ROOT%\server\xampp-windows\php\php.exe"
SET "CHROME=%USB_ROOT%\core\chrome-lecteur-pc\GoogleChromePortable\GoogleChromePortable.exe"
SET "EXPORT_DIR=%SCRIPT_DIR%export-nuxit"
SET "PORT=8765"

echo.
echo ================================================================
echo   DEV NOMADE — Verification visuelle de l'export Nuxit
echo   Port : %PORT%
echo ================================================================
echo.
echo [INFO] Racine USB detectee : %USB_ROOT%
echo [INFO] Dossier a servir    : %EXPORT_DIR%
echo.

REM -- 2. Localisation de php.exe ----------------------------------
IF EXIST "%PHP_EXE%" (
    echo [OK] PHP portable trouve sur la cle.
    GOTO :PHP_TROUVE
)

echo [AVERT] PHP portable introuvable. Tentative avec le PHP systeme...
WHERE php >nul 2>&1
IF ERRORLEVEL 1 (
    echo.
    echo [ERREUR] PHP est introuvable sur ce poste.
    echo.
    echo  Solutions :
    echo   1. Verifiez que XAMPP est present dans :
    echo      %USB_ROOT%\server\xampp-windows\
    echo   2. Ou installez PHP et ajoutez-le au PATH systeme.
    echo.
    pause
    exit /b 1
)
SET "PHP_EXE=php"
echo [OK] PHP systeme trouve dans le PATH (fallback).

:PHP_TROUVE

REM -- 3. Verification du dossier export-nuxit --------------------
IF NOT EXIST "%EXPORT_DIR%\" (
    echo.
    echo [ERREUR] Le dossier export-nuxit est introuvable :
    echo                 %EXPORT_DIR%
    echo.
    echo  Lancez d'abord lancer-export.bat pour generer l'export.
    echo.
    pause
    exit /b 1
)
echo [OK] Dossier export-nuxit trouve.
echo.

REM -- 4. Demarrage du serveur PHP sur le port %PORT% -------------
echo [INFO] Demarrage du serveur PHP sur http://localhost:%PORT% ...
echo.
start /b "" "%PHP_EXE%" -S localhost:%PORT% -t "%EXPORT_DIR%"

REM -- 5. Boucle d'attente active (max 10 tentatives x ~1s) -------
echo [INFO] Attente du demarrage du serveur...
SET /A COMPTEUR=0

:BOUCLE_ATTENTE
ping 127.0.0.1 -n 2 >nul
SET /A COMPTEUR=%COMPTEUR%+1
echo   ... tentative %COMPTEUR%/10

netstat -ano | findstr ":%PORT% " | findstr "LISTENING" >nul 2>&1
IF NOT ERRORLEVEL 1 (
    SET "SERVEUR_OK=1"
    GOTO :SERVEUR_PRET
)

IF %COMPTEUR% GEQ 10 GOTO :TIMEOUT
GOTO :BOUCLE_ATTENTE

:TIMEOUT
echo.
echo [ERREUR] Le serveur PHP ne repond pas apres 10 secondes.
echo          Verifiez qu'aucun autre processus n'occupe le port %PORT%.
echo.
pause
exit /b 1

:SERVEUR_PRET
echo.
echo [OK] Serveur PHP demarre sur http://localhost:%PORT%
echo.

REM -- 6. Ouverture du navigateur ---------------------------------
IF EXIST "%CHROME%" (
    echo [INFO] Ouverture via Chrome Portable en mode app...
    start "" "%CHROME%" --app=http://localhost:%PORT%/
) ELSE (
    echo [INFO] Chrome Portable introuvable. Ouverture avec le navigateur systeme...
    start "" http://localhost:%PORT%/
)

echo.
echo ================================================================
echo   Serveur actif sur http://localhost:%PORT%
echo   CETTE FENETRE DOIT RESTER OUVERTE.
echo   Fermez-la (ou Ctrl+C) pour arreter le serveur.
echo ================================================================
echo.

pause >nul