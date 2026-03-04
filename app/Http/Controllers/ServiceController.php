<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;

class ServiceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
      $services = Service::all();
      return Inertia::render('services/index', [
          'services' => $services
      ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        Log::info($request);
        $validateData = $request->validate([
            
            'id_diagnostic' => 'required|integer|max: 200',
            'id_user' => 'required|integer|max: 200',
            'orden_service' => 'required|string|max:255',
            'check_in_date' => 'required|date',
            'check_out_date' => 'nullable|date|after_or_equal:check_in_date',
        ]);


        Service::create([
            
            'id_diagnostic' => $validateData['id_diagnostic'],
            'id_user' => $validateData['id_user'],
            'orden_service' => $validateData['orden_service'],
            'check_in_date' => $validateData['check_in_date'],
            'check_out_date' => $validateData['check_out_date'],
        ]);

            return to_route('services.index');
        }
    

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        $service=service::query()->findOrfail($service->id);
        $service ->update([
            'id_diagnostic' => $request->id_diagnostic,
            
            'id_user' => $request->id_user,
            'orden_service' => $request->orden_service,
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
        ]);

        $payload=[
            'id_diagnostic' => $service->id_diagnostic,
          
            'id_user' => $service->id_user,
            'orden_service' => $service->orden_service,
            'check_in_date' => $service->check_in_date,
            'check_out_date' => $service->check_out_date,
        ];

        return to_route('services.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        $service::destroy($service_id);
        return to_route('services.index');
    }
}
