# Rúbrica de Evaluación — Cumplimiento de Requisitos

**Examen Segundo Parcial · RBAC con laravel-permission**
**INF781 — Seguridad de Software · UATF — Ing. Informática**

---

## Parte A — Instalación, middleware y seeder por guard (15 pts)

### 1. Instalación de `spatie/laravel-permission`

**[composer.json](composer.json)**
```json
"spatie/laravel-permission": "^7.0",
"laravel/sanctum": "^4.3"
```

La migración de tablas Spatie se encuentra en [database/migrations/2026_05_25_150259_create_permission_tables.php](database/migrations/2026_05_25_150259_create_permission_tables.php), que crea: `permissions`, `roles`, `model_has_permissions`, `model_has_roles`, `role_has_permissions`.

### 2. Registro de middleware

**[bootstrap/app.php:14-20](bootstrap/app.php)**
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'role'               => RoleMiddleware::class,
        'permission'         => PermissionMiddleware::class,
        'role_or_permission' => RoleOrPermissionMiddleware::class,
    ]);
})
```
Los tres alias de Spatie quedan registrados globalmente para usarse en rutas y controladores.

### 3. Seeder separado por guard

**[database/seeders/RolesAndPermissionsSeeder.php](database/seeders/RolesAndPermissionsSeeder.php)**

| Guard | Permisos creados | Roles creados |
|-------|-----------------|---------------|
| `web` | ver/crear/editar/eliminar productos, registrar movimiento, aprobar movimiento, gestionar roles | `admin`, `supervisor`, `almacenista` |
| `api` | ver productos, confirmar entrega | `repartidor` |

```php
// Línea 17 — limpia caché antes de sembrar
app()[PermissionRegistrar::class]->forgetCachedPermissions();

// Líneas 21-33 — permisos guard WEB
Permission::create(['name' => 'ver productos',      'guard_name' => 'web']);
Permission::create(['name' => 'gestionar roles',    'guard_name' => 'web']);
// ...

// Líneas 37-44 — permisos guard API (aislados del guard web)
Permission::create(['name' => 'ver productos',      'guard_name' => 'api']);
Permission::create(['name' => 'confirmar entrega',  'guard_name' => 'api']);
```

El usuario `repartidor@almatrack.com` recibe un token Sanctum real (líneas 108-111):
```php
$repartidorUser->createToken('repartidor-token')->plainTextToken;
```

**[database/seeders/DatabaseSeeder.php:18](database/seeders/DatabaseSeeder.php)** invoca el seeder:
```php
$this->call(RolesAndPermissionsSeeder::class);
```

---

## Parte B — Rutas web con atributos `#[Middleware]` (25 pts)

### Rutas definidas

**[routes/web.php:17-33](routes/web.php)**
```php
Route::middleware('auth')->group(function () {
    Route::resource('products',  ProductController::class);
    Route::resource('movements', MovementController::class);
    Route::post('movements/{id}/approve', [MovementController::class, 'approve']);
    Route::resource('roles', RoleController::class);
});
```

### Middleware declarativo en controladores (interfaz `HasMiddleware`)

#### [app/Http/Controllers/ProductController.php:10,16-32](app/Http/Controllers/ProductController.php)
```php
class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:ver productos',              only: ['index', 'show']),
            new Middleware('permission:crear productos',            only: ['create', 'store']),
            new Middleware('role_or_permission:admin|editar productos', only: ['edit', 'update']),
            new Middleware('permission:eliminar productos',         only: ['destroy']),
        ];
    }
}
```

#### [app/Http/Controllers/MovementController.php:11,13-19](app/Http/Controllers/MovementController.php)
```php
class MovementController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:registrar movimiento', only: ['create', 'store']),
            new Middleware('permission:aprobar movimiento',   only: ['approve']),
        ];
    }
}
```

#### [app/Http/Controllers/RoleController.php:12,14-19](app/Http/Controllers/RoleController.php)
```php
class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:gestionar roles'), // aplica a todas las acciones
        ];
    }
}
```

---

## Parte C — Policy de almacén + directivas Blade (20 pts)

### Policy definida

**[app/Policies/MovementPolicy.php](app/Policies/MovementPolicy.php)**

| Método | Lógica |
|--------|--------|
| `create()` (línea 43) | Requiere permiso `registrar movimiento` en guard `web` |
| `register()` (líneas 24-37) | Requiere `registrar movimiento`; si el usuario es `almacenista`, solo puede registrar movimientos de su propio almacén |
| `approve()` (líneas 14-17) | Requiere permiso `aprobar movimiento` en guard `web` |

```php
public function register(User $user, Movement $movement): bool
{
    if (!$user->hasPermissionTo('registrar movimiento', 'web')) return false;
    // Aislamiento por almacén para el rol almacenista
    if ($user->hasRole('almacenista') && $movement->warehouse_id !== $user->warehouse_id) {
        return false;
    }
    return true;
}
```

