# Guía de Despliegue - Calculadora Financiera

## Opción 1: Desplegar en Render (Recomendado)

### Pasos para desplegar:

1. **Crear cuenta en Render**
   - Ve a [https://render.com](https://render.com)
   - Regístrate con tu cuenta de GitHub

2. **Subir código a GitHub**
   ```bash
   # Si no tienes repositorio remoto, créalo en GitHub primero
   git add .
   git commit -m "Preparar para despliegue en Render"
   git push origin master
   ```

3. **Crear Web Service en Render**
   - Haz clic en "New +" y selecciona "Web Service"
   - Conecta tu repositorio de GitHub
   - IMPORTANTE: Configuración manual (NO uses render.yaml por ahora):
     - **Name**: calculadora-financiera (o el que prefieras)
     - **Root Directory**: `teenEconomia/calculadora`
     - **Environment**: Docker
     - **Dockerfile Path**: `./Dockerfile`
     - **Docker Build Context Directory**: `teenEconomia/calculadora`
     - **Plan**: Free (gratis)

4. **Variables de Entorno** (Agregar manualmente en Render)
   En la sección "Environment" agrega:
   ```
   APP_NAME=CalculadoraFinanciera
   APP_ENV=production
   APP_DEBUG=false
   APP_KEY=base64:GENERADO_AUTOMATICAMENTE
   LOG_CHANNEL=errorlog
   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   DB_CONNECTION=sqlite
   DB_DATABASE=/var/www/html/database/database.sqlite
   ```

   Para generar `APP_KEY`, ejecuta localmente:
   ```bash
   php artisan key:generate --show
   ```
   Y copia el resultado completo (incluye "base64:")

5. **Desplegar**
   - Click en "Create Web Service"
   - Render automáticamente empezará a construir tu aplicación usando Docker
   - El proceso toma 10-15 minutos la primera vez
   - Tu app estará disponible en: `https://calculadora-financiera.onrender.com`

### Notas importantes:

- El plan gratuito de Render "duerme" la aplicación después de 15 minutos de inactividad
- La primera carga después de dormir puede tardar 30-60 segundos
- Para apps siempre activas, necesitas el plan de paga ($7/mes)
- SQLite se usa por defecto; para bases de datos más robustas, usa PostgreSQL

---

## Opción 2: Desplegar en Railway

1. **Crear cuenta en Railway**
   - Ve a [https://railway.app](https://railway.app)
   - Regístrate con GitHub

2. **Crear nuevo proyecto**
   - "New Project" → "Deploy from GitHub repo"
   - Selecciona tu repositorio

3. **Configurar variables de entorno**
   ```
   APP_KEY=<se genera con: php artisan key:generate --show>
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://${{RAILWAY_PUBLIC_DOMAIN}}
   ```

4. **Configurar comando de inicio**
   - En Settings → Deploy
   - Build Command: `composer install && npm install && npm run build`
   - Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

---

## Opción 3: Desplegar en Fly.io

1. **Instalar Fly CLI**
   ```bash
   # Windows (PowerShell)
   iwr https://fly.io/install.ps1 -useb | iex
   ```

2. **Autenticarse**
   ```bash
   fly auth login
   ```

3. **Lanzar aplicación**
   ```bash
   fly launch
   ```

4. **Desplegar**
   ```bash
   fly deploy
   ```

---

## Verificar después del despliegue:

1. Visita tu URL de producción
2. Verifica que todas las rutas funcionen
3. Prueba la calculadora financiera
4. Revisa los logs si hay errores:
   - Render: Dashboard → Logs
   - Railway: Dashboard → Deployments → View Logs
   - Fly.io: `fly logs`

---

## Solución de problemas comunes:

### Error: "No application encryption key has been specified"
- Asegúrate de que `APP_KEY` esté configurada en las variables de entorno
- O ejecuta: `php artisan key:generate` en el servidor

### Error 500 después del despliegue
- Revisa los logs del servidor
- Verifica que `APP_DEBUG=false` en producción
- Asegúrate de que los permisos de `storage/` y `bootstrap/cache/` sean correctos

### Assets no cargan (CSS/JS)
- Ejecuta `npm run build` antes del despliegue
- Verifica que `public/build/` tenga los archivos compilados

### Base de datos no funciona
- Para SQLite: asegúrate de que `database/database.sqlite` exista
- Para PostgreSQL: configura correctamente las variables DB_*

---

## Actualizaciones futuras:

Cada vez que hagas cambios:

```bash
git add .
git commit -m "Descripción de los cambios"
git push origin master
```

Render/Railway/Fly.io detectarán los cambios y redesplegarán automáticamente.
