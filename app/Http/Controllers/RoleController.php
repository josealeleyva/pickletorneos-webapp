<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Rol
 */
class RoleController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Role::class, 'rol');
    }
    /**
     * Obtener todos 
     * 
     * @response 200 [{"id": 1,"name": "Superadministrador","guard_name": "web","created_at": "2023-12-07T13:16:56.000000Z","updated_at": "2023-12-07T13:16:56.000000Z"},{"id": 2,"name": "Administrador","guard_name": "web","created_at": "2023-12-07T13:16:56.000000Z","updated_at": "2023-12-07T13:16:56.000000Z"},{"id": 3,"name": "Operador","guard_name": "web","created_at": "2023-12-07T13:16:56.000000Z","updated_at": "2023-12-07T13:16:56.000000Z"}]
     */
    public function index()
    {
        return response()->json(Role::all(), Response::HTTP_OK);
    }

    /**
     * Crear 
     * 
     * @response 201 {"id": 1,"name": "Superadministrador","guard_name": "web","created_at": "2023-12-07T13:16:56.000000Z","updated_at": "2023-12-07T13:16:56.000000Z"}
     * @response 401 {"message": "Unauthenticated."}
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:roles,name'],
        ]);

        $rol = Role::create([
            'name' => $request->name,
            'guard_name' => 'web',
        ]);

        return response()->json($rol, Response::HTTP_CREATED);
    }

    /**
     * Eliminar 
     * 
     * @response 200 {"id": 1,"name": "Superadministrador","guard_name": "web","created_at": "2023-12-07T13:16:56.000000Z","updated_at": "2023-12-07T13:16:56.000000Z"}
     * @response 401 {"message": "Unauthenticated."}
     */
    public function destroy(Role $rol)
    {
        $rol->delete();
        return response()->json($rol);
    }
}
