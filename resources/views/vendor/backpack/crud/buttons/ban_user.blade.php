@if ($entry->is_banned)
<a href="javascript:void(0)" class="btn btn-sm btn-success" data-bs-toggle="tooltip" title="Desbanear usuario"
   onclick="(function(userId){
        swal({
            title: '¿Desbanear usuario?',
            text: 'El usuario podrá acceder nuevamente al sistema',
            icon: 'warning',
            buttons: ['Cancelar', 'Sí, desbanear'],
        }).then(function(confirm) {
            if (confirm) {
                fetch('{{ url(config('backpack.base.route_prefix')) }}/user/' + userId + '/ban', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    }
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        swal('Éxito', data.message, 'success').then(function() { location.reload(); });
                    } else {
                        swal('Error', data.message || 'Error desconocido', 'error');
                    }
                })
                .catch(function() { swal('Error', 'Error de conexión', 'error'); });
            }
        });
    })({{ $entry->id }})">
    <i class="la la-check"></i> Desbanear
</a>
@else
<a href="javascript:void(0)" class="btn btn-sm btn-danger" data-bs-toggle="tooltip" title="Banear usuario"
   onclick="(function(userId){
        swal({
            title: 'Banear usuario',
            text: 'Ingresa la razón del baneo (opcional):',
            content: {
                element: 'input',
                attributes: {
                    placeholder: 'Razón del baneo...',
                    type: 'text',
                },
            },
            buttons: ['Cancelar', 'Banear'],
            dangerMode: true,
        }).then(function(reason) {
            if (reason !== null && reason !== false) {
                fetch('{{ url(config('backpack.base.route_prefix')) }}/user/' + userId + '/ban', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({ reason: reason || '' })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        swal('Éxito', data.message, 'success').then(function() { location.reload(); });
                    } else {
                        swal('Error', data.message || 'Error desconocido', 'error');
                    }
                })
                .catch(function() { swal('Error', 'Error de conexión', 'error'); });
            }
        });
    })({{ $entry->id }})">
    <i class="la la-ban"></i> Banear
</a>
@endif
