# Reborn Rentals - Laravel MVC Application

Aplicación web completa de alquiler de maquinaria construida con Laravel 12, Tailwind CSS y arquitectura MVC.

## 🚀 Características

- ✅ **Backend Completo**: Modelos, Controladores, Rutas
- ✅ **Frontend Integrado**: Vistas Blade con Tailwind CSS via CDN
- ✅ **Carrito de Compras**: Sistema de sesiones
- ✅ **Autenticación**: Login/Registro con sesiones Laravel
- ✅ **Checkout Completo**: Proceso de pedidos con validación
- ✅ **Gestión de Órdenes**: Visualización de pedidos del usuario
- ✅ **Base de Datos**: SQLite con migraciones completas

## 📋 Requisitos

- PHP >= 8.2
- Composer
- SQLite

## 🛠️ Instalación

1. **Configurar el entorno:**
```bash
# Ya está configurado para SQLite
```

2. **Instalar dependencias:**
```bash
composer install
```

3. **Ejecutar migraciones y seeders:**
```bash
php artisan migrate --seed
```

4. **Iniciar el servidor:**
```bash
php -S localhost:8000 -t public
```

## 👤 Usuarios de Prueba

- **Admin**: 
  - Email: `admin@rebornrentals.com`
  - Password: `password`

- **Usuario Normal**:
  - Email: `john@example.com`
  - Password: `password`

## 📁 Estructura del Proyecto

```
app/
├── Http/Controllers/
│   ├── Auth/          # Login y Registro
│   ├── CartController.php      # Gestión de carrito
│   ├── CheckoutController.php  # Proceso de checkout
│   ├── HomeController.php      # Página principal
│   ├── OrderController.php     # Gestión de órdenes
│   └── ProductController.php   # Productos
├── Models/            # Todos los modelos con relaciones
└── ...

resources/views/
├── layouts/
│   └── app.blade.php           # Layout principal
├── auth/
│   ├── login.blade.php
│   └── register.blade.php
├── checkout/
│   └── index.blade.php
├── orders/
│   ├── index.blade.php
│   └── show.blade.php
├── products/
│   ├── index.blade.php
│   └── show.blade.php
└── home.blade.php

routes/
└── web.php            # Todas las rutas web
```

## 🎨 Diseño

- **Tailwind CSS**: Via CDN (sin npm)
- **Colores principales**:
  - Primario: `#CE9704` (Dorado)
  - Fondo oscuro: `#4A4A4A`
  - Fondo carrito: `#2F2F2F`
  - Gris claro: `#BBBBBB`

## 🛒 Funcionalidades

### Carrito de Compras
- Agregar productos
- Actualizar cantidad
- Eliminar productos
- Vista en sidebar
- Persistencia con sesiones

### Checkout
- Selección de fechas
- Dirección de entrega
- Códigos de descuento
- Métodos de pago
- Cálculo automático de impuestos

### Órdenes
- Listado de pedidos
- Detalle de pedido
- Historial completo

## 🔐 Autenticación

- Sistema de sesiones nativo de Laravel
- Login/Registro
- Middleware de autenticación
- Protección de rutas

## 📝 Notas

- El carrito usa sesiones de Laravel (no localStorage)
- Los productos están en la base de datos
- Las imágenes se encuentran en `public/`
- Tailwind CSS se carga via CDN para evitar problemas de compilación

## 🚀 Próximos Pasos

1. Agregar panel de administración
2. Mejorar sistema de notificaciones
3. Agregar pagos reales (Stripe/PayPal)
4. Sistema de email
5. Mejorar responsividad móvil
