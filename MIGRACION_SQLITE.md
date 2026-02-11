# Migración de JSON a SQLite - NosaNet Laravel

## Resumen de Cambios

El sistema ha sido migrado de un almacenamiento basado en archivos JSON a una base de datos SQLite. Todas las funcionalidades anteriores se mantienen intactas.

## Configuración de la Base de Datos

### 1. Archivo de Configuración (.env)

La configuración de la base de datos se encuentra en el archivo `.env`:

```env
DB_CONNECTION=sqlite
DB_DATABASE=C:\Users\bruno\IdeaProjects\NosaNet_Laravel\database\database.sqlite
```

### 2. Modificar la Ruta de la Base de Datos

Para cambiar la ubicación de la base de datos, edita la variable `DB_DATABASE` en el archivo `.env`:

```env
DB_DATABASE=/ruta/completa/a/tu/database.sqlite
```

**Nota para Windows:** Usa rutas absolutas con barras invertidas o barras normales:
- `C:\ruta\a\database.sqlite`
- `C:/ruta/a/database.sqlite`

**Nota para Linux/Mac:** Usa rutas absolutas:
- `/home/usuario/proyecto/database/database.sqlite`

## Estructura de las Tablas

### Tabla `users`
- `id` (string, primary key)
- `username` (string, unique)
- `email` (string, unique)
- `password` (string, hash bcrypt)
- `isProfessor` (string: 'True' o 'False')
- `theme` (string: 'light' o 'dark')
- `created_at` (timestamp)
- `updated_at` (timestamp)

### Tabla `messages`
- `id` (string, primary key)
- `user` (string)
- `title` (string)
- `text` (text)
- `asignatura` (string)
- `approved` (string: 'true', 'false', 'pending')
- `status` (string: 'active', 'deleted')
- `timestamp` (string)
- `dangerous_content` (string: 'false', 'words', 'attack')
- `approve_reason` (text, nullable)
- `delete_reason` (text, nullable)
- `moderated_at` (string, nullable)
- `moderated_by` (string, nullable)
- `deleted_at` (string, nullable)
- `deleted_by` (string, nullable)
- `created_at` (timestamp)
- `updated_at` (timestamp)

## Pasos para Configurar el Sistema

### 1. Ejecutar las Migraciones

Desde la raíz del proyecto, ejecuta:

```bash
php artisan migrate
```

Esto creará todas las tablas necesarias en la base de datos SQLite.

### 2. Migrar Datos Existentes de JSON

Si tienes datos existentes en los archivos JSON (`storage/json/users.json` y `storage/json/messages.json`), puedes migrarlos a la base de datos con el siguiente comando:

```bash
php artisan migrate:json-to-db
```

Este comando:
- Lee los archivos JSON de `storage/json/`
- Verifica si ya existen datos en la base de datos
- Migra usuarios y mensajes a SQLite
- Evita duplicados verificando IDs existentes

### 3. Verificar la Migración

Puedes verificar que los datos se migraron correctamente:

```bash
php artisan tinker
```

Luego ejecuta:
```php
\App\Models\User::count();
\App\Models\Message::count();
```

## Credenciales de Base de Datos

SQLite no requiere credenciales de usuario/contraseña. La seguridad se basa en:

1. **Permisos del archivo**: Solo el propietario del archivo debe tener permisos de lectura/escritura
2. **Ubicación del archivo**: El archivo debe estar fuera del directorio público (`public/`)

### Configurar Permisos (Linux/Mac)

```bash
chmod 644 database/database.sqlite
chmod 755 database/
```

### Configurar Permisos (Windows)

- Click derecho en `database.sqlite`
- Propiedades > Seguridad
- Editar permisos para limitar el acceso solo a tu usuario

## Backup de la Base de Datos

### Crear Backup

Para hacer una copia de seguridad de tu base de datos:

**Linux/Mac:**
```bash
cp database/database.sqlite database/backup_$(date +%Y%m%d_%H%M%S).sqlite
```

**Windows:**
```cmd
copy database\database.sqlite database\backup_%date:~-4,4%%date:~-10,2%%date:~-7,2%.sqlite
```

### Restaurar Backup

Simplemente reemplaza el archivo actual con el backup:

```bash
cp database/backup_20250211_120000.sqlite database/database.sqlite
```

## Cambios en el Código

### Modelos Actualizados

- **`User`**: Ahora extiende `Illuminate\Database\Eloquent\Model` en lugar de `JsonModel`
- **`Message`**: Ahora extiende `Illuminate\Database\Eloquent\Model` en lugar de `JsonModel`

### Controladores Actualizados

Todos los controladores han sido actualizados para trabajar con Eloquent:
- `AuthController`: Registro de usuarios
- `LoginController`: Login y logout
- `MessageController`: Crear y listar mensajes
- `ModerationController`: Aprobar y eliminar mensajes
- `ThemeController`: Cambiar tema de usuario

### Archivos Obsoletos

Los siguientes archivos ya no se utilizan pero se mantienen por compatibilidad:
- `app/Models/JsonModel.php` - Modelo base antiguo
- `storage/json/users.json` - Datos antiguos de usuarios
- `storage/json/messages.json` - Datos antiguos de mensajes

**Puedes eliminarlos después de verificar que todo funciona correctamente.**

## Solución de Problemas

### Error: "Database not found"

1. Verifica que el archivo `database.sqlite` existe
2. Verifica que la ruta en `.env` es correcta y absoluta
3. Ejecuta `php artisan migrate` para crear las tablas

### Error: "SQLSTATE[HY000]: General error: 8 attempt to write a readonly database"

1. Verifica los permisos del archivo `database.sqlite`
2. Verifica los permisos del directorio `database/`
3. En Windows, asegúrate de que el archivo no está marcado como "solo lectura"

### Los datos no aparecen después de migrar

1. Verifica que los archivos JSON existen en `storage/json/`
2. Ejecuta el comando de migración de nuevo: `php artisan migrate:json-to-db`
3. Verifica que las rutas en el comando apuntan a los archivos correctos

## Comandos Útiles

```bash
# Ver estructura de la base de datos
php artisan db:show

# Ver tablas
php artisan db:table users
php artisan db:table messages

# Limpiar base de datos y volver a migrar
php artisan migrate:fresh

# Limpiar y migrar con datos de JSON
php artisan migrate:fresh && php artisan migrate:json-to-db

# Abrir consola interactiva
php artisan tinker
```

## Contacto y Soporte

Para cualquier problema o pregunta sobre la migración, contacta al desarrollador del sistema.

---

**Fecha de migración:** 11 de febrero de 2026
**Versión:** 1.0
