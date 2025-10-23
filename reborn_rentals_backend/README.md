# 🏗️ Reborn Rentals Backend API

API REST desarrollada en **Laravel 11** con autenticación **JWT**, roles de usuario (admin / cliente) y documentación generada con **L5-Swagger (OpenAPI 3)**.

---

## 🚀 Características principales

- Autenticación con **JSON Web Tokens (JWT)**.
- Roles de usuario (`admin` y `usuario`).
- CRUD completo de:
  - Usuarios
  - Categorías
  - Productos
  - Cupones
  - Pedidos (Orders)
  - Contactos
  - Jobs
  - Información de pago
- Documentación interactiva con **Swagger UI**.
- Filtros, paginación y validación avanzada.
- Compatible con MySQL, SQLite o PostgreSQL.

---

## 🧩 Requisitos previos

Asegúrate de tener instalado:

- PHP >= 8.2  
- Composer >= 2.x  
- Laravel CLI (`composer global require laravel/installer`)
- SQLite / MySQL / PostgreSQL  
- Git
- Opcional: Postman / Insomnia para pruebas adicionales

---

## ⚙️ Instalación

### 1️⃣ Clonar el repositorio

```bash
git clone https://github.com/tuusuario/reborn_rentals_backend.git
cd reborn_rentals_backend
```
### 2️⃣ Instalar dependencias
composer install
3️⃣ Configurar el entorno

Copia el archivo .env.example a .env:
```
cp .env.example .env
```

Edita .env y asegúrate de configurar:
```
APP_NAME=RebornRentals
APP_URL=http://localhost:8000

DB_CONNECTION=sqlite

```
---
# JWT
```
JWT_SECRET=

```
---
# Swagger
```
L5_SWAGGER_CONST_HOST=${APP_URL}
```

Luego genera la clave JWT:
```
php artisan jwt:secret
```

Y la clave de aplicación Laravel:
```
php artisan key:generate
```

# 4️⃣ Migrar la base de datos
```
php artisan migrate --seed
```
(El seed puede crear un usuario administrador por defecto, verifica el seeder correspondiente.)
---

# ▶️ Ejecución del servidor
```
php artisan serve
```
```
Por defecto la API correrá en:
👉 http://localhost:8000
```
---
# 🔐 Autenticación JWT

El flujo básico es:
```
Registro – /api/auth/register

{
  "name": "Fer Almaraz",
  "email": "fer@example.com",
  "password": "123456",
  "password_confirmation": "123456"
}

Login – /api/auth/login
Respuesta:

{
  "access_token": "JWT_TOKEN",
  "token_type": "bearer",
  "expires_in": 3600
}
```

En cada petición autenticada añade el header:

Authorization: Bearer TU_TOKEN_JWT

---
# 📘 Documentación Swagger
Generar documentación

Si realizas cambios en las rutas o anotaciones Swagger:
```
php artisan l5-swagger:generate
```
Ver documentación

Inicia el servidor y accede a:
```
👉 http://localhost:8000/api/documentation
```
Ahí podrás:

Explorar todos los endpoints.

Ver modelos de request/response.

Probar llamadas reales con el token JWT.

Ejemplo de autenticación en Swagger UI

Haz clic en el botón "Authorize" y coloca:

Bearer TU_TOKEN_JWT


Luego podrás ejecutar cualquier endpoint protegido directamente desde la interfaz.

---
# 🧠 Estructura principal del proyecto
```
app/
 ├── Helpers/
 │   └── AuthHelper.php
 ├── Http/
 │   ├── Controllers/
 │   │   └── API/
 │   │       ├── AuthController.php
 │   │       ├── UserController.php
 │   │       ├── ProductController.php
 │   │       ├── CategoryController.php
 │   │       ├── OrderController.php
 │   │       ├── CuponController.php
 │   │       ├── JobController.php
 │   │       ├── ContactController.php
 │   │       └── PaymentInfoController.php
 │   └── Middleware/
 ├── Models/
 └── Swagger/
     └── OpenApi.php      ← Configuración base de Swagger
```
---
# 🧾 Ejemplo de endpoints principales
```
Recurso	Método	Ruta	Rol
Auth	POST	/api/auth/login	Público
Auth	POST	/api/auth/register	Público
Productos	GET	/api/products	Público
Productos	POST	/api/product	Admin
Órdenes	GET	/api/orders	Admin/Usuario
Usuarios	GET	/api/auth/users	Admin
Categorías	POST	/api/categories	Admin
```
---
# 🧰 Comandos útiles

Acción	Comando
Generar claves JWT	php artisan jwt:secret
Generar documentación Swagger	php artisan l5-swagger:generate
Limpiar cachés	php artisan optimize:clear
Migrar BD	php artisan migrate
Ejecutar servidor local	php artisan serve

---
# 🧑‍💻 Contribución

Crea una rama de desarrollo:
```
git checkout -b feature/nueva-funcionalidad
```

Haz tus cambios y confírmalos.

Envía un Pull Request con una descripción clara.

---
# 🛡️ Licencia

Este proyecto se distribuye bajo la licencia MIT.