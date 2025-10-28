# Cómo Ejecutar el Proyecto

## Pasos para Iniciar

### 1. Verificar que tienes PHP 8.2+ instalado
```bash
php -v
```

### 2. Instalar dependencias (si es necesario)
```bash
composer install
```

### 3. Configurar la base de datos
```bash
# Si no tienes archivo .env, cópialo
# (Ya debería existir)

# Crear/actualizar la base de datos
php artisan migrate:fresh --seed
```

### 4. Iniciar el servidor
```bash
php artisan serve
```

O con puerto específico:
```bash
php artisan serve --port=8000
```

### 5. Abrir en el navegador
```
http://localhost:8000
```

## Credenciales de Prueba

### Admin Panel
- URL: http://localhost:8000/admin
- Email: `admin@rebornrentals.com`
- Password: `password`

### Usuario Normal
- URL: http://localhost:8000
- Email: `john@example.com`
- Password: `password`

## Si hay problemas

### Limpiar caché
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Verificar base de datos
```bash
php artisan migrate:status
```

### Regenerar key de aplicación
```bash
php artisan key:generate
```

## Comandos Útiles

```bash
# Ver todas las rutas
php artisan route:list

# Verificar errores de sintaxis
php artisan about

# Ejecutar tests (si los hay)
php artisan test
```