### Registro de la Policy

**[app/Providers/AppServiceProvider.php:12-20](app/Providers/AppServiceProvider.php)**
```php
protected $policies = [
    Movement::class => MovementPolicy::class,
];

public function boot(): void
{
    $this->registerPolicies();
}
```

### Directivas Blade `@can`

#### [resources/views/products/index.blade.php](resources/views/products/index.blade.php)
```blade
@can('crear productos')
    <a href="{{ route('products.create') }}">Nuevo Producto</a>
@endcan

@can('editar productos')
    <a href="{{ route('products.edit', $product) }}">Editar</a>
@endcan

@can('eliminar productos')
    <form method="POST" action="{{ route('products.destroy', $product) }}">...</form>
@endcan
```

#### [resources/views/movements/index.blade.php](resources/views/movements/index.blade.php)
```blade
@can('registrar movimiento')
    <a href="{{ route('movements.create') }}">Registrar Movimiento</a>
@endcan

@can('approve', $movement)   {{-- pasa el modelo a la Policy --}}
    <form method="POST" action="{{ route('movements.approve', $movement) }}">...</form>
@endcan
```

#### [resources/views/products/show.blade.php](resources/views/products/show.blade.php)
```blade
@can('editar productos')   ... @endcan
@can('eliminar productos') ... @endcan
```

---

## Parte D — API con guard `api` y aislamiento de tokens (20 pts)

### Configuración del guard `api`

**[config/auth.php:40-50](config/auth.php)**
```php
'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],
    'api' => ['driver' => 'sanctum',  'provider' => 'users'],
],
```

El guard `web` usa sesión; el guard `api` usa **Sanctum** (tokens Bearer), completamente aislados.

### Rutas de la API

**[routes/api.php](routes/api.php)**
```php
Route::middleware('auth:api')->group(function () {
    Route::get('/products',                    [ProductApiController::class, 'index']);
    Route::post('/deliveries/{id}/confirm',    [DeliveryController::class,   'confirm']);
});
```

### Controladores API con `HasMiddleware` y guard explícito

#### [app/Http/Controllers/Api/ProductApiController.php:15-24](app/Http/Controllers/Api/ProductApiController.php)
```php
class ProductApiController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            // auth:api = Sanctum token | permission verifica en guard 'api'
            new Middleware(['auth:api', 'permission:ver productos,api']),
        ];
    }
}
```

#### [app/Http/Controllers/Api/DeliveryController.php:14-22](app/Http/Controllers/Api/DeliveryController.php)
```php
class DeliveryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware(['auth:api', 'permission:confirmar entrega,api']),
        ];
    }
}
```

El sufijo `,api` en `permission:confirmar entrega,api` obliga a Spatie a buscar el permiso en la tabla con `guard_name = 'api'`, aislando completamente los permisos web de los API.

### Aislamiento demostrado

| Guard | Permiso `ver productos` | Acceso a rutas web | Acceso a rutas api |
|-------|------------------------|--------------------|--------------------|
| `web` | admin, supervisor, almacenista | ✅ | ❌ |
| `api` | repartidor (token Sanctum) | ❌ | ✅ |

---

## Parte E — CRUD dinámico de roles + caché + rol protegido (15 pts)

### CRUD completo en [app/Http/Controllers/RoleController.php](app/Http/Controllers/RoleController.php)

| Acción | Método | Líneas | Detalle |
|--------|--------|--------|---------|
| Listar | `index()` | 22-27 | Roles del guard `web` + sus permisos |
| Formulario crear | `create()` | 29-32 | Carga todos los permisos disponibles |
| Guardar nuevo rol | `store()` | 35-46 | Valida nombre, asigna permisos, limpia caché |
| Formulario editar | `edit()` | 48-52 | Carga rol y permisos actuales |
| Actualizar rol | `update()` | 55-70 | Protege `admin`, sincroniza permisos, limpia caché |
| Eliminar rol | `destroy()` | 72-86 | Protege `admin`, elimina, limpia caché |

### Invalidación de caché en cada mutación

```php
// store() — línea 43
app()[PermissionRegistrar::class]->forgetCachedPermissions();

// update() — línea 67
app()[PermissionRegistrar::class]->forgetCachedPermissions();

// destroy() — línea 83
app()[PermissionRegistrar::class]->forgetCachedPermissions();
```

Cada vez que se crea, modifica o elimina un rol, el caché de Spatie se invalida de inmediato, evitando que permisos obsoletos queden activos durante el TTL de 24 horas.

### Rol protegido `admin`

