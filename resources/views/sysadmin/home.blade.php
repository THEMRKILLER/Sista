@extends('sysadmin.layout.auth')

@section('content')

<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <div><h4>Dashboard</h4> </div>
                    <a href="altausuario">
                        <button class="btn btn-primary">
                        Dar de alta usuario
                    </button>
                    </a>
                </div>

                <div class="panel-body">
                    @if(isset($users))
                    <ul class="list-group">
                       <table class="table">
                                    <thead>
                                      <tr>
                                        <th>ID</th>
                                        <th>Calendario</th>
                                        <th>Nombre</th>
                                        <th>Correo Electronico</th>
                                        <th>Acción</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            <tr>
                                                <form action="completar" method="post">
                                                <td>
                                                    {{$user->id}}
                                                </td>
                                                <td>
                                                    {{$user->calendario->id}}
                                                </td>
                                                <td>
                                                    {{$user->name}}
                                                </td>
                                                <td>
                                                    {{$user->email}}
                                                </td>
                                                <td>
                                                    @if(isset($user->extra->completo))
                                                        <button type="submit">Completar Proceso Registro</button>
                                                    @else
                                                        <label>Proceso completado</label>
                                                    @endif
                                                    
                                                </td>
                                                </form>
                                            </tr>
                                    
                                        @endforeach
                                     
                                    </tbody>
                        </table>
                       
                    </ul>
                       
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
