-- ============================================================================
-- NosaNet Laravel - Datos de Prueba (Seed)
-- ============================================================================
-- Archivo: database/seed.sql
-- Descripción: Script para insertar datos iniciales de prueba en la base de datos
-- Fecha: 11 de febrero de 2026
-- ============================================================================

-- Limpiar datos existentes (opcional, comentar si no se desea)
-- DELETE FROM messages;
-- DELETE FROM users;

-- ============================================================================
-- SECCIÓN 1: USUARIOS
-- ============================================================================

-- Usuario 1: Profesor (Moderador)
INSERT INTO users (id, username, email, password, isProfessor, theme, created_at, updated_at)
VALUES (
    '67abcd1234prof',
    'profesor',
    'profesor@nosanet.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/lewKrE8QjSW8RJz9u', -- Contraseña: "profesor123"
    'True',
    'light',
    datetime('now'),
    datetime('now')
);

-- Usuario 2: Alumno
INSERT INTO users (id, username, email, password, isProfessor, theme, created_at, updated_at)
VALUES (
    '67abcd5678alum',
    'alumno',
    'alumno@nosanet.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/lewKrE8QjSW8RJz9u', -- Contraseña: "alumno123"
    'False',
    'dark',
    datetime('now'),
    datetime('now')
);

-- Usuario 3: Profesor adicional
INSERT INTO users (id, username, email, password, isProfessor, theme, created_at, updated_at)
VALUES (
    '67abcd9012prof2',
    'maria.garcia',
    'maria.garcia@nosanet.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/lewKrE8QjSW8RJz9u', -- Contraseña: "profesor123"
    'True',
    'light',
    datetime('now'),
    datetime('now')
);

-- Usuario 4: Alumno adicional
INSERT INTO users (id, username, email, password, isProfessor, theme, created_at, updated_at)
VALUES (
    '67abcd3456alum2',
    'juan.lopez',
    'juan.lopez@nosanet.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/lewKrE8QjSW8RJz9u', -- Contraseña: "alumno123"
    'False',
    'light',
    datetime('now'),
    datetime('now')
);

-- Usuario 5: Alumno adicional
INSERT INTO users (id, username, email, password, isProfessor, theme, created_at, updated_at)
VALUES (
    '67abcd7890alum3',
    'ana.martinez',
    'ana.martinez@nosanet.com',
    '$2y$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMQJqhN8/lewKrE8QjSW8RJz9u', -- Contraseña: "alumno123"
    'False',
    'dark',
    datetime('now'),
    datetime('now')
);

-- ============================================================================
-- SECCIÓN 2: MENSAJES
-- ============================================================================

-- Mensaje 1: Mensaje aprobado del profesor
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg001',
    'profesor',
    'Bienvenidos al curso de Matemáticas',
    'Bienvenidos a todos los estudiantes. Este año trabajaremos con álgebra, geometría y cálculo básico.',
    'Matemáticas',
    'true',
    'active',
    '10:30 11/02/2026',
    'false',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    datetime('now', '-2 days'),
    datetime('now', '-2 days')
);

-- Mensaje 2: Mensaje aprobado de un alumno
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg002',
    'alumno',
    '¿Dudas sobre los ejercicios?',
    'Hola, tengo dudas sobre los ejercicios de la página 45. ¿Alguien me puede ayudar?',
    'Matemáticas',
    'true',
    'active',
    '14:15 11/02/2026',
    'false',
    'Mensaje educativo apropiado',
    NULL,
    '14:20 11/02/2026',
    'profesor',
    NULL,
    NULL,
    datetime('now', '-1 day'),
    datetime('now', '-1 day')
);

-- Mensaje 3: Mensaje pendiente de moderación
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg003',
    'juan.lopez',
    'Consulta sobre el examen',
    '¿Cuándo será el próximo examen de Historia? Necesito organizarme con otras asignaturas.',
    'Historia',
    'pending',
    'active',
    '09:45 11/02/2026',
    'false',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    datetime('now', '-5 hours'),
    datetime('now', '-5 hours')
);

-- Mensaje 4: Mensaje con palabras detectadas pero aprobado
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg004',
    'ana.martinez',
    'Proyecto de Ciencias',
    'Nuestro grupo necesita más información para el proyecto. ¿Alguien tiene recursos adicionales?',
    'Ciencias Naturales',
    'true',
    'active',
    '16:30 10/02/2026',
    'false',
    'Contenido educativo verificado',
    NULL,
    '17:00 10/02/2026',
    'maria.garcia',
    NULL,
    NULL,
    datetime('now', '-1 day', '-8 hours'),
    datetime('now', '-1 day', '-7 hours')
);

-- Mensaje 5: Mensaje eliminado por contenido inapropiado
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg005',
    'juan.lopez',
    'Mensaje de prueba',
    'Este es un mensaje con contenido que fue eliminado',
    'General',
    'false',
    'deleted',
    '11:20 10/02/2026',
    'words',
    NULL,
    'Contenido inapropiado detectado por el sistema',
    NULL,
    NULL,
    '11:25 10/02/2026',
    'profesor',
    datetime('now', '-1 day', '-12 hours'),
    datetime('now', '-1 day', '-12 hours')
);

