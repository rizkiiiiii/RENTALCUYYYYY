<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory; // <--- INI YANG TADI HILANG
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function brand() { 
        return $this->belongsTo(Brand::class); 
    }
    
    public function rentals() { 
        return $this->hasMany(Rental::class); 
    }
    
    public function comments() { 
        return $this->hasMany(Comment::class); 
    }
}