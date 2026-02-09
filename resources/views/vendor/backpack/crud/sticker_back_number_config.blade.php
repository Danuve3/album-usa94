@extends(backpack_view('blank'))

@section('header')
    <section class="header-operation container-fluid animated fadeIn d-flex mb-2 align-items-baseline d-print-none" bp-section="page-header">
        <h1 class="text-capitalize mb-0" bp-section="page-heading">Configurar Numero en Reverso</h1>
        <p class="ms-2 ml-2 mb-0" bp-section="page-subheading">Configura la posicion y estilo del numero en el reverso de los cromos</p>
        <a href="{{ backpack_url('sticker-mapper') }}" class="btn btn-sm btn-outline-secondary ms-auto">
            <i class="la la-arrow-left"></i> Volver al Mapper
        </a>
    </section>
@endsection

@section('content')
    <div class="row">
        {{-- Vertical Config --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cromo Vertical</h5>
                </div>
                <div class="card-body">
                    @include('vendor.backpack.crud.partials.back_number_form', [
                        'orientation' => 'vertical',
                        'config' => $vertical,
                        'backImage' => 'sticker_back.webp',
                    ])
                </div>
            </div>
        </div>

        {{-- Horizontal Config --}}
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Cromo Horizontal</h5>
                </div>
                <div class="card-body">
                    @include('vendor.backpack.crud.partials.back_number_form', [
                        'orientation' => 'horizontal',
                        'config' => $horizontal,
                        'backImage' => 'sticker_back_horizontal.webp',
                    ])
                </div>
            </div>
        </div>
    </div>
@endsection

@push('after_scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.back-number-form').forEach(form => {
        const orientation = form.dataset.orientation;
        const preview = form.querySelector('.preview-number');
        const previewContainer = form.querySelector('.preview-container');

        const inputs = {
            enabled: form.querySelector(`[name="enabled"]`),
            positionX: form.querySelector(`[name="position_x"]`),
            positionXRange: form.querySelector(`[name="position_x_range"]`),
            positionY: form.querySelector(`[name="position_y"]`),
            positionYRange: form.querySelector(`[name="position_y_range"]`),
            fontSize: form.querySelector(`[name="font_size"]`),
            fontSizeRange: form.querySelector(`[name="font_size_range"]`),
            fontWeight: form.querySelector(`[name="font_weight"]`),
            fontFamily: form.querySelector(`[name="font_family"]`),
            color: form.querySelector(`[name="color"]`),
        };

        function syncRange(input, range) {
            input.addEventListener('input', () => { range.value = input.value; updatePreview(); });
            range.addEventListener('input', () => { input.value = range.value; updatePreview(); });
        }

        syncRange(inputs.positionX, inputs.positionXRange);
        syncRange(inputs.positionY, inputs.positionYRange);
        syncRange(inputs.fontSize, inputs.fontSizeRange);

        inputs.fontWeight.addEventListener('change', updatePreview);
        inputs.fontFamily.addEventListener('change', updatePreview);
        inputs.color.addEventListener('input', updatePreview);
        inputs.enabled.addEventListener('change', updatePreview);

        function updatePreview() {
            const enabled = inputs.enabled.checked;
            preview.style.display = enabled ? 'block' : 'none';

            if (enabled) {
                preview.style.left = inputs.positionX.value + '%';
                preview.style.top = inputs.positionY.value + '%';
                preview.style.fontSize = inputs.fontSize.value + 'cqi';
                preview.style.fontWeight = inputs.fontWeight.value;
                preview.style.fontFamily = inputs.fontFamily.value;
                preview.style.color = inputs.color.value;
            }
        }

        updatePreview();

        form.querySelector('.btn-save').addEventListener('click', function() {
            const btn = this;
            btn.disabled = true;
            btn.innerHTML = '<i class="la la-spinner la-spin"></i> Guardando...';

            fetch('{{ backpack_url("sticker-mapper/back-number-config") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    orientation: orientation,
                    enabled: inputs.enabled.checked,
                    position_x: parseFloat(inputs.positionX.value),
                    position_y: parseFloat(inputs.positionY.value),
                    font_size: parseFloat(inputs.fontSize.value),
                    font_weight: inputs.fontWeight.value,
                    font_family: inputs.fontFamily.value,
                    color: inputs.color.value,
                }),
            })
            .then(r => r.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    btn.innerHTML = '<i class="la la-check"></i> Guardado';
                    setTimeout(() => { btn.innerHTML = '<i class="la la-save"></i> Guardar'; }, 2000);
                } else {
                    btn.innerHTML = '<i class="la la-times"></i> Error';
                    setTimeout(() => { btn.innerHTML = '<i class="la la-save"></i> Guardar'; }, 2000);
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = '<i class="la la-times"></i> Error';
                setTimeout(() => { btn.innerHTML = '<i class="la la-save"></i> Guardar'; }, 2000);
            });
        });
    });
});
</script>
@endpush

@push('after_styles')
<style>
    .preview-container {
        container-type: inline-size;
        position: relative;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 1rem;
    }
    .preview-container img {
        display: block;
        width: 100%;
        height: auto;
    }
    .preview-number {
        position: absolute;
        transform: translate(-50%, -50%);
        pointer-events: none;
        white-space: nowrap;
        line-height: 1;
    }
    .config-row {
        margin-bottom: 0.75rem;
    }
    .config-row label {
        font-weight: 600;
        font-size: 0.85rem;
        margin-bottom: 0.25rem;
    }
    .range-with-input {
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
    .range-with-input input[type="range"] {
        flex: 1;
    }
    .range-with-input input[type="number"] {
        width: 70px;
    }
</style>
@endpush
