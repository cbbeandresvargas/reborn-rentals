# Credenciales de Usuarios de Prueba

Este documento contiene las credenciales de los usuarios creados por el seeder para facilitar las pruebas.

## 👤 Usuario Administrador

- **Email:** admin@rebornrentals.com
- **Password:** admin123
- **Rol:** admin
- **Acceso:** Panel de administración en `/admin`

## 👤 Usuario Cliente

- **Email:** cliente@example.com
- **Password:** cliente123
- **Rol:** user
- **Acceso:** Funcionalidades de usuario normal (carrito, checkout, órdenes)

---

## 📝 Notas

- Estos usuarios se crean automáticamente al ejecutar `php artisan db:seed` o `php artisan migrate:fresh --seed`
- Las contraseñas son simples para facilitar las pruebas, en producción deben ser más seguras
- Todos los usuarios tienen el mismo email y password inicial: puedes cambiarlos desde el panel de administración una vez que esté implementado

