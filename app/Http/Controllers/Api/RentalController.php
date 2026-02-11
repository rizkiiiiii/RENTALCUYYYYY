<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Car;
use Illuminate\Http\Request;

class RentalController extends Controller
{
    // USER: Lihat riwayat booking milik sendiri
    public function index(Request $request)
    {
        // Ambil data rental CUMA milik user yang lagi login
        $rentals = Rental::with(['car', 'car.brand']) // Eager load brand juga biar lengkap
            ->where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return response()->json($rentals);
    }

    // ADMIN: Lihat semua transaksi
    public function indexAdmin()
    {
        return response()->json(
            Rental::with(['user', 'car', 'car.brand'])->latest()->get()
        );
    }

    // USER: Booking Mobil
    public function store(Request $request)
    {
        $request->validate([
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date', // Boleh sama hari (sewa 1 hari)
        ]);

        $car = Car::findOrFail($request->car_id);

        // 1. Cek Ketersediaan (Date Overlap Logic)
        // Rumus Overlap: (StartA <= EndB) and (EndA >= StartB)
        // Kita cari booking yang "overlap" dengan request user. Kalau ada, berarti GAK BISA sewa.
        $isBooked = Rental::where('car_id', $car->id)
            ->whereIn('status', ['pending', 'paid', 'active']) // Hanya status aktif yang nge-blok jadwal
            ->where(function ($query) use ($request) {
            $query->whereBetween('start_date', [$request->start_date, $request->end_date])
                ->orWhereBetween('end_date', [$request->start_date, $request->end_date])
                ->orWhere(function ($q) use ($request) {
                $q->where('start_date', '<', $request->start_date)
                    ->where('end_date', '>', $request->end_date);
            }
            );
        })
            ->exists();

        if ($isBooked) {
            return response()->json([
                'message' => 'Mobil tidak tersedia pada tanggal tersebut. Silahkan pilih tanggal lain.'
            ], 422);
        }

        // 2. Hitung Total Harga
        $start = new \DateTime($request->start_date);
        $end = new \DateTime($request->end_date);
        // Tambah 1 hari karena kalau tgl 10-10 itu dihitung 1 hari sewa
        $days = $end->diff($start)->days + 1;

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

        // JANGAN update $car->is_available = false di sini. 
        // Biarkan mobil tetap "available" secara global, tapi "booked" di tanggal tertentu.

        return response()->json([
            'message' => 'Booking berhasil dibuat.',
            'data' => $rental
        ], 201);
    }

    // ADMIN: Update status transaksi
    public function update(Request $request, $id)
    {
        $rental = Rental::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,paid,active,completed,cancelled'
        ]);

        $rental->update(['status' => $request->status]);

        // Kita tidak perlu mainin flag is_available di table cars lagi.
        // Availability murni dicek dari jadwal rental.

        return response()->json([
            'message' => 'Status rental berhasil diperbarui.',
            'data' => $rental
        ]);
    }

    public function destroy(string $id)
    {
        $rental = Rental::find($id);

        if (!$rental) {
            return response()->json(['message' => 'Data not found'], 404);
        }

        // Hanya boleh cancel kalau status masih pending
        if ($rental->status !== 'pending') {
            return response()->json(['message' => 'Tidak bisa membatalkan pesanan yang sudah diproses.'], 400);
        }

        $rental->delete();

        return response()->json(['message' => 'Booking berhasil dibatalkan.']);
    }
}