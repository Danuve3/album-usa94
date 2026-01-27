@extends(backpack_view('blank'))

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">Mapeo de Cromos</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Arrastra los cromos para posicionarlos en la página</p>
    </section>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Seleccionar Página</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ backpack_url('sticker-mapper') }}">
                        <div class="mb-3">
                            <select name="page" class="form-control" onchange="this.form.submit()">
                                <option value="">-- Seleccionar página --</option>
                                @foreach($pages as $page)
                                    <option value="{{ $page->number }}" {{ $selectedPage && $selectedPage->number == $page->number ? 'selected' : '' }}>
                                        Página {{ $page->number }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </form>

                    @if($selectedPage && $stickers->count() > 0)
                        <hr>
                        <h6>Cromos en esta página</h6>
                        <div class="list-group sticker-list" style="max-height: 400px; overflow-y: auto;">
                            @foreach($stickers as $sticker)
                                <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center sticker-list-item"
                                     data-sticker-id="{{ $sticker->id }}"
                                     style="cursor: pointer;">
                                    <div>
                                        <strong>#{{ $sticker->number }}</strong>
                                        <small class="d-block text-muted">{{ Str::limit($sticker->name, 20) }}</small>
                                    </div>
                                    <div class="text-end">
                                        <small class="text-muted">{{ $sticker->position_x }}, {{ $sticker->position_y }}</small>
                                        @if($sticker->is_horizontal)
                                            <span class="badge bg-info ms-1">H</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            @if($selectedPage)
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Cromo Seleccionado</h5>
                    </div>
                    <div class="card-body" id="sticker-details">
                        <p class="text-muted">Selecciona un cromo en el canvas o en la lista</p>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-9">
            @if($selectedPage)
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Página {{ $selectedPage->number }}</h5>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="zoom-out">
                                <i class="la la-minus"></i>
                            </button>
                            <span id="zoom-level" class="mx-2">100%</span>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="zoom-in">
                                <i class="la la-plus"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" id="reset-zoom">
                                <i class="la la-compress"></i> Reset
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div id="canvas-container" style="overflow: auto; max-height: 80vh; position: relative;">
                            <div id="page-canvas" style="position: relative; display: inline-block; transform-origin: top left;">
                                @if($selectedPage->image_path)
                                    <img src="{{ Storage::disk('public')->url($selectedPage->image_path) }}"
                                         alt="Página {{ $selectedPage->number }}"
                                         id="page-image"
                                         style="display: block; max-width: none;">
                                @else
                                    <div style="width: 800px; height: 1200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                        <span class="text-muted">Sin imagen de página</span>
                                    </div>
                                @endif

                                @foreach($stickers as $sticker)
                                    <div class="sticker-draggable"
                                         data-sticker-id="{{ $sticker->id }}"
                                         data-sticker-number="{{ $sticker->number }}"
                                         data-sticker-name="{{ $sticker->name }}"
                                         data-is-horizontal="{{ $sticker->is_horizontal ? '1' : '0' }}"
                                         style="position: absolute;
                                                left: {{ $sticker->position_x }}px;
                                                top: {{ $sticker->position_y }}px;
                                                width: {{ $sticker->width }}px;
                                                height: {{ $sticker->height }}px;
                                                border: 2px solid #007bff;
                                                background: rgba(0, 123, 255, 0.2);
                                                cursor: move;
                                                display: flex;
                                                align-items: center;
                                                justify-content: center;
                                                font-size: 12px;
                                                font-weight: bold;
                                                color: #007bff;
                                                user-select: none;
                                                box-sizing: border-box;">
                                        <span class="sticker-label">#{{ $sticker->number }}</span>
                                        <div class="resize-handle" style="position: absolute; right: 0; bottom: 0; width: 12px; height: 12px; background: #007bff; cursor: se-resize;"></div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="la la-map-marked-alt" style="font-size: 4rem; color: #ccc;"></i>
                        <h4 class="mt-3 text-muted">Selecciona una página para comenzar</h4>
                        <p class="text-muted">Elige una página del selector de la izquierda para ver y posicionar sus cromos.</p>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('after_styles')
<style>
    .sticker-draggable {
        transition: box-shadow 0.2s;
    }
    .sticker-draggable:hover {
        box-shadow: 0 0 10px rgba(0, 123, 255, 0.5);
        z-index: 100;
    }
    .sticker-draggable.selected {
        border-color: #28a745 !important;
        background: rgba(40, 167, 69, 0.2) !important;
        z-index: 101;
    }
    .sticker-draggable.selected .sticker-label {
        color: #28a745;
    }
    .sticker-draggable.selected .resize-handle {
        background: #28a745;
    }
    .sticker-draggable.dragging {
        opacity: 0.8;
        z-index: 1000;
    }
    .sticker-list-item.active {
        background-color: #28a745 !important;
        color: white !important;
    }
    .sticker-list-item.active .text-muted {
        color: rgba(255, 255, 255, 0.8) !important;
    }
    .resize-handle {
        opacity: 0;
        transition: opacity 0.2s;
    }
    .sticker-draggable:hover .resize-handle,
    .sticker-draggable.selected .resize-handle {
        opacity: 1;
    }
    #page-canvas {
        background-color: #e9ecef;
    }
