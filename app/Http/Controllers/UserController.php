<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

/**
 * @group Usuarios
 */

class UserController extends Controller
{
    public function __construct()
    {
        //control de los permisos
        $this->authorizeResource(User::class, 'user');
    }

    /**
     * Obtener todos
     */
    public function index(Request $request)
    {
        //Query parameters
        $validated = $request->validate([
            //Example: matias@gmail
            'email' => 'string|sometimes',
            //Example: jose
            'name' => 'string|sometimes',
            // Example: 3
            'rol_id' => 'numeric|sometimes',
            // Example: 10
            'items' => 'numeric|sometimes',
            // Example: asc
            'order_dir' => 'in:asc,desc|sometimes',
            //Example: created_at
            'order_by' => 'in:name,email,created_at,updated_at|sometimes',
        ]);

        $cantItems = $request->items ? $request->items : config('app_settings.items_per_page');

        $listBD = User::orderBy('email', 'asc')
            ->when($request->rol_id != null, function ($query) use ($request) {
                $query->whereHas('roles', function ($query) use ($request) {
                    $query->where('roles.id', $request->rol_id);
                });
            })
            ->when($request->email != null, function ($query) use ($request) {
                $query->where('email', 'LIKE', "%{$request->email}%");
            })->when($request->name != null, function ($query) use ($request) {
                $query->where('name', 'LIKE', "%{$request->name}%");
            })->paginate($cantItems);

        return response()->json($listBD, Response::HTTP_OK);
    }

    /**
     * Crear usuario
     */
    public function store(Request $request)
    {
        //Body parameters
        $validated = $request->validate([
            'name' => ['string', 'required'],
            'email' => ['string', 'required'],
            'password' => ['string', 'required'],
            'rol_id' => ['numeric', 'sometimes'],
        ]);

        $user = User::create($request->only(['name', 'email']));
        $user->password = Hash::make($request->password);
        $user->save();

        if ($request->has('rol_id')) {
            $user->assignRole(Role::find($request->rol_id));
        }

        return response()->json($user, Response::HTTP_CREATED);
    }


    /**
     * Obtener datos
     * 
     * Devuelve datos de un usuario
     */
    public function show(User $user)
    {
        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Actualizar
     * 
     * Se actualiza un usuario
     */
    public function update(Request $request, User $user)
    {
        //Body parameters
        $validated = $request->validate([
            'name' => ['string', 'sometimes'],
            'email' => ['string', 'sometimes'],
            'password' => ['string', 'sometimes'],
            'rol_id' => ['numeric', 'sometimes'],
        ]);

        $user->update($request->only(['name', 'email']));

        if ($request->has('password')) {
            $user->password = Hash::make($request->input('password'));
        }
        if ($request->has('rol_id')) {
            $oldRoles = $user->getRoleNames();
            if ($oldRoles) {
                foreach ($oldRoles as $rol) {
                    $user->removeRole($rol);
                }
            }
            $user->assignRole(Role::find($request->input('rol_id')));
        }
        $user->save();

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Actualizar rol
     * 
     * Se actualiza un rol de un usuario, es decir, se asigna o se elimina un rol
     */
    public function  updateRole(Request $request, User $user)
    {
        $this->authorize('updateRole', $user);

        //Body parameters
        $validated = $request->validate([
            //Example: 1
            'assign' => ['boolean', 'required'],
            //Example: Superadministrador
            'role' => ['string', 'required', 'exists:roles,name'],
        ]);

        $role = $request->role;

        if ($request->assign) {
            $user->assignRole($role);
        } else {
            $user->removeRole($role);
        }

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        $user->delete();
        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Obtener datos del usuario autenticado
     *
     */
    public function me(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(["message" => 'Usuario no encontrado'], Response::HTTP_NOT_FOUND);
        }

        return response()->json($user, Response::HTTP_OK);
    }
}