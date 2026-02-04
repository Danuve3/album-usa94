@extends(backpack_view('blank'))

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">Asignar Cromos</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Gestiona la asignación de cromos a cada página</p>
    </section>
@endsection

@section('content')
    <div class="row">
        {{-- Left panel: Page selector and assigned stickers --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="la la-file-image"></i> Cromos de la Página
                    </h5>
                    <a href="{{ backpack_url('sticker-mapper') }}{{ $selectedPageNumber ? '?page=' . $selectedPageNumber : '' }}"
                       class="btn btn-sm btn-outline-primary">
                        <i class="la la-map-marked-alt"></i> Ir al Mapeador
                    </a>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ backpack_url('sticker-assigner') }}" class="mb-3">
                        <div class="input-group">
                            <select name="page" class="form-control" id="page-selector">
                                <option value="">-- Seleccionar página --</option>
                                @foreach($pages as $page)
                                    <option value="{{ $page->number }}" {{ $selectedPageNumber == $page->number ? 'selected' : '' }}>
                                        Página {{ $page->number }} ({{ $page->stickers()->count() }} cromos)
                                    </option>
                                @endforeach
                            </select>
                            <button type="submit" class="btn btn-primary">
                                <i class="la la-search"></i>
                            </button>
                        </div>
                    </form>

                    {{-- Page preview --}}
                    @if($selectedPage && $selectedPage->image_path)
                        <div class="page-preview mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <small class="text-muted">
                                    <i class="la la-image"></i> Preview de Página {{ $selectedPage->number }}
                                </small>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="toggle-preview">
                                    <i class="la la-eye"></i>
                                </button>
                            </div>
                            <div class="preview-container" id="preview-container">
                                <img src="{{ asset('storage/' . $selectedPage->image_path) }}"
                                     alt="Página {{ $selectedPage->number }}"
                                     class="img-fluid rounded border"
                                     style="max-height: 300px; width: 100%; object-fit: contain; background: #f5f5f5;">
                            </div>
                        </div>
                    @elseif($selectedPage && !$selectedPage->image_path)
                        <div class="alert alert-warning py-2 mb-3">
                            <small><i class="la la-exclamation-triangle"></i> Esta página no tiene imagen asignada</small>
                        </div>
                    @endif

                    @if($selectedPageNumber)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <input type="checkbox" id="select-all-assigned" class="form-check-input">
                                <label for="select-all-assigned" class="form-check-label">Seleccionar todos</label>
                            </div>
                            <button type="button" class="btn btn-sm btn-warning" id="unassign-btn" disabled>
                                <i class="la la-arrow-right"></i> Desasignar seleccionados
                            </button>
                        </div>

                        <div class="sticker-list assigned-list" style="max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                            @forelse($assignedStickers as $sticker)
                                <div class="sticker-item d-flex align-items-center p-2 border-bottom" data-sticker-id="{{ $sticker->id }}">
                                    <input type="checkbox" class="form-check-input me-2 sticker-checkbox assigned-checkbox" value="{{ $sticker->id }}">
                                    <div class="flex-grow-1">
                                        <strong>#{{ $sticker->number }}</strong>
                                        <small class="text-muted d-block">{{ $sticker->name }}</small>
                                    </div>
                                    @if($sticker->position_x > 0 || $sticker->position_y > 0)
                                        <span class="badge bg-success" title="Posicionado">
                                            <i class="la la-check"></i>
                                        </span>
                                    @else
                                        <span class="badge bg-warning" title="Sin posicionar">
                                            <i class="la la-exclamation"></i>
                                        </span>
                                    @endif
                                </div>
                            @empty
                                <div class="p-4 text-center text-muted">
                                    <i class="la la-inbox" style="font-size: 2rem;"></i>
                                    <p class="mb-0 mt-2">No hay cromos asignados a esta página</p>
                                </div>
                            @endforelse
                        </div>

                        <div class="mt-2 text-muted small">
                            <strong>{{ $assignedStickers->count() }}</strong> cromo(s) asignados
                            @php
                                $positionedCount = $assignedStickers->filter(fn($s) => $s->position_x > 0 || $s->position_y > 0)->count();
                            @endphp
                            @if($assignedStickers->count() > 0)
                                | <span class="text-success">{{ $positionedCount }} posicionados</span>
                                | <span class="text-warning">{{ $assignedStickers->count() - $positionedCount }} sin posicionar</span>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="la la-hand-pointer" style="font-size: 3rem;"></i>
                            <p class="mt-2">Selecciona una página para ver sus cromos asignados</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Right panel: Unassigned stickers --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="la la-images"></i> Cromos sin Asignar
                        <span class="badge bg-secondary">{{ $unassignedStickers->count() }}</span>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <input type="text" class="form-control" id="search-unassigned" placeholder="Buscar por número o nombre...">
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <input type="checkbox" id="select-all-unassigned" class="form-check-input">
                            <label for="select-all-unassigned" class="form-check-label">Seleccionar todos</label>
                        </div>
                        <button type="button" class="btn btn-sm btn-success" id="assign-btn" disabled {{ !$selectedPageNumber ? 'title=Selecciona una página primero' : '' }}>
                            <i class="la la-arrow-left"></i> Asignar seleccionados
                        </button>
                    </div>

                    <div class="sticker-list unassigned-list" style="max-height: 500px; overflow-y: auto; border: 1px solid #dee2e6; border-radius: 4px;">
                        @forelse($unassignedStickers as $sticker)
                            <div class="sticker-item d-flex align-items-center p-2 border-bottom" data-sticker-id="{{ $sticker->id }}" data-search="{{ strtolower($sticker->number . ' ' . $sticker->name) }}">
                                <input type="checkbox" class="form-check-input me-2 sticker-checkbox unassigned-checkbox" value="{{ $sticker->id }}">
                                <div class="flex-grow-1">
                                    <strong>#{{ $sticker->number }}</strong>
                                    <small class="text-muted d-block">{{ $sticker->name }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 text-center text-muted" id="no-unassigned-message">
                                <i class="la la-check-circle" style="font-size: 2rem; color: #28a745;"></i>
                                <p class="mb-0 mt-2">Todos los cromos están asignados</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-2 text-muted small">
                        <span id="unassigned-count"><strong>{{ $unassignedStickers->count() }}</strong></span> cromo(s) sin asignar
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_styles')
<style>
    .sticker-item {
        transition: background-color 0.15s;
    }
    .sticker-item:hover {
        background-color: #f8f9fa;
    }
    .sticker-item.selected {
        background-color: #e8f4fd;
    }
    .sticker-list {
        background: #fff;
    }
    .sticker-item.hidden {
        display: none !important;
    }
    .page-preview .preview-container {
        transition: max-height 0.3s ease, opacity 0.3s ease;
        overflow: hidden;
    }
    .page-preview .preview-container.collapsed {
        max-height: 0 !important;
        opacity: 0;
    }
    .page-preview img {
        cursor: pointer;
    }
</style>
@endpush

@push('after_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const pageSelector = document.getElementById('page-selector');
    const selectedPageNumber = {{ $selectedPageNumber ?? 'null' }};

    // Auto-submit on page change
    pageSelector?.addEventListener('change', function() {
        if (this.value) {
            this.form.submit();
        }
    });

    // Toggle preview visibility
    const togglePreviewBtn = document.getElementById('toggle-preview');
    const previewContainer = document.getElementById('preview-container');
    togglePreviewBtn?.addEventListener('click', function() {
        previewContainer.classList.toggle('collapsed');
        const icon = this.querySelector('i');
        if (previewContainer.classList.contains('collapsed')) {
            icon.className = 'la la-eye-slash';
        } else {
            icon.className = 'la la-eye';
        }
    });

    // Click on preview image to open in modal/new tab
    previewContainer?.querySelector('img')?.addEventListener('click', function() {
        window.open(this.src, '_blank');
    });

    // Search unassigned stickers
    const searchInput = document.getElementById('search-unassigned');
    searchInput?.addEventListener('input', function() {
        const query = this.value.toLowerCase();
        document.querySelectorAll('.unassigned-list .sticker-item').forEach(item => {
            const searchText = item.dataset.search || '';
            if (searchText.includes(query)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    });

    // Select all assigned
    const selectAllAssigned = document.getElementById('select-all-assigned');
    selectAllAssigned?.addEventListener('change', function() {
        document.querySelectorAll('.assigned-checkbox').forEach(cb => {
            cb.checked = this.checked;
        });
        updateButtons();
    });

    // Select all unassigned
    const selectAllUnassigned = document.getElementById('select-all-unassigned');
    selectAllUnassigned?.addEventListener('change', function() {
        document.querySelectorAll('.unassigned-checkbox:not(.hidden)').forEach(cb => {
            cb.checked = this.checked;
        });
        updateButtons();
    });

    // Update buttons on checkbox change
    document.querySelectorAll('.sticker-checkbox').forEach(cb => {
        cb.addEventListener('change', updateButtons);
    });

    function updateButtons() {
        const assignedChecked = document.querySelectorAll('.assigned-checkbox:checked').length;
        const unassignedChecked = document.querySelectorAll('.unassigned-checkbox:checked').length;

        const unassignBtn = document.getElementById('unassign-btn');
        const assignBtn = document.getElementById('assign-btn');

        if (unassignBtn) {
            unassignBtn.disabled = assignedChecked === 0;
        }
        if (assignBtn) {
            assignBtn.disabled = unassignedChecked === 0 || !selectedPageNumber;
        }
    }

    // Assign stickers
    document.getElementById('assign-btn')?.addEventListener('click', function() {
        const stickerIds = Array.from(document.querySelectorAll('.unassigned-checkbox:checked')).map(cb => cb.value);

        if (stickerIds.length === 0 || !selectedPageNumber) return;

        this.disabled = true;
        this.innerHTML = '<i class="la la-spinner la-spin"></i> Asignando...';

        fetch('{{ backpack_url("sticker-assigner/assign") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sticker_ids: stickerIds,
                page_number: selectedPageNumber
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification('Error al asignar cromos', 'danger');
                this.disabled = false;
                this.innerHTML = '<i class="la la-arrow-left"></i> Asignar seleccionados';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al asignar cromos', 'danger');
            this.disabled = false;
            this.innerHTML = '<i class="la la-arrow-left"></i> Asignar seleccionados';
        });
    });

    // Unassign stickers
    document.getElementById('unassign-btn')?.addEventListener('click', function() {
        const stickerIds = Array.from(document.querySelectorAll('.assigned-checkbox:checked')).map(cb => cb.value);

        if (stickerIds.length === 0) return;

        this.disabled = true;
        this.innerHTML = '<i class="la la-spinner la-spin"></i> Desasignando...';

        fetch('{{ backpack_url("sticker-assigner/unassign") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                sticker_ids: stickerIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => location.reload(), 500);
            } else {
                showNotification('Error al desasignar cromos', 'danger');
                this.disabled = false;
                this.innerHTML = '<i class="la la-arrow-right"></i> Desasignar seleccionados';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error al desasignar cromos', 'danger');
            this.disabled = false;
            this.innerHTML = '<i class="la la-arrow-right"></i> Desasignar seleccionados';
        });
    });

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} position-fixed`;
        notification.style.cssText = 'top: 70px; right: 20px; z-index: 9999; min-width: 200px;';
        notification.innerHTML = message;
        document.body.appendChild(notification);
        setTimeout(() => notification.remove(), 3000);
    }
});
</script>
@endpush
