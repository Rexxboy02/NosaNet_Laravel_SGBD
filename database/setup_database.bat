@echo off
REM ============================================================================
REM NosaNet Laravel - Script de Configuración de Base de Datos (Windows)
REM ============================================================================
REM Archivo: database/setup_database.bat
REM Descripción: Script para crear y poblar la base de datos SQLite en Windows
REM Uso: database\setup_database.bat
REM ============================================================================

setlocal enabledelayedexpansion

echo.
echo ============================================
echo   NosaNet - Configuracion de Base de Datos
echo ============================================
echo.

REM Verificar si sqlite3 está disponible
where sqlite3 >nul 2>nul
if %errorlevel% neq 0 (
    echo [ERROR] sqlite3 no esta instalado o no esta en el PATH
    echo.
    echo Por favor, descarga SQLite desde: https://www.sqlite.org/download.html
    echo Extrae sqlite3.exe y agregalo al PATH del sistema
    echo.
    pause
    exit /b 1
)

REM Obtener el directorio del proyecto
set "PROJECT_DIR=%~dp0.."
set "DATABASE_DIR=%PROJECT_DIR%\database"
set "DATABASE_FILE=%DATABASE_DIR%\database.sqlite"
set "SCHEMA_FILE=%DATABASE_DIR%\schema.sql"
set "SEED_FILE=%DATABASE_DIR%\seed.sql"

echo [INFO] Directorio del proyecto: %PROJECT_DIR%
echo [INFO] Base de datos: %DATABASE_FILE%
echo.

REM Verificar si los archivos SQL existen
if not exist "%SCHEMA_FILE%" (
    echo [ERROR] No se encontro el archivo schema.sql en %SCHEMA_FILE%
    pause
    exit /b 1
)

if not exist "%SEED_FILE%" (
    echo [WARNING] No se encontro el archivo seed.sql en %SEED_FILE%
    echo [INFO] Se creara solo la estructura sin datos de prueba
    set "SEED_FILE="
)

REM Preguntar si desea sobrescribir la base de datos existente
if exist "%DATABASE_FILE%" (
    echo [WARNING] La base de datos ya existe en: %DATABASE_FILE%
    set /p "RESPUESTA=Desea sobrescribirla? Esto eliminara todos los datos existentes. (S/N): "
    if /i not "!RESPUESTA!"=="S" (
        if /i not "!RESPUESTA!"=="Y" (
            echo [INFO] Operacion cancelada por el usuario
            pause
            exit /b 0
        )
    )
    echo [WARNING] Eliminando base de datos existente...
    del "%DATABASE_FILE%" >nul 2>nul
)

REM Crear el directorio database si no existe
if not exist "%DATABASE_DIR%" (
    mkdir "%DATABASE_DIR%"
)

REM Crear la base de datos y ejecutar el esquema
echo [INFO] Creando estructura de la base de datos...
sqlite3 "%DATABASE_FILE%" < "%SCHEMA_FILE%"

if %errorlevel% equ 0 (
    echo [SUCCESS] Estructura de base de datos creada exitosamente
) else (
    echo [ERROR] Error al crear la estructura de la base de datos
    pause
    exit /b 1
)

REM Insertar datos de prueba si existe el archivo seed
if defined SEED_FILE (
    echo [INFO] Insertando datos de prueba...
    sqlite3 "%DATABASE_FILE%" < "%SEED_FILE%"

    if !errorlevel! equ 0 (
        echo [SUCCESS] Datos de prueba insertados exitosamente
    ) else (
        echo [ERROR] Error al insertar datos de prueba
        pause
        exit /b 1
    )
)

REM Mostrar información final
echo.
echo [SUCCESS] Base de datos configurada correctamente!
echo.
echo [INFO] Ubicacion: %DATABASE_FILE%

REM Calcular tamaño del archivo
for %%A in ("%DATABASE_FILE%") do set "SIZE=%%~zA"
set /a SIZE_KB=!SIZE! / 1024
echo [INFO] Tamanio: !SIZE_KB! KB
echo.

REM Mostrar credenciales si se insertaron datos de prueba
if defined SEED_FILE (
    echo ============================================
    echo         CREDENCIALES DE ACCESO
    echo ============================================
    echo.
    echo PROFESOR:
    echo   Usuario:    profesor
    echo   Email:      profesor@nosanet.com
    echo   Contraseña: profesor123
    echo.
    echo ALUMNO:
    echo   Usuario:    alumno
    echo   Email:      alumno@nosanet.com
    echo   Contraseña: alumno123
    echo.
    echo Otros usuarios de prueba:
    echo   - maria.garcia ^(Profesora^) - profesor123
    echo   - juan.lopez ^(Alumno^) - alumno123
    echo   - ana.martinez ^(Alumna^) - alumno123
    echo.
    echo ============================================
)

echo.
echo [INFO] Proximos pasos:
echo   1. Verifica el archivo .env y asegurate de que DB_DATABASE apunte a:
echo      %DATABASE_FILE%
echo   2. Si usas Laravel, ejecuta: php artisan migrate:status
echo   3. Inicia tu aplicacion: php artisan serve
echo.

pause
exit /b 0