```php
// update() — líneas 60-62: no se puede renombrar
if ($role->name === 'admin') {
    return redirect()->route('roles.index')->with('error', 'El rol admin no puede modificarse.');
}

// destroy() — líneas 75-78: no se puede eliminar
if ($role->name === 'admin') {
    return redirect()->route('roles.index')->with('error', 'El rol admin no puede eliminarse.');
}
```

La vista [resources/views/roles/index.blade.php:37-55](resources/views/roles/index.blade.php) también oculta los botones de edición/eliminación para el rol `admin` mediante condicionales Blade.

---

## Parte F — Pregunta teórica: caché de permisos (5 pts)

**Archivo:** [RESPUESTAS.md](RESPUESTAS.md)

### ¿Por qué Spatie cachea los permisos?

Sin caché, cada llamada a `can()`, `hasRole()` o directiva `@can` ejecuta una consulta SQL contra las tablas `roles`, `permissions` y sus pivotes. En una solicitud web típica puede haber decenas de estas verificaciones, lo que multiplica la carga en la base de datos de forma exponencial.

Spatie resuelve esto cargando **todos** los roles y permisos en caché la primera vez y sirviéndolos desde ahí durante el TTL configurado.

**[config/permission.php:196-218](config/permission.php)**
```php
'cache' => [
    'expiration_time' => DateInterval::createFromDateString('24 hours'),
    'key'   => 'spatie.permission.cache',
    'store' => 'default',
],
```

### Problema de seguridad si no se limpia el caché

Si se revocan permisos a un usuario pero el caché no se invalida, el sistema seguirá concediendo esos permisos hasta que expire el TTL. Ejemplo: se le quita `eliminar productos` al supervisor, pero durante las próximas 24 horas el caché antiguo aún se lo concede.

### Solución implementada

En este proyecto, `forgetCachedPermissions()` se llama **en cada operación de escritura** sobre roles ([RoleController.php:43,67,83](app/Http/Controllers/RoleController.php)) y al inicio del seeder ([RolesAndPermissionsSeeder.php:17](database/seeders/RolesAndPermissionsSeeder.php)), garantizando que cualquier cambio en la configuración RBAC tenga efecto inmediato.

---

## Guía práctica: Generar y usar el token API del repartidor

### Problema resuelto: base de datos

El error `SQLSTATE[08006] connection refused port 5432` significa que **PostgreSQL no está corriendo**.
Se resolvió cambiando a **SQLite** (más simple para desarrollo/examen):

**Paso 1 — Habilitar SQLite en Laragon** (solo una vez)

Abrir `C:\laragon\bin\php\php-8.3.31-Win32-vs16-x64\php.ini` y descomentar:
```ini
extension=pdo_sqlite
extension=sqlite3
```

**Paso 2 — Cambiar el `.env`**

```env
DB_CONNECTION=sqlite
# Comentar o eliminar las líneas DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD
```

**Paso 3 — Crear el archivo SQLite y migrar**

```bash
# Crear el archivo vacío
touch database/database.sqlite        # Linux/Mac/Git Bash
# o en Windows:
echo $null >> database\database.sqlite

# Limpiar caché y migrar con datos de prueba
php artisan config:clear
php artisan migrate:fresh --seed
```

Al correr el seeder verás en consola el token del repartidor:
```
Token repartidor (guardar para pruebas): 1|Dv6stqD7ZZ7dSdVSO2mtXUJsSjH31MdcGBgWGsCWf509b97b
```

> Si corres `migrate:fresh --seed` de nuevo se genera un token diferente. Usa siempre el último que aparezca en consola.

---

### Cómo funciona el token (Sanctum)

El token se crea en el seeder ([RolesAndPermissionsSeeder.php:108-111](database/seeders/RolesAndPermissionsSeeder.php)):

```php
$repartidorUser = User::create([
    'name'     => 'Repartidor Uno',
    'email'    => 'repartidor@almatrack.com',
    'password' => Hash::make('password'),
]);
$repartidorUser->assignRole('repartidor');  // rol con guard 'api'
echo "Token repartidor: " . $repartidorUser->createToken('repartidor-token')->plainTextToken;
```

El token tiene formato `{id}|{hash}`. El guard `api` de Sanctum lo valida automáticamente cuando llega en el header `Authorization: Bearer …`.

---

### Rutas API disponibles

**[routes/api.php](routes/api.php)** — base URL: `http://127.0.0.1:8000/api`

| Método | Endpoint | Permiso requerido | Guard |
|--------|----------|------------------|-------|
| `GET`  | `/api/products` | `ver productos` (api) | `api` |
| `POST` | `/api/deliveries/{id}/confirm` | `confirmar entrega` (api) | `api` |

---

### Usar el token en un cliente HTTP

#### Bruno (VSCode Extension)

Bruno tiene una pestaña **Auth** que agrega el header `Authorization` automáticamente — **no tocar la pestaña Headers manualmente**.

