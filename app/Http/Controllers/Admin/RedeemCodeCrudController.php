<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\RedeemCodeRequest;
use App\Models\RedeemCode;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class RedeemCodeCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;

    public function setup()
    {
        CRUD::setModel(RedeemCode::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/redeem-code');
        CRUD::setEntityNameStrings('código de canjeo', 'códigos de canjeo');
    }

    protected function setupListOperation()
    {
        CRUD::column('code')
            ->label('Código')
            ->type('closure')
            ->escaped(false)
            ->function(fn ($entry) => '<code class="fw-bold">'.$entry->code.'</code>');

        CRUD::column('packs_count')
            ->label('Sobres')
            ->type('closure')
            ->escaped(false)
            ->function(fn ($entry) => '<span class="badge bg-primary">'.$entry->packs_count.'</span>');

        CRUD::column('redemptions')
            ->label('Canjes')
            ->type('closure')
            ->escaped(false)
            ->function(function ($entry) {
                $max = $entry->max_redemptions ?? '∞';

                return "<span class=\"badge bg-info\">{$entry->times_redeemed} / {$max}</span>";
            });

        CRUD::column('expires_at')
            ->label('Expiración')
            ->type('closure')
            ->function(function ($entry) {
                if (! $entry->expires_at) {
                    return 'Sin expiración';
                }

                $expired = $entry->expires_at->isPast();

                return $entry->expires_at->format('d/m/Y H:i').($expired ? ' (expirado)' : '');
            });

        CRUD::column('user_id')
            ->label('Usuario asignado')
            ->type('closure')
            ->function(fn ($entry) => $entry->user?->name ?? 'Cualquiera');

        CRUD::column('is_active')
            ->label('Activo')
            ->type('boolean');

        CRUD::orderBy('created_at', 'desc');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(RedeemCodeRequest::class);

        CRUD::field('code')
            ->label('Código')
            ->type('text')
            ->hint('El código que los usuarios introducirán para canjear');

        CRUD::field('packs_count')
            ->label('Número de sobres')
            ->type('number')
            ->attributes(['min' => 1])
            ->default(1);

        CRUD::field('max_redemptions')
            ->label('Máximo de canjes')
            ->type('number')
            ->attributes(['min' => 1])
            ->hint('Dejar vacío = ilimitado');

        CRUD::field('expires_at')
            ->label('Fecha de expiración')
            ->type('datetime')
            ->hint('Dejar vacío = sin expiración');

        CRUD::field('user_id')
            ->label('Usuario asignado')
            ->type('select')
            ->entity('user')
            ->model('App\Models\User')
            ->attribute('name')
            ->hint('Dejar vacío = cualquier usuario puede canjearlo');

        CRUD::field('is_active')
            ->label('Activo')
            ->type('checkbox')
            ->default(true);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        CRUD::column('code')
            ->label('Código');

        CRUD::column('packs_count')
            ->label('Sobres por canjeo');

        CRUD::column('max_redemptions')
            ->label('Máximo de canjes')
            ->type('closure')
            ->function(fn ($entry) => $entry->max_redemptions ?? 'Ilimitado');

        CRUD::column('times_redeemed')
            ->label('Veces canjeado');

        CRUD::column('expires_at')
            ->label('Expiración')
            ->type('closure')
            ->function(fn ($entry) => $entry->expires_at?->format('d/m/Y H:i') ?? 'Sin expiración');

        CRUD::column('user_id')
            ->label('Usuario asignado')
            ->type('closure')
            ->function(fn ($entry) => $entry->user?->name ?? 'Cualquiera');

        CRUD::column('is_active')
            ->label('Activo')
            ->type('boolean');

        CRUD::column('usages')
            ->label('Historial de canjes')
            ->type('closure')
            ->escaped(false)
            ->function(function ($entry) {
                $usages = $entry->usages()->with('user')->orderBy('created_at', 'desc')->get();

                if ($usages->isEmpty()) {
                    return '<p class="text-muted">Nadie ha canjeado este código todavía</p>';
                }

                $html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                $html .= '<thead><tr><th>Usuario</th><th>Sobres</th><th>Fecha</th></tr></thead>';
                $html .= '<tbody>';

                foreach ($usages as $usage) {
                    $date = $usage->created_at->format('d/m/Y H:i:s');
                    $userName = $usage->user?->name ?? '<span class="text-muted">Eliminado</span>';

                    $html .= '<tr>';
                    $html .= "<td>{$userName}</td>";
                    $html .= "<td>{$usage->packs_given}</td>";
                    $html .= "<td>{$date}</td>";
                    $html .= '</tr>';
                }

                $html .= '</tbody></table></div>';

                return $html;
            });

        CRUD::column('created_at')
            ->label('Creado');

        CRUD::column('updated_at')
            ->label('Última modificación');
    }
}
