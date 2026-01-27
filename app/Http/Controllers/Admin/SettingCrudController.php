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
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Setting::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/setting');
        CRUD::setEntityNameStrings('configuración', 'configuraciones');

        CRUD::denyAccess(['create', 'delete']);
    }

    protected function setupListOperation()
    {
        CRUD::column('name')
            ->label('Nombre')
            ->type('closure')
            ->function(fn ($entry) => $entry->name ?? $entry->key);

        CRUD::column('key')
            ->label('Clave')
            ->type('closure')
            ->function(fn ($entry) => '<code>'.$entry->key.'</code>');

        CRUD::column('value')
            ->label('Valor')
            ->type('closure')
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

        CRUD::field('info')
            ->type('custom_html')
            ->value(function () {
                $entry = $this->crud->getCurrentEntry();
                $name = $entry->name ?? $entry->key;
                $description = $entry->description ?? '';

                return "
                    <div class='alert alert-info'>
                        <strong>{$name}</strong>
                        <p class='mb-0'>{$description}</p>
                    </div>
                ";
            });

        CRUD::field('value')
            ->label('Valor')
            ->type(function () {
                $entry = $this->crud->getCurrentEntry();

                return match ($entry->key) {
                    'shiny_probability' => 'number',
                    'packs_per_day', 'stickers_per_pack' => 'number',
                    default => 'text',
                };
            })
            ->attributes(function () {
                $entry = $this->crud->getCurrentEntry();

                return match ($entry->key) {
                    'shiny_probability' => ['step' => '0.01', 'min' => '0', 'max' => '1'],
                    'packs_per_day' => ['min' => '1', 'max' => '100'],
                    'stickers_per_pack' => ['min' => '1', 'max' => '20'],
                    default => [],
                };
            })
            ->hint(function () {
                $entry = $this->crud->getCurrentEntry();

                return match ($entry->key) {
                    'shiny_probability' => 'Valor entre 0 y 1 (ej: 0.05 = 5%)',
                    'packs_per_day' => 'Cantidad de sobres que recibe cada usuario por día',
                    'stickers_per_pack' => 'Cantidad de cromos en cada sobre',
                    default => '',
                };
            });
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
