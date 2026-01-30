<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class AdminUserController extends Controller
{
    public function manage(Request $request)
    {
        $users = User::where('role', 'user')->get();

        if ($request->isMethod('post')) {
            $validated = $request->validate([
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', Password::defaults()],
                'name' => ['nullable', 'string', 'max:255'],
            ]);

            $admin = $request->user();

            User::create([
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'role' => 'user',
                'company_id' => $admin->company_id,
                'name' => $validated['name'] ?? $validated['email'],
            ]);

            return redirect()->route('admin.users.manage')->with('success', 'Usuario creado exitosamente.');
        }

        return view('admin.users.manage', compact('users'));
    }

    public function edit(User $user)
    {
        return response()->json([
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
        ]);
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'email' => ['required', 'email', 'unique:users,email,' . $user->id],
                'name' => ['nullable', 'string', 'max:255'],
                'password' => ['nullable', Password::defaults()],
            ]);

            $user->email = $validated['email'];
            $user->name = $validated['name'] ?? $user->name;

            if (!empty($validated['password'])) {
                $user->password = Hash::make($validated['password']);
            }

            $user->save();

            if ($request->expectsJson()) {
                return response()->json(['success' => true]);
            }

            return redirect()->route('admin.users.manage')->with('success', 'Usuario actualizado correctamente.');

        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => ['error' => [$e->getMessage()]]], 500);
            }

            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.manage')->with('success', 'Usuario eliminado correctamente.');
    }
}