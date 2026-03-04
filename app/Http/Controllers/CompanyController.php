<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia as InertiaAlias;

class CompanyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $company = Company::all();
        return InertiaAlias::render('companies/index', [
            'companies' => $company,
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
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'ruc' => 'required|string|size:11|unique:companies,ruc',
            'company_name' => 'required|string|max:150',
            'address' => 'required|string|max:255',
            'district' => 'required|string|max:150',
            'province' => 'required|string|max:150',
            'department' => 'required|string|max:150',
            'state' => 'required|in:active,inactive',
            'registration_date' => 'nullable|date',
            'config' => 'nullable|array',
        ]);

        // Company::create($validateData);
        Company::create([
            'avatar' => $validateData['avatar'],
            'ruc' => $validateData['ruc'],
            'company_name' => $validateData['company_name'],
            'address' => $validateData['address'],
            'district' => $validateData['district'],
            'province' => $validateData['province'],
            'department' => $validateData['department'],
            'state' => $validateData['state'],
            'registration_date' => $validateData['registration_date'],
            'config' => $validateData['config'],
        ]);

        return to_route('companies.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Company $company)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Company $company)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $companyid)
    {
        $companies = Company::query()->findOrFail($companyid);

        $validateData = $request->validate([
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            // 'ruc' => 'required|string|size:11|unique:companies,ruc',
            'company_name' => 'required|string|max:150',
            'address' => 'required|string|max:255',
            'district' => 'required|string|max:150',
            'province' => 'required|string|max:150',
            'department' => 'required|string|max:150',
            'state' => 'required|in:active,inactive',
            // 'registration_date' => 'nullable|date',
            'config' => 'nullable|array',
        ]);

        $payload = [
            'avatar' => $validateData['avatar'],
            // 'ruc' => $validateData['ruc'],
            'company_name' => $validateData['company_name'],
            'address' => $validateData['address'],
            'district' => $validateData['district'],
            'province' => $validateData['province'],
            'department' => $validateData['department'],
            'state' => $validateData['state'],
            // 'registration_date' => $validateData['registration_date'],
            'config' => $validateData['config'],
        ];

        $companies->update($payload);
        return to_route('companies.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $companyid)
    {
        $companies = Company::query()->findOrFail($companyid);

        $companies->delete();
        return to_route('companies.index');
    }
}
