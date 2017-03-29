<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
@if(isset($articulo))
	<meta property="og:url" content="www.creatver.com/sistaarticulo/sistacrimi/{{$articulo->id}}">
	<meta property="og:title" content="{{$articulo->titulo}}">
	<meta property="og:image" content="{{$articulo->caratula}}">
	<meta property="og:image:secure_url" content="{{$articulo->caratula}}">
	<meta property="og:image:type" content="image/EXT">
	<meta property="og:description" content="{{$articulo->resumen}}">
	<meta property="og:type" content="article"/>
@endif

</head>

<body>
</body>

</html>