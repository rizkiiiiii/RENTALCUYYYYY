<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Car;
use Illuminate\Http\Request; // Kita pakai Request bawaan aja
// use App\Http\Requests\StoreRentalRequest; // <-- KITA BUANG INI BIAR GAK RIBET

class RentalController extends Controller
{
    // ADMIN: Lihat semua transaksi
    public function indexAdmin()
    {
        return response()->json(
            Rental::with(['user', 'car'])->latest()->get()
        );
    }

    // USER: Booking Mobil
    public function store(Request $request) 
    {
        // 1. VALIDASI LANGSUNG DI SINI (Tanpa file terpisah)
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
        ]);

        $car = Car::findOrFail($request->car_id);
        
        // 2. Hitung selisih hari
        $start = new \DateTime($request->start_date);
        $end = new \DateTime($request->end_date);
        $days = $end->diff($start)->days;
        
        // Jaga-jaga kalau user iseng input tanggal sama
        if ($days < 1) $days = 1;

        $total = $days * $car->price_per_day;

        // 3. Simpan ke Database
        $rental = Rental::create([
            'user_id' => $request->user()->id,
            'car_id' => $car->id,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'total_price' => $total,
            'status' => 'pending'
        ]);

        // 4. Update status mobil jadi tidak tersedia
        $car->update(['is_available' => false]);

        return response()->json($rental, 201);
    }

    // ADMIN: Update status transaksi
    public function update(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);
        
        $request->validate([
            'status' => 'required|in:pending,paid,active,completed,cancelled'
        ]);

        $rental->update(['status' => $request->status]);

        // Jika status 'active', mobil jadi tidak tersedia
        if ($request->status === 'active') {
            $rental->car()->update(['is_available' => false]);
        }
        // Jika status 'completed' atau 'cancelled', mobil tersedia lagi
        if (in_array($request->status, ['completed', 'cancelled'])) {
            $rental->car()->update(['is_available' => true]);
        }

        return response()->json($rental);
    }
}