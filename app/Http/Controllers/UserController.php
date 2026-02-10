<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function show(){
        $users = User::orderBy('name', 'asc')->get();

        return view('usuarios/index', ['users' => $users]);
    }

    public function destroy(Request $request){
        $user = User::findOrFail($request->userId);

        $user->delete();

        session()->flash('mensagem', 'Usuário excluído com sucesso!');
        
        return redirect()->route('usuarios.index');
    }

    public function edit($id){
        $user = User::findOrFail($id);

        return view('usuarios/edit', ['user' => $user]);
    }

    public function update(Request $request, $id){
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'tipo' => 'required|in:admin,medico,tecnico,dev',

            'especialidade' => 'required_if:tipo,medico|max:255',
            'conselho_classe' => 'required_if:tipo,medico|max:255',

            'password' => 'nullable|confirmed|min:8',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'tipo' => $request->tipo,
        ]);

        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
            $user->save();
        }

        if ($request->tipo === 'medico') {
            $user->medico()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'especialidade' => $request->especialidade,
                    'conselho_classe' => $request->conselho_classe,
                ]
            );
        } else {
            if ($user->medico) {
                $user->medico()->delete();
            }
        }

        return redirect()
            ->route('usuarios.index')
            ->with('success', 'Usuário atualizado com sucesso!');

    }
}
