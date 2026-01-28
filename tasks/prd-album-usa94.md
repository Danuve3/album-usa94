# PRD: Álbum de Cromos Virtual - Mundial USA 94

## Overview

Aplicación web que recrea la experiencia de coleccionar el álbum de cromos del Mundial USA 1994. Los usuarios pueden registrarse, recibir sobres diarios con cromos, pegarlos en un álbum interactivo con efecto de libro realista, y intercambiar cromos repetidos con otros coleccionistas. El panel de administración permite gestionar usuarios, cromos, rarezas y configuración del sistema.

## Goals

- Recrear la experiencia nostálgica de coleccionar cromos con interacciones modernas y fluidas
- Permitir a usuarios coleccionar los 444 cromos del álbum oficial
- Ofrecer sistema de intercambio social entre coleccionistas
- Proporcionar panel de administración completo para gestionar todo el sistema
- Garantizar seguridad en autenticación y transacciones de intercambio

## Quality Gates

Estos comandos deben pasar para cada user story:
- `php artisan test` - Tests unitarios y de feature
- `./vendor/bin/pint --test` - Estilo de código Laravel

Para historias de UI:
- Verificar funcionamiento en navegador (Chrome/Firefox)

## User Stories

### Fase 1: Setup del Proyecto

#### US-001: Inicializar proyecto Laravel
**Description:** Como desarrollador, quiero un proyecto Laravel limpio configurado para comenzar el desarrollo.

**Acceptance Criteria:**
- [ ] Proyecto Laravel 11 creado en el directorio actual
- [ ] Configuración de `.env.example` con variables necesarias
- [ ] Base de datos SQLite configurada para desarrollo
- [ ] `composer install` ejecuta sin errores

---

#### US-002: Instalar y configurar Livewire
**Description:** Como desarrollador, quiero Livewire instalado para crear componentes reactivos.

**Acceptance Criteria:**
- [ ] Livewire 3 instalado via Composer
- [ ] Assets de Livewire publicados
- [ ] Componente de prueba "Hello World" funciona correctamente
- [ ] Layout base incluye directivas `@livewireStyles` y `@livewireScripts`

---

#### US-003: Instalar y configurar Backpack
**Description:** Como desarrollador, quiero Backpack instalado para el panel de administración.

**Acceptance Criteria:**
- [ ] Backpack CRUD instalado y configurado
- [ ] Ruta `/admin` accesible
- [ ] Usuario admin creado con seeder
- [ ] Dashboard básico de Backpack funcional

---

#### US-004: Configurar Tailwind CSS
**Description:** Como desarrollador, quiero Tailwind CSS para estilar la aplicación.

**Acceptance Criteria:**
- [ ] Tailwind CSS instalado y configurado con Vite
- [ ] Archivo `tailwind.config.js` configurado para Laravel/Livewire
- [ ] Estilos base aplicados correctamente
- [ ] `npm run build` compila sin errores

---

### Fase 2: Base de Datos y Modelos

#### US-005: Crear migración y modelo de Stickers (Cromos)
**Description:** Como sistema, necesito almacenar información de cada cromo del álbum.

**Acceptance Criteria:**
- [ ] Migración `stickers` con campos: id, number (1-444), name, page_number, position_x, position_y, width, height, is_horizontal, rarity (enum: common, shiny), image_path
- [ ] Modelo `Sticker` con fillables y casts apropiados
- [ ] Relación con `UserSticker` definida
- [ ] Factory para testing creada

---

#### US-006: Crear migración y modelo de Pages (Páginas del álbum)
**Description:** Como sistema, necesito almacenar información de cada página del álbum.

**Acceptance Criteria:**
- [ ] Migración `pages` con campos: id, number (orden), image_path
- [ ] Modelo `Page` con relación a stickers de esa página
- [ ] Scope para ordenar por número de página
- [ ] Factory para testing creada

---

#### US-007: Crear migración y modelo de UserSticker (Cromos del usuario)
**Description:** Como sistema, necesito trackear qué cromos tiene cada usuario y su estado.

**Acceptance Criteria:**
- [ ] Migración `user_stickers` con campos: id, user_id, sticker_id, is_glued (pegado), obtained_at, timestamps
- [ ] Índice único en [user_id, sticker_id, obtained_at] para permitir repetidos
- [ ] Modelo con relaciones a User y Sticker
- [ ] Scopes: `glued()`, `unglued()`, `duplicates()`

---

