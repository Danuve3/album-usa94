@if ($crud->hasAccess('update') && $crud->get('list.bulkActions'))
<div class="dropdown d-inline">
    <button class="btn btn-outline-warning dropdown-toggle bulk-button" type="button" data-bs-toggle="dropdown" aria-expanded="false" disabled>
        <i class="la la-star"></i> Cambiar Rareza
    </button>
    <ul class="dropdown-menu">
        <li>
            <a class="dropdown-item" href="#" onclick="bulkSetRarity('shiny')">
                <i class="la la-star text-warning"></i> Marcar como Brillante
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="#" onclick="bulkSetRarity('common')">
                <i class="la la-circle text-secondary"></i> Marcar como Común
            </a>
        </li>
    </ul>
</div>

@push('after_scripts')
<script>
    function bulkSetRarity(rarity) {
        var checkedRows = crud.checkedItems;
        if (checkedRows.length === 0) {
            new Noty({
                type: 'warning',
                text: 'Por favor, selecciona al menos un cromo.'
            }).show();
            return;
        }

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ url(config('backpack.base.route_prefix').'/sticker/bulk-rarity') }}';
        form.style.display = 'none';

        var csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);

        var rarityInput = document.createElement('input');
        rarityInput.type = 'hidden';
        rarityInput.name = 'rarity';
        rarityInput.value = rarity;
        form.appendChild(rarityInput);

        checkedRows.forEach(function(id) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'entries[]';
            input.value = id;
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }
</script>
@endpush
@endif
