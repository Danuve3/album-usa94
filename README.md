# Album USA 94

Aplicación web para coleccionar stickers virtuales del Mundial de Fútbol USA 1994. Revive la nostalgia de completar tu álbum Panini con esta experiencia digital.

## Funcionalidades

- **Álbum virtual**: Visualiza tu álbum con las páginas originales y pega tus stickers
- **Sobres de stickers**: Abre packs y descubre qué figuritas te tocan
- **Stickers duplicados**: Gestiona tus repetidas para intercambiar
- **Mercado**: Compra y vende stickers con otros coleccionistas
- **Intercambios**: Propón y negocia trades con la comunidad
- **Estadísticas**: Consulta tu progreso y logros
- **Notificaciones**: Recibe alertas de trades y actividad del mercado
- **Panel admin**: Gestión completa con Backpack CRUD

## Stack Tecnológico

- **Backend**: Laravel 12 + PHP 8.3
- **Frontend**: Livewire 4 + Tailwind CSS 4
- **Admin**: Backpack CRUD 6
- **Build**: Vite 7
- **Base de datos**: SQLite (por defecto)

## Instalación Local

### Requisitos

- PHP 8.2+
- Composer
- Node.js 18+
- npm

### Pasos

1. **Clonar el repositorio**
   ```bash
   git clone <url-del-repo>
   cd album-usa94
   ```

2. **Instalar dependencias y configurar**
   ```bash
   composer setup
   ```
   Este comando ejecuta automáticamente:
   - Instalación de dependencias PHP
   - Creación del archivo `.env`
   - Generación de la clave de aplicación
   - Ejecución de migraciones
   - Instalación de dependencias npm
   - Build de assets

3. **Iniciar el servidor de desarrollo**
   ```bash
   composer dev
   ```
   Esto levanta simultáneamente:
   - Servidor Laravel en `http://localhost:8000`
   - Queue worker para jobs en background
   - Laravel Pail para logs en tiempo real
   - Vite para hot reload de assets

## Comandos Útiles

```bash
# Solo servidor de desarrollo
php artisan serve

# Ejecutar tests
composer test

# Linter de código
./vendor/bin/pint
```

## Estructura del Proyecto

```
app/
├── Models/          # Modelos Eloquent (User, Sticker, Trade, etc.)
├── Livewire/        # Componentes Livewire
└── Http/Controllers/Admin/  # Controllers de Backpack

database/
├── migrations/      # Migraciones de la BD
├── seeders/         # Seeders con datos iniciales
└── data/            # Imágenes de páginas y stickers

resources/views/
├── livewire/        # Vistas de componentes Livewire
└── components/      # Blade components
```

---

## Despliegue en Producción

### Requisitos del Servidor

- PHP 8.2+ con extensiones: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML
- Composer 2.x
- Node.js 18+ y npm (solo para build)
- Servidor web: Nginx o Apache
- Base de datos: MySQL 8.0+ / PostgreSQL 13+ / SQLite 3.35+
- Supervisor (para queue workers)
- Cron

### 1. Preparar el Servidor

```bash
# Clonar el repositorio
git clone <url-del-repo> /var/www/album-usa94
cd /var/www/album-usa94

# Establecer permisos
sudo chown -R www-data:www-data /var/www/album-usa94
sudo chmod -R 755 /var/www/album-usa94
sudo chmod -R 775 storage bootstrap/cache
```

### 2. Instalar Dependencias

```bash
# Dependencias PHP (sin dev)
composer install --optimize-autoloader --no-dev

# Dependencias Node y build de assets
npm ci
npm run build
```

### 3. Configurar el Entorno

```bash
# Copiar archivo de entorno
cp .env.example .env

# Generar clave de aplicación
php artisan key:generate
```

Editar `.env` con los valores de producción:

```env
APP_NAME="Album USA 94"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tu-dominio.com

# Base de datos (ejemplo MySQL)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=album_usa94
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña

# Cola de trabajos
QUEUE_CONNECTION=database

# Cache y sesiones
CACHE_STORE=file
SESSION_DRIVER=file

# Mail (configurar según tu proveedor)
MAIL_MAILER=smtp
MAIL_HOST=smtp.ejemplo.com
MAIL_PORT=587
MAIL_USERNAME=tu_usuario
MAIL_PASSWORD=tu_contraseña
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tu-dominio.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 4. Configurar la Base de Datos

```bash
# Ejecutar migraciones
php artisan migrate --force

# (Opcional) Cargar datos iniciales
php artisan db:seed --force
```

### 5. Optimizar para Producción

```bash
# Cachear configuración
php artisan config:cache

# Cachear rutas
php artisan route:cache

# Cachear vistas
php artisan view:cache

# Cachear eventos
php artisan event:cache

# Crear enlace simbólico para storage
php artisan storage:link
```

### 6. Configurar el Servidor Web

#### Nginx (recomendado)

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name tu-dominio.com;
    root /var/www/album-usa94/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

#### Apache

Asegúrate de que `mod_rewrite` esté habilitado. El archivo `.htaccess` en `/public` ya está configurado.

```apache
<VirtualHost *:80>
    ServerName tu-dominio.com
    DocumentRoot /var/www/album-usa94/public

    <Directory /var/www/album-usa94/public>
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/album-usa94-error.log
    CustomLog ${APACHE_LOG_DIR}/album-usa94-access.log combined
</VirtualHost>
```

### 7. Configurar Cron (Tareas Programadas)

El cron es **esencial** para la entrega automática de sobres a los usuarios.

```bash
# Editar crontab
sudo crontab -e -u www-data
```

Añadir esta línea:

```cron
* * * * * cd /var/www/album-usa94 && php artisan schedule:run >> /dev/null 2>&1
```

Esto ejecuta el scheduler cada minuto, que se encarga de:
- **`packs:deliver`**: Entrega sobres a usuarios según el intervalo configurado en `/admin/setting`
- **`packs:daily`**: Tareas diarias de mantenimiento

### 8. Configurar Queue Worker con Supervisor

Supervisor mantiene el worker de colas ejecutándose permanentemente.

```bash
# Instalar Supervisor
sudo apt install supervisor
```

Crear archivo de configuración:

```bash
sudo nano /etc/supervisor/conf.d/album-usa94-worker.conf
```

Contenido:

```ini
[program:album-usa94-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/album-usa94/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/album-usa94/storage/logs/worker.log
stopwaitsecs=3600
```

Activar el worker:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start album-usa94-worker:*
```

### 9. Configurar SSL (HTTPS)

Se recomienda usar Let's Encrypt con Certbot:

```bash
# Instalar Certbot
sudo apt install certbot python3-certbot-nginx

# Obtener certificado
sudo certbot --nginx -d tu-dominio.com

# Renovación automática (ya configurada por defecto)
sudo certbot renew --dry-run
```

### 10. Verificación Post-Despliegue

```bash
# Verificar que todo está correcto
php artisan about

# Verificar colas
php artisan queue:monitor database

# Verificar scheduler
php artisan schedule:list

# Probar entrega de sobres manualmente
php artisan packs:deliver
```

### Comandos de Mantenimiento

```bash
# Limpiar cachés (después de actualizaciones)
php artisan optimize:clear

# Recachear todo
php artisan optimize

# Ver logs en tiempo real
tail -f storage/logs/laravel.log

# Reiniciar workers después de actualizar código
sudo supervisorctl restart album-usa94-worker:*
```

### Actualización del Proyecto

```bash
# Activar modo mantenimiento
php artisan down

# Obtener cambios
git pull origin main

# Instalar dependencias
composer install --optimize-autoloader --no-dev
npm ci && npm run build

# Ejecutar migraciones
php artisan migrate --force

# Limpiar y recachear
php artisan optimize:clear
php artisan optimize

# Reiniciar workers
sudo supervisorctl restart album-usa94-worker:*

# Desactivar modo mantenimiento
php artisan up
```

---

## Licencia

MIT