#### US-008: Crear migración y modelo de Packs (Sobres)
**Description:** Como sistema, necesito trackear los sobres disponibles y abiertos por usuario.

**Acceptance Criteria:**
- [ ] Migración `packs` con campos: id, user_id, opened_at (nullable), created_at
- [ ] Modelo `Pack` con relación a User
- [ ] Scopes: `unopened()`, `opened()`, `today()`
- [ ] Constante configurable: STICKERS_PER_PACK = 5

---

#### US-009: Crear migración para configuración del sistema
**Description:** Como admin, necesito poder configurar parámetros del sistema en BD.

**Acceptance Criteria:**
- [ ] Migración `settings` con campos: key, value, type
- [ ] Modelo `Setting` con métodos estáticos `get()` y `set()`
- [ ] Seeder con configuraciones iniciales: packs_per_day (5), stickers_per_pack (5), shiny_probability (0.05)
- [ ] Cache de settings para rendimiento

---

### Fase 3: Autenticación

#### US-010: Implementar registro de usuarios
**Description:** Como visitante, quiero registrarme para comenzar mi colección.

**Acceptance Criteria:**
- [ ] Formulario de registro con: nombre, email, contraseña, confirmación
- [ ] Validación de campos con mensajes en español
- [ ] Hash seguro de contraseña con bcrypt
- [ ] Redirección al álbum tras registro exitoso
- [ ] Protección CSRF activa

---

#### US-011: Implementar login de usuarios
**Description:** Como usuario registrado, quiero iniciar sesión para acceder a mi colección.

**Acceptance Criteria:**
- [ ] Formulario de login con email y contraseña
- [ ] Opción "Recordarme" funcional
- [ ] Rate limiting: máximo 5 intentos por minuto
- [ ] Mensaje de error genérico (no revelar si email existe)
- [ ] Redirección al álbum tras login exitoso

---

#### US-012: Implementar logout y protección de rutas
**Description:** Como usuario, quiero cerrar sesión de forma segura.

**Acceptance Criteria:**
- [ ] Botón de logout visible en header
- [ ] Logout invalida sesión completamente
- [ ] Middleware `auth` protege rutas del álbum
- [ ] Redirección a login si no autenticado

---

### Fase 4: Sistema de Sobres

#### US-013: Crear comando para asignar sobres diarios
**Description:** Como sistema, debo asignar sobres diarios a cada usuario activo.

**Acceptance Criteria:**
- [ ] Comando `php artisan packs:daily` creado
- [ ] Asigna 5 sobres (configurable) a cada usuario
- [ ] No duplica si ya se asignaron hoy
- [ ] Loggea cantidad de usuarios procesados
- [ ] Puede programarse en scheduler

---

#### US-014: Crear lógica de apertura de sobre
**Description:** Como sistema, necesito generar cromos aleatorios al abrir un sobre.

**Acceptance Criteria:**
- [ ] Service `PackService` con método `open(Pack $pack)`
- [ ] Genera 5 cromos aleatorios respetando probabilidad de shiny
- [ ] Marca el sobre como abierto con timestamp
- [ ] Crea registros en `user_stickers`
- [ ] Retorna colección de cromos obtenidos
- [ ] Transacción DB para atomicidad

---

#### US-015: Crear componente Livewire de pila de sobres
**Description:** Como usuario, quiero ver mis sobres sin abrir en pantalla.

**Acceptance Criteria:**
- [ ] Componente `PackPile` muestra sobres apilados visualmente
- [ ] Muestra contador de sobres disponibles
- [ ] Click en pila inicia proceso de apertura
- [ ] Actualiza en tiempo real al abrir sobres
- [ ] Muestra mensaje si no hay sobres disponibles

---

#### US-016: Crear animación de apertura de sobre
**Description:** Como usuario, quiero una animación satisfactoria al abrir sobres arrastrando.

**Acceptance Criteria:**
- [ ] Modal/overlay al iniciar apertura de sobre
- [ ] Imagen del sobre visible y arrastrable
- [ ] Gesto de arrastrar hacia abajo "rompe" el sobre
- [ ] Animación de sobre rasgándose con CSS/JS
- [ ] Transición fluida a revelación de cromos

---

#### US-017: Crear animación de revelación de cromos
**Description:** Como usuario, quiero ver los cromos del sobre revelarse uno a uno.

