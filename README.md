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

## Licencia

MIT
