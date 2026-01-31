# PRD: Corrección de Errores Album USA 94

## Overview
Este PRD aborda la corrección de múltiples errores encontrados tanto en el panel de administración como en el frontend del álbum de cromos USA 94. Los errores van desde problemas de autenticación (419 Page Expired), errores 500 en el panel admin, hasta mejoras de UX en la visualización y gestión de cromos.

## Goals
- Restaurar la funcionalidad completa del panel de administración
- Corregir el sistema de intercambio de cromos entre usuarios
- Mejorar la experiencia visual del álbum con efecto abanico para cromos
- Garantizar que todos los flujos críticos funcionen sin errores

## Quality Gates

Estos comandos/verificaciones deben pasar para cada user story:
- `php artisan test` - Tests de Laravel
- Verificación manual en navegador del flujo afectado
- Sin errores en `storage/logs/laravel.log` durante la prueba

## User Stories

### US-001: Corregir error 419 Page Expired en panel admin
As a administrador, I want to acceder al panel de administración sin errores de sesión expirada so that puedo gestionar el contenido del álbum.

**Acceptance Criteria:**
- [ ] Identificar la causa del error 419 (tokens CSRF, configuración de sesiones)
- [ ] Corregir la configuración de sesiones en base de datos SQLite
- [ ] Verificar que todas las rutas admin funcionan tras login
- [ ] El token CSRF se regenera correctamente en cada petición

### US-002: Corregir error 500 en /admin/sticker
As a administrador, I want to acceder a la gestión de cromos sin errores so that puedo ver y administrar todos los cromos del álbum.

**Acceptance Criteria:**
- [ ] Identificar la causa del error 500 (probablemente Backpack PRO filters)
- [ ] Eliminar o reemplazar dependencias de Backpack PRO
- [ ] Implementar filtros básicos sin dependencias de pago si es necesario
- [ ] La página /admin/sticker carga correctamente mostrando la lista de cromos

### US-003: Corregir sticker-mapper para definir posiciones de cromos
As a administrador, I want to usar el sticker-mapper para definir las posiciones de los cromos so that los cromos se muestren correctamente en las páginas del álbum.

**Acceptance Criteria:**
- [ ] Revisar el CSS del mapper para que la imagen de fondo se muestre correctamente
- [ ] Los cromos existentes se muestran en sus posiciones guardadas
- [ ] Se pueden ajustar las posiciones de los cromos (arrastrar o editar coordenadas)
- [ ] Los cambios se guardan correctamente en la base de datos

### US-004: Implementar modal de confirmación para intercambios
As a usuario, I want to ver un modal de confirmación antes de aceptar o rechazar un intercambio so that no hago acciones accidentales.

**Acceptance Criteria:**
- [ ] Al hacer clic en "Aceptar intercambio" aparece modal de confirmación
- [ ] Al hacer clic en "Rechazar intercambio" aparece modal de confirmación
- [ ] El modal muestra los detalles del intercambio (qué cromos se intercambian)
- [ ] Solo se ejecuta la acción al confirmar en el modal
- [ ] Los botones ejecutan correctamente los métodos del backend

### US-005: Añadir efecto abanico a cromos sin pegar
As a usuario, I want to ver mis cromos sin pegar con efecto abanico superpuestos so that tenga una experiencia visual más atractiva similar a cartas en mano.

**Acceptance Criteria:**
- [ ] Los cromos sin pegar se muestran con superposición ~40%
- [ ] Cada cromo tiene una ligera rotación aleatoria para efecto natural
- [ ] Al pasar el cursor sobre un cromo, se eleva y destaca
- [ ] El efecto funciona correctamente en móvil y desktop

### US-006: Añadir efecto abanico a cromos repetidos
As a usuario, I want to ver mis cromos repetidos con efecto abanico so that pueda visualizar cuántos duplicados tengo de forma atractiva.

**Acceptance Criteria:**
- [ ] Los cromos repetidos se muestran con superposición ~40%
- [ ] Se indica visualmente la cantidad de repetidos
- [ ] El efecto es consistente con el de cromos sin pegar
- [ ] Se puede interactuar con los cromos para seleccionarlos para intercambio

### US-007: Mostrar imagen y cantidad en página Mis Cromos
As a usuario, I want to ver la imagen y cantidad de cada cromo en la página Mis Cromos so that pueda gestionar mi colección fácilmente.

**Acceptance Criteria:**
- [ ] Cada cromo muestra su imagen correctamente
- [ ] Se muestra la cantidad de unidades de cada cromo
- [ ] El diseño es responsive y se adapta a diferentes tamaños de pantalla
- [ ] Los cromos se organizan de forma clara y navegable

## Functional Requirements
- FR-1: El sistema debe mantener sesiones válidas durante la navegación del panel admin
- FR-2: El sistema debe cargar la lista de cromos sin dependencias de Backpack PRO
- FR-3: El sticker-mapper debe permitir posicionar cromos sobre la imagen de página
- FR-4: Los intercambios deben requerir confirmación antes de ejecutarse
- FR-5: Los cromos deben mostrarse con efecto visual de abanico cuando hay múltiples
- FR-6: La página Mis Cromos debe mostrar imagen y cantidad de cada cromo

## Non-Goals (Out of Scope)
- Rediseño completo del panel de administración
- Implementación de nuevas funcionalidades no mencionadas
- Migración a otro sistema de sesiones (se mantiene database)
- Animaciones complejas o efectos 3D en los cromos
- Sistema de notificaciones push para intercambios

## Technical Considerations
- Las sesiones usan SQLite como driver de base de datos
- Backpack está instalado pero algunas funciones requieren PRO (de pago)
- El sticker-mapper ya tiene lógica existente que necesita corrección de CSS
- Los intercambios ya tienen métodos backend, solo fallan los botones frontend
- El proyecto usa Laravel con Blade templates y posiblemente Livewire/Alpine.js

## Success Metrics
- 0 errores 419 o 500 en el panel de administración
- 100% de las páginas admin accesibles tras login
- Intercambios se completan correctamente con confirmación
- Efecto abanico visible y funcional en cromos sin pegar y repetidos

## Open Questions
- ¿Existe documentación sobre la estructura de datos del sticker-mapper?
- ¿Qué librerías JS se usan actualmente para interactividad (Alpine, Livewire, vanilla)?
- ¿Hay tests existentes que deban mantenerse funcionando?