**Acceptance Criteria:**
- [ ] Cromos aparecen uno a uno con delay de 0.5s
- [ ] Efecto de "flip" o "slide-in" para cada cromo
- [ ] Cromos shiny tienen efecto brillante distintivo
- [ ] Indicador si el cromo es repetido
- [ ] Botón "Continuar" al terminar revelación
- [ ] Cromos se añaden automáticamente a pila de sin pegar

---

### Fase 5: Álbum con Páginas

#### US-018: Integrar librería de flip de páginas
**Description:** Como desarrollador, necesito una librería para el efecto de libro.

**Acceptance Criteria:**
- [ ] StPageFlip (o similar compatible) instalada via npm
- [ ] Wrapper de JavaScript que se integra con Livewire
- [ ] Efecto de pasar página realista funcionando
- [ ] Eventos de cambio de página capturables

---

#### US-019: Crear componente Livewire del álbum
**Description:** Como usuario, quiero ver mi álbum como un libro con páginas pasables.

**Acceptance Criteria:**
- [ ] Componente `Album` renderiza todas las páginas
- [ ] Imágenes de páginas cargan desde storage
- [ ] Navegación con click en esquinas o swipe
- [ ] Indicador de página actual "Página X de Y"
- [ ] Botones de ir a primera/última página

---

#### US-020: Renderizar cromos pegados en páginas
**Description:** Como usuario, quiero ver mis cromos pegados en sus posiciones correctas.

**Acceptance Criteria:**
- [ ] Cromos pegados se superponen en su posición exacta
- [ ] Posición definida por position_x, position_y del sticker
- [ ] Cromos horizontales rotados correctamente
- [ ] Tamaño de cromo respeta width/height definidos
- [ ] Rendimiento optimizado (lazy loading por página visible)

---

#### US-021: Mostrar huecos vacíos de cromos
**Description:** Como usuario, quiero ver qué cromos me faltan en cada página.

**Acceptance Criteria:**
- [ ] Huecos sin cromo muestran silueta o número
- [ ] Tooltip al hover muestra nombre del cromo faltante
- [ ] Contador de cromos: "X/Y en esta página"
- [ ] Visual diferenciado entre pegado, vacío y disponible para pegar

---

### Fase 6: Pegar Cromos (Drag & Drop)

#### US-022: Crear componente de pila de cromos sin pegar
**Description:** Como usuario, quiero ver mis cromos obtenidos pendientes de pegar.

**Acceptance Criteria:**
- [ ] Componente `UngluedStickers` visible junto al álbum
- [ ] Muestra cromos apilados o en grid scrolleable
- [ ] Cada cromo es arrastrable (draggable)
- [ ] Contador total de cromos sin pegar
- [ ] Filtro/búsqueda por número o nombre

---

#### US-023: Crear componente de pila de cromos repetidos
**Description:** Como usuario, quiero ver mis cromos repetidos separados.

**Acceptance Criteria:**
- [ ] Componente `DuplicateStickers` visible en pantalla
- [ ] Muestra cromos con contador de cantidad (ej: "x3")
- [ ] Separado visualmente de cromos sin pegar
- [ ] Acceso rápido a funciones de intercambio

---

#### US-024: Implementar drag & drop para pegar cromos
**Description:** Como usuario, quiero arrastrar cromos a su lugar en el álbum para pegarlos.

**Acceptance Criteria:**
- [ ] Cromo arrastrable desde pila de sin pegar
- [ ] Drop zone activa solo en la posición correcta del cromo
- [ ] Feedback visual al acercar cromo a posición válida
- [ ] Si suelta en posición incorrecta, vuelve a la pila
- [ ] Animación de "pegado" al soltar en lugar correcto

---

#### US-025: Persistir cromo pegado en base de datos
**Description:** Como sistema, debo guardar cuando un usuario pega un cromo.

**Acceptance Criteria:**
- [ ] Endpoint/action Livewire `glueSticker(stickerId)`
- [ ] Actualiza `is_glued = true` en user_stickers
- [ ] Validación: usuario posee el cromo y no está pegado
- [ ] Actualización optimista en UI
- [ ] Manejo de errores con rollback visual

---

### Fase 7: Panel de Administración

#### US-026: CRUD de Cromos en Backpack
**Description:** Como admin, quiero gestionar los cromos del álbum.

**Acceptance Criteria:**
- [ ] Listado de cromos con filtros por página, rareza
- [ ] Crear/editar cromo con todos sus campos
- [ ] Upload de imagen de cromo
- [ ] Preview de imagen en formulario
- [ ] Importación masiva desde CSV (opcional)

---

#### US-027: CRUD de Páginas en Backpack
**Description:** Como admin, quiero gestionar las páginas del álbum.

