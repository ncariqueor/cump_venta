<!DOCTYPE html>
<html>
<head>
<style>
table {
    width: 100%;
    border-collapse: collapse;
}

table, td, th {
    border: 1px solid black;
    padding: 5px;
}

th {text-align: left;}
</style>
</head>
<body>

<?php
$periodo = intval($_GET['periodo']);
echo $periodo;
//$con = mysqli_connect('localhost','peter','abc123','my_db');
$ventas = new mysqli('localhost', 'root', '', 'ventas');
if (!$ventas) {
    die('No se pudo conectar: ' . mysqli_error($ventas));
}

//mysqli_select_db($con,"ajax_demo");
$sql    = "SELECT periodo, desde, hasta FROM depto_meta WHERE periodo >= $periodo GROUP BY periodo";
$res    = mysqli_query($ventas,$sql);
$fecha  = date("Ymd");

if(isset($_GET['periodo'])){
  $get           = $_GET['periodo'];
  $get_aux       = explode("&", $get);
  $periodo_temp  = $get_aux[0];
  $periodo_temp  = substr($periodo_temp, 0, -2);

  while($row = mysqli_fetch_assoc($res)){
    $periodo        = $row['periodo'];
    //obtiene el periodo comercial
    $periodo_aux    = substr($periodo, -2);
    //obtiene el año comercial
    $periodo_temp2  = substr($periodo, 0, -2);
    $desde          = $row['desde'];
    $hasta          = $row['hasta'];
    if($periodo_temp == $periodo_temp2){
      if($fecha >= $desde && $fecha <= $hasta){
        echo "<option value='$periodo&$desde&$hasta' selected='selected'>Periodo comercial $periodo_aux:  " . date("d/m/Y", strtotime("{$desde}")) . " al " . date("d/m/Y", strtotime("{$hasta}")) . "</option>";
      }
      else{
        echo "<option value='$periodo&$desde&$hasta'>Periodo comercial $periodo_aux:  " . date("d/m/Y", strtotime("{$desde}")) . " al " . date("d/m/Y", strtotime("{$hasta}")) . "</option>";
      }
  }
}
}
/*
else{
  $res = $ventas->query($query);

  while($row = mysqli_fetch_assoc($res)){
    $periodo = $row['periodo'];
    $periodo_aux = substr($periodo, -2);
    $desde = $row['desde'];
    $hasta = $row['hasta'];
    echo "<option value='$periodo&$desde&$hasta' selected>Período 1234 comercial $periodo_aux:  " . date("d/m/Y", strtotime("{$desde}")) . " al " . date("d/m/Y", strtotime("{$hasta}")) . "</option>";
  }
}
*/
mysqli_close($ventas);
?>
</body>
</html>
