<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Panel de Seguimiento Metas</title>
        <link rel="stylesheet" type="text/css" href="bootstrap-3.3.6-dist/css/bootstrap.css" />
        <script>
            (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
                m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
            })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

            ga('create', 'UA-88784345-1', 'auto');
            ga('send', 'pageview');

        </script>
    </head>

    <body>
        <div class="container">
            <h1 class="text-center"><a href="http://10.95.17.114/paneles"><img src="paris.png" width="140" height="100"></a>Panel de Seguimiento Metas</h1><br>

            <div class="row">
                <div class="col-lg-11 col-sm-11"><h5 class="text-center text-success" style="margin-left: 200px;"><?php
                        date_default_timezone_set("America/Santiago");
                        $ventas = new mysqli('localhost', 'root', '', 'ventas');
                        $query = "select hora from actualizar";


                        ?></h5>
                </div>

                <div class="col-lg-5 col-sm-5" style="margin-left: 30%;">
                    <select class="form-control">
                        <?php
                        $query = "select periodo, desde, hasta from depto_meta where periodo = 201610 limit 1";
                        $res = $ventas->query($query);

                        $periodo = 0;
                        $desde = 0;
                        $hasta = 0;

                        while($row = mysqli_fetch_assoc($res)){
                            $periodo = $row['periodo'];
                            $desde = $row['desde'];
                            $hasta = $row['hasta'];
                        }

                        echo "<option value='#'>Período $periodo - desde el " . date("d/m/Y", strtotime("{$desde}")) . " hasta el " . date("d/m/Y", strtotime("{$hasta}")) . "</option>";

                        ?>
                    </select>
                </div>
            </div>
        </div><br>

        <div class="container">
        <table class="table table-condensed">
            <thead>
                <tr>
                    <th style="color: white; background-color: #00ABFF;"><h5><b>División / Departamento</b></h5></th>
                    <th style="color: white; background-color: #00ABFF;"><h5><b>Ingreso Neto (Sin IVA)</b></h5></th>
                    <th style="color: white; background-color: #00ABFF;"><h5><b>Meta Venta</b></h5></th>
                    <th style="color: white; background-color: #00ABFF;"><h5><b>% Cumplimiento</b></h5></th>
                </tr>
            </thead>
        <?php

        $inicio = $desde;

        $fin = $hasta;

        $query = "select sum(meta) as meta from depto_meta where periodo = $periodo";

        $res = $ventas->query($query);

        $goal = 0;

        while($row = mysqli_fetch_assoc($res))
            $goal = $row['meta'];

        $query = "select sum(mingresoneto) as mingresoneto from resdepto1 where diaactual between $inicio and $fin";

        $res = $ventas->query($query);

        $total_meta = 0;

        while($row = mysqli_fetch_assoc($res))
            $total_meta = $row['mingresoneto'];

        $cump = number_format(round(($total_meta / $goal) * 100 , 1), 1, ',', '.');

        $total_meta = number_format($total_meta, 0, ',', '.');

        $goal = number_format($goal, 0, ',', '.');

        $label = "";

        if($cump < 0)
            $label = "label label-danger";

        if($cump >= 0 && $cump < 100)
            $label = "label label-warning";

        if($cump >= 100)
            $label = "label label-success";

        echo "<tr style='background-color: #C8D7DF;'><td><h5><b>META TOTAL</b></h5></td>";
        echo "<td><h5 class='text-center'><b>$total_meta</b></h5></td>";
        echo "<td><h5 class='text-center'><b>$goal</b></h5></td>";
        echo "<td class='text-center' style='font-size: 15px;'><h5 class='$label''><b>$cump</b></h5></td></tr>";

        $query = "SELECT dm.depto as depto1, d.nomdepto as nomdepto, d.division as division, dm.meta as meta from depto_meta dm, depto d where dm.depto = d.depto1 and dm.periodo = $periodo group by division, depto1";

        $res = $ventas->query($query);

        $div_temp = "";

        while($row = mysqli_fetch_assoc($res)){
            $division = $row['division'];
            $depto = $row['depto1'];
            $nomdepto = $row['nomdepto'];
            $meta = $row['meta'];

            if($div_temp != $division) {
                $query = "select sum(dm.meta) as meta from depto_meta dm, depto d where d.division = '$division' and dm.depto = d.depto1 and dm.periodo = $periodo";

                $result = $ventas->query($query);

                $meta_div = 0;

                while ($fila = mysqli_fetch_assoc($result))
                    $meta_div = $fila['meta'];

                $query = "select d.depto1 as depto1 from depto_meta dm, depto d where d.division = '$division' and dm.depto = d.depto1 and dm.periodo = $periodo";

                $result = $ventas->query($query);

                $cant = mysqli_num_rows($result);

                $in = "";

                $i = 0;

                while ($fila = mysqli_fetch_assoc($result)) {
                    $in = $in . $fila['depto1'];

                    if ($i < $cant - 1)
                        $in = $in . ", ";

                    $i++;
                }

                $query = "select sum(mingresoneto) as mingresoneto from resdepto1 where depto1 in ($in) and diaactual between $inicio and $fin";

                $result = $ventas->query($query);

                $mingresoneto = 0;

                while ($fila = mysqli_fetch_assoc($result))
                    $mingresoneto = $fila['mingresoneto'];

                $cump = 0;
                if($meta_div != 0)
                    $cump = number_format(round(($mingresoneto / $meta_div) * 100, 1), 1, ',', '.');

                $mingresoneto = number_format($mingresoneto, 0, ',', '.');

                $meta_div = number_format($meta_div, 0, ',', '.');

                $label = "";

                if($cump < 0)
                    $label = "label label-danger";

                if($cump >= 0 && $cump < 100)
                    $label = "label label-warning";

                if($cump >= 100)
                    $label = "label label-success";

                echo '<tr><td><h5><a href="#" style="text-decoration: none;" onclick="mostrar'; echo "('.$division'); return false;"; echo '"><b>' . $division . '</b> <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span></h5></a></td>';
                echo "<td class='text-center'><h5>$mingresoneto</h5></td>";
                echo "<td class='text-center'><h5>$meta_div</h5></td>";
                echo "<td class='text-center' style='font-size: 15px;'><h5 class='$label'>$cump</h5></td></tr>";
            }

            $query = "select sum(mingresoneto) as mingresoneto from resdepto1 where depto1 = $depto and diaactual between $inicio and $fin";

            $result = $ventas->query($query);

            $mingresoneto = 0;

            while($fila = mysqli_fetch_assoc($result))
                $mingresoneto = $fila['mingresoneto'];

            $cump = 0;
            if($meta != 0)
                $cump = round(($mingresoneto / $meta) * 100, 1);

            $mingresoneto = number_format($mingresoneto, 0, ',', '.');

            $cump = number_format($cump, 1, ',', '.');

            $meta = number_format($meta, 0, ',', '.');

            $label = "";

            if($cump < 0)
                $label = "label label-danger";

            if($cump >= 0 && $cump < 100)
                $label = "label label-warning";

            if($cump >= 100)
                $label = "label label-success";

            echo "<tr><td class='$division' style='display:none;'><h5>$depto - $nomdepto</h5></td>";
            echo "<td class='$division' style='display:none;'><h5 class='text-center'>$mingresoneto</h5></td>";
            echo "<td class='$division' style='display:none;'><h5 class='text-center'>$meta</h5></td>";
            echo "<td class='$division text-center' style='display:none; font-size: 15px;'><h5 class='$label'>$cump</h5></td></tr>";

            $div_temp = $division;
        }
        ?>
        </table>
        </div>
        <script src="jquery-1.12.0.min.js"></script>
        <script src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
        <script>
            function mostrar(id){
                var estado = document.querySelectorAll(id);
                var cant   = estado.length;

                for(var i = 0; i < cant; i++){
                    var vista = estado[i].style.display;
                    if(vista == 'none')
                        vista = 'table-cell';
                    else
                        vista = 'none';
                    estado[i].style.display = vista;
                }
            }
        </script>
    </body>
</html>