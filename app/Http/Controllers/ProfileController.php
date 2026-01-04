<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = auth()->user();
        $votingHistory = $user->votes()
            ->with('election')
            ->latest()
            ->paginate(10);
            
        return view('profile.show', compact('user', 'votingHistory'));
    }
    
    /**
     * Show the form for editing the profile.
     */
    public function edit()
    {
        return view('profile.edit', ['user' => auth()->user()]);
    }
    
    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone_number' => 'nullable|string|max:20',
            'bio' => 'nullable|string|max:500',
            'email_notifications' => 'nullable|boolean',
            'sms_notifications' => 'nullable|boolean',
        ]);
        
        // Convert checkboxes to boolean
        $validated['email_notifications'] = $request->has('email_notifications');
        $validated['sms_notifications'] = $request->has('sms_notifications');
        
        $user->update($validated);
        
        return redirect()->route('profile.show')
            ->with('success', 'Profile updated successfully!');
    }
    
    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required',
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);
        
        if (!Hash::check($validated['current_password'], auth()->user()->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect']);
        }
        
        auth()->user()->update([
            'password' => Hash::make($validated['password'])
        ]);
        
        return back()->with('success', 'Password updated successfully!');
    }
    
    /**
     * Update the user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|max:2048' // 2MB max
        ]);
        
        $user = auth()->user();
        
        // Delete old avatar if exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
        
        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update(['avatar' => $path]);
        
        return back()->with('success', 'Avatar updated successfully!');
    }
    
    /**
     * Delete the user's avatar.
     */
    public function deleteAvatar()
    {
        $user = auth()->user();
        
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->update(['avatar' => null]);
        }
        
        return back()->with('success', 'Avatar removed successfully!');
    }
}
