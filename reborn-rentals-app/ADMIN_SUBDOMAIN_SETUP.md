# Configuración del Subdominio Admin

Este proyecto está configurado para que el panel de administración funcione en el subdominio `admin.rebornrentals.com`.

## ✅ Archivos Creados/Modificados

1. **`routes/admin.php`** - Rutas del panel de administración (solo accesibles desde el subdominio admin)
2. **`app/Http/Middleware/SubdomainMiddleware.php`** - Middleware para validar el subdominio
3. **`bootstrap/app.php`** - Configuración para registrar rutas admin condicionalmente
4. **`routes/web.php`** - Eliminadas las rutas admin (ahora están en `routes/admin.php`)
5. **`app/Http/Controllers/Auth/LoginController.php`** - Actualizado para redirigir al subdominio correcto
6. **`app/Http/Middleware/AdminMiddleware.php`** - Actualizado para trabajar con subdominios

## 🔧 Configuración para Desarrollo Local

### Opción 1: Usar localhost directamente (MÁS FÁCIL) ✅

**No necesitas configurar nada adicional.** Simplemente:

1. Ejecuta `php artisan serve`
2. Accede al sitio público: `http://localhost:8000`
3. Accede al admin: `http://localhost:8000/admin`

**¡Eso es todo!** En desarrollo local, las rutas admin están disponibles con el prefijo `/admin`.

### Opción 2: Usar subdominio local (Opcional)

Si prefieres usar subdominios también en desarrollo:

#### 1. Editar archivo hosts (Windows)

Edita el archivo `C:\Windows\System32\drivers\etc\hosts` (como administrador) y agrega:

```
127.0.0.1    rebornrentals.test
127.0.0.1    admin.rebornrentals.test
```

#### 2. Configurar servidor de desarrollo

**Opción A: Usar Laravel Valet (Recomendado)**
```bash
valet link rebornrentals
# Acceder a: http://rebornrentals.test
# Admin: http://admin.rebornrentals.test
```

**Opción B: Usar Laragon/XAMPP/WAMP**
Configura virtual hosts para ambos dominios apuntando al mismo directorio `public`.

#### 3. Configurar .env

Asegúrate de tener en tu `.env`:
```env
APP_URL=http://rebornrentals.test
APP_DOMAIN=rebornrentals.test
```

## 🌐 Configuración para Producción

### 1. DNS

Configura los registros DNS:
- `A` record para `rebornrentals.com` → IP del servidor
- `A` record para `admin.rebornrentals.com` → IP del servidor (misma IP)

### 2. Servidor Web (Nginx)

```nginx
# Dominio principal
server {
    listen 80;
    server_name rebornrentals.com www.rebornrentals.com;
    root /ruta/a/tu/proyecto/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}

# Subdominio admin
server {
    listen 80;
    server_name admin.rebornrentals.com;
    root /ruta/a/tu/proyecto/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

### 3. Servidor Web (Apache)

```apache
# Dominio principal
<VirtualHost *:80>
    ServerName rebornrentals.com
    ServerAlias www.rebornrentals.com
    DocumentRoot /ruta/a/tu/proyecto/public
    
    <Directory /ruta/a/tu/proyecto/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>

# Subdominio admin
<VirtualHost *:80>
    ServerName admin.rebornrentals.com
    DocumentRoot /ruta/a/tu/proyecto/public
    
    <Directory /ruta/a/tu/proyecto/public>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### 4. Configurar .env en producción

```env
APP_URL=https://rebornrentals.com
APP_DOMAIN=rebornrentals.com
```

## 🔐 Seguridad

- Las rutas admin solo están disponibles desde el subdominio `admin.rebornrentals.com`
- Si alguien intenta acceder a rutas admin desde el dominio principal, recibirá un 404
- El middleware `SubdomainMiddleware` valida que el subdominio sea correcto
- El middleware `AdminMiddleware` valida autenticación y rol de admin

## 🧪 Testing

### En Desarrollo Local (localhost)

1. Accede a `http://localhost:8000` → Debe mostrar el sitio público
2. Accede a `http://localhost:8000/admin` → Debe mostrar el login del admin
3. Accede a `http://localhost:8000/admin/login` → Login del admin
4. Después de hacer login como admin → Redirige a `http://localhost:8000/admin` (dashboard)

### Con Subdominio Configurado

1. Accede a `http://rebornrentals.test` → Debe mostrar el sitio público
2. Accede a `http://admin.rebornrentals.test` → Debe mostrar el login del admin
3. Intenta acceder a `http://rebornrentals.test/admin` → Debe dar 404 (rutas admin no existen en dominio principal en producción)

## 📝 Notas

- Ambas URLs (principal y admin) apuntan al mismo proyecto Laravel
- Laravel detecta automáticamente el subdominio y carga las rutas correspondientes
- Las sesiones se comparten entre ambos subdominios (mismo dominio base)
- Los usuarios admin que hagan login desde el dominio principal serán redirigidos automáticamente al subdominio admin
