@extends('sysadmin.layout.auth')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-success">
                <div class="panel-heading">
                    <div><h4>Dashboard</h4> </div>
                    <a href="alta_usuario">
                        <button class="btn btn-primary">
                        Dar de alta usuario
                    </button>
                    </a>
                </div>

                <div class="panel-body">
                    @if(isset($users))
                    <ul class="list-group">
                      
                       @foreach($users as $user)
                            <li class="list-group-item">
                                Nombre : {{$user->name}}
                                <br>
                                Correo Electronico : {{$user->email}}

                            </li>
                            
                        @endforeach
                    </ul>
                       
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
