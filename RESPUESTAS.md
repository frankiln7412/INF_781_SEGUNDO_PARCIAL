# Parte F — Pregunta teórica

## ¿Por qué spatie/laravel-permission cachea los permisos?

`spatie/laravel-permission` cachea los roles y permisos de cada usuario porque, sin caché,
cada llamada a `can()`, `hasRole()` o cualquier directiva `@can` / `@role` en Blade dispararía
al menos una query SQL adicional a las tablas `permissions`, `roles` y sus pivotes.
En una aplicación con muchas verificaciones por request, esto multiplicaría el número de
consultas y degradaría el rendimiento notablemente.

## Problema de seguridad/consistencia si se olvida limpiar la caché

Si en producción se **revocan permisos** a un rol (p. ej., se quita `eliminar productos` al
supervisor) pero **no se llama** a `app()[PermissionRegistrar::class]->forgetCachedPermissions()`
ni a `php artisan permission:cache-reset`, el sistema seguirá sirviendo los permisos
anteriores hasta que la caché expire. Durante ese intervalo, el usuario conservará acceso
a acciones que ya no debería poder ejecutar, constituyendo una **brecha de seguridad real**
aunque la base de datos esté correctamente actualizada.

Por eso, en este proyecto se llama a `forgetCachedPermissions()` en cada operación de
`RoleController` (store, update, destroy) y al inicio del seeder.
