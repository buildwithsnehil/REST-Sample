<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Appointment;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreAppointmentRequest;
use App\Http\Requests\UpdateAppointmentRequest;
use App\Http\Resources\Admin\AppointmentResource;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class AppointmentsApiController extends Controller
{
    /**
     * @return AppointmentResource
     */
    public function index(): AppointmentResource
    {
        abort_if(Gate::denies('appointment_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AppointmentResource(Appointment::with(['client', 'employee', 'services'])->get());
    }

    /**
     * @param StoreAppointmentRequest $request
     * 
     * @return Mixed
     */
    public function store(StoreAppointmentRequest $request): Mixed
    {
        $appointment = Appointment::create($request->all());
        $appointment->services()->sync($request->input('services', []));

        return (new AppointmentResource($appointment))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param Appointment $appointment
     * 
     * @return AppointmentResource
     */
    public function show(Appointment $appointment): AppointmentResource
    {
        abort_if(Gate::denies('appointment_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new AppointmentResource($appointment->load(['client', 'employee', 'services']));
    }

    /**
     * @param UpdateAppointmentRequest $request
     * @param Appointment $appointment
     * 
     * @return Mixed
     */
    public function update(UpdateAppointmentRequest $request, Appointment $appointment): Mixed
    {
        $appointment->update($request->all());
        $appointment->services()->sync($request->input('services', []));

        return (new AppointmentResource($appointment))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param Appointment $appointment
     * 
     * @return Mixed
     */
    public function destroy(Appointment $appointment): Mixed
    {
        abort_if(Gate::denies('appointment_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $appointment->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