**Acceptance Criteria:**
- [ ] Listado de páginas ordenadas por número
- [ ] Crear/editar página con número e imagen
- [ ] Upload de imagen de página
- [ ] Preview de página con posiciones de cromos
- [ ] Reordenar páginas con drag & drop

---

#### US-028: Gestión de Usuarios en Backpack
**Description:** Como admin, quiero gestionar usuarios del sistema.

**Acceptance Criteria:**
- [ ] Listado de usuarios con búsqueda
- [ ] Ver estadísticas: cromos totales, pegados, repetidos
- [ ] Banear/desbanear usuario
- [ ] Dar sobres manualmente a usuario
- [ ] Ver historial de actividad

---

#### US-029: Panel de Configuración del Sistema
**Description:** Como admin, quiero configurar parámetros del sistema.

**Acceptance Criteria:**
- [ ] Página de settings en Backpack
- [ ] Editar: sobres por día, cromos por sobre
- [ ] Editar: probabilidad de cromo shiny (0-1)
- [ ] Cambios aplican inmediatamente
- [ ] Log de cambios de configuración

---

#### US-030: Gestión de Rareza de Cromos
**Description:** Como admin, quiero definir qué cromos son shiny/brillantes.

**Acceptance Criteria:**
- [ ] En CRUD de cromos, campo "rareza" editable
- [ ] Bulk action para marcar múltiples como shiny
- [ ] Vista de todos los shiny con sus probabilidades
- [ ] Preview de cómo se ve el efecto shiny

---

### Fase 8: Sistema de Intercambio

#### US-031: Crear migración para trades (intercambios)
**Description:** Como sistema, necesito almacenar propuestas de intercambio.

**Acceptance Criteria:**
- [ ] Migración `trades` con: id, sender_id, receiver_id, status (pending/accepted/rejected/cancelled), created_at, expires_at
- [ ] Migración `trade_items` con: trade_id, user_sticker_id, direction (offered/requested)
- [ ] Modelo Trade con relaciones y scopes
- [ ] Status enum con transiciones válidas

---

#### US-032: Crear interfaz de intercambio directo
**Description:** Como usuario, quiero proponer intercambio a otro usuario específico.

**Acceptance Criteria:**
- [ ] Buscar usuario por nombre
- [ ] Ver cromos repetidos del otro usuario (públicos)
- [ ] Seleccionar cromos que ofrezco
- [ ] Seleccionar cromos que solicito
- [ ] Enviar propuesta de intercambio

---

#### US-033: Crear mercado público de cromos
**Description:** Como usuario, quiero publicar cromos en un mercado para intercambio.

**Acceptance Criteria:**
- [ ] Componente `Market` con listado de ofertas
- [ ] Publicar cromo repetido con precio (en otros cromos)
- [ ] Filtrar por cromo específico buscado
- [ ] Ver quién ofrece cromos que necesito
- [ ] Iniciar intercambio desde mercado

---

#### US-034: Crear bandeja de intercambios pendientes
**Description:** Como usuario, quiero ver y gestionar mis intercambios.

**Acceptance Criteria:**
- [ ] Listado de intercambios enviados y recibidos
- [ ] Ver detalle de cada propuesta
- [ ] Aceptar/rechazar propuestas recibidas
- [ ] Cancelar propuestas enviadas
- [ ] Notificación visual de pendientes

---

#### US-035: Implementar ejecución de intercambio
**Description:** Como sistema, debo transferir cromos al aceptar intercambio.

**Acceptance Criteria:**
- [ ] Service `TradeService` con método `execute(Trade)`
- [ ] Transferencia atómica de cromos entre usuarios
- [ ] Validación de que ambos aún poseen los cromos
- [ ] Actualización de estado a "accepted"
- [ ] Notificación a ambas partes

---

### Fase 9: Interfaz Principal

#### US-036: Crear layout principal del coleccionista
**Description:** Como usuario, quiero una interfaz integrada con álbum, cromos y sobres.

**Acceptance Criteria:**
- [ ] Layout responsive con álbum centrado
- [ ] Panel lateral izquierdo: pila de cromos sin pegar
- [ ] Panel lateral derecho: cromos repetidos
- [ ] Header: logo, usuario, sobres disponibles, logout
- [ ] Footer: enlaces a mercado, intercambios, stats

---

#### US-037: Crear página de estadísticas del usuario
**Description:** Como usuario, quiero ver mi progreso de colección.

