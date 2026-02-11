#!/bin/bash
# ============================================================================
# NosaNet Laravel - Script de Configuración de Base de Datos
# ============================================================================
# Archivo: database/setup_database.sh
# Descripción: Script para crear y poblar la base de datos SQLite
# Uso: bash database/setup_database.sh
# ============================================================================

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para imprimir mensajes con color
print_info() {
    echo -e "${BLUE}[INFO]${NC} $1"
}

print_success() {
    echo -e "${GREEN}[SUCCESS]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Verificar si sqlite3 está instalado
if ! command -v sqlite3 &> /dev/null; then
    print_error "sqlite3 no está instalado. Por favor, instálalo primero."
    print_info "Ubuntu/Debian: sudo apt-get install sqlite3"
    print_info "MacOS: brew install sqlite3"
    print_info "Windows: Descarga desde https://www.sqlite.org/download.html"
    exit 1
fi

# Directorio del proyecto
PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
DATABASE_DIR="$PROJECT_DIR/database"
DATABASE_FILE="$DATABASE_DIR/database.sqlite"
SCHEMA_FILE="$DATABASE_DIR/schema.sql"
SEED_FILE="$DATABASE_DIR/seed.sql"

print_info "Directorio del proyecto: $PROJECT_DIR"
print_info "Base de datos: $DATABASE_FILE"

# Verificar si los archivos SQL existen
if [ ! -f "$SCHEMA_FILE" ]; then
    print_error "No se encontró el archivo schema.sql en $SCHEMA_FILE"
    exit 1
fi

if [ ! -f "$SEED_FILE" ]; then
    print_warning "No se encontró el archivo seed.sql en $SEED_FILE"
    print_info "Se creará solo la estructura sin datos de prueba"
    SEED_FILE=""
fi

# Preguntar si desea sobrescribir la base de datos existente
if [ -f "$DATABASE_FILE" ]; then
    print_warning "La base de datos ya existe en: $DATABASE_FILE"
    read -p "¿Desea sobrescribirla? Esto eliminará todos los datos existentes. (s/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[SsYy]$ ]]; then
        print_info "Operación cancelada por el usuario"
        exit 0
    fi
    print_warning "Eliminando base de datos existente..."
    rm "$DATABASE_FILE"
fi

# Crear el directorio database si no existe
mkdir -p "$DATABASE_DIR"

# Crear la base de datos y ejecutar el esquema
print_info "Creando estructura de la base de datos..."
sqlite3 "$DATABASE_FILE" < "$SCHEMA_FILE"

if [ $? -eq 0 ]; then
    print_success "Estructura de base de datos creada exitosamente"
else
    print_error "Error al crear la estructura de la base de datos"
    exit 1
fi

# Insertar datos de prueba si existe el archivo seed
if [ -n "$SEED_FILE" ]; then
    print_info "Insertando datos de prueba..."
    sqlite3 "$DATABASE_FILE" < "$SEED_FILE"

    if [ $? -eq 0 ]; then
        print_success "Datos de prueba insertados exitosamente"
    else
        print_error "Error al insertar datos de prueba"
        exit 1
    fi
fi

# Establecer permisos correctos
print_info "Configurando permisos..."
chmod 644 "$DATABASE_FILE"
chmod 755 "$DATABASE_DIR"

# Mostrar información final
echo ""
print_success "¡Base de datos configurada correctamente!"
echo ""
print_info "Ubicación: $DATABASE_FILE"
print_info "Tamaño: $(du -h "$DATABASE_FILE" | cut -f1)"
echo ""

# Mostrar credenciales si se insertaron datos de prueba
if [ -n "$SEED_FILE" ]; then
    echo -e "${GREEN}============================================${NC}"
    echo -e "${GREEN}        CREDENCIALES DE ACCESO${NC}"
    echo -e "${GREEN}============================================${NC}"
    echo ""
    echo -e "${BLUE}PROFESOR:${NC}"
    echo "  Usuario:    profesor"
    echo "  Email:      profesor@nosanet.com"
    echo "  Contraseña: profesor123"
    echo ""
    echo -e "${BLUE}ALUMNO:${NC}"
    echo "  Usuario:    alumno"
    echo "  Email:      alumno@nosanet.com"
    echo "  Contraseña: alumno123"
    echo ""
    echo -e "${YELLOW}Otros usuarios de prueba:${NC}"
    echo "  - maria.garcia (Profesora) - profesor123"
    echo "  - juan.lopez (Alumno) - alumno123"
    echo "  - ana.martinez (Alumna) - alumno123"
    echo ""
    echo -e "${GREEN}============================================${NC}"
fi

echo ""
print_info "Próximos pasos:"
echo "  1. Verifica el archivo .env y asegúrate de que DB_DATABASE apunte a:"
echo "     $DATABASE_FILE"
echo "  2. Si usas Laravel, ejecuta: php artisan migrate:status"
echo "  3. Inicia tu aplicación: php artisan serve"
echo ""

exit 0
