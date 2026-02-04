<a href="javascript:void(0)" class="btn btn-sm btn-info" data-bs-toggle="tooltip" title="Dar sobres al usuario"
   onclick="openGivePacksModal({{ $entry->id }}, '{{ addslashes($entry->name) }}')">
    <i class="la la-gift"></i> Dar sobres
</a>

<script>
(function() {
    // Only initialize once
    if (window.givePacksInitialized) return;
    window.givePacksInitialized = true;

    // Create modal HTML
    var modalHtml = `
    <div class="modal fade" id="givePacksModal" tabindex="-1" aria-labelledby="givePacksModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="givePacksModalLabel">
                        <i class="la la-gift text-info"></i> Dar sobres a <span id="givePacksUserName"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="givePacksUserId">
                    <div class="mb-3">
                        <label for="givePacksCountInput" class="form-label">Cantidad de sobres</label>
                        <input type="number" class="form-control" id="givePacksCountInput" min="1" max="100" value="1">
                        <div class="form-text">Entre 1 y 100 sobres</div>
                    </div>
                    <div class="mb-3">
                        <label for="givePacksMessageInput" class="form-label">Mensaje para el usuario (opcional)</label>
                        <textarea class="form-control" id="givePacksMessageInput" rows="3" placeholder="Ej: Premio por ser el usuario más activo"></textarea>
                        <div class="form-text">Este mensaje se mostrará en la notificación que reciba el usuario</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-info" onclick="executeGivePacks()">
                        <i class="la la-gift"></i> Enviar sobres
                    </button>
                </div>
            </div>
        </div>
    </div>`;

    // Append modal to body
    document.body.insertAdjacentHTML('beforeend', modalHtml);

    // Initialize modal variable
    window.givePacksModal = null;

    window.openGivePacksModal = function(userId, userName) {
        if (!window.givePacksModal) {
            window.givePacksModal = new bootstrap.Modal(document.getElementById('givePacksModal'));
        }
        // Set user data and reset form
        document.getElementById('givePacksUserId').value = userId;
        document.getElementById('givePacksUserName').textContent = userName;
        document.getElementById('givePacksCountInput').value = 1;
        document.getElementById('givePacksMessageInput').value = '';
        window.givePacksModal.show();
    };

    window.executeGivePacks = function() {
        var userId = document.getElementById('givePacksUserId').value;
        var count = parseInt(document.getElementById('givePacksCountInput').value) || 1;
        var message = document.getElementById('givePacksMessageInput').value.trim();

        if (count < 1 || count > 100) {
            swal('Error', 'La cantidad debe ser entre 1 y 100', 'error');
            return;
        }

        // Close the modal
        window.givePacksModal.hide();

        fetch('{{ url(config('backpack.base.route_prefix')) }}/user/' + userId + '/give-packs', {
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
                swal('Éxito', data.message, 'success').then(function() { location.reload(); });
            } else {
                swal('Error', data.message || 'Error desconocido', 'error');
            }
        })
        .catch(function() { swal('Error', 'Error de conexión', 'error'); });
    };
})();
</script>
