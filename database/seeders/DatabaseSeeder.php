<?php

namespace Database\Seeders;
use App\Models\User;
use App\Models\Brand;
use App\Models\Car;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // 1. Admin Boss
        User::create([
            'name' => 'Big Boss',
            'email' => 'boss@neorental.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'admin_type' => 'boss',
        ]);

        // 2. Admin Pegawai
        User::create([
            'name' => 'Staff Satu',
            'email' => 'staff@neorental.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'admin_type' => 'employee',
        ]);

        // 3. User Penyewa
        User::create([
            'name' => 'Gen Z Renter',
            'email' => 'user@gmail.com',
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        // 4. Brands & Cars
        $toyota = Brand::create(['name' => 'Toyota', 'slug' => 'toyota']);
        $honda = Brand::create(['name' => 'Honda', 'slug' => 'honda']);

        Car::create([
            'brand_id' => $toyota->id,
            'name' => 'Toyota Raize GR Sport',
            'slug' => 'toyota-raize-gr',
            'price_per_day' => 450000,
            'capacity' => 5,
            'is_available' => true,
            'image' => 'cars/raize.jpg' // Asumsi nanti di upload
        ]);

        Car::create([
            'brand_id' => $honda->id,
            'name' => 'Honda Civic Turbo',
            'slug' => 'honda-civic-turbo',
            'price_per_day' => 1200000,
            'capacity' => 4,
            'is_available' => true,
            'image' => 'cars/civic.jpg'
        ]);
    }
}