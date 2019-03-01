@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Erro 419: Sua sessão expirou.</div>
                <h2>{{ $exception->getMessage() }}</h2>
            </div>
        </div>
    </div>
</div>
@endsectio