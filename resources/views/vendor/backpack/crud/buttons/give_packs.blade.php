<a href="javascript:void(0)" onclick="givePacks({{ $entry->id }})" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Dar sobres al usuario">
    <i class="la la-gift"></i> Dar sobres
</a>

@push('after_scripts')
@once
<script>
function givePacks(userId) {
    Swal.fire({
        title: 'Dar sobres al usuario',
        input: 'number',
        inputLabel: 'Cantidad de sobres',
        inputValue: 1,
        inputAttributes: {
            min: 1,
            max: 100,
            step: 1
        },
        showCancelButton: true,
        confirmButtonText: 'Dar sobres',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#17a2b8',
        inputValidator: (value) => {
            if (!value || value < 1 || value > 100) {
                return 'Ingresa un número entre 1 y 100';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url(config('backpack.base.route_prefix')) }}/user/${userId}/give-packs`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ count: parseInt(result.value) })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Éxito', data.message, 'success').then(() => {
                        window.location.reload();
                    });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch(() => {
                Swal.fire('Error', 'Ocurrió un error al procesar la solicitud', 'error');
            });
        }
    });
}
</script>
@endonce
@endpush
