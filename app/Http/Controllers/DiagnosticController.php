<?php

namespace App\Http\Controllers;

use App\Models\Diagnostic;
use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DiagnosticController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $diagnostic = Diagnostic::all();
        $reservation = Reservation::all();
        $user = User::all();
        return Inertia::render('diagnostics/index', [
            'diagnostics' => $diagnostic,
            'reservations' => $reservation,
            'users'=>$user,
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
        $validateData = $request->validate([
            'id_user'=>['required', 'exists:users,id'],
            'id_reservation'=>['required', 'exists:reservations,id'],
            'description'=>['required', 'string'],
            // 'registration_date'=>['required','date'],
            'cost_aprox'=>['required','numeric','min:0'],
        ]);

        Diagnostic::create($validateData);

        return to_route('diagnostics.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Diagnostic $diagnostic)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Diagnostic $diagnostic)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $diagnosticid)
    {
        $diagnostics = Diagnostic::query()->findOrFail($diagnosticid);

        $validateData = $request->validate([
            'id_user'=>['required', 'exists:users,id'],
            'id_reservation'=>['required', 'exists:reservations,id'],
            'description'=>['required', 'string'],
            // 'registration_date'=>['required','date'],
            'cost_aprox'=>['required','numeric','min:0'],
        ]);

        $payload = [
            'id_user'=>$validateData['id_user'],
            'id_reservation'=>$validateData['id_reservation'],
            'description'=>$validateData['description'],
            // 'registration_date'=>$validateData['registration_date'],
            'cost_aprox'=>$validateData['cost_aprox'],
        ];

        $diagnostics->update($payload);
        return to_route('diagnostics.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $diagnosticid)
    {
        $diagnostics = Diagnostic::query()->findOrFail($diagnosticid);
        $diagnostics->delete();

        return to_route('diagnostics.index');
    }
}
