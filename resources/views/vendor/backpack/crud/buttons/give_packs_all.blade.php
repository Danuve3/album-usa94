<a href="javascript:void(0)" class="btn btn-warning" onclick="openGivePacksAllModal()">
    <i class="la la-gift"></i> Dar sobres a todos
</a>

<script>
(function() {
    // Only initialize once
    if (window.givePacksAllInitialized) return;
    window.givePacksAllInitialized = true;

    // Create modal HTML
    var modalHtml = `
    <div class="modal fade" id="givePacksAllModal" tabindex="-1" aria-labelledby="givePacksAllModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="givePacksAllModalLabel">
                        <i class="la la-gift text-warning"></i> Dar sobres a todos los usuarios
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="la la-info-circle"></i>
                        Se entregarán sobres a todos los usuarios activos (no baneados) y se les enviará una notificación.
                    </div>
                    <div class="mb-3">
                        <label for="packsCountInput" class="form-label">Cantidad de sobres por usuario</label>
                        <input type="number" class="form-control" id="packsCountInput" min="1" max="100" value="1">
                        <div class="form-text">Entre 1 y 100 sobres</div>
                    </div>
                    <div class="mb-3">
                        <label for="packsMessageInput" class="form-label">Mensaje para los usuarios (opcional)</label>
                        <textarea class="form-control" id="packsMessageInput" rows="3" placeholder="Ej: Regalo por el aniversario del album"></textarea>
                        <div class="form-text">Este mensaje se mostrará en la notificación que reciban los usuarios</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-warning" onclick="executeGivePacksAll()">
                        <i class="la la-gift"></i> Enviar sobres
                    </button>
                </div>
            </div>
        </div>
    </div>`;

    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Initialize modal variable
    window.givePacksAllModal = null;

    window.openGivePacksAllModal = function() {
        if (!window.givePacksAllModal) {
            window.givePacksAllModal = new bootstrap.Modal(document.getElementById('givePacksAllModal'));
        }
        // Reset form
        document.getElementById('packsCountInput').value = 1;
        document.getElementById('packsMessageInput').value = '';
        window.givePacksAllModal.show();
    };

    window.executeGivePacksAll = function() {
        var count = parseInt(document.getElementById('packsCountInput').value) || 1;
        var message = document.getElementById('packsMessageInput').value.trim();

        if (count < 1 || count > 100) {
            swal('Error', 'La cantidad debe ser entre 1 y 100', 'error');
            return;
        }

        swal({
            title: 'Confirmar entrega masiva',
            text: '¿Estás seguro de que quieres dar ' + count + ' sobre(s) a TODOS los usuarios activos?',
            icon: 'warning',
            buttons: ['Cancelar', 'Sí, enviar sobres'],
            dangerMode: true,
        }).then(function(confirmed) {
            if (confirmed) {
                // Close the modal
                window.givePacksAllModal.hide();

                // Show loading
                swal({
                    title: 'Procesando...',
                    text: 'Enviando sobres a todos los usuarios',
                    icon: 'info',
                    buttons: false,
                    closeOnClickOutside: false,
                    closeOnEsc: false,
                });

                fetch('{{ route("user.give-packs-all") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                    },
                    body: JSON.stringify({
                        count: count,
                        message: message || null
                    })
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.success) {
                        swal('Éxito', data.message, 'success').then(function() {
                            location.reload();
                        });
                    } else {
                        swal('Error', data.message || 'Error desconocido', 'error');
                    }
                })
                .catch(function() {
                    swal('Error', 'Error de conexión', 'error');
                });
            }
        });
    };
})();
</script>
