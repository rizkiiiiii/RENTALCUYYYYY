<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    // Ambil semua merek (Untuk dropdown di form mobil)
    public function index()
    {
        return response()->json(Brand::all());
    }

    // Tambah merek baru
    public function store(Request $request)
    {
        $request->validate(['name' => 'required|unique:brands,name']);
        
        $brand = Brand::create([
            'name' => $request->name,
            'slug' => \Str::slug($request->name)
        ]);

        return response()->json($brand, 201);
    }
}