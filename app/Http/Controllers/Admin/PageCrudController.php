<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\PageRequest;
use App\Models\Page;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class PageCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ReorderOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Page::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/page');
        CRUD::setEntityNameStrings('página', 'páginas');
    }

    protected function setupListOperation()
    {
        CRUD::column('number')
            ->label('Número')
            ->type('number');

        CRUD::column('image_path')
            ->label('Imagen')
            ->type('image')
            ->disk('public')
            ->height('80px');

        CRUD::column('stickers_status')
            ->label('Cromos')
            ->type('closure')
            ->function(function ($entry) {
                $stickers = $entry->stickers;
                $total = $stickers->count();

                if ($total === 0) {
                    return '<span class="badge bg-secondary">0 cromos</span>';
                }

                $positioned = $stickers->filter(fn($s) => $s->position_x > 0 || $s->position_y > 0)->count();
                $unpositioned = $total - $positioned;

                $html = '<span class="badge bg-primary">' . $total . ' cromos</span>';

                if ($positioned > 0) {
                    $html .= ' <span class="badge bg-success" title="Posicionados">' . $positioned . '</span>';
                }
                if ($unpositioned > 0) {
                    $html .= ' <span class="badge bg-warning" title="Sin posicionar">' . $unpositioned . '</span>';
                }

                return $html;
            });

        CRUD::column('created_at')
            ->label('Creado')
            ->type('datetime');

        CRUD::orderBy('number', 'asc');

        // Add button to manage stickers
        CRUD::addButtonFromView('line', 'manage_stickers', 'manage_stickers', 'beginning');
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(PageRequest::class);

        CRUD::field('number')
            ->label('Número de página')
            ->type('number')
            ->attributes(['min' => 1])
            ->wrapper(['class' => 'form-group col-md-6']);

        CRUD::field('image_path')
            ->label('Imagen de la página')
            ->type('upload')
            ->withFiles([
                'disk' => 'public',
                'path' => 'pages',
            ]);
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function setupShowOperation()
    {
        CRUD::column('number')
            ->label('Número de página');

        CRUD::column('image_path')
            ->label('Imagen')
            ->type('image')
            ->disk('public')
            ->height('400px');

        CRUD::column('stickers_preview')
            ->label('Cromos en esta página')
            ->type('closure')
            ->function(function ($entry) {
                $stickers = $entry->stickers()->orderBy('number')->get();

                if ($stickers->isEmpty()) {
                    return '<p class="text-muted">No hay cromos en esta página</p>';
                }

                $html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                $html .= '<thead><tr><th>Nº</th><th>Nombre</th><th>Posición</th><th>Tamaño</th><th>Imagen</th></tr></thead>';
                $html .= '<tbody>';

                foreach ($stickers as $sticker) {
                    $imageUrl = $sticker->image_path ? \Storage::disk('public')->url($sticker->image_path) : '';
                    $imageTag = $imageUrl ? "<img src=\"{$imageUrl}\" style=\"height: 40px;\" />" : '-';

                    $html .= '<tr>';
                    $html .= "<td>{$sticker->number}</td>";
                    $html .= "<td>{$sticker->name}</td>";
                    $html .= "<td>({$sticker->position_x}, {$sticker->position_y})</td>";
                    $html .= "<td>{$sticker->width}x{$sticker->height}</td>";
                    $html .= "<td>{$imageTag}</td>";
                    $html .= '</tr>';
                }

                $html .= '</tbody></table></div>';

                return $html;
            });

        CRUD::column('created_at')
            ->label('Creado');

        CRUD::column('updated_at')
            ->label('Actualizado');
    }

    protected function setupReorderOperation()
    {
        CRUD::set('reorder.label', 'number');
        CRUD::set('reorder.max_level', 1);
    }
}