**Paso a paso para `GET /api/products`:**

1. Selecciona la request `GET leer` en tu colección
2. Haz clic en la pestaña **Auth** (no en Headers)
3. En el dropdown de tipo de autenticación selecciona **Bearer Token**
4. En el campo **Token** pega solo el token (sin escribir "Bearer"):
   ```
   3|4OwU75qyuNqtojCnm2wQUeyGmrdcmNMIU6xGA4eef1759197
   ```
5. Bruno mostrará un asterisco `Auth *` en la pestaña — eso indica que está activo
6. Presiona **Send** → recibirás `200 OK` con el JSON de productos

> **Error común:** Si pegaste el token en la pestaña **Headers** manualmente, bórralo de ahí. Bruno duplica el header y puede generar `ERR_INVALID_CHAR` si el valor tiene caracteres inesperados. Usa siempre la pestaña **Auth**.

**Para regenerar el token** (si el actual ya no funciona):

En PowerShell con backtick para escapar el `$`:
```powershell
php artisan tinker --execute="`$u = App\Models\User::where('email','repartidor@almatrack.com')->first(); `$u->tokens()->delete(); echo `$u->createToken('tok')->plainTextToken;"
```

Copia el token que imprime y pégalo en la pestaña **Auth** de Bruno.

---

#### Thunder Client / Postman / Insomnia

**1. Listar productos (GET)**

```
GET  http://127.0.0.1:8000/api/products
```

Headers:
```
Accept:        application/json
Authorization: Bearer 1|Dv6stqD7ZZ7dSdVSO2mtXUJsSjH31MdcGBgWGsCWf509b97b
```

Respuesta esperada `200 OK`:
```json
[
  { "id": 1, "name": "Producto A", "price": "10.00", "stock": 50, ... },
  ...
]
```

**2. Confirmar una entrega (POST)**

```
POST  http://127.0.0.1:8000/api/deliveries/1/confirm
```

Headers:
```
Accept:        application/json
Authorization: Bearer 1|Dv6stqD7ZZ7dSdVSO2mtXUJsSjH31MdcGBgWGsCWf509b97b
Content-Type:  application/json
```

Respuesta esperada `200 OK`:
```json
{ "message": "Entrega confirmada." }
```

**3. Sin token → 401 Unauthorized**

Omitir el header `Authorization` devuelve:
```json
{ "message": "Unauthenticated." }
```

**4. Con token web intentando acceder a ruta api → 403 Forbidden**

Un usuario logueado en el guard `web` no tiene permisos en el guard `api` — el aislamiento es por diseño.

#### cURL (terminal)

```bash
# Listar productos
curl -X GET http://127.0.0.1:8000/api/products \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|Dv6stqD7ZZ7dSdVSO2mtXUJsSjH31MdcGBgWGsCWf509b97b"

# Confirmar entrega del movimiento ID=1
curl -X POST http://127.0.0.1:8000/api/deliveries/1/confirm \
  -H "Accept: application/json" \
  -H "Authorization: Bearer 1|Dv6stqD7ZZ7dSdVSO2mtXUJsSjH31MdcGBgWGsCWf509b97b"
```

---

### Verificar el aislamiento de guards

El permiso `ver productos` existe en **dos guards distintos** — son registros separados en la BD:

```sql
-- guard web  → para admin, supervisor, almacenista (sesión)
SELECT * FROM permissions WHERE name = 'ver productos' AND guard_name = 'web';

-- guard api  → solo para repartidor (token Sanctum)
SELECT * FROM permissions WHERE name = 'ver productos' AND guard_name = 'api';
```

El middleware `permission:ver productos,api` en `ProductApiController` verifica **explícitamente** el guard `api`, por lo que un token web nunca puede usarlo.

---

## Resumen general

| Parte | Criterio | Pts | Archivos clave |
|-------|----------|-----|----------------|
| A | Instalación, middleware y seeder por guard | 15 | `composer.json`, `bootstrap/app.php`, `RolesAndPermissionsSeeder.php`, `config/permission.php` |
| B | Rutas web con `HasMiddleware` | 25 | `routes/web.php`, `ProductController.php`, `MovementController.php`, `RoleController.php` |
| C | Policy + directivas Blade | 20 | `MovementPolicy.php`, `AppServiceProvider.php`, vistas `products/`, `movements/` |
| D | API con guard `api` + aislamiento de tokens | 20 | `routes/api.php`, `config/auth.php`, `ProductApiController.php`, `DeliveryController.php` |
| E | CRUD dinámico + caché + rol protegido | 15 | `RoleController.php` |
| F | Pregunta teórica (caché de permisos) | 5 | `RESPUESTAS.md`, `config/permission.php` |
| | **TOTAL** | **100** | |
