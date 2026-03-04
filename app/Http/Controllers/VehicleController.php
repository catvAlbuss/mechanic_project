<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Log;
class VehicleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vehicles = Vehicle::all();
        return Inertia::render('vehicles/index', [
            'vehicles' => $vehicles
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
      'id_user' => 'required|integer|max: 200',
      'plate' => 'required|string|max:255',
      'tipe' => 'required|in:motorcycle,car,truck',
      'vin' => 'required|string|max:255',
      'engine' => 'required|string|max:255',
      'color' => 'required|string|max:255',
      'brand' => 'required|string|max:255',
      'year' => 'nullable|date',
      'mileage' => 'required|integer|min:0',
      'model' => 'required|string|max:255',
      'state' => 'required|in:active,inactive',
      
     
  ]);


  Vehicle::create([
      'id_user' => $validateData['id_user'],
      'plate' => $validateData['plate'],
      'tipe' => $validateData['tipe'],
      'vin' => $validateData['vin'],
      'engine' => $validateData['engine'],
      'color' => $validateData['color'],
      'brand' => $validateData['brand'],
      'year' => $validateData['year'],
      'mileage' => $validateData['mileage'],
      'model' => $validateData['model'],
      'state' => $validateData['state'],
     ['registration_date'],
     
    ]);
    
     return to_route('vehicles.index');
    }
    /**
     * Display the specified resource.
     */
    public function show(Vehicles $vehicle)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Vehicle $vehicle)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $vehicles=Vehicle::query()->findOrFail($vehicle->id);

        $vehicles->update([
        'id_user'=>$request->id_user,
        'plate'=>$request->plate,
        'tipe'=>$request->tipe,
        'vin'=>$request->vin,
        'engine'=>$request->engine,
        'color'=>$request->color,
        'brand'=>$request->brand,
        'year'=>$request->year,
        'mileage'=>$request->mileage,
        'model'=>$request->model,
        'state'=>$request->state,
    
    
    ]);

    $payload = [
        'id_user' => $vehicles->id_user,
        'plate' => $vehicles->plate,
        'tipe' => $vehicles->tipe,
        'vin' => $vehicles->vin,
        'engine' => $vehicles->engine,
        'color' => $vehicles->color,
        'brand' => $vehicles->brand,
        'year' => $vehicles->year,
        'mileage' => $vehicles->mileage,
        'model' => $vehicles->model,
        'state' => $vehicles->state,
       
       
    ];
     return to_route('vehicles.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Vehicle $vehicle)
    {
        vehicle::destroy($vehicle->id);
        return to_route('vehicles.index');
    }
}
