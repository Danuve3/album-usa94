<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\StickerRequest;
use App\Models\Sticker;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StickerCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\CreateOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(Sticker::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/sticker');
        CRUD::setEntityNameStrings('cromo', 'cromos');
    }

    protected function setupListOperation()
    {
        CRUD::addButton('top', 'import', 'view', 'crud::buttons.import_stickers');

        CRUD::column('number')
            ->label('Número')
            ->type('number');

        CRUD::column('image_path')
            ->label('Imagen')
            ->type('image')
            ->disk('public')
            ->height('50px')
            ->width('50px');

        CRUD::column('name')
            ->label('Nombre')
            ->type('text')
            ->searchLogic(function ($query, $column, $searchTerm) {
                $query->orWhere('name', 'like', '%'.$searchTerm.'%');
            });

        CRUD::column('page_number')
            ->label('Página')
            ->type('number');

        CRUD::column('rarity')
            ->label('Rareza')
            ->type('select_from_array')
            ->options([
                'common' => 'Común',
                'shiny' => 'Brillante',
            ]);

        CRUD::column('is_horizontal')
            ->label('Horizontal')
            ->type('boolean');

        CRUD::orderBy('number', 'asc');

        $this->setupFilters();
    }

    protected function setupFilters()
    {
        CRUD::filter('page_number')
            ->type('dropdown')
            ->label('Página')
            ->values(function () {
                return Sticker::select('page_number')
                    ->distinct()
                    ->orderBy('page_number')
                    ->pluck('page_number', 'page_number')
                    ->toArray();
            })
            ->whenActive(function ($value) {
                CRUD::addClause('where', 'page_number', $value);
            });

        CRUD::filter('rarity')
            ->type('dropdown')
            ->label('Rareza')
            ->values([
                'common' => 'Común',
                'shiny' => 'Brillante',
            ])
            ->whenActive(function ($value) {
                CRUD::addClause('where', 'rarity', $value);
            });

        CRUD::filter('is_horizontal')
            ->type('dropdown')
            ->label('Orientación')
            ->values([
                '1' => 'Horizontal',
                '0' => 'Vertical',
            ])
            ->whenActive(function ($value) {
                CRUD::addClause('where', 'is_horizontal', $value);
            });
    }

    protected function setupCreateOperation()
    {
        CRUD::setValidation(StickerRequest::class);

        CRUD::field('number')
            ->label('Número')
            ->type('number')
            ->attributes(['min' => 1])
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('name')
            ->label('Nombre')
            ->type('text')
            ->wrapper(['class' => 'form-group col-md-8']);

        CRUD::field('page_number')
            ->label('Número de página')
            ->type('number')
            ->attributes(['min' => 1])
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('rarity')
            ->label('Rareza')
            ->type('select_from_array')
            ->options([
                'common' => 'Común',
                'shiny' => 'Brillante',
            ])
            ->default('common')
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('is_horizontal')
            ->label('¿Es horizontal?')
            ->type('boolean')
            ->default(false)
            ->wrapper(['class' => 'form-group col-md-4']);

        CRUD::field('separator_position')
            ->type('custom_html')
            ->value('<hr><h5>Posición en el álbum</h5>');

        CRUD::field('position_x')
            ->label('Posición X')
            ->type('number')
            ->attributes(['min' => 0])
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('position_y')
            ->label('Posición Y')
            ->type('number')
            ->attributes(['min' => 0])
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('width')
            ->label('Ancho')
            ->type('number')
            ->attributes(['min' => 1])
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('height')
            ->label('Alto')
            ->type('number')
            ->attributes(['min' => 1])
            ->wrapper(['class' => 'form-group col-md-3']);

        CRUD::field('separator_image')
            ->type('custom_html')
            ->value('<hr><h5>Imagen del cromo</h5>');

        CRUD::field('image_path')
            ->label('Imagen')
            ->type('upload')
            ->withFiles([
                'disk' => 'public',
                'path' => 'stickers',
            ]);

        $this->addImagePreview();
    }

    protected function setupUpdateOperation()
    {
        $this->setupCreateOperation();
    }

    protected function addImagePreview(): void
    {
        $entry = $this->crud->getCurrentEntry();
        if ($entry && $entry->image_path) {
            $imageUrl = \Storage::disk('public')->url($entry->image_path);
            CRUD::field('image_preview')
                ->type('custom_html')
                ->value("
                    <div class='mb-3'>
                        <label class='form-label'>Vista previa actual</label>
                        <div class='border rounded p-2 bg-light' style='max-width: 300px;'>
                            <img src='{$imageUrl}' alt='Preview' class='img-fluid' style='max-height: 200px;'>
                        </div>
                    </div>
                ");
        }
    }

    protected function setupShowOperation()
    {
        CRUD::column('number')
            ->label('Número');

        CRUD::column('name')
            ->label('Nombre');

        CRUD::column('page_number')
            ->label('Página');

        CRUD::column('rarity')
            ->label('Rareza')
            ->type('select_from_array')
            ->options([
                'common' => 'Común',
                'shiny' => 'Brillante',
            ]);

        CRUD::column('is_horizontal')
            ->label('Horizontal')
            ->type('boolean');

        CRUD::column('position_x')
            ->label('Posición X');

        CRUD::column('position_y')
            ->label('Posición Y');

        CRUD::column('width')
            ->label('Ancho');

        CRUD::column('height')
            ->label('Alto');

        CRUD::column('image_path')
            ->label('Imagen')
            ->type('image')
            ->disk('public')
            ->height('200px');

        CRUD::column('created_at')
            ->label('Creado');

        CRUD::column('updated_at')
            ->label('Actualizado');
    }

    public function importForm()
    {
        $this->crud->hasAccessOrFail('create');

        return view('vendor.backpack.crud.sticker_import');
    }

    public function import(Request $request)
    {
        $this->crud->hasAccessOrFail('create');

        $validator = Validator::make($request->all(), [
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ], [
            'csv_file.required' => 'El archivo CSV es requerido.',
            'csv_file.mimes' => 'El archivo debe ser de tipo CSV.',
            'csv_file.max' => 'El archivo no puede superar los 5MB.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $file = $request->file('csv_file');
        $updateExisting = $request->boolean('update_existing');

        $handle = fopen($file->getPathname(), 'r');
        if (! $handle) {
            return back()->with('error', 'No se pudo abrir el archivo.');
        }

        $header = fgetcsv($handle);
        if (! $header) {
            fclose($handle);

            return back()->with('error', 'El archivo está vacío.');
        }

        $header = array_map('strtolower', array_map('trim', $header));

        $requiredColumns = ['number', 'name', 'page_number', 'position_x', 'position_y', 'width', 'height'];
        $missingColumns = array_diff($requiredColumns, $header);

        if (! empty($missingColumns)) {
            fclose($handle);

            return back()->with('error', 'Faltan columnas requeridas: '.implode(', ', $missingColumns));
        }

        $created = 0;
        $updated = 0;
        $errors = [];
        $row = 1;

        DB::beginTransaction();

        try {
            while (($data = fgetcsv($handle)) !== false) {
                $row++;

                if (count($data) !== count($header)) {
                    $errors[] = "Fila {$row}: número de columnas incorrecto.";

                    continue;
                }

                $rowData = array_combine($header, $data);

                $rowValidator = Validator::make($rowData, [
                    'number' => 'required|integer|min:1',
                    'name' => 'required|string|max:255',
                    'page_number' => 'required|integer|min:1',
                    'position_x' => 'required|integer|min:0',
                    'position_y' => 'required|integer|min:0',
                    'width' => 'required|integer|min:1',
                    'height' => 'required|integer|min:1',
                    'rarity' => 'nullable|in:common,shiny',
                    'is_horizontal' => 'nullable',
                ]);

                if ($rowValidator->fails()) {
                    $errors[] = "Fila {$row}: ".implode(', ', $rowValidator->errors()->all());

                    continue;
                }

                $stickerData = [
                    'number' => (int) $rowData['number'],
                    'name' => trim($rowData['name']),
                    'page_number' => (int) $rowData['page_number'],
                    'position_x' => (int) $rowData['position_x'],
                    'position_y' => (int) $rowData['position_y'],
                    'width' => (int) $rowData['width'],
                    'height' => (int) $rowData['height'],
                    'rarity' => isset($rowData['rarity']) && in_array($rowData['rarity'], ['common', 'shiny'])
                        ? $rowData['rarity']
                        : 'common',
                    'is_horizontal' => isset($rowData['is_horizontal'])
                        ? in_array(strtolower($rowData['is_horizontal']), ['1', 'true', 'yes', 'si'])
                        : false,
                ];

                $existing = Sticker::where('number', $stickerData['number'])->first();

                if ($existing) {
                    if ($updateExisting) {
                        $existing->update($stickerData);
                        $updated++;
                    } else {
                        $errors[] = "Fila {$row}: el cromo #{$stickerData['number']} ya existe.";
                    }
                } else {
                    Sticker::create($stickerData);
                    $created++;
                }
            }

            fclose($handle);

            if (! empty($errors) && $created === 0 && $updated === 0) {
                DB::rollBack();

                return back()->with('error', 'No se importó ningún registro. Errores: '.implode(' | ', array_slice($errors, 0, 5)));
            }

            DB::commit();

            $message = "Importación completada: {$created} creados, {$updated} actualizados.";
            if (! empty($errors)) {
                $message .= ' Con '.count($errors).' errores.';
            }

            return redirect(url(config('backpack.base.route_prefix').'/sticker'))
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);

            return back()->with('error', 'Error durante la importación: '.$e->getMessage());
        }
    }

    public function importTemplate(): StreamedResponse
    {
        $this->crud->hasAccessOrFail('create');

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="stickers_template.csv"',
        ];

        $columns = ['number', 'name', 'page_number', 'position_x', 'position_y', 'width', 'height', 'rarity', 'is_horizontal'];

        return response()->stream(function () use ($columns) {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, $columns);
            fputcsv($handle, [1, 'Ejemplo Cromo', 1, 50, 100, 150, 200, 'common', 0]);
            fputcsv($handle, [2, 'Cromo Brillante', 1, 250, 100, 150, 200, 'shiny', 1]);
            fclose($handle);
        }, 200, $headers);
    }
}
