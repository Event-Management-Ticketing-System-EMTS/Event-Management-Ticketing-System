<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return view('user.profile', ['user' => $request->user()]);
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'avatar'            => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            // password change is optional, but if provided current_password must match
            'current_password'  => ['nullable', 'required_with:password', 'current_password'],
            'password'          => ['nullable', 'confirmed', 'min:8'],
        ]);

        // --- Avatar handling ---
        $avatarPath = $user->avatar_path;
        if ($request->hasFile('avatar')) {
            if ($avatarPath) {
                Storage::disk('public')->delete($avatarPath);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
        }

        // --- Build update payload ---
        $data = [
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'avatar_path'  => $avatarPath,
        ];

        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        // One-shot persist (no ->save())
        $user->update($data);

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Profile updated successfully.');
    }
}