-- Mensaje 6: Mensaje aprobado del profesor en Lengua
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg006',
    'maria.garcia',
    'Recordatorio: Entrega de ensayos',
    'Recuerden que la fecha límite para entregar sus ensayos es el viernes. No se aceptarán trabajos tardíos.',
    'Lengua',
    'true',
    'active',
    '08:00 11/02/2026',
    'false',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    datetime('now', '-3 hours'),
    datetime('now', '-3 hours')
);

-- Mensaje 7: Mensaje pendiente con pregunta válida
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg007',
    'alumno',
    'Recursos para estudiar',
    '¿Alguien tiene buenos recursos en línea para practicar inglés? Necesito mejorar mi pronunciación.',
    'Inglés',
    'pending',
    'active',
    '13:45 11/02/2026',
    'false',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    datetime('now', '-2 hours'),
    datetime('now', '-2 hours')
);

-- Mensaje 8: Mensaje aprobado sobre actividad extracurricular
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg008',
    'ana.martinez',
    'Club de lectura',
    '¿Alguien está interesado en formar un club de lectura? Podríamos reunirnos los miércoles después de clase.',
    'Lengua',
    'true',
    'active',
    '15:20 10/02/2026',
    'false',
    'Iniciativa positiva aprobada',
    NULL,
    '15:30 10/02/2026',
    'maria.garcia',
    NULL,
    NULL,
    datetime('now', '-1 day', '-1 hour'),
    datetime('now', '-1 day', '-1 hour')
);

-- Mensaje 9: Mensaje del profesor con anuncio importante
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg009',
    'profesor',
    'Cambio de horario - Matemáticas',
    'IMPORTANTE: La clase de matemáticas del viernes se trasladará al aula 205 por mantenimiento del aula habitual.',
    'Matemáticas',
    'true',
    'active',
    '07:30 11/02/2026',
    'false',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    datetime('now', '-4 hours'),
    datetime('now', '-4 hours')
);

-- Mensaje 10: Mensaje pendiente sobre material de estudio
INSERT INTO messages (
    id, user, title, text, asignatura, approved, status,
    timestamp, dangerous_content, approve_reason, delete_reason,
    moderated_at, moderated_by, deleted_at, deleted_by,
    created_at, updated_at
)
VALUES (
    '67msg010',
    'juan.lopez',
    'Material de Historia',
    '¿Alguien tiene los apuntes de la semana pasada de Historia? Falté por enfermedad y necesito ponerme al día.',
    'Historia',
    'pending',
    'active',
    '12:10 11/02/2026',
    'false',
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    NULL,
    datetime('now', '-3 hours'),
    datetime('now', '-3 hours')
);

-- ============================================================================
-- VERIFICACIÓN DE DATOS INSERTADOS
-- ============================================================================

-- Verificar usuarios insertados
SELECT 'Usuarios insertados:' as info;
SELECT id, username, email, isProfessor, theme FROM users;

-- Verificar mensajes insertados
SELECT '' as separator;
SELECT 'Mensajes insertados:' as info;
SELECT id, user, title, asignatura, approved, status, dangerous_content FROM messages;

-- Estadísticas
SELECT '' as separator;
SELECT 'Estadísticas:' as info;
SELECT 'Total usuarios: ' || COUNT(*) as stat FROM users
UNION ALL
SELECT 'Profesores: ' || COUNT(*) FROM users WHERE isProfessor = 'True'
UNION ALL
SELECT 'Alumnos: ' || COUNT(*) FROM users WHERE isProfessor = 'False'
UNION ALL
SELECT 'Total mensajes: ' || COUNT(*) FROM messages
UNION ALL
SELECT 'Mensajes aprobados: ' || COUNT(*) FROM messages WHERE approved = 'true' AND status = 'active'
UNION ALL
SELECT 'Mensajes pendientes: ' || COUNT(*) FROM messages WHERE approved = 'pending' AND status = 'active'
UNION ALL
SELECT 'Mensajes eliminados: ' || COUNT(*) FROM messages WHERE status = 'deleted';

-- ============================================================================
-- INFORMACIÓN DE CREDENCIALES
-- ============================================================================

SELECT '' as separator;
SELECT '============================================' as info;
SELECT 'CREDENCIALES DE ACCESO' as info;
SELECT '============================================' as info;
SELECT '' as separator;
SELECT 'PROFESOR:' as info;
SELECT '  Usuario: profesor' as info;
SELECT '  Email: profesor@nosanet.com' as info;
SELECT '  Contraseña: profesor123' as info;
SELECT '' as separator;
SELECT 'ALUMNO:' as info;
SELECT '  Usuario: alumno' as info;
SELECT '  Email: alumno@nosanet.com' as info;
SELECT '  Contraseña: alumno123' as info;
SELECT '' as separator;
SELECT 'OTROS USUARIOS (misma contraseña: profesor123 o alumno123):' as info;
SELECT '  - maria.garcia (Profesora)' as info;
SELECT '  - juan.lopez (Alumno)' as info;
SELECT '  - ana.martinez (Alumna)' as info;
SELECT '============================================' as info;

-- ============================================================================
-- FIN DEL SEED
-- ============================================================================
