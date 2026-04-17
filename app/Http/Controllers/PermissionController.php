<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Symfony\Component\HttpFoundation\Response;
use Spatie\Permission\Models\Role;


/**
 * @group Permisos
 */
class PermissionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Permission::class, 'permission');
    }
    /**
     * Mostrar todos los permisos
     * 
     * @response 200 [{"group": "areas","description": "Conjunto de permisos de: areas","permissions": [{"id": 12,"group": "areas","name": "Destroy areas","description": null},{"id": 10,"group": "areas","name": "Store areas","description": null},{"id": 11,"group": "areas","name": "Update areas","description": null},{"id": 9,"group": "areas","name": "View areas","description": null}]},]
     * @response 401 {"message": "Unauthenticated."}
     */
    public function index(Request $request)
    {
        //Query parameters
        $validated = $request->validate([
            //Example: Superadministrador
            'role' => 'string|sometimes|exists:roles,name',
        ]);

        $role = Role::where('name', $request->role)->first();

        //Permisos existentes agrupados por grupo
        $permissionBD = Permission::select('id', 'group', 'name', 'description')
            ->orderBy('group', 'ASC')
            ->orderBy('name', 'ASC')
            ->get();
        $colGrupos = $permissionBD->groupBy('group')->all();


        //Indica que permisos están asignados al rol indicado
        if ($role) {
            $permissionAssignedID = $role->permissions->pluck('id')->all();
            foreach ($colGrupos as $grupo) {
                foreach ($grupo as $permiso) {
                    $permisoID = $permiso['id'];

                    $permiso['asignado'] = in_array($permisoID, $permissionAssignedID) ? 1 : 0;
                }
            }
        }

        //Agrega descripción a cada 'grupo'
        $colDevolver = collect();
        foreach ($colGrupos as $key => $permisos) {

            $descripcionGrupo = "Conjunto de permisos de: " . $key;

            $colDevolver->push([
                'group' => $key,
                'description' => $descripcionGrupo,
                'permissions' => $permisos
            ]);
        }

        return response()->json($colDevolver, Response::HTTP_OK);
    }

    /**
     * Asignar un permiso a un rol
     * 
     * @response 200 {"id": 8,"name": "Destroy entities","description": null,"guard_name": "web","created_at": "2023-12-07T13:16:57.000000Z","updated_at": "2023-12-07T13:16:57.000000Z","group": "entities"}
     * @response 401 {"message": "Unauthenticated."}
     */
    public function assign(Request $request, Permission $permission)
    {
        $this->authorize('assign', $permission);

        //body parameters
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $role = Role::where('name', $request->role)->first();

        $role->givePermissionTo($permission);

        return response()->json($permission, Response::HTTP_OK);
    }

    /**
     * Denegar un permiso de un rol
     * 
     * @response 200 {"id": 8,"name": "Destroy entities","description": null,"guard_name": "web","created_at": "2023-12-07T13:16:57.000000Z","updated_at": "2023-12-07T13:16:57.000000Z","group": "entities"}
     * @response 401 {"message": "Unauthenticated."}
     */
    public function deny(Request $request, Permission $permission)
    {
        $this->authorize('denyPolicy', $permission);

        //body parameters
        $request->validate([
            'role' => ['required', 'string', 'exists:roles,name'],
        ]);

        $role = Role::where('name', $request->role)->first();

        $role->revokePermissionTo($permission);

        return response()->json($permission, Response::HTTP_OK);
    }
}