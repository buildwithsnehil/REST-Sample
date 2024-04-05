<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\Admin\UserResource;
use App\User;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class UsersApiController extends Controller
{

    /**
     * @param Service $service
     * 
     * @return UserResource
     */
    public function index(): UserResource
    {
        abort_if(Gate::denies('user_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource(User::with(['roles'])->get());
    }

    /**
     * @param Service $service
     * 
     * @return Mixed
     */
    public function store(StoreUserRequest $request): Mixed
    {
        $user = User::create($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param Service $service
     * 
     * @return UserResource
     */
    public function show(User $user): UserResource
    {
        abort_if(Gate::denies('user_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new UserResource($user->load(['roles']));
    }

    /**
     * @param Service $service
     * 
     * @return Mixed
     */
    public function update(UpdateUserRequest $request, User $user): Mixed
    {
        $user->update($request->all());
        $user->roles()->sync($request->input('roles', []));

        return (new UserResource($user))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param Service $service
     * 
     * @return Mixed
     */
    public function destroy(User $user): Mixed
    {
        abort_if(Gate::denies('user_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $user->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
