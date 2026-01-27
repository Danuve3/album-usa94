<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Backpack\CRUD\app\Http\Controllers\CrudController;
use Backpack\CRUD\app\Library\CrudPanel\CrudPanelFacade as CRUD;

class UserCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\ShowOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\UpdateOperation;

    public function setup()
    {
        CRUD::setModel(User::class);
        CRUD::setRoute(config('backpack.base.route_prefix').'/user');
        CRUD::setEntityNameStrings('usuario', 'usuarios');
    }

    protected function setupListOperation()
    {
        CRUD::column('id')
            ->label('ID');

        CRUD::column('name')
            ->label('Nombre')
            ->searchLogic(function ($query, $column, $searchTerm) {
                $query->orWhere('name', 'like', '%'.$searchTerm.'%');
            });

        CRUD::column('email')
            ->label('Email')
            ->searchLogic(function ($query, $column, $searchTerm) {
                $query->orWhere('email', 'like', '%'.$searchTerm.'%');
            });

        CRUD::column('total_stickers')
            ->label('Total Cromos')
            ->type('closure')
            ->function(fn ($entry) => $entry->total_stickers_count);

        CRUD::column('glued_stickers')
            ->label('Pegados')
            ->type('closure')
            ->function(fn ($entry) => $entry->glued_stickers_count);

        CRUD::column('duplicate_stickers')
            ->label('Repetidos')
            ->type('closure')
            ->function(fn ($entry) => $entry->duplicate_stickers_count);

        CRUD::column('unopened_packs')
            ->label('Sobres')
            ->type('closure')
            ->function(fn ($entry) => $entry->unopened_packs_count);

        CRUD::column('is_banned')
            ->label('Estado')
            ->type('closure')
            ->function(fn ($entry) => $entry->is_banned
                ? '<span class="badge bg-danger">Baneado</span>'
                : '<span class="badge bg-success">Activo</span>');

        CRUD::column('created_at')
            ->label('Registrado')
            ->type('datetime');

        CRUD::addButtonFromView('line', 'ban_user', 'ban_user', 'beginning');
        CRUD::addButtonFromView('line', 'give_packs', 'give_packs', 'beginning');
    }

    protected function setupShowOperation()
    {
        CRUD::column('id')
            ->label('ID');

        CRUD::column('name')
            ->label('Nombre');

        CRUD::column('email')
            ->label('Email');

        CRUD::column('stats')
            ->label('Estadísticas')
            ->type('closure')
            ->function(function ($entry) {
                $total = $entry->total_stickers_count;
                $glued = $entry->glued_stickers_count;
                $duplicates = $entry->duplicate_stickers_count;
                $unopened = $entry->unopened_packs_count;

                return "
                    <div class='row'>
                        <div class='col-md-3'>
                            <div class='card bg-primary text-white'>
                                <div class='card-body text-center'>
                                    <h3>{$total}</h3>
                                    <p class='mb-0'>Cromos Totales</p>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='card bg-success text-white'>
                                <div class='card-body text-center'>
                                    <h3>{$glued}</h3>
                                    <p class='mb-0'>Pegados</p>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='card bg-warning text-dark'>
                                <div class='card-body text-center'>
                                    <h3>{$duplicates}</h3>
                                    <p class='mb-0'>Repetidos</p>
                                </div>
                            </div>
                        </div>
                        <div class='col-md-3'>
                            <div class='card bg-info text-white'>
                                <div class='card-body text-center'>
                                    <h3>{$unopened}</h3>
                                    <p class='mb-0'>Sobres sin abrir</p>
                                </div>
                            </div>
                        </div>
                    </div>
                ";
            });

        CRUD::column('is_banned')
            ->label('Estado de cuenta')
            ->type('closure')
            ->function(function ($entry) {
                if ($entry->is_banned) {
                    $bannedAt = $entry->banned_at ? $entry->banned_at->format('d/m/Y H:i') : 'N/A';
                    $reason = $entry->ban_reason ?? 'Sin razón especificada';

                    return "
                        <div class='alert alert-danger'>
                            <strong>Usuario Baneado</strong><br>
                            <small>Fecha: {$bannedAt}</small><br>
                            <small>Razón: {$reason}</small>
                        </div>
                    ";
                }

                return '<span class="badge bg-success">Activo</span>';
            });

        CRUD::column('activity_logs')
            ->label('Historial de Actividad')
            ->type('closure')
            ->function(function ($entry) {
                $logs = $entry->activityLogs()->orderBy('created_at', 'desc')->limit(20)->get();

                if ($logs->isEmpty()) {
                    return '<p class="text-muted">No hay actividad registrada</p>';
                }

                $html = '<div class="table-responsive"><table class="table table-sm table-bordered">';
                $html .= '<thead><tr><th>Fecha</th><th>Acción</th><th>Descripción</th></tr></thead>';
                $html .= '<tbody>';

                $actionLabels = [
                    'banned' => '<span class="badge bg-danger">Baneado</span>',
                    'unbanned' => '<span class="badge bg-success">Desbaneado</span>',
                    'packs_given' => '<span class="badge bg-info">Sobres otorgados</span>',
                    'pack_opened' => '<span class="badge bg-secondary">Sobre abierto</span>',
                    'sticker_glued' => '<span class="badge bg-primary">Cromo pegado</span>',
                ];

                foreach ($logs as $log) {
                    $date = $log->created_at->format('d/m/Y H:i');
                    $action = $actionLabels[$log->action] ?? "<span class='badge bg-secondary'>{$log->action}</span>";
                    $description = $log->description ?? '-';

                    $html .= '<tr>';
                    $html .= "<td>{$date}</td>";
                    $html .= "<td>{$action}</td>";
                    $html .= "<td>{$description}</td>";
                    $html .= '</tr>';
                }

                $html .= '</tbody></table></div>';

                return $html;
            });

        CRUD::column('created_at')
            ->label('Registrado');

        CRUD::column('updated_at')
            ->label('Actualizado');
    }

    protected function setupUpdateOperation()
    {
        CRUD::setValidation(UserRequest::class);

        CRUD::field('name')
            ->label('Nombre')
            ->type('text');

        CRUD::field('email')
            ->label('Email')
            ->type('email');
    }

    public function banUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->is_banned) {
            $user->unban();

            return response()->json([
                'success' => true,
                'message' => 'Usuario desbaneado correctamente',
            ]);
        }

        $reason = request('reason');
        $user->ban($reason);

        return response()->json([
            'success' => true,
            'message' => 'Usuario baneado correctamente',
        ]);
    }

    public function givePacks($id)
    {
        $user = User::findOrFail($id);
        $count = (int) request('count', 1);

        if ($count < 1 || $count > 100) {
            return response()->json([
                'success' => false,
                'message' => 'La cantidad debe ser entre 1 y 100',
            ], 422);
        }

        $user->givePacks($count);

        return response()->json([
            'success' => true,
            'message' => "Se otorgaron {$count} sobres al usuario",
        ]);
    }
}
