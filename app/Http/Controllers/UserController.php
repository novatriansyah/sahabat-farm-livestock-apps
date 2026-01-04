<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): View
    {
        $users = User::paginate(10);
        return view('users.index', compact('users'));
    }

    public function create(Request $request): View
    {
        // Only show partners who don't have a user account yet
        // OR allow the pre-selected one (even if strict check says otherwise, logically it shouldn't have one if link is shown)
        
        $partners = \App\Models\MasterPartner::doesntHave('user')->get();
        // If we have a preselected partner that DOESNT appear in the list (rare race condition or list logic), 
        // strictly speaking we might want to fetch it to append it, but doesn'tHave('user') matches the button logic.
        
        $preselectedRole = $request->query('role', '');
        $preselectedPartnerId = $request->query('partner_id', '');
        
        return view('users.create', compact('partners', 'preselectedRole', 'preselectedPartnerId'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'in:OWNER,STAFF,BREEDER,PARTNER'],
            'partner_id' => ['nullable', 'exists:master_partners,id'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'partner_id' => $request->role === 'PARTNER' ? $request->partner_id : null,
        ]);

        event(new Registered($user));

        return redirect()->route('users.index')->with('success', 'User registered successfully.');
    }

    public function edit(User $user): View
    {
        $partners = \App\Models\MasterPartner::all();
        return view('users.edit', compact('user', 'partners'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:OWNER,STAFF,BREEDER,PARTNER'],
            'partner_id' => ['nullable', 'exists:master_partners,id'],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
        ]);

        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'partner_id' => $validated['role'] === 'PARTNER' ? $validated['partner_id'] : null,
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->withErrors(['msg' => 'Cannot delete yourself.']);
        }

        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
