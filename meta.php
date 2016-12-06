<?php
$con = odbc_connect('beom', '', '');

$ventas = new mysqli('localhost', 'root', '', 'ventas');

$query = "select meta.tienda, meta.depto, meta.periodo, meta.meta, meta.desde, meta.hasta from meta";

$res = odbc_exec($con, $query);

while(odbc_fetch_row($res)){
    $tienda = odbc_result($res, 1);
    $depto = odbc_result($res, 2);
    $periodo = odbc_result($res, 3);
    $meta = odbc_result($res, 4);
    $desde = odbc_result($res, 5);
    $hasta = odbc_result($res, 6);

    $query = "insert into depto_meta values($tienda, $depto, $periodo, $meta, $desde, $hasta)";

    if($ventas->query($query))
        echo "Se actualizo con exito <br>";
    else
        echo "Hubo errores <br>";
}