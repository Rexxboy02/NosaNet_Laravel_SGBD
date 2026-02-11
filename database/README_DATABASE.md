# Base de Datos NosaNet - Instrucciones

## 📁 Archivos Incluidos

- **`schema.sql`** - Esquema completo de la base de datos con tablas, índices, vistas y triggers
- **`seed.sql`** - Datos de prueba iniciales (usuarios y mensajes)
- **`setup_database.sh`** - Script automático para Linux/Mac
- **`setup_database.bat`** - Script automático para Windows
- **`README_DATABASE.md`** - Este archivo con instrucciones

## 🚀 Instalación Rápida

### Opción 1: Usando Scripts Automáticos (Recomendado)

#### En Windows:
```cmd
cd database
setup_database.bat
```

#### En Linux/Mac:
```bash
cd database
chmod +x setup_database.sh
./setup_database.sh
```

Los scripts automáticos:
- ✅ Verifican que SQLite3 esté instalado
- ✅ Crean la base de datos desde cero
- ✅ Ejecutan el esquema completo
- ✅ Insertan los datos de prueba
- ✅ Configuran los permisos correctos
- ✅ Muestran las credenciales de acceso

### Opción 2: Ejecución Manual

#### 1. Crear la base de datos vacía:

**Windows:**
```cmd
type nul > database.sqlite
```

**Linux/Mac:**
```bash
touch database.sqlite
```

#### 2. Ejecutar el esquema:

```bash
sqlite3 database.sqlite < schema.sql
```

#### 3. Insertar datos de prueba (opcional):

```bash
sqlite3 database.sqlite < seed.sql
```

## 👥 Credenciales de Acceso

### Profesor (Moderador)
- **Usuario:** `profesor`
- **Email:** `profesor@nosanet.com`
- **Contraseña:** `profesor123`
- **Permisos:** Puede publicar mensajes (aprobados automáticamente), moderar y eliminar mensajes de otros usuarios

### Alumno
- **Usuario:** `alumno`
- **Email:** `alumno@nosanet.com`
- **Contraseña:** `alumno123`
- **Permisos:** Puede publicar mensajes (requieren moderación antes de ser visibles)

### Usuarios Adicionales

| Usuario | Rol | Email | Contraseña |
|---------|-----|-------|------------|
| maria.garcia | Profesora | maria.garcia@nosanet.com | profesor123 |
| juan.lopez | Alumno | juan.lopez@nosanet.com | alumno123 |
| ana.martinez | Alumna | ana.martinez@nosanet.com | alumno123 |

> **Nota:** Las contraseñas están hasheadas con bcrypt (12 rounds). Los valores mostrados son las contraseñas en texto plano que debes usar para iniciar sesión.

## 📊 Estructura de la Base de Datos

### Tablas Principales

#### 1. **users**
Almacena información de usuarios (profesores y alumnos)

```sql
- id (TEXT PRIMARY KEY)
- username (TEXT UNIQUE)
- email (TEXT UNIQUE)
- password (TEXT) -- Hash bcrypt
- isProfessor (TEXT) -- 'True' o 'False'
- theme (TEXT) -- 'light' o 'dark'
- created_at (DATETIME)
- updated_at (DATETIME)
```

#### 2. **messages**
Almacena mensajes publicados por los usuarios

```sql
- id (TEXT PRIMARY KEY)
- user (TEXT FK -> users.username)
- title (TEXT)
- text (TEXT)
- asignatura (TEXT)
- approved (TEXT) -- 'true', 'false', 'pending'
- status (TEXT) -- 'active', 'deleted'
- timestamp (TEXT)
- dangerous_content (TEXT) -- 'false', 'words', 'attack'
- approve_reason (TEXT nullable)
- delete_reason (TEXT nullable)
- moderated_at (TEXT nullable)
- moderated_by (TEXT nullable FK -> users.username)
- deleted_at (TEXT nullable)
- deleted_by (TEXT nullable FK -> users.username)
- created_at (DATETIME)
- updated_at (DATETIME)
```

### Vistas Útiles

El esquema incluye varias vistas para facilitar consultas:

- **`v_messages_with_users`** - Mensajes con información completa del usuario
- **`v_approved_messages`** - Solo mensajes aprobados y activos
- **`v_pending_messages`** - Mensajes pendientes de moderación
- **`v_professors`** - Lista de profesores
- **`v_students`** - Lista de alumnos

### Triggers Automáticos

El esquema incluye triggers que:

- ✅ Actualizan automáticamente `updated_at` en cambios
- ✅ Validan que `isProfessor` solo sea 'True' o 'False'
- ✅ Validan que `theme` solo sea 'light' o 'dark'
- ✅ Validan que `approved` solo sea 'true', 'false' o 'pending'
- ✅ Validan que `status` solo sea 'active' o 'deleted'
- ✅ Validan que `dangerous_content` solo sea 'false', 'words' o 'attack'

## 📝 Datos de Ejemplo Incluidos

El archivo `seed.sql` inserta:

