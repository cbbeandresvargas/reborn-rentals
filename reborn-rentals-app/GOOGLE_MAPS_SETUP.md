# Configuración de Google Maps API Key

## 🔐 Seguridad

La API key de Google Maps ha sido movida a variables de entorno para mayor seguridad. Ya no está hardcodeada en el código fuente.

## 📋 Pasos para Configurar

### 1. Obtener una API Key de Google Maps

1. Ve a [Google Cloud Console](https://console.cloud.google.com/)
2. Crea un nuevo proyecto o selecciona uno existente
3. Habilita la **Maps JavaScript API** y **Places API**
4. Ve a **Credenciales** → **Crear credenciales** → **Clave de API**
5. Copia tu API key

### 2. Configurar la Variable de Entorno

Abre tu archivo `.env` y agrega la siguiente línea:

```env
GOOGLE_MAPS_API_KEY=tu_api_key_aqui
```

**Ejemplo:**
```env
GOOGLE_MAPS_API_KEY=AIzaSyD_Nb12Kw5gcqefI5sJNwL45M24vxDh5wM
```

### 3. Restringir la API Key (Recomendado)

Para mayor seguridad, restringe tu API key en Google Cloud Console:

1. Ve a **Credenciales** en Google Cloud Console
2. Haz clic en tu API key
3. En **Restricciones de aplicación**, selecciona:
   - **Referencias HTTP (sitios web)**
   - Agrega tus dominios permitidos (ej: `rebornrentals.com`, `*.rebornrentals.com`)
4. En **Restricciones de API**, selecciona solo:
   - **Maps JavaScript API**
   - **Places API**
   - **Geocoding API** (si es necesario)

### 4. Verificar la Configuración

Después de agregar la variable al `.env`, limpia la caché de configuración:

```bash
php artisan config:clear
php artisan cache:clear
```

## ⚠️ Importante

- **Nunca** subas tu archivo `.env` al repositorio
- **Nunca** compartas tu API key públicamente
- Si tu API key se compromete, revócala inmediatamente en Google Cloud Console
- Considera usar diferentes API keys para desarrollo y producción

## 🔍 Verificación

Si la API key no está configurada correctamente, verás un mensaje de error en la consola del navegador:
```
Google Maps API key is not configured. Please set GOOGLE_MAPS_API_KEY in your .env file.
```

## 📝 Notas

- La API key se carga desde `config/services.php`
- Se pasa a la vista usando `config('services.google.maps_api_key')`
- El código verifica que la key exista antes de cargar el script de Google Maps
