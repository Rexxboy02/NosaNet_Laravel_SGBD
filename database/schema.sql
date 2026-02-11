-- ============================================================================
-- NosaNet Laravel - Esquema de Base de Datos SQLite
-- ============================================================================
-- Archivo: database/schema.sql
-- Descripción: Script para crear la estructura completa de la base de datos
-- Fecha: 11 de febrero de 2026
-- ============================================================================

-- Eliminar tablas si existen (para recrear desde cero)
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS users;
DROP TABLE IF EXISTS password_reset_tokens;
DROP TABLE IF EXISTS sessions;
DROP TABLE IF EXISTS cache;
DROP TABLE IF EXISTS cache_locks;
DROP TABLE IF EXISTS jobs;
DROP TABLE IF EXISTS job_batches;
DROP TABLE IF EXISTS failed_jobs;

-- ============================================================================
-- TABLA: users
-- Descripción: Almacena información de usuarios (profesores y alumnos)
-- ============================================================================
CREATE TABLE users (
    id TEXT PRIMARY KEY NOT NULL,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    isProfessor TEXT NOT NULL DEFAULT 'False',
    theme TEXT NOT NULL DEFAULT 'light',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimizar búsquedas
CREATE UNIQUE INDEX idx_users_username ON users(username);
CREATE UNIQUE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_isProfessor ON users(isProfessor);

-- ============================================================================
-- TABLA: messages
-- Descripción: Almacena mensajes publicados por los usuarios
-- ============================================================================
CREATE TABLE messages (
    id TEXT PRIMARY KEY NOT NULL,
    user TEXT NOT NULL,
    title TEXT NOT NULL,
    text TEXT NOT NULL,
    asignatura TEXT NOT NULL,
    approved TEXT NOT NULL DEFAULT 'pending',
    status TEXT NOT NULL DEFAULT 'active',
    timestamp TEXT NOT NULL,
    dangerous_content TEXT NOT NULL DEFAULT 'false',
    approve_reason TEXT,
    delete_reason TEXT,
    moderated_at TEXT,
    moderated_by TEXT,
    deleted_at TEXT,
    deleted_by TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user) REFERENCES users(username),
    FOREIGN KEY (moderated_by) REFERENCES users(username),
    FOREIGN KEY (deleted_by) REFERENCES users(username)
);

-- Índices para optimizar búsquedas
CREATE INDEX idx_messages_user ON messages(user);
CREATE INDEX idx_messages_approved ON messages(approved);
CREATE INDEX idx_messages_status ON messages(status);
CREATE INDEX idx_messages_moderated_by ON messages(moderated_by);
CREATE INDEX idx_messages_deleted_by ON messages(deleted_by);
CREATE INDEX idx_messages_approved_status ON messages(approved, status);
CREATE INDEX idx_messages_asignatura ON messages(asignatura);
CREATE INDEX idx_messages_dangerous_content ON messages(dangerous_content);

-- ============================================================================
-- TABLA: password_reset_tokens
-- Descripción: Tokens para reseteo de contraseñas
-- ============================================================================
CREATE TABLE password_reset_tokens (
    email TEXT PRIMARY KEY NOT NULL,
    token TEXT NOT NULL,
    created_at DATETIME
);

