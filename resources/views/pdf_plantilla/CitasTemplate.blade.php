<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<meta charset="utf-8">
<style>
table {
    border-collapse: collapse;
    width: 100%;
}

th, td {
    text-align: left;
    padding: 8px;
}

tr:nth-child(even){background-color: #f2f2f2}

th {
    background-color: #4CAF50;
    color: white;
}
</style>
</head>
<body>

<div align="center">
	<h2>Informe de citas</h2>
	<h4>Periodo : del  {{$inicio}} al {{$final}} </h4>
</div>
<table>
  <tr>
    <th>Nombre </th>
    <th>Servicio</th>
    <th>Fecha</th>
    <th>Costo</th>
  </tr>
  @foreach($citas as $cita)
  	<tr>
  		<td>
  			{{$cita->cliente_nombre}}
  		</td>
  		<td>
			{{$cita->tipo->nombre}}  			
  		</td>
  		<td>
  			{{$cita->fecha_inicio}}

  		</td>
  		<td>
  			{{$cita->costo}}
  		</td>
  	</tr>
@endforeach

</table>

<table>
	<tr>
		<th>
			Total Citas
		</th>
		<th>
			Total Costo
		</th>
		
	</tr>
	<tr>
		<td>
			{{count($citas)}}
		</td>
		<td>
			${{$total}}
		</td>
	</tr>
</table>  

</body>
</html>