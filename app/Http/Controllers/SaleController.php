<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Http\Controllers\Controller;
use App\Models\Component;
use App\Models\Service;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sale = Sale::all();
        $user = User::all();
        $component = Component::all();
        $service = Service::all();
        return Inertia::render('sales/index', [
            'sales' => $sale,
            'users' => $user,
            'components' => $component,
            'services' => $service,
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
            'id_user' => ['required', 'exists:users,id'],
            'id_component' => ['required', 'exists:components,id'],
            'id_service' => ['required', 'exists:service,id'],
            'num_voucher' => ['required', 'string', 'max:50', 'unique:sales,num_voucher'],
            'type_voucher' => ['required', 'in:receipt,invoice'],
            'payment_method' => ['required', 'in:paid,pending,rejected'],
            'quantity' => ['required', 'integer', 'min:1'],
            // 'igv' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'registration_date' => ['nullable', 'date'],
            'descuento' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        Sale::create($validateData);

        return to_route('sales.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Sale $sale)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Sale $sale)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $saleid)
    {
        $sales = Sale::query()->findOrFail($saleid);

        $validateData = $request->validate([
            'id_user' => ['required', 'exists:users,id'],
            'id_component' => ['required', 'exists:components,id'],
            'id_service' => ['required', 'exists:service,id'],
            'num_voucher' => ['required', 'string', 'max:50', 'unique:sales,num_voucher'],
            'type_voucher' => ['required', 'in:receipt,invoice'],
            'payment_method' => ['required', 'in:paid,pending,rejected'],
            'quantity' => ['required', 'integer', 'min:1'],
            // 'igv' => ['required', 'numeric', 'min:0'],
            'total' => ['required', 'numeric', 'min:0'],
            'registration_date' => ['nullable', 'date'],
            'descuento' => ['nullable', 'integer', 'min:0', 'max:100'],
        ]);

        $payload = [
            'id_user' => $validateData['id_user'],
            'id_component' => $validateData['id_component'],
            'id_service' => $validateData['id_service'],
            'num_voucher' => $validateData['num_voucher'],
            'type_voucher' => $validateData['type_voucher'],
            'payment_method' => $validateData['payment_method'],
            'quantity' => $validateData['quantity'],
            'total' => $validateData['total'],
            'registration_date' => $validateData['registration_date'],
            'descuento' => $validateData['descuento'],
        ];

        $sales->update($payload);
        return to_route('sales.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $saleid)
    {
        $sales = Sale::query()->findOrFail($saleid);
        $sales->delete();

        return to_route('sales.index');
    }
}
