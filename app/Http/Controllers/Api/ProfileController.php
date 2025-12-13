<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        // Validasi
        $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,' . $user->id,
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048', // Max 2MB
        ]);

        // Logic Upload Avatar
        if ($request->hasFile('avatar')) {
            // Hapus avatar lama kalau ada (biar gak numpuk sampah)
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Simpan yang baru
            $path         = $request->file('avatar')->store('avatars', 'public');
            $user->avatar = $path;
        }

        // Update Data Lain
        $user->name  = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully!',
            'user'    => $user,
        ]);
    }
}
