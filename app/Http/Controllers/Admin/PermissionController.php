<?php

namespace App\Http\Controllers\Admin;

use App\Permission;
use App\Perpage;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;

class PermissionController extends Controller
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
        $permissions = new Permission;

        // filtros
        if (request()->has('name')){
            $permissions = $permissions->where('name', 'like', '%' . request('name') . '%');
        }

        if (request()->has('description')){
            $permissions = $permissions->where('description', 'like', '%' . request('description') . '%');
        }

        // ordena
        $permissions = $permissions->orderBy('name', 'asc');

        // se a requisição tiver um novo valor para a quantidade
        // de páginas por visualização ele altera aqui
        if(request()->has('perpage')) {
            session(['perPage' => request('perpage')]);
        }

        // consulta a tabela perpage para ter a lista de
        // quantidades de paginação
        $perpages = Perpage::orderBy('valor')->get();

        // paginação
        $permissions = $permissions->paginate(session('perPage'))->appends([          
            'name' => request('name'),
            'description' => request('description'),           
            ]);

        return view('admin.permissions.index', compact('permissions', 'perpages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.permissions.create');
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
          'description' => 'required',
        ]);

        $permission = $request->all();

        Permission::create($permission); //salva

        Session::flash('create_permission', 'Permissão cadastrada com sucesso!');

        return redirect(route('permissions.index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // permissão que será exibido e pode ser excluido
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.show', compact('permission'));
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
        $permission = Permission::findOrFail($id);

        return view('admin.permissions.edit', compact('permission'));
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
          'description' => 'required',
        ]);

        $permission = Permission::findOrFail($id);
            
        $permission->update($request->all());
        
        Session::flash('edited_permission', 'Permissão alterada com sucesso!');

        return redirect(route('permissions.edit', $id));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Permission::findOrFail($id)->delete();

        Session::flash('deleted_permission', 'Permissão excluída com sucesso!');

        return redirect(route('permissions.index'));
    }
}
