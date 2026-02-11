NosaNet - Red Social Académica
Descripción del Proyecto
NosaNet es una red social académica desarrollada con Laravel que permite a estudiantes y profesores compartir mensajes educativos. El sistema incluye funciones de moderación, sistema de temas claro/oscuro, y validación de contenido contra lenguaje ofensivo o ataques de seguridad.

Justificación de Elección del Framework
Se eligió Laravel como framework de desarrollo por varias razones fundamentales: su arquitectura MVC permite una clara separación de responsabilidades, su sistema de rutas y middleware facilita la implementación de control de acceso por roles, y su ecosistema robusto con Eloquent (aunque adaptado para JSON) proporciona una capa de abstracción para la persistencia de datos. Laravel ofrece una sintaxis elegante y expresiva que acelera el desarrollo, herramientas de seguridad integradas, y un sistema de sesiones y autenticación que, aunque personalizado para este proyecto, sigue los patrones de Laravel. Adicionalmente, su sistema de plantillas Blade permite una construcción modular de interfaces con herencia de layouts.

Patrones de Diseño Aplicados
Patrón Repository
Implementación: Las clases JsonModel, Message y User implementan este patrón. JsonModel actúa como un repositorio base abstracto que encapsula toda la lógica de acceso a datos JSON, proporcionando métodos CRUD estandarizados (all(), find(), create(), update(), delete()).

Justificación: Este patrón permite desacoplar la lógica de negocio de los detalles de persistencia. Si en el futuro se migrara a una base de datos relacional, solo sería necesario modificar las clases del repositorio sin afectar a los controladores. Además, centraliza las operaciones de lectura/escritura de JSON, promoviendo la reutilización de código y facilitando el mantenimiento.

Patrón Factory (implícito)
Implementación: Aunque no hay una clase Factory explícita, el patrón se aplica implícitamente en los métodos create() de los modelos y en la creación de mensajes en MessageController::store(). Los controladores actúan como "fábricas" que ensamblan objetos complejos con datos validados.

Justificación: Este enfoque permite encapsular la lógica de creación de entidades, asegurando que los objetos se creen en un estado válido y consistente. En MessageController::store(), por ejemplo, se determinan automáticamente campos como approved (basado en si el usuario es profesor), timestamp, y dangerous_content (mediante validación). Esto simplifica la creación de objetos complejos y garantiza la coherencia de datos.

Instrucciones de Instalación y Arranque Local
Requisitos Previos
PHP >= 8.0

Composer

Git

Pasos de Instalación

Clonar el repositorio:

bash
git clone https://github.com/99pablogz/NosaNet_Laravel
cd nosanet

Instalar dependencias de Composer:

bash
composer install

Configurar permisos de directorios:

bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache

Verificar estructura de directorios JSON:

bash
mkdir -p database/json
touch database/json/messages.json
touch database/json/users.json

Inicializar archivos JSON:

bash
echo '[]' > database/json/messages.json
echo '[]' > database/json/users.json

Configurar variables de entorno (opcional):

bash
cp .env.example .env
php artisan key:generate

Iniciar el servidor de desarrollo:

bash
php artisan serve

Acceder a la aplicación:

Abrir navegador en: http://localhost:8000

🛣️ Listado de Rutas y Roles Requeridos
Rutas Públicas
Método	Ruta	Controlador	Acción	Acceso
GET	/	HomeController	index()	Público
GET	/register	AuthController	showRegister()	Solo invitados
POST	/register	AuthController	register()	Solo invitados
GET	/login	LoginController	showLogin()	Solo invitados
POST	/login	LoginController	login()	Solo invitados
POST	/theme	ThemeController	toggle()	Todos
Rutas Autenticadas (estudiantes y profesores)
Método	Ruta	Controlador	Acción	Acceso
POST	/logout	LoginController	logout()	Autenticados
POST	/messages	MessageController	store()	Autenticados
GET	/messages/my-messages	MessageController	myMessages()	Autenticados
Rutas de Moderación (solo profesores)
Método	Ruta	Controlador	Acción	Acceso
GET	/moderation	ModerationController	index()	Profesores
POST	/moderation/{id}/approve	ModerationController	approve()	Profesores
POST	/moderation/{id}/delete	ModerationController	delete()	Profesores
🔒 Explicación de Validación y Sanitización Implementada
Validación de Formularios
Registro: Valida formato de username (solo letras, números, _, -, .), email válido, contraseña mínima de 6 caracteres.

Login: Valida campos requeridos y verifica credenciales con hash SHA256.

Mensajes: Valida título (max 100 chars), texto (max 250 chars), asignatura requerida.

Moderación: Requiere razón de aprobación/eliminación (3-500 caracteres).