**Acceptance Criteria:**
- [ ] Porcentaje de álbum completado
- [ ] Cromos por categoría/página
- [ ] Cromos shiny obtenidos
- [ ] Historial de sobres abiertos
- [ ] Gráfico de progreso en el tiempo

---

#### US-038: Implementar notificaciones en tiempo real
**Description:** Como usuario, quiero recibir notificaciones de intercambios.

**Acceptance Criteria:**
- [ ] Icono de notificaciones en header
- [ ] Badge con contador de no leídas
- [ ] Notificación al recibir propuesta de intercambio
- [ ] Notificación al aceptar/rechazar mi propuesta
- [ ] Usar Laravel Echo + Pusher/Reverb o polling

---

### Fase 10: Seeding y Datos Iniciales

#### US-039: Crear seeder de páginas del álbum
**Description:** Como desarrollador, necesito poblar las páginas del álbum.

**Acceptance Criteria:**
- [ ] Seeder lee imágenes de carpeta de páginas
- [ ] Crea registros de Page con número e imagen
- [ ] Orden basado en nombre de archivo (1.webp, 2.webp...)
- [ ] Copia imágenes a storage público
- [ ] Idempotente (puede re-ejecutarse)

---

#### US-040: Crear seeder de cromos
**Description:** Como desarrollador, necesito poblar los 444 cromos.

**Acceptance Criteria:**
- [ ] Seeder lee imágenes de carpeta de cromos
- [ ] Crea registros con número, imagen, rareza default
- [ ] Posiciones X/Y iniciales (ajustables después)
- [ ] Detecta orientación horizontal/vertical por dimensiones
- [ ] Asocia cromo a página correspondiente

---

#### US-041: Crear herramienta de mapeo de posiciones
**Description:** Como admin, necesito posicionar los cromos en sus páginas exactas.

**Acceptance Criteria:**
- [ ] Vista especial en admin para mapear cromos
- [ ] Mostrar página con cromos arrastrables
- [ ] Guardar posición X/Y al soltar cromo
- [ ] Ajustar tamaño del cromo visualmente
- [ ] Marcar como horizontal si aplica

---

## Functional Requirements

- FR-01: El sistema debe soportar 444 cromos distribuidos en ~50 páginas
- FR-02: Cada usuario recibe exactamente 5 sobres diarios a las 00:00
- FR-03: Cada sobre contiene exactamente 5 cromos aleatorios
- FR-04: La probabilidad de cromo shiny es configurable (default 5%)
- FR-05: Un cromo solo puede pegarse en su posición única del álbum
- FR-06: Los cromos repetidos se acumulan sin límite
- FR-07: Los intercambios expiran después de 7 días sin respuesta
- FR-08: Las contraseñas deben tener mínimo 8 caracteres
- FR-09: El rate limiting de login es 5 intentos por minuto
- FR-10: Todas las transacciones de intercambio son atómicas

## Non-Goals (Out of Scope)

- App móvil nativa (solo web responsive)
- Compra de sobres con dinero real
- Chat entre usuarios
- Torneos o competiciones
- Cromos animados o con video
- Integración con redes sociales
- Sistema de logros/achievements (posible v2)
- Soporte multi-idioma (solo español)
- PWA/offline mode

## Technical Considerations

- **Stack:** Laravel 11 + Livewire 3 + Backpack 6 + Tailwind CSS
- **Base de datos:** MySQL/PostgreSQL para producción, SQLite para desarrollo
- **Flip de páginas:** StPageFlip (vanilla JS, sin jQuery)
- **Drag & drop:** SortableJS o interact.js para drag nativo
- **Animaciones:** CSS animations + Alpine.js para orquestar
- **Imágenes:** WebP optimizado, lazy loading, CDN recomendado
- **Tiempo real:** Laravel Reverb (WebSockets) o polling como fallback
- **Storage:** Laravel Storage con disco público para imágenes

## Success Metrics

- Álbum renderiza todas las páginas sin lag perceptible
- Animación de sobres fluida a 60fps
- Drag & drop de cromos funciona sin errores
- Tiempo de carga inicial < 3 segundos
- Intercambios se ejecutan sin pérdida de datos
- Panel admin permite gestionar todo el contenido

## Open Questions

- ¿Estructura exacta de carpetas de imágenes? (páginas vs cromos)
- ¿Hay datos de nombres de jugadores/equipos para cada cromo?
- ¿Dominio/hosting donde se desplegará?
- ¿Límite de usuarios concurrentes esperado?
