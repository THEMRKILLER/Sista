<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta charset="utf-8">
</head>
<body>

<h4>
  Restablecimiento de contraseña
</h4>
<br>
Hola {{$usuario->name}} se ha realizado una petición para restablecer tu contraseña, a continuación te enviamos la siguiente url para hacer dicha acción, esta url expira en 2 horas a partir de la fecha y hora de la expedición de este correo electrónico : <a href="{{$url}}" target="_blank"> Url para restablecer contraseña</a> 
 
</body>
</html>