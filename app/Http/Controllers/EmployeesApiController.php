<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Employee;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Traits\MediaUploadingTrait;
use App\Http\Requests\StoreEmployeeRequest;
use App\Http\Requests\UpdateEmployeeRequest;
use App\Http\Resources\Admin\EmployeeResource;
use Gate;
use Symfony\Component\HttpFoundation\Response;

class EmployeesApiController extends Controller
{
    use MediaUploadingTrait;

    /**
     * @return EmployeeResource
     */
    public function index(): EmployeeResource
    {
        abort_if(Gate::denies('employee_access'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new EmployeeResource(Employee::with(['services'])->get());
    }

    /**
     * @param StoreEmployeeRequest $request
     * 
     * @return Mixed
     */
    public function store(StoreEmployeeRequest $request): Mixed
    {
        $employee = Employee::create($request->all());
        $employee->services()->sync($request->input('services', []));

        if ($request->input('photo', false)) {
            $employee->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
        }

        return (new EmployeeResource($employee))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }

    /**
     * @param Appointment $appointment
     * 
     * @return EmployeeResource
     */
    public function show(Employee $employee)
    {
        abort_if(Gate::denies('employee_show'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        return new EmployeeResource($employee->load(['services']));
    }

    /**
     * @param Appointment $appointment
     * 
     * @return Mixed
     */
    public function update(UpdateEmployeeRequest $request, Employee $employee): Mixed
    {
        $employee->update($request->all());
        $employee->services()->sync($request->input('services', []));

        if ($request->input('photo', false)) {
            if (!$employee->photo || $request->input('photo') !== $employee->photo->file_name) {
                $employee->addMedia(storage_path('tmp/uploads/' . $request->input('photo')))->toMediaCollection('photo');
            }
        } elseif ($employee->photo) {
            $employee->photo->delete();
        }

        return (new EmployeeResource($employee))
            ->response()
            ->setStatusCode(Response::HTTP_ACCEPTED);
    }

    /**
     * @param Appointment $appointment
     * 
     * @return Mixed
     */
    public function destroy(Employee $employee): Mixed
    {
        abort_if(Gate::denies('employee_delete'), Response::HTTP_FORBIDDEN, '403 Forbidden');

        $employee->delete();

        return response(null, Response::HTTP_NO_CONTENT);
    }
}
