<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $query = User::query()
            ->when($request->search, function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            });

        $sortField = $request->sort ?? 'name';
        $direction = $request->direction ?? 'asc';

        switch ($sortField) {
            case 'name':
                $query->orderBy('name', $direction);
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $users = $query->paginate(10)->withQueryString();
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'photo' => 'nullable|image|max:2048',
            'role' => 'required|in:citizen,admin',
        ]);

        // gerar senha aleatoria com letras, numeros e simbolos
        $randomPassword = Str::password(8, true, true, true);

        $user = new User([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($randomPassword),
            'role' => $validated['role'],
        ]);

        if ($request->hasFile('photo')) {
            $user->profile_photo_path = $request->file('photo')->store('profile-photos', 'public');
        }

        $user->save();

        // Mail::to($user->email)->send(new SendPasswordMail($randomPassword));

        return redirect()->route('users.index')
            ->with('success', "Utilizador com sucesso");
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        $requests = $user->requests()
            ->with('book')
            ->latest()
            ->paginate(10);

        return view('users.show', compact('user', 'requests'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:citizen,admin'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg', 'max:2048'],
        ]);

        // upload da nova foto 
        if ($request->hasFile('photo')) {
            // remove a foto antiga se existir
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }
            $validated['profile_photo'] = $request->file('photo')->store('users', 'public');
        } else {
            // mante, a foto existente se nenhuma nova for enviada
            unset($validated['profile_photo']);
        }

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Utilizador atualizado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        DB::transaction(function () use ($user) {
            //  remove todas as associacoes com requests
            $user->requests()->delete();

            // remove a foto se existir
            if ($user->profile_photo) {
                Storage::disk('public')->delete($user->profile_photo);
            }

            $user->delete();
        });

        return redirect()->route('users.index')
            ->with('success', 'Utilizador removido com sucesso!');
    }
}
