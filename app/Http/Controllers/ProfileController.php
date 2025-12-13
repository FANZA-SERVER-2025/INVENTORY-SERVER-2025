<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function edit()
    {
        $user = auth()->user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($validated);

        return redirect()->route('profile.edit')
            ->with('success', 'Profile berhasil diperbarui!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required|string',
            'password' => ['required', 'string', Password::min(8)],
        ], [
            'current_password.required' => 'Password saat ini harus diisi',
            'password.required' => 'Password baru harus diisi',
            'password.min' => 'Password minimal 8 karakter',
        ]);

        $user = auth()->user();

        // Check current password
        if (!Hash::check($request->current_password, $user->password)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['current_password' => 'Password saat ini tidak sesuai']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        return redirect()->route('profile.edit')
            ->with('success', 'Password berhasil diperbarui!');
    }
}
