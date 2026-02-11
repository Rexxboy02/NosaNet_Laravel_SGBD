# Arquitectura del Proyecto NosaNet Laravel

## Patrón de Diseño: MVC con Repositorios y Workers

Este proyecto implementa una arquitectura robusta basada en el patrón **Modelo-Vista-Controlador (MVC)** con capas adicionales para separar responsabilidades:

### 📁 Estructura de Capas

```
┌─────────────────────────────────────────────────┐
│              VISTAS (Blade)                      │
│         Presentación de datos                    │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│           CONTROLADORES                          │
│  - Thin Controllers                              │
│  - Validación de entrada                         │
│  - Gestión de respuestas HTTP                    │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│              WORKERS                             │
│  - Lógica de negocio                             │
│  - Validaciones complejas                        │
│  - Orquestación de operaciones                   │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│           REPOSITORIOS                           │
│  - Abstracción de persistencia                   │
│  - Operaciones CRUD                              │
│  - Consultas a la base de datos                  │
└──────────────────┬──────────────────────────────┘
                   │
┌──────────────────▼──────────────────────────────┐
│             MODELOS (Eloquent)                   │
│  - Representación de entidades                   │
│  - Relaciones entre tablas                       │
└─────────────────────────────────────────────────┘
```

---

## 🏗️ Componentes de la Arquitectura

### 1. **Controladores (Controllers)**
**Ubicación:** `app/Http/Controllers/`

Los controladores son **thin controllers** que:
- Reciben peticiones HTTP
- Validan datos de entrada
- Delegan lógica de negocio a los Workers
- Retornan vistas o respuestas HTTP

**Ejemplo:**
```php
public function register(Request $request)
{
    $validator = Validator::make($request->all(), [...]);
    
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator);
    }
    
    // Delegar al worker
    $result = $this->authWorker->register([...]);
    
    return redirect()->route('login')->with('success', 'Registro exitoso');
}
```

**Controladores existentes:**
- `AuthController` - Registro de usuarios
- `LoginController` - Autenticación y cierre de sesión
- `HomeController` - Página principal
- `MessageController` - Gestión de mensajes
- `ModerationController` - Moderación de contenido
- `ThemeController` - Cambio de tema

---

### 2. **Workers**
**Ubicación:** `app/Workers/`

Los workers contienen la **lógica de negocio** de la aplicación:
- Procesan datos
- Aplican reglas de negocio
- Coordinan operaciones entre repositorios
- Retornan resultados estructurados

**Workers existentes:**
- `AuthWorker` - Lógica de autenticación y registro
- `MessageWorker` - Lógica de creación y obtención de mensajes
- `ContentValidationWorker` - Validación de contenido peligroso
- `ModerationWorker` - Lógica de moderación de mensajes

**Ejemplo:**
```php
public function register(array $data): array
{
    if ($this->userRepository->findByUsername($data['username'])) {
        return ['success' => false, 'error' => 'Usuario en uso'];
    }
    
    $user = $this->userRepository->create([...]);
    
    return ['success' => true, 'user' => $user];
}
```

---

### 3. **Repositorios (Repositories)**
**Ubicación:** `app/Repositories/`

Los repositorios abstraen el acceso a datos:
- Realizan operaciones CRUD
- Encapsulan consultas a la base de datos
- Usan Eloquent para interactuar con los modelos
- Facilitan el testing y mantenimiento

**Repositorios existentes:**
- `UserRepository` - Persistencia de usuarios
- `MessageRepository` - Persistencia de mensajes

**Ejemplo:**
```php
public function findByUsername(string $username): ?User
{
    return User::where('username', $username)->first();
}

public function create(array $data): User
{
    return User::create($data);
}
```

---

### 4. **Modelos (Models)**
**Ubicación:** `app/Models/`

Los modelos representan las entidades de la base de datos:
- Definen atributos y relaciones
- Configuran Eloquent ORM
- **NO contienen lógica de negocio**

**Modelos existentes:**
- `User` - Entidad de usuario
- `Message` - Entidad de mensaje

**Nota:** Los métodos estáticos en los modelos están marcados como `@deprecated` para migrar su uso a los repositorios.

---

## 🔄 Flujo de Datos

### Ejemplo: Creación de un Mensaje

```
1. Usuario envía formulario
        ↓
2. MessageController::store() recibe request
        ↓
3. Validación de datos en el controlador
        ↓
4. MessageWorker::createMessage() - Lógica de negocio
   - Determina aprobación automática
   - Valida contenido con ContentValidationWorker
   - Sanitiza datos
        ↓
5. MessageRepository::create() - Persistencia
        ↓
6. Message Model - Eloquent ORM
        ↓
7. Base de datos SQLite
        ↓
8. Respuesta al controlador
        ↓
9. Redirect con mensaje de éxito
```

---

## ✅ Ventajas de esta Arquitectura

### **Separación de Responsabilidades**
- Cada capa tiene un propósito claro y único
- Facilita el mantenimiento y escalabilidad

### **Testabilidad**
- Workers y repositorios son fáciles de testear
- Mock de dependencias simplificado

### **Reutilización**
- Workers pueden ser usados por múltiples controladores
- Repositorios centralizan acceso a datos

### **Mantenibilidad**
- Cambios en la lógica de negocio se hacen en un solo lugar
- Controladores más simples y legibles

### **Cumplimiento MVC Estricto**
- Modelos solo representan datos
- Controladores solo gestionan HTTP
- Lógica separada en Workers

---

## 📊 Base de Datos

El proyecto usa **SQLite** con un esquema simplificado:

### Tablas Principales
- `users` - Usuarios del sistema
- `messages` - Mensajes publicados

### Características
- **Sin triggers** - Validaciones en capa de aplicación
- **Constraints CHECK** - Validación de valores permitidos
- **Índices optimizados** - Búsquedas eficientes

Ver `database/schema.sql` para el esquema completo.

---

## 🚀 Inyección de Dependencias

Laravel resuelve automáticamente las dependencias en constructores:

```php
public function __construct(
    AuthWorker $authWorker,
    MessageRepository $messageRepository
) {
    $this->authWorker = $authWorker;
    $this->messageRepository = $messageRepository;
}
```

---

## 📝 Documentación PHPDoc

Todo el código incluye comentarios PHPDoc para:
- Tipos de parámetros y retorno
- Descripciones de métodos y clases
- Deprecations cuando aplica

---

## 🔧 Próximos Pasos Recomendados

1. **Service Container Bindings** - Registrar repositorios y workers en `AppServiceProvider`
2. **Interfaces** - Crear interfaces para repositorios y workers
3. **Tests Unitarios** - Crear tests para workers y repositorios
4. **Middleware** - Extraer validaciones de permisos a middleware
5. **Form Requests** - Mover validaciones a Form Request classes

---

## 📚 Referencias

- [Laravel Documentation](https://laravel.com/docs)
- [Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html)
- [Service Layer Pattern](https://martinfowler.com/eaaCatalog/serviceLayer.html)
