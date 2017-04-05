<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
@if(isset($articulo))
	
	<meta property="og:type"   content="article" />
	<meta property="og:title" content="{{$articulo->titulo}}">
	<meta property="og:description" content="{{$articulo->resumen}}">
	<meta property="og:image" content="{{$articulo->caratula}}">

@endif

</head>

<body>
</body>
<script
  src="https://code.jquery.com/jquery-3.2.1.js"
  integrity="sha256-DZAnKJ/6XZ9si04Hgrsxu/8s717jcIzLy3oi35EouyE="
  crossorigin="anonymous"></script>
<script>
 
 $(document).ready(function(){
 	window.location.assign("{{$articulo->user->extra->dominio}}/articulo/{{$articulo->id}}");
 });

</script>

</html>