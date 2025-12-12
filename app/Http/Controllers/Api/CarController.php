<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    // Public: List & Search
    public function index(Request $request) {
        $query = Car::with('brand', 'comments');
        
        if($request->brand) $query->whereHas('brand', fn($q) => $q->where('name', 'like', "%$request->brand%"));
        if($request->min_price) $query->where('price_per_day', '>=', $request->min_price);
        if($request->max_price) $query->where('price_per_day', '<=', $request->max_price);

        return response()->json($query->get());
    }

    // Public: Detail
    public function show($id) {
        return response()->json(Car::with('brand', 'comments.user')->findOrFail($id));
    }

    // Admin Only: Create
    public function store(Request $request) {
        $data = $request->validate([
            'name' => 'required',
            'brand_id' => 'required',
            'price_per_day' => 'required|numeric',
            'capacity' => 'required',
            'image' => 'required|image'
        ]);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('cars', 'public');
            $data['image'] = $path;
        }
        
        // Slug auto generate simpel
        $data['slug'] = \Str::slug($data['name']) . '-' . time();

        $car = Car::create($data);
        return response()->json($car, 201);
    }
}