Sanitización de Entrada
HTML Special Chars: Todos los campos de texto pasan por htmlspecialchars() con flags ENT_QUOTES | ENT_SUBSTITUTE para prevenir XSS.

Validación de Contenido Peligroso:

Palabras ofensivas: Sistema de regex para detectar más de 50 palabras ofensivas en español e inglés.

Ataques de seguridad: Detecta patrones de SQL injection, XSS, path traversal, comandos del sistema.

Hash de Contraseñas: Las contraseñas se hashean con SHA256 en cliente y se almacenan con Hash::make() de Laravel.

Validación de Roles
Middleware auth.custom: Verifica sesión activa.

Middleware professor: Verifica que is_professor === 'True'.

Middleware guest: Redirige usuarios autenticados.

👥 Usuarios de Prueba
Profesor (Rol de moderador)
Username: profe_juan

Email: juan@universidad.edu

Contraseña: Profesor123

Rol: True (profesor)

Características: Puede aprobar/eliminar mensajes, acceso a panel de moderación, sus mensajes se aprueban automáticamente.

Alumno (Rol estándar)
Username: alumno_maria

Email: maria@universidad.edu

Contraseña: Estudiante123

Rol: False (alumno)

Características: Puede publicar mensajes (requieren moderación), ver sus mensajes aprobados/pendientes.

Crear Usuarios de Prueba Manualmente
Puedes registrar estos usuarios a través del formulario de registro en /register, o agregarlos directamente al archivo database/json/users.json:

json
[
  {
    "id": "profesor001",
    "username": "profe_juan",
    "email": "juan@universidad.edu",
    "password": "$2y$10$...",
    "isProfessor": "True",
    "theme": "light",
    "created_at": "2024-01-28 10:00:00"
  },
  {
    "id": "alumno001",
    "username": "alumno_maria",
    "email": "maria@universidad.edu",
    "password": "$2y$10$...",
    "isProfessor": "False",
    "theme": "light",
    "created_at": "2024-01-28 10:00:00"
  }
]
Nota: Las contraseñas deben generarse con Hash::make('password') en PHP o usando el formulario de registro.

📁 Estructura del Proyecto
text
nosanet/
├── app/
│   ├── Http/
│   │   ├── Controllers/     # Controladores MVC
│   │   └── Middleware/      # Middleware personalizado
│   ├── Models/              # Modelos (Message, User, JsonModel)
│   ├── Helpers/             # Funciones helper
│   └── Providers/           # Service Providers
├── database/
│   └── json/               # Almacenamiento JSON
├── resources/
│   └── views/              # Plantillas Blade
├── routes/
│   └── web.php             # Definición de rutas
└── public/
    └── css/                # Estilos CSS
🛡️ Características de Seguridad
CSRF Protection: Tokens en todos los formularios.

XSS Prevention: Sanitización con htmlspecialchars().

SQL Injection Prevention: Validación de patrones peligrosos.

Session Security: Regeneración de IDs de sesión en login.

Role-Based Access Control: Middleware para control de acceso.

Input Validation: Validación en servidor y cliente.

🎨 Características Adicionales
Sistema de Temas: Toggle entre modo claro/oscuro con persistencia.

Responsive Design: CSS moderno con variables CSS para temas.

Validación de Contenido: Detección automática de contenido inapropiado.

Feedback al Usuario: Mensajes de éxito/error con sesiones flash.

Dropdown de Perfil: Interfaz de usuario mejorada.

Desarrollado con Laravel y almacenamiento JSON para simplicidad y portabilid

# BBDD - NosaNet
Se ha elegido como SGBD SQLite debido a su facilidad de uso y portabilidad. Esto permite que el proyecto se ejecute en 
cualquier sistema operativo, que la configuración sea rápida y sencilla, y que no requiera instalación adicional de 
servidores de bases de datos.

Comandos para crear la BBDD:
cd "C:\Users\bruno\IdeaProjects\NosaNet_Laravel" && php artisan make:migration create_users_table
cd "C:\Users\bruno\IdeaProjects\NosaNet_Laravel" && php artisan make:migration create_messages_table
cd "C:\Users\bruno\IdeaProjects\NosaNet_Laravel" && where php
cd "C:\Users\bruno\IdeaProjects\NosaNet_Laravel" && C:\Users\bruno\.config\herd\bin\php.bat artisan make:migration create_users_table
cd "C:\Users\bruno\IdeaProjects\NosaNet_Laravel" && C:\Users\bruno\.config\herd\bin\php.bat artisan make:migration create_messages_table
cd "C:\Users\bruno\IdeaProjects\NosaNet_Laravel" && type nul > database\database.sqlite

php artisan migrate

Tras crear todas las migraciones se debe ejecutar el comando php artisan migrate para crear las tablas en la base de 
datos en base a los contenidos de las migraciones.