- **5 usuarios**: 2 profesores y 3 alumnos
- **10 mensajes**:
  - 6 mensajes aprobados y activos
  - 3 mensajes pendientes de moderación
  - 1 mensaje eliminado por contenido inapropiado

Los mensajes cubren varias asignaturas: Matemáticas, Historia, Ciencias Naturales, Lengua e Inglés.

## 🔧 Consultas SQL Útiles

### Ver todos los usuarios
```sql
SELECT id, username, email, isProfessor, theme FROM users;
```

### Ver todos los mensajes activos
```sql
SELECT * FROM v_approved_messages;
```

### Ver mensajes pendientes de moderación
```sql
SELECT * FROM v_pending_messages;
```

### Ver mensajes de un usuario específico
```sql
SELECT * FROM messages WHERE user = 'alumno';
```

### Estadísticas rápidas
```sql
SELECT
    (SELECT COUNT(*) FROM users) as total_usuarios,
    (SELECT COUNT(*) FROM users WHERE isProfessor = 'True') as profesores,
    (SELECT COUNT(*) FROM users WHERE isProfessor = 'False') as alumnos,
    (SELECT COUNT(*) FROM messages) as total_mensajes,
    (SELECT COUNT(*) FROM messages WHERE approved = 'true' AND status = 'active') as mensajes_aprobados,
    (SELECT COUNT(*) FROM messages WHERE approved = 'pending') as mensajes_pendientes;
```

## 🛠️ Herramientas de Gestión

### DB Browser for SQLite (Recomendado)
**Gratuito y multiplataforma**

- **Descargar:** https://sqlitebrowser.org/
- **Características:** Interfaz gráfica, editor SQL, visualización de esquemas

### DBeaver (Alternativa)
**Gratuito y profesional**

- **Descargar:** https://dbeaver.io/
- **Características:** Soporte multi-base de datos, autocompletado SQL

### Desde la línea de comandos

```bash
# Abrir base de datos
sqlite3 database.sqlite

# Comandos útiles dentro de sqlite3:
.tables              # Listar todas las tablas
.schema users        # Ver esquema de la tabla users
.mode column         # Modo columna para mejor visualización
.headers on          # Mostrar encabezados
SELECT * FROM users; # Ejecutar consulta
.quit                # Salir
```

## 📋 Configuración en Laravel

Asegúrate de que tu archivo `.env` tenga la configuración correcta:

```env
DB_CONNECTION=sqlite
DB_DATABASE=/ruta/completa/a/NosaNet_Laravel/database/database.sqlite
```

**En Windows:**
```env
DB_DATABASE=C:\Users\bruno\IdeaProjects\NosaNet_Laravel\database\database.sqlite
```

**En Linux/Mac:**
```env
DB_DATABASE=/home/usuario/NosaNet_Laravel/database/database.sqlite
```

## 🔄 Resetear la Base de Datos

Si necesitas resetear la base de datos a su estado inicial:

**Opción 1: Volver a ejecutar el script**
```bash
# Windows
database\setup_database.bat

# Linux/Mac
./database/setup_database.sh
```

**Opción 2: Manual**
```bash
# Eliminar base de datos actual
rm database.sqlite

# Volver a crear
sqlite3 database.sqlite < schema.sql
sqlite3 database.sqlite < seed.sql
```

## ⚠️ Notas Importantes

1. **Contraseñas:** Todas las contraseñas están hasheadas con bcrypt. No intentes cambiarlas directamente en la base de datos.

2. **IDs:** Los IDs son strings generados con `uniqid()`. No uses IDs auto-incrementales.

3. **Backups:** Haz backups regulares copiando el archivo `database.sqlite` a un lugar seguro.

4. **Permisos:** En producción, asegúrate de que el archivo de base de datos tenga los permisos correctos:
   ```bash
   chmod 644 database.sqlite
   chmod 755 database/
   ```

5. **Git:** El archivo `.gitignore` debe incluir `database.sqlite` para evitar subir datos locales al repositorio.

## 🆘 Solución de Problemas

### Error: "sqlite3: command not found"

**Windows:**
1. Descarga SQLite desde https://www.sqlite.org/download.html
2. Extrae `sqlite3.exe` a una carpeta
3. Añade la carpeta al PATH del sistema

**Linux:**
```bash
sudo apt-get install sqlite3
```

**Mac:**
```bash
brew install sqlite3
```

### Error: "database is locked"

Cierra todas las conexiones abiertas a la base de datos (aplicaciones, terminales, etc.)

### Error: "unable to open database file"

Verifica que:
1. La ruta en `.env` sea correcta y absoluta
2. El directorio `database/` exista
3. Tengas permisos de escritura en el directorio

## 📞 Soporte

Para más información sobre la migración, consulta:
- `MIGRACION_SQLITE.md` - Guía completa de migración
- `DIAGRAMA_ER.md` - Diagrama Entidad-Relación
- `README.md` - Documentación general del proyecto

---

**Última actualización:** 11 de febrero de 2026
**Versión de la base de datos:** 1.0
