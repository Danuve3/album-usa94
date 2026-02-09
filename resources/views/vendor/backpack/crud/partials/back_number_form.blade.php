<div class="back-number-form" data-orientation="{{ $orientation }}">
    {{-- Preview --}}
    <div class="preview-container">
        <img src="{{ asset('images/stickers/' . $backImage) }}" alt="Reverso {{ $orientation }}">
        <span class="preview-number"
              style="left: {{ $config['position_x'] }}%; top: {{ $config['position_y'] }}%; font-size: {{ $config['font_size'] }}cqi; font-weight: {{ $config['font_weight'] }}; font-family: {{ $config['font_family'] }}; color: {{ $config['color'] }}; display: {{ $config['enabled'] ? 'block' : 'none' }};">
            123
        </span>
    </div>

    {{-- Enabled toggle --}}
    <div class="config-row">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="enabled" id="enabled-{{ $orientation }}" {{ $config['enabled'] ? 'checked' : '' }}>
            <label class="form-check-label" for="enabled-{{ $orientation }}">Mostrar numero en el reverso</label>
        </div>
    </div>

    {{-- Position X --}}
    <div class="config-row">
        <label>Posicion X (%)</label>
        <div class="range-with-input">
            <input type="range" name="position_x_range" min="0" max="100" step="1" value="{{ $config['position_x'] }}" class="form-range">
            <input type="number" name="position_x" min="0" max="100" step="1" value="{{ $config['position_x'] }}" class="form-control form-control-sm">
        </div>
    </div>

    {{-- Position Y --}}
    <div class="config-row">
        <label>Posicion Y (%)</label>
        <div class="range-with-input">
            <input type="range" name="position_y_range" min="0" max="100" step="1" value="{{ $config['position_y'] }}" class="form-range">
            <input type="number" name="position_y" min="0" max="100" step="1" value="{{ $config['position_y'] }}" class="form-control form-control-sm">
        </div>
    </div>

    {{-- Font Size --}}
    <div class="config-row">
        <label>Tamano de fuente (cqi)</label>
        <div class="range-with-input">
            <input type="range" name="font_size_range" min="1" max="50" step="1" value="{{ $config['font_size'] }}" class="form-range">
            <input type="number" name="font_size" min="1" max="50" step="1" value="{{ $config['font_size'] }}" class="form-control form-control-sm">
        </div>
    </div>

    {{-- Font Weight --}}
    <div class="config-row">
        <label>Grosor de fuente</label>
        <select name="font_weight" class="form-select form-select-sm">
            <option value="normal" {{ $config['font_weight'] === 'normal' ? 'selected' : '' }}>Normal</option>
            <option value="500" {{ $config['font_weight'] === '500' ? 'selected' : '' }}>500 (Medium)</option>
            <option value="600" {{ $config['font_weight'] === '600' ? 'selected' : '' }}>600 (Semi Bold)</option>
            <option value="bold" {{ $config['font_weight'] === 'bold' ? 'selected' : '' }}>Bold</option>
            <option value="800" {{ $config['font_weight'] === '800' ? 'selected' : '' }}>800 (Extra Bold)</option>
            <option value="900" {{ $config['font_weight'] === '900' ? 'selected' : '' }}>900 (Black)</option>
        </select>
    </div>

    {{-- Font Family --}}
    <div class="config-row">
        <label>Familia de fuente</label>
        <select name="font_family" class="form-select form-select-sm">
            <option value="Arial, sans-serif" {{ $config['font_family'] === 'Arial, sans-serif' ? 'selected' : '' }}>Arial</option>
            <option value="Helvetica, sans-serif" {{ $config['font_family'] === 'Helvetica, sans-serif' ? 'selected' : '' }}>Helvetica</option>
            <option value="Georgia, serif" {{ $config['font_family'] === 'Georgia, serif' ? 'selected' : '' }}>Georgia</option>
            <option value="'Times New Roman', serif" {{ $config['font_family'] === "'Times New Roman', serif" ? 'selected' : '' }}>Times New Roman</option>
            <option value="'Courier New', monospace" {{ $config['font_family'] === "'Courier New', monospace" ? 'selected' : '' }}>Courier New</option>
            <option value="Verdana, sans-serif" {{ $config['font_family'] === 'Verdana, sans-serif' ? 'selected' : '' }}>Verdana</option>
            <option value="Impact, sans-serif" {{ $config['font_family'] === 'Impact, sans-serif' ? 'selected' : '' }}>Impact</option>
            <option value="'Trebuchet MS', sans-serif" {{ $config['font_family'] === "'Trebuchet MS', sans-serif" ? 'selected' : '' }}>Trebuchet MS</option>
            <option value="system-ui, sans-serif" {{ $config['font_family'] === 'system-ui, sans-serif' ? 'selected' : '' }}>System UI</option>
        </select>
    </div>

    {{-- Color --}}
    <div class="config-row">
        <label>Color</label>
        <div class="d-flex align-items-center gap-2">
            <input type="color" name="color" value="{{ $config['color'] }}" class="form-control form-control-color" style="width: 50px; height: 35px;">
            <code>{{ $config['color'] }}</code>
        </div>
    </div>

    {{-- Save Button --}}
    <button type="button" class="btn btn-primary w-100 btn-save">
        <i class="la la-save"></i> Guardar
    </button>
</div>
