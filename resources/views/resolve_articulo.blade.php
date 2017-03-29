<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
@if(isset($articulo))
	<meta property="og:url" content="https://www.creatver.com/sistaarticulo/sistacrimi/{{$articulo->id}}">
	<meta property="og:type"   content="article" />
	<meta property="og:title" content="{{$articulo->titulo}}">
	<meta property="og:description" content="{{$articulo->resumen}}">
	<meta property="og:image" content="{{$articulo->caratula}}">

@endif

</head>

<body>
</body>

</html>