<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Http\Resources\Admin\ServiceResource;
use App\Service;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class ServicesApiController extends Controller
{

    /**
     * @return ServiceResource
     */
    public function index(): ServiceResource
    {
        abort_if(Gate::denies('service_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceResource(Service::all());
    }

    /**
     * @param StoreServiceRequest $request
     * 
     * @return Mixed
     */
    public function store(StoreServiceRequest $request): Mixed
    {
        $service = Service::create($request->all());

        return (new ServiceResource($service))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param Service $service
     *  
     * @return ServiceResource
     */
    public function show(Service $service): ServiceResource
    {
        abort_if(Gate::denies('service_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new ServiceResource($service);
    }

    /**
     * @param UpdateServiceRequest $request
     * @param Service $service
     * 
     * @return Mixed
     */
    public function update(UpdateServiceRequest $request, Service $service): Mixed
    {
        $service->update($request->all());

        return (new ServiceResource($service))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param Service $service
     * 
     * @return Mixed
     */
    public function destroy(Service $service): Mixed
    {
        abort_if(Gate::denies('service_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $service->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
