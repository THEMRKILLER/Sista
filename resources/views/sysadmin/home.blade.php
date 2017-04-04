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
                                                     {{ csrf_field() }}
                                                    <input type="hidden" name="calendario" value="{{$user->calendario->id}}">
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
                                                        @if($user->extra->completo)
                                                            <label>Proceso Completado</label>
                                                        @else
                                                            <button type="submit">Completar Proceso Registro</button>
                                                        @endif
                                                    @else
                                                        <label>Proceso Completado</label>
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
