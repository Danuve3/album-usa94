<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\SettingRequest;
use App\Models\ConfigurationLog;
use App\Models\Setting;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class SettingCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation { update as traitUpdate; }

    public function setup()
    {
        CRUD::setModel(Setting::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/setting');
        CRUD::setEntityNameStrings('configuración', 'configuraciones');

        CRUD::denyAccess(['create', 'delete']);
    }

    protected function setupListOperation()
    {
        // Hide deprecated settings
        CRUD::addClause('where', 'key', '!=', 'packs_per_day');

        CRUD::column('name')
            ->label('Nombre')
            ->type('closure')
            ->function(fn ($entry) => $entry->name ?? $entry->key);

        CRUD::column('key')
            ->label('Clave')
            ->type('closure')
            ->escaped(false)
            ->function(fn ($entry) => '<code>'.$entry->key.'</code>');

        CRUD::column('value')
            ->label('Valor')
            ->type('closure')
            ->escaped(false)
            ->function(function ($entry) {
                $value = $entry->value;
                if ($entry->type === 'float' && $entry->key === 'shiny_probability') {
                    return '<span class="badge bg-warning text-dark">'.($value * 100).'%</span>';
                }
                if ($entry->type === 'integer') {
                    return '<span class="badge bg-primary">'.$value.'</span>';
                }

                return $value;
            });

        CRUD::column('description')
            ->label('Descripción');

        CRUD::column('updated_at')
            ->label('Última modificación')
            ->type('datetime');

        CRUD::orderBy('group');
        CRUD::orderBy('key');
    }

    protected function setupShowOperation()
    {
        CRUD::column('name')
            ->label('Nombre')
            ->type('closure')
            ->function(fn ($entry) => $entry->name ?? $entry->key);

        CRUD::column('key')
            ->label('Clave');

        CRUD::column('value')
            ->label('Valor actual')
            ->type('closure')
            ->function(function ($entry) {
                $value = $entry->value;
                if ($entry->type === 'float' && $entry->key === 'shiny_probability') {
                    return $value.' ('.($value * 100).'%)';
                }

                return $value;
            });

        CRUD::column('type')
            ->label('Tipo');

        CRUD::column('description')
            ->label('Descripción');

        CRUD::column('configuration_logs')
            ->label('Historial de cambios')
            ->type('closure')
            ->function(function ($entry) {
                $logs = $entry->configurationLogs()->with('user')->orderBy('created_at', 'desc')->limit(20)->get();

                if ($logs->isEmpty()) {
                    return '<p class="text-muted">No hay cambios registrados</p>';
                }

                $html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                $html .= '<thead><tr><th>Fecha</th><th>Usuario</th><th>Valor anterior</th><th>Nuevo valor</th></tr></thead>';
                $html .= '<tbody>';

                foreach ($logs as $log) {
                    $date = $log->created_at->format('d/m/Y H:i:s');
                    $userName = $log->user?->name ?? '<span class="text-muted">Sistema</span>';
                    $oldValue = $log->old_value ?? '<span class="text-muted">-</span>';
                    $newValue = $log->new_value;

                    $html .= '<tr>';
                    $html .= "<td>{$date}</td>";
                    $html .= "<td>{$userName}</td>";
                    $html .= "<td><code>{$oldValue}</code></td>";
                    $html .= "<td><code>{$newValue}</code></td>";
                    $html .= '</tr>';
                }

                $html .= '</tbody></table></div>';

                return $html;
            });

        CRUD::column('updated_at')
            ->label('Última modificación');
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(SettingRequest::class);

        $entry = $this->crud->getCurrentEntry();
        $name = $entry->name ?? $entry->key;
        $description = $entry->description ?? '';

        CRUD::field('info')
            ->type('custom_html')
            ->value("
                <div class='alert alert-info'>
                    <strong>{$name}</strong>
                    <p class='mb-0'>{$description}</p>
                </div>
            ");

        $fieldType = match ($entry->key) {
            'shiny_probability' => 'number',
            'stickers_per_pack', 'pack_delivery_interval_minutes', 'packs_per_delivery' => 'number',
            default => 'text',
        };

        $fieldAttributes = match ($entry->key) {
            'shiny_probability' => ['step' => '0.01', 'min' => '0', 'max' => '1'],
            'stickers_per_pack' => ['min' => '1', 'max' => '20'],
            'pack_delivery_interval_minutes' => ['min' => '1', 'max' => '1440'],
            'packs_per_delivery' => ['min' => '1', 'max' => '10'],
            default => [],
        };

        $fieldHint = match ($entry->key) {
            'shiny_probability' => 'Valor entre 0 y 1 (ej: 0.05 = 5%)',
            'stickers_per_pack' => 'Cantidad de cromos en cada sobre',
            'pack_delivery_interval_minutes' => 'Minutos entre entregas (1-1440). Ejemplos: 1=cada minuto, 30=media hora, 60=1 hora, 240=4 horas',
            'packs_per_delivery' => 'Cantidad de sobres por cada entrega (1-10)',
            default => '',
        };

        CRUD::field('value')
            ->label('Valor')
            ->type($fieldType)
            ->attributes($fieldAttributes)
            ->hint($fieldHint);
    }

    public function update()
    {
        $entry = $this->crud->getCurrentEntry();
        $oldValue = $entry->value;
        $newValue = request('value');

        $response = $this->traitUpdate();

        if ($oldValue !== $newValue) {
            ConfigurationLog::logChange(
                $entry->fresh(),
                $oldValue,
                $newValue,
                backpack_user()
            );

            Setting::clearCache();
        }

        return $response;
    }
}
