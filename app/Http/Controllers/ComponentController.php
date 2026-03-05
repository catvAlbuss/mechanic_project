<?php

namespace App\Http\Controllers;

use App\Models\Component;
use App\Models\Category;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ComponentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $components = Component::all();
        $categories = Category::all();
        return Inertia::render('components/index', [
            'components' => $components,
            'categories' => $categories,
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
        $request->validate([
            'id_category' => ["required", "exists:categories,id"],
            'sku' => ["required", "string", "max:100", "unique:components,sku"],
            'name' => ["required", "string", "max:255"],
            'brand' => ["required", "string", "max:255"],
            'cost_price' => ["required", "numeric"],
            'sale_price' => ["required", "numeric"],
            'stock' => ["required", "integer"],
            'factory_date' => ["required", "date"],
            'made' => ["required", "string", "max:255"],
            'state' => ["required", "in:active,inactive"],
        ]);

        Component::create([
            'id_category' => $request->id_category,
            'sku' => $request->sku,
            'name' => $request->name,
            'brand' => $request->brand,
            'cost_price' => $request->cost_price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'factory_date' => $request->factory_date,
            'made' => $request->made,
            'state' => $request->state,
        ]);

        return to_route('components.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Component $component)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Component $component)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $componentid)
    {
        $component = Component::findOrFail($componentid);

        $request->validate([
            'id_category' => ["required", "exists:categories,id"],
            'sku' => ["required", "string", "max:100", "unique:components,sku," . $component->id],
            'name' => ["required", "string", "max:255"],
            'brand' => ["required", "string", "max:255"],
            'cost_price' => ["required", "numeric"],
            'sale_price' => ["required", "numeric"],
            'stock' => ["required", "integer"],
            'factory_date' => ["required", "date"],
            'made' => ["required", "string", "max:255"],
            'state' => ["required", "in:active,inactive"],
        ]);

        $payload = [
            'id_category' => $request->id_category,
            'sku' => $request->sku,
            'name' => $request->name,
            'brand' => $request->brand,
            'cost_price' => $request->cost_price,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'factory_date' => $request->factory_date,
            'made' => $request->made,
            'state' => $request->state,
        ];

        $component->update($payload);

        return to_route('components.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(String $componentid)
    {
        $component = Component::findOrFail($componentid);
        $component->delete();

        return to_route('components.index');
    }
}
