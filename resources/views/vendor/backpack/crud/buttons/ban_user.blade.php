@if ($entry->is_banned)
    <a href="javascript:void(0)" onclick="unbanUser({{ $entry->id }})" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Desbanear usuario">
        <i class="la la-check"></i> Desbanear
    </a>
@else
    <a href="javascript:void(0)" onclick="banUser({{ $entry->id }})" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Banear usuario">
        <i class="la la-ban"></i> Banear
    </a>
@endif

@push('after_scripts')
@once
<script>
function banUser(userId) {
    Swal.fire({
        title: 'Banear usuario',
        input: 'text',
        inputLabel: 'Razón del baneo (opcional)',
        inputPlaceholder: 'Ingresa la razón...',
        showCancelButton: true,
        confirmButtonText: 'Banear',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#dc3545',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url(config('backpack.base.route_prefix')) }}/user/${userId}/ban`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ reason: result.value })
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

function unbanUser(userId) {
    Swal.fire({
        title: '¿Desbanear usuario?',
        text: 'El usuario podrá acceder nuevamente al sistema',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, desbanear',
        cancelButtonText: 'Cancelar',
        confirmButtonColor: '#28a745',
    }).then((result) => {
        if (result.isConfirmed) {
            fetch(`{{ url(config('backpack.base.route_prefix')) }}/user/${userId}/ban`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
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
