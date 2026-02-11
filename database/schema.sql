-- ============================================================================
-- NosaNet Laravel - Esquema de Base de Datos SQLite
-- ============================================================================
-- Archivo: database/schema.sql
-- Descripción: Script para crear la estructura de la base de datos
-- Fecha: 11 de febrero de 2026
-- ============================================================================

-- Eliminar tablas si existen
DROP TABLE IF EXISTS messages;
DROP TABLE IF EXISTS users;

-- ============================================================================
-- TABLA: users
-- Descripción: Almacena información de usuarios (profesores y alumnos)
-- ============================================================================
CREATE TABLE users (
    id TEXT PRIMARY KEY NOT NULL,
    username TEXT NOT NULL UNIQUE,
    email TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    isProfessor TEXT NOT NULL DEFAULT 'False' CHECK(isProfessor IN ('True', 'False')),
    theme TEXT NOT NULL DEFAULT 'light' CHECK(theme IN ('light', 'dark')),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Índices para optimizar búsquedas en users
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
    approved TEXT NOT NULL DEFAULT 'pending' CHECK(approved IN ('true', 'false', 'pending')),
    status TEXT NOT NULL DEFAULT 'active' CHECK(status IN ('active', 'deleted')),
    timestamp TEXT NOT NULL,
    dangerous_content TEXT NOT NULL DEFAULT 'false' CHECK(dangerous_content IN ('false', 'words', 'attack')),
    approve_reason TEXT,
    delete_reason TEXT,
    moderated_at TEXT,
    moderated_by TEXT,
    deleted_at TEXT,
    deleted_by TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user) REFERENCES users(username)
);

-- Índices para optimizar búsquedas en messages
CREATE INDEX idx_messages_user ON messages(user);
CREATE INDEX idx_messages_approved_status ON messages(approved, status);
CREATE INDEX idx_messages_asignatura ON messages(asignatura);

-- ============================================================================
-- FIN DEL ESQUEMA
-- ============================================================================
