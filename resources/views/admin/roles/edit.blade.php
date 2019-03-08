@extends('layouts.app')

@section('content')
<div class="container-fluid">
  <nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Lista de Operadores</a></li>
      <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Perfis</a></li>
      <li class="breadcrumb-item active" aria-current="page">Alterar Registro</li>
    </ol>
  </nav>
</div>
{{-- avisa se uma permissão foi alterada --}}
  @if(Session::has('edited_role'))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Info!</strong>  {{ session('edited_role') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
  @endif
<div class="container">
  <form method="POST" action="{{ route('roles.update', $role->id) }}">
    @csrf
    @method('PUT')
    <div class="form-row">
      <div class="form-group col-md-6">
        <label for="name">Nome</label>
        <input type="text" class="form-control{{ $errors->has('name') ? ' is-invalid' : '' }}" name="name" value="{{ old('name') ?? $role->name }}">
        @if ($errors->has('name'))
        <div class="invalid-feedback">
        {{ $errors->first('name') }}
        </div>
        @endif
      </div>
      <div class="form-group col-md-6">
        <label for="description">Descrição</label>
        <input type="text" class="form-control{{ $errors->has('description') ? ' is-invalid' : '' }}" name="description" value="{{ old('description') ?? $role->description }}">
        @if ($errors->has('description'))
        <div class="invalid-feedback">
        {{ $errors->first('description') }}
        </div>
        @endif
      </div>
    </div>  
    <button type="submit" class="btn btn-primary"><i class="fas fa-edit"></i> Alterar Dados do Perfil</button>
  </form>

  <div class="float-right">
    <a href="{{ route('roles.index') }}" class="btn btn-secondary btn-sm" role="button"><i class="fas fa-long-arrow-alt-left"></i> Voltar</i></a>
  </div>
</div>
@endsection