</style>
@endpush

@push('after_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('page-canvas');
    if (!canvas) return;

    let selectedSticker = null;
    let isDragging = false;
    let isResizing = false;
    let startX, startY, startLeft, startTop, startWidth, startHeight;
    let zoom = 1;

    // Zoom controls
    const zoomIn = document.getElementById('zoom-in');
    const zoomOut = document.getElementById('zoom-out');
    const zoomLevel = document.getElementById('zoom-level');
    const resetZoom = document.getElementById('reset-zoom');

    function updateZoom(newZoom) {
        zoom = Math.max(0.25, Math.min(2, newZoom));
        canvas.style.transform = `scale(${zoom})`;
        zoomLevel.textContent = Math.round(zoom * 100) + '%';
    }

    zoomIn?.addEventListener('click', () => updateZoom(zoom + 0.25));
    zoomOut?.addEventListener('click', () => updateZoom(zoom - 0.25));
    resetZoom?.addEventListener('click', () => updateZoom(1));

    // Select sticker
    function selectSticker(element) {
        document.querySelectorAll('.sticker-draggable.selected').forEach(el => el.classList.remove('selected'));
        document.querySelectorAll('.sticker-list-item.active').forEach(el => el.classList.remove('active'));

        if (element) {
            element.classList.add('selected');
            selectedSticker = element;

            const listItem = document.querySelector(`.sticker-list-item[data-sticker-id="${element.dataset.stickerId}"]`);
            if (listItem) {
                listItem.classList.add('active');
                listItem.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }

            updateStickerDetails(element);
        } else {
            selectedSticker = null;
            updateStickerDetails(null);
        }
    }

    function updateStickerDetails(element) {
        const detailsContainer = document.getElementById('sticker-details');
        if (!detailsContainer) return;

        if (!element) {
            detailsContainer.innerHTML = '<p class="text-muted">Selecciona un cromo en el canvas o en la lista</p>';
            return;
        }

        const id = element.dataset.stickerId;
        const number = element.dataset.stickerNumber;
        const name = element.dataset.stickerName;
        const isHorizontal = element.dataset.isHorizontal === '1';
        const rect = element.getBoundingClientRect();
        const canvasRect = canvas.getBoundingClientRect();

        const x = parseInt(element.style.left);
        const y = parseInt(element.style.top);
        const width = parseInt(element.style.width);
        const height = parseInt(element.style.height);

        detailsContainer.innerHTML = `
            <div class="mb-2">
                <strong>#${number}</strong> - ${name}
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label class="form-label small mb-1">Posición X</label>
                    <input type="number" class="form-control form-control-sm" id="edit-x" value="${x}" min="0">
                </div>
                <div class="col-6">
                    <label class="form-label small mb-1">Posición Y</label>
                    <input type="number" class="form-control form-control-sm" id="edit-y" value="${y}" min="0">
                </div>
            </div>
            <div class="row mb-2">
                <div class="col-6">
                    <label class="form-label small mb-1">Ancho</label>
                    <input type="number" class="form-control form-control-sm" id="edit-width" value="${width}" min="1">
                </div>
                <div class="col-6">
                    <label class="form-label small mb-1">Alto</label>
                    <input type="number" class="form-control form-control-sm" id="edit-height" value="${height}" min="1">
                </div>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="edit-horizontal" ${isHorizontal ? 'checked' : ''}>
                <label class="form-check-label" for="edit-horizontal">
                    Orientación horizontal
                </label>
            </div>
            <button type="button" class="btn btn-primary btn-sm w-100" id="save-details">
                <i class="la la-save"></i> Guardar cambios
            </button>
        `;

        // Add event listeners
        document.getElementById('save-details').addEventListener('click', () => saveFromForm(id));
    }

    function saveFromForm(stickerId) {
        const x = parseInt(document.getElementById('edit-x').value);
        const y = parseInt(document.getElementById('edit-y').value);
        const width = parseInt(document.getElementById('edit-width').value);
        const height = parseInt(document.getElementById('edit-height').value);
        const isHorizontal = document.getElementById('edit-horizontal').checked;

        savePosition(stickerId, x, y, width, height, isHorizontal);
    }

    // Sticker click handlers
    document.querySelectorAll('.sticker-draggable').forEach(sticker => {
        sticker.addEventListener('mousedown', function(e) {
            if (e.target.classList.contains('resize-handle')) {
                isResizing = true;
                startX = e.clientX;
                startY = e.clientY;
                startWidth = parseInt(this.style.width);
                startHeight = parseInt(this.style.height);
            } else {
                isDragging = true;
                startX = e.clientX;
                startY = e.clientY;
                startLeft = parseInt(this.style.left);
                startTop = parseInt(this.style.top);
            }
            selectSticker(this);
            this.classList.add('dragging');
            e.preventDefault();
        });
    });

    // List item click handlers
    document.querySelectorAll('.sticker-list-item').forEach(item => {
        item.addEventListener('click', function() {
            const stickerId = this.dataset.stickerId;
            const stickerElement = document.querySelector(`.sticker-draggable[data-sticker-id="${stickerId}"]`);
            if (stickerElement) {
                selectSticker(stickerElement);
                stickerElement.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    });

    // Mouse move handler
    document.addEventListener('mousemove', function(e) {
        if (!selectedSticker) return;

        if (isDragging) {
            const dx = (e.clientX - startX) / zoom;
            const dy = (e.clientY - startY) / zoom;
            const newLeft = Math.max(0, startLeft + dx);
            const newTop = Math.max(0, startTop + dy);
            selectedSticker.style.left = newLeft + 'px';
            selectedSticker.style.top = newTop + 'px';
        } else if (isResizing) {
            const dx = (e.clientX - startX) / zoom;
            const dy = (e.clientY - startY) / zoom;
            const newWidth = Math.max(20, startWidth + dx);
            const newHeight = Math.max(20, startHeight + dy);
            selectedSticker.style.width = newWidth + 'px';
            selectedSticker.style.height = newHeight + 'px';
        }
    });

    // Mouse up handler
    document.addEventListener('mouseup', function(e) {
        if ((isDragging || isResizing) && selectedSticker) {
            selectedSticker.classList.remove('dragging');

            const id = selectedSticker.dataset.stickerId;
            const x = parseInt(selectedSticker.style.left);
            const y = parseInt(selectedSticker.style.top);
            const width = parseInt(selectedSticker.style.width);
            const height = parseInt(selectedSticker.style.height);
            const isHorizontal = selectedSticker.dataset.isHorizontal === '1';

            if (isDragging || isResizing) {
                savePosition(id, x, y, width, height, isHorizontal);
            }

            updateStickerDetails(selectedSticker);
        }
        isDragging = false;
        isResizing = false;
    });

    // Click outside to deselect
    canvas.addEventListener('click', function(e) {
        if (e.target === canvas || e.target.id === 'page-image') {
            selectSticker(null);
        }
    });

    // Save position to server
    function savePosition(stickerId, x, y, width, height, isHorizontal) {
        fetch(`{{ backpack_url('sticker-mapper') }}/${stickerId}/position`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                position_x: Math.round(x),
                position_y: Math.round(y),
                width: Math.round(width),
                height: Math.round(height),
                is_horizontal: isHorizontal
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the list item position display
                const listItem = document.querySelector(`.sticker-list-item[data-sticker-id="${stickerId}"]`);
                if (listItem) {
                    const positionSpan = listItem.querySelector('.text-muted:last-child') || listItem.querySelector('small.text-muted:last-of-type');
                    if (positionSpan) {
                        positionSpan.textContent = `${Math.round(x)}, ${Math.round(y)}`;
                    }
                }

                // Update the sticker element data
                const stickerEl = document.querySelector(`.sticker-draggable[data-sticker-id="${stickerId}"]`);
                if (stickerEl) {
                    stickerEl.dataset.isHorizontal = isHorizontal ? '1' : '0';
                }

                showNotification('Posición guardada', 'success');
            } else {
                showNotification('Error al guardar', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al guardar', 'danger');
        });
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} position-fixed`;
        notification.style.cssText = 'top: 70px; right: 20px; z-index: 9999; min-width: 200px;';
        notification.innerHTML = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 2000);
    }
});
</script>
@endpush
