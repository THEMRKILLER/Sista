<!DOCTYPE html>
<html>
<head>
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
			Total :
		</th>
		<th>
			${{$total}}
		</th>
	</tr>
</table>  

</body>
</html>