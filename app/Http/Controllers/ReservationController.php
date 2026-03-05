<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reservations = Reservation::query()
        ->with('vehicle:id,plate')
        ->latest()
        ->paginate(10)
        ->through(fn (Reservation $reservation): array =>[
            'id' => $reservation->id,
            'id_vehicle' => $reservation->vehicle->id_vehicle,
            'plate' => $reservation->vehicle?->plate,
            'reservation_date' => $reservation->reservation_date,
            'description' => $reservation->description,
            'state' => $reservation->state,
        ]);

        return Inertia::render('reservations/index',[
            'reservations' => $reservations,
            'vehicles' => Vehicle::query()->orderBy('plate')->get(['id','plate']),
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
        $validate = $request->validate([
            'id_vehicle' => 'required|integer',
            'description' => 'required|string|max:250',
            'reservation_date' => 'required|date',
            'state'=>'required|in:accepted,rejected'
        ]);

        Reservation::create([
            'id_vehicle' => $validate['id_vehicle'],
            'description' => $validate['description'],
            'reservation_date' => $validate['reservation_date'],
            'state' => $validate['state'],
        ]);

        return to_route('reservations.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Reservation $reservation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request)
    {


    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $id_res)
    {
        //
        $reservation = Reservation::query()->findOrFail($id_res);
        $validate = $request->validate([
            'id_vehicle' => 'required|integer',
            'description' => 'required|string|max:250',
            'reservation_date' => 'required|date',
            'state'=>'required|in:accepted,rejected'
        ]);

         $payload = [
            'id_vehicle' => $validate['id_vehicle'],
            'description' => $validate['description'],
            'reservation_date' => $validate['reservation_date'],
            'state' => $validate['state'],
        ];

        $reservation->update($payload);
        return to_route('reservations.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $id_res)
    {
        //
         $reservation = Reservation::query()->findOrFail($id_res);
         $reservation->delete();
         return to_route('reservations.index');

    }
}
