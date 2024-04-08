<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Client;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreClientRequest;
use App\Http\Requests\UpdateClientRequest;
use App\Http\Resources\Admin\ClientResource;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class ClientsApiController extends Controller
{
    /**
     * @return ClientResource
     */
    public function index(): ClientResource
    {
        abort_if(Gate::denies('client_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ClientResource(Client::all());
    }

    /**
     * @param StoreClientRequest $request
     * 
     * @return Response
     */
    public function store(StoreClientRequest $request): Response
    {
        $client = Client::create($request->all());

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param Client $client
     * 
     * @return ClientResource
     */
    public function show(Client $client): ClientResource
    {
        abort_if(Gate::denies('client_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ClientResource($client);
    }

    /**
     * @param UpdateClientRequest $request
     * @param Client $client
     * 
     * @return Mixed
     */
    public function update(UpdateClientRequest $request, Client $client): Mixed
    {
        $client->update($request->all());

        return (new ClientResource($client))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param Client $client
     * 
     * @return Mixed
     */
    public function destroy(Client $client): Mixed
    {
        abort_if(Gate::denies('client_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $client->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
