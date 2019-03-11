<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Perpage;
use App\Role;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    /**
     * Construtor.
     *
     * precisa estar logado ao sistema
     * precisa ter a conta ativa (access)
     *
     * @return 
     */
    public function __construct()
    {
        $this->middleware(['middleware' => 'auth']);
        $this->middleware(['middleware' => 'hasaccess']);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = new User;

        // filtros
        if (request()->has('name')){
            $users = $users->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('email')){
            $users = $users->where('email', 'like', '%' . request('email') . '%');
        }

        // ordena
        $users = $users->orderBy('name', 'asc');        

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $users = $users->paginate(session('perPage'))->appends([          
            'name' => request('name'),
            'email' => request('email'),           
            ]);

        return view('admin.users.index', compact('users', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // listagem de perfis (roles)
        $roles = Role::orderBy('description','asc')->get();

        return view('admin.users.create', compact('roles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
          'name' => 'required',
          'email' => 'required|email|unique:users,email',
          'password' => 'required|min:6|confirmed'
        ]);

        $user = $request->all();
        $user['active'] = 'Y'; // torna o novo registro ativo
        $user['password'] = Hash::make($user['password']); // criptografa a senha

        $newUser = User::create($user); //salva

        // salva os perfis (roles)
        if(isset($user['roles']) && count($user['roles'])){
            foreach ($user['roles'] as $key => $value) {
                $newUser->roles()->attach($value);
            }

        }    

        Session::flash('create_user', 'Operador cadastrado com sucesso!');

        return redirect(route('users.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // usuário que será exibido e pode ser excluido
        $user = User::findOrFail($id);

        return view('admin.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // usuário que será alterado
        $user = User::findOrFail($id);

        // listagem de perfis (roles)
        $roles = Role::orderBy('description','asc')->get();

        return view('admin.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validate($request, [
          'name' => 'required',
          'email' => 'required|email',
        ]);

        $user = User::findOrFail($id);

        // atualiza a senha do usuário se esse campo tiver sido preenchido
        if ($request->has('password')) {
            $input = $request->all();
            $input['password'] = Hash::make($input['password']);
        } else {
            $input = $request->except('password');
        }   

        // configura se operador está habilitado ou não a usar o sistema
        if (isset($input['active'])) {
            $input['active'] = 'Y';
        } else {
            $input['active'] = 'N';
        }

        // remove todos os perfis vinculados a esse operador
        $roles = $user->roles;
        if(count($roles)){
            foreach ($roles as $key => $value) {
               $user->roles()->detach($value->id);
            }
        }

        // vincula os novos perfis desse operador
        if(isset($input['roles']) && count($input['roles'])){
            foreach ($input['roles'] as $key => $value) {
               $user->roles()->attach($value);
            }
        }

        $user->update($input);
        
        Session::flash('edited_user', 'Operador alterado com sucesso!');

        return redirect(route('users.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        User::findOrFail($id)->delete();

        Session::flash('deleted_user', 'Operador excluído com sucesso!');

        return redirect(route('users.index'));
    }
}
