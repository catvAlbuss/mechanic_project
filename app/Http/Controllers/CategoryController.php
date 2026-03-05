<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Provider;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::all();
        $providers = Provider::all();
        return Inertia::render('categories/index', [
            'categories' => $categories,
            'providers' => $providers,
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
            'id_provider' => ["required", "exists:providers,id"],
            'name' => ["required", "string", "max:255"],
            'brand' => ["required", "string", "max:255"],
            'description' => ["required", "string", "max:255"],
        ]);

        Category::create([
            'id_provider' => $request->id_provider,
            'name' => $request->name,
            'brand' => $request->brand,
            'description' => $request->description,
        ]);

        return to_route('categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, String $categoryid)
    {
        $category = Category::findOrFail($categoryid);

        $validateData = $request->validate([
            "id_provider"=> ["required", "exists:providers,id"],
            "name"=> ["required", "string", "max:255"],
            "brand"=>["required", "string", "max:255"],
            "description"=>["required", "string", "max:255"],
            "state"=>["required", "in:active,inactive"],
        ]);

        $payload = [
            "id_provider"=>$validateData["id_provider"],
            "name"=>$validateData["name"],
            "brand"=>$validateData["brand"],
            "description"=>$validateData["description"],
            "state"=>$validateData["state"],
        ];

        $category->update($payload);

        return to_route('categories.index');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy( String $categoryid)
    {
        $category = Category::query()->findOrFail($categoryid);
        $category->delete();

        return to_route('categories.index');
    }
}
