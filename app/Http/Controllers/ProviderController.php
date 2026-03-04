<?php

namespace App\Http\Controllers;

use App\Models\Provider;
use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Pest\ArchPresets\Strict;

class ProviderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $provider = Provider::all();
        $companies = Company::all();
        return Inertia::render('providers/index', [
            'providers' => $provider,
            'companies' => $companies,
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
        $validateData = $request -> validate([
            'id_company' => ['required', 'exists:companies,id'],
            'ruc' => ['required', 'string', 'size:11', 'unique:providers,ruc'],
            'company_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:providers,email'],
            'contact'=>['required', 'string', 'max:20'],
            'state' =>['required', 'in:active,anactive'],
            // 'registration_date'=>['required', 'date'],
        ]);

        Provider::create($validateData);

        return to_route('providers.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Provider $provider)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Provider $provider)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $providerid)
    {
        $providers = Provider::query()->findOrFail($providerid);


        $validateData = $request -> validate([
            'id_company' => ['required', 'exists:companies,id'],
            // 'ruc' => ['required', 'string', 'size:11', 'unique:providers,ruc'],
            'company_name' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'email', 'max:255', 'unique:providers,email'],
            'contact'=>['required', 'string', 'max:20'],
            'state' =>['required', 'in:active,inactive'],
            // 'registration_date'=>['required', 'date'],
        ]);

        $payload = [
            'id_company'=>$validateData['id_company'],
            // 'ruc'=>$validateData['ruc'],
            'company_name'=>$validateData['company_name'],
            'address'=>$validateData['address'],
            // 'email'=>$validateData['email'],
            'contact'=>$validateData['contact'],
            'state'=>$validateData['state'],
        ];

        $providers->update($payload);

        return to_route('providers.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $providerid)
    {
        $providers = Provider::query()->findOrFail($providerid);
        $providers->delete();

        return to_route('providers.index');
    }
}
