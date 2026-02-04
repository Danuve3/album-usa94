@extends(backpack_view('blank'))

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">{{ $title }}</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">
            <a href="{{ url(config('backpack.base.route_prefix').'/sticker') }}">
                <i class="la la-arrow-left"></i> Volver a Cromos
            </a>
        </p>
    </section>
@endsection

@section('content')
<div class="row">
    {{-- Statistics Card --}}
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="la la-chart-pie"></i> Estadísticas de Rareza</h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h2 class="mb-0 text-primary">{{ $totalStickers }}</h2>
                            <small class="text-muted">Total de Cromos</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-warning bg-opacity-10">
                            <h2 class="mb-0 text-warning">{{ $shinyCount }}</h2>
                            <small class="text-muted">Cromos Brillantes</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3">
                            <h2 class="mb-0 text-secondary">{{ $totalStickers - $shinyCount }}</h2>
                            <small class="text-muted">Cromos Comunes</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="border rounded p-3 bg-info bg-opacity-10">
                            <h2 class="mb-0 text-info">{{ $probability }}%</h2>
                            <small class="text-muted">Probabilidad Shiny</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Style Settings Card --}}
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="la la-palette"></i> Configuración de Estilos Visuales</h5>
            </div>
            <div class="card-body">
                <p class="text-muted mb-4">
                    Activa o desactiva los estilos visuales de fondo de los cromos. Cuando están desactivados, los cromos mostrarán solo su imagen sin efectos de fondo.
                </p>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card {{ $normalStyleEnabled ? 'border-success' : 'border-secondary' }}">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="la la-square {{ $normalStyleEnabled ? 'text-success' : 'text-secondary' }}"></i>
                                        Estilo Cromos Normales
                                    </h6>
                                    <small class="text-muted">Fondo gris para cromos comunes</small>
                                </div>
                                <form action="{{ url(config('backpack.base.route_prefix').'/sticker/toggle-style-setting') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="setting" value="sticker_style_normal_enabled">
                                    <button type="submit" class="btn {{ $normalStyleEnabled ? 'btn-success' : 'btn-outline-secondary' }}">
                                        <i class="la {{ $normalStyleEnabled ? 'la-toggle-on' : 'la-toggle-off' }}"></i>
                                        {{ $normalStyleEnabled ? 'Activado' : 'Desactivado' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card {{ $shinyStyleEnabled ? 'border-warning' : 'border-secondary' }}">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <h6 class="mb-1">
                                        <i class="la la-star {{ $shinyStyleEnabled ? 'text-warning' : 'text-secondary' }}"></i>
                                        Estilo Cromos Brillantes
                                    </h6>
                                    <small class="text-muted">Efecto dorado animado para cromos shiny</small>
                                </div>
                                <form action="{{ url(config('backpack.base.route_prefix').'/sticker/toggle-style-setting') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="setting" value="sticker_style_shiny_enabled">
                                    <button type="submit" class="btn {{ $shinyStyleEnabled ? 'btn-warning' : 'btn-outline-secondary' }}">
                                        <i class="la {{ $shinyStyleEnabled ? 'la-toggle-on' : 'la-toggle-off' }}"></i>
                                        {{ $shinyStyleEnabled ? 'Activado' : 'Desactivado' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Preview Card --}}
    <div class="col-md-12 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0"><i class="la la-eye"></i> Preview de Estilos</h5>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-4 text-center">
                        <p class="text-muted mb-2">Cromo Normal</p>
                        <div class="border rounded p-4 bg-light d-inline-block">
                            <div class="{{ $normalStyleEnabled ? 'sticker-normal' : '' }}" style="width: 120px; height: 160px; {{ !$normalStyleEnabled ? 'background: transparent; border: 2px dashed #ccc;' : '' }} border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="la la-user" style="font-size: 48px; color: #64748b;"></i>
                            </div>
                        </div>
                        <p class="mt-2">
                            <span class="badge {{ $normalStyleEnabled ? 'bg-success' : 'bg-secondary' }}">
                                {{ $normalStyleEnabled ? 'Estilo activo' : 'Sin estilo' }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-4 text-center">
                        <p class="text-muted mb-2">Cromo Brillante <span class="badge bg-warning text-dark">SHINY</span></p>
                        <div class="border rounded p-4 bg-light d-inline-block">
                            <div class="{{ $shinyStyleEnabled ? 'sticker-shiny' : '' }}" style="width: 120px; height: 160px; {{ !$shinyStyleEnabled ? 'background: transparent; border: 2px dashed #f59e0b;' : '' }} border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                <i class="la la-user" style="font-size: 48px; color: #92400e;"></i>
                            </div>
                        </div>
                        <p class="mt-2">
                            <span class="badge {{ $shinyStyleEnabled ? 'bg-warning text-dark' : 'bg-secondary' }}">
                                {{ $shinyStyleEnabled ? 'Estilo activo' : 'Sin estilo' }}
                            </span>
                        </p>
                    </div>
                </div>
                <p class="text-center text-muted mt-3">
                    <small>Los estilos afectan cómo se muestran los cromos en el álbum, sobres y todas las vistas.</small>
                </p>
            </div>
        </div>
    </div>

    {{-- Current Shiny Stickers --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="la la-star"></i> Cromos Brillantes ({{ $shinyCount }})</h5>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                @if($shinyStickers->isEmpty())
                    <p class="text-muted text-center py-4">No hay cromos brillantes definidos.</p>
                @else
                    <div class="list-group list-group-flush">
                        @foreach($shinyStickers->sortBy('number') as $sticker)
                            <div class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    @if($sticker->image_path)
                                        <img src="{{ Storage::disk('public')->url($sticker->image_path) }}"
                                             alt="{{ $sticker->name }}"
                                             class="rounded me-3 sticker-shiny"
                                             style="width: 40px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="rounded me-3 sticker-shiny d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 50px;">
                                            <i class="la la-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>#{{ $sticker->number }}</strong> - {{ $sticker->name }}
                                        <br><small class="text-muted">Página {{ $sticker->page_number }}</small>
                                    </div>
                                </div>
                                <form action="{{ url(config('backpack.base.route_prefix').'/sticker/'.$sticker->id.'/toggle-shiny') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Quitar brillo">
                                        <i class="la la-times"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add New Shiny --}}
    <div class="col-md-6 mb-4">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="la la-plus-circle"></i> Añadir Cromos Brillantes</h5>
            </div>
            <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                @if($commonStickers->isEmpty())
                    <p class="text-muted text-center py-4">Todos los cromos son brillantes.</p>
                @else
                    <div class="mb-3">
                        <input type="text" class="form-control" id="searchCommon" placeholder="Buscar por nombre o número...">
                    </div>
                    <div class="list-group list-group-flush" id="commonList">
                        @foreach($commonStickers as $sticker)
                            <div class="list-group-item d-flex justify-content-between align-items-center common-item"
                                 data-search="{{ strtolower($sticker->number . ' ' . $sticker->name) }}">
                                <div class="d-flex align-items-center">
                                    @if($sticker->image_path)
                                        <img src="{{ Storage::disk('public')->url($sticker->image_path) }}"
                                             alt="{{ $sticker->name }}"
                                             class="rounded me-3"
                                             style="width: 40px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="rounded me-3 bg-light d-flex align-items-center justify-content-center"
                                             style="width: 40px; height: 50px;">
                                            <i class="la la-image text-muted"></i>
                                        </div>
                                    @endif
                                    <div>
                                        <strong>#{{ $sticker->number }}</strong> - {{ $sticker->name }}
                                        <br><small class="text-muted">Página {{ $sticker->page_number }}</small>
                                    </div>
                                </div>
                                <form action="{{ url(config('backpack.base.route_prefix').'/sticker/'.$sticker->id.'/toggle-shiny') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-warning" title="Hacer brillante">
                                        <i class="la la-star"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('after_styles')
<style>
    .sticker-normal {
        background: linear-gradient(135deg, #e2e8f0 0%, #cbd5e1 100%);
    }

    @keyframes shiny-shimmer {
        0% { background-position: -200% center; }
        100% { background-position: 200% center; }
    }

    @keyframes shiny-glow {
        0%, 100% {
            box-shadow: 0 0 10px rgba(234, 179, 8, 0.5),
                        0 0 20px rgba(234, 179, 8, 0.3),
                        0 0 30px rgba(234, 179, 8, 0.1),
                        inset 0 0 15px rgba(255, 255, 255, 0.2);
        }
        50% {
            box-shadow: 0 0 15px rgba(234, 179, 8, 0.7),
                        0 0 30px rgba(234, 179, 8, 0.5),
                        0 0 45px rgba(234, 179, 8, 0.3),
                        inset 0 0 20px rgba(255, 255, 255, 0.4);
        }
    }

    .sticker-shiny {
        background: linear-gradient(
            135deg,
            #fef3c7 0%,
            #fcd34d 15%,
            #fbbf24 30%,
            #f59e0b 45%,
            #fcd34d 60%,
            #fef3c7 75%,
            #fcd34d 90%,
            #f59e0b 100%
        );
        background-size: 200% 200%;
        animation: shiny-shimmer 3s ease-in-out infinite, shiny-glow 2s ease-in-out infinite;
        border: 2px solid #f59e0b;
        position: relative;
        overflow: hidden;
    }

    .sticker-shiny::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 30%,
            rgba(255, 255, 255, 0.4) 50%,
            transparent 70%
        );
        animation: shiny-shimmer 2s ease-in-out infinite;
        pointer-events: none;
    }
</style>
@endpush

@push('after_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchCommon');
    const commonItems = document.querySelectorAll('.common-item');

    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();

            commonItems.forEach(function(item) {
                const searchData = item.getAttribute('data-search');
                if (searchData.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endpush