-- ============================================================================
-- TABLA: sessions
-- Descripción: Sesiones de usuarios activos
-- ============================================================================
CREATE TABLE sessions (
    id TEXT PRIMARY KEY NOT NULL,
    user_id TEXT,
    ip_address TEXT,
    user_agent TEXT,
    payload TEXT NOT NULL,
    last_activity INTEGER NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE INDEX idx_sessions_user_id ON sessions(user_id);
CREATE INDEX idx_sessions_last_activity ON sessions(last_activity);

-- ============================================================================
-- TABLA: cache
-- Descripción: Sistema de caché de Laravel
-- ============================================================================
CREATE TABLE cache (
    key TEXT PRIMARY KEY NOT NULL,
    value TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE INDEX idx_cache_expiration ON cache(expiration);

-- ============================================================================
-- TABLA: cache_locks
-- Descripción: Locks del sistema de caché
-- ============================================================================
CREATE TABLE cache_locks (
    key TEXT PRIMARY KEY NOT NULL,
    owner TEXT NOT NULL,
    expiration INTEGER NOT NULL
);

CREATE INDEX idx_cache_locks_expiration ON cache_locks(expiration);

-- ============================================================================
-- TABLA: jobs
-- Descripción: Cola de trabajos de Laravel
-- ============================================================================
CREATE TABLE jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    attempts INTEGER NOT NULL,
    reserved_at INTEGER,
    available_at INTEGER NOT NULL,
    created_at INTEGER NOT NULL
);

CREATE INDEX idx_jobs_queue ON jobs(queue);

-- ============================================================================
-- TABLA: job_batches
-- Descripción: Lotes de trabajos
-- ============================================================================
CREATE TABLE job_batches (
    id TEXT PRIMARY KEY NOT NULL,
    name TEXT NOT NULL,
    total_jobs INTEGER NOT NULL,
    pending_jobs INTEGER NOT NULL,
    failed_jobs INTEGER NOT NULL,
    failed_job_ids TEXT NOT NULL,
    options TEXT,
    cancelled_at INTEGER,
    created_at INTEGER NOT NULL,
    finished_at INTEGER
);

-- ============================================================================
-- TABLA: failed_jobs
-- Descripción: Registro de trabajos fallidos
-- ============================================================================
CREATE TABLE failed_jobs (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    uuid TEXT NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload TEXT NOT NULL,
    exception TEXT NOT NULL,
    failed_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE UNIQUE INDEX idx_failed_jobs_uuid ON failed_jobs(uuid);

-- ============================================================================
-- VISTAS ÚTILES
-- ============================================================================

-- Vista: Mensajes con información del usuario
CREATE VIEW IF NOT EXISTS v_messages_with_users AS
SELECT
    m.*,
    u.email as user_email,
    u.isProfessor as user_isProfessor,
    mod.username as moderator_username,
    mod.email as moderator_email
FROM messages m
LEFT JOIN users u ON m.user = u.username
LEFT JOIN users mod ON m.moderated_by = mod.username;

-- Vista: Mensajes aprobados y activos
CREATE VIEW IF NOT EXISTS v_approved_messages AS
SELECT * FROM messages
WHERE approved = 'true' AND status = 'active'
ORDER BY timestamp DESC;

-- Vista: Mensajes pendientes de moderación
CREATE VIEW IF NOT EXISTS v_pending_messages AS
SELECT * FROM messages
WHERE approved = 'pending' AND status = 'active'
ORDER BY timestamp DESC;

-- Vista: Profesores/Moderadores
CREATE VIEW IF NOT EXISTS v_professors AS
SELECT * FROM users
WHERE isProfessor = 'True';

-- Vista: Alumnos
CREATE VIEW IF NOT EXISTS v_students AS
SELECT * FROM users
WHERE isProfessor = 'False';

-- ============================================================================
-- TRIGGERS
-- ============================================================================

-- Trigger: Actualizar updated_at en users
CREATE TRIGGER IF NOT EXISTS update_users_timestamp
AFTER UPDATE ON users
BEGIN
    UPDATE users SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Trigger: Actualizar updated_at en messages
CREATE TRIGGER IF NOT EXISTS update_messages_timestamp
AFTER UPDATE ON messages
BEGIN
    UPDATE messages SET updated_at = CURRENT_TIMESTAMP WHERE id = NEW.id;
END;

-- Trigger: Validar isProfessor solo permite 'True' o 'False'
CREATE TRIGGER IF NOT EXISTS validate_isProfessor_insert
BEFORE INSERT ON users
BEGIN
    SELECT CASE
        WHEN NEW.isProfessor NOT IN ('True', 'False') THEN
            RAISE(ABORT, 'isProfessor debe ser "True" o "False"')
    END;
END;

CREATE TRIGGER IF NOT EXISTS validate_isProfessor_update
BEFORE UPDATE ON users
BEGIN
    SELECT CASE
        WHEN NEW.isProfessor NOT IN ('True', 'False') THEN
            RAISE(ABORT, 'isProfessor debe ser "True" o "False"')
    END;
END;

-- Trigger: Validar theme solo permite 'light' o 'dark'
CREATE TRIGGER IF NOT EXISTS validate_theme_insert
BEFORE INSERT ON users
BEGIN
    SELECT CASE
        WHEN NEW.theme NOT IN ('light', 'dark') THEN
            RAISE(ABORT, 'theme debe ser "light" o "dark"')
    END;
END;

CREATE TRIGGER IF NOT EXISTS validate_theme_update
BEFORE UPDATE ON users
BEGIN
    SELECT CASE
        WHEN NEW.theme NOT IN ('light', 'dark') THEN
            RAISE(ABORT, 'theme debe ser "light" o "dark"')
    END;
END;

-- Trigger: Validar approved en messages
CREATE TRIGGER IF NOT EXISTS validate_approved_insert
BEFORE INSERT ON messages
BEGIN
    SELECT CASE
        WHEN NEW.approved NOT IN ('true', 'false', 'pending') THEN
            RAISE(ABORT, 'approved debe ser "true", "false" o "pending"')
    END;
END;

CREATE TRIGGER IF NOT EXISTS validate_approved_update
BEFORE UPDATE ON messages
BEGIN
    SELECT CASE
        WHEN NEW.approved NOT IN ('true', 'false', 'pending') THEN
            RAISE(ABORT, 'approved debe ser "true", "false" o "pending"')
    END;
END;

-- Trigger: Validar status en messages
CREATE TRIGGER IF NOT EXISTS validate_status_insert
BEFORE INSERT ON messages
BEGIN
    SELECT CASE
        WHEN NEW.status NOT IN ('active', 'deleted') THEN
            RAISE(ABORT, 'status debe ser "active" o "deleted"')
    END;
END;

CREATE TRIGGER IF NOT EXISTS validate_status_update
BEFORE UPDATE ON messages
BEGIN
    SELECT CASE
        WHEN NEW.status NOT IN ('active', 'deleted') THEN
            RAISE(ABORT, 'status debe ser "active" o "deleted"')
    END;
END;

-- Trigger: Validar dangerous_content en messages
CREATE TRIGGER IF NOT EXISTS validate_dangerous_content_insert
BEFORE INSERT ON messages
BEGIN
    SELECT CASE
        WHEN NEW.dangerous_content NOT IN ('false', 'words', 'attack') THEN
            RAISE(ABORT, 'dangerous_content debe ser "false", "words" o "attack"')
    END;
END;

CREATE TRIGGER IF NOT EXISTS validate_dangerous_content_update
BEFORE UPDATE ON messages
BEGIN
    SELECT CASE
        WHEN NEW.dangerous_content NOT IN ('false', 'words', 'attack') THEN
            RAISE(ABORT, 'dangerous_content debe ser "false", "words" o "attack"')
    END;
END;

-- ============================================================================
-- FIN DEL ESQUEMA
-- ============================================================================

-- Verificar la creación de tablas
SELECT 'Esquema de base de datos creado exitosamente' as status;
SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;
