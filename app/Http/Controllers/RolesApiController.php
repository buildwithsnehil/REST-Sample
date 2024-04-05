<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\Admin\RoleResource;
use App\Role;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class RolesApiController extends Controller
{
    /**
     * @return RoleResource
     */
    public function index(): RoleResource
    {
        abort_if(Gate::denies('role_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RoleResource(Role::with(['permissions'])->get());
    }

    /**
     * @param StoreRoleRequest $request
     * 
     * @return Mixed
     */
    public function store(StoreRoleRequest $request): Mixed
    {
        $role = Role::create($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param Role $role
     * 
     * @return RoleResource
     */
    public function show(Role $role): RoleResource
    {
        abort_if(Gate::denies('role_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new RoleResource($role->load(['permissions']));
    }

    /**
     * @param UpdateRoleRequest $request
     * @param Role $role
     * 
     * @return RoleResource
     */
    public function update(UpdateRoleRequest $request, Role $role): RoleResource
    {
        $role->update($request->all());
        $role->permissions()->sync($request->input('permissions', []));

        return (new RoleResource($role))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param Role $role
     * 
     * @return Mixed
     */
    public function destroy(Role $role): Mixed
    {
        abort_if(Gate::denies('role_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $role->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
