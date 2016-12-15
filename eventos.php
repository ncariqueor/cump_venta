<!DOCTYPE html>
<html lang="es">
    <head>
        <title>Panel de Seguimiento Metas</title>
        <link rel="stylesheet" type="text/css" href="bootstrap-3.3.6-dist/css/bootstrap.css" />
    </head>
    <style>
      select {
       ...
       padding: 0 0 0 20px;
      }
    </style>

    <body>

<!-- INICIO CONTAINER -->
      <div class="container">
          <h1 class="text-center"><a href="http://10.95.17.114/paneles"><img src="paris.png" width="140" height="100"></a>Panel de Seguimiento Metas</h1><br>
          <div class="row">
            <div class="col-lg-11 col-sm-11">
              <h5 class="text-center text-success" style="margin-left: 200px;">
                <?php
                  date_default_timezone_set("America/Santiago");
                  $ventas = new mysqli('localhost', 'root', '', 'ventas');
                  $query  = "select hora from actualizar";
                  $fecha  = date("Ym", strtotime(" -2 months"));
                ?>
              </h5>
            </div>
          </div>

<!-- COMIENZO COMBOBOX -->
          <div class="row">
            <div class="col-sm-6 col-sm-offset-3">
              <div class="row">
                <form>
                  <div class="col-sm-8">
                    <div class="text-center"><span class="label label-primary" style="font-size: 13px;">Evento Comercial</span></div>
                      <div class="row" method="get" action="index.php">
                        <select name="periodo" id="periodo" class="form-control">
                          <?php
                            $query  = "SELECT distinct periodo, desde, hasta from depto_meta GROUP BY periodo";
                            $res    = $ventas->query($query);
                            $fecha  = date("Ymd");

                            if(isset($_GET['periodo'])){
                              $get = $_GET['periodo'];

                              while($row = mysqli_fetch_assoc($res)){
                                $periodo = $row['periodo'];
                                if((int)$periodo == false){
                                  $desde = $row['desde'];
                                  $hasta = $row['hasta'];
                                  echo "<option value='$periodo&$desde&$hasta' selected='selected'>$periodo desde el " . date("d/m/Y", strtotime("{$desde}")) . " al " . date("d/m/Y", strtotime("{$hasta}")) . "</option>";
                                }
                              }
                            }
                            else{
                              $res = $ventas->query($query);

                              while($row = mysqli_fetch_assoc($res)){
                                $periodo = $row['periodo'];
                                if((int)$periodo == false){
                                  $desde        = $row['desde'];
                                  $hasta        = $row['hasta'];
                                  if($fecha >= $desde && $fecha <= $hasta){
                                    echo "<option value='$periodo&$desde&$hasta' selected='selected'>$periodo desde el " . date("d/m/Y", strtotime("{$desde}")) . " al " . date("d/m/Y", strtotime("{$hasta}")) . "</option>";
                                    $desde_ss   = $desde;
                                    $hasta_ss   = $hasta;
                                    $periodo_ss = $periodo;
                                  }
                                  else{
                                    echo "<option value='$periodo&$desde&$hasta'>$periodo desde el " . date("d/m/Y", strtotime("{$desde}")) . " al " . date("d/m/Y", strtotime("{$hasta}")) . "</option>";
                                    $desde_ss   = $desde;
                                    $hasta_ss   = $hasta;
                                    $periodo_ss = $periodo;
                                  }
                                }
                              }
                            }
                          ?>
                        </select>
                      </div>
                    </div>
                  <div class="col-sm-4"><br>
                    <button class="btn btn-primary">Seleccionar evento</button>
                  </div>
                </form>
              </div>
            </div>

            <div class="col-sm-2"><br>
              <div class="dropdown">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  Seleccione Tipo de Panel
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                  <li><a href="index.php">Año comercial</a></li>
                  <li><a href="eventos.php">Evento comercial</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div><br>

<!-- COMIENZO LLENADO TABLA -->
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
          if(isset($_GET['periodo'])){
            $get      = $_GET['periodo'];
            $get_aux  = explode("&", $get);
            $periodo  = $get_aux[0];
            $desde    = $get_aux[1];
            $hasta    = $get_aux[2];
            $inicio   = $desde;
            $fin      = $hasta;
          }
          else{
            $inicio   = $desde_ss;
            $fin      = $hasta_ss;
            $periodo  = $periodo_ss;
          }

          // variables para el manejo de OTROS
          $otros  = 0;
          $deptos = array();
          $k      = 0;

          $query = "select sum(meta) as meta from depto_meta where periodo = '$periodo'";

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

          $query = "SELECT dm.depto as depto1, d.nomdepto as nomdepto, d.division as division, dm.meta as meta from depto_meta dm, depto d where dm.depto = d.depto1 and dm.periodo = '$periodo' group by division, depto1";

          $res = $ventas->query($query);

          $div_temp = "";

          while($row = mysqli_fetch_assoc($res)){
              $division = $row['division'];
              $depto = $row['depto1'];
              $nomdepto = $row['nomdepto'];
              $meta = $row['meta'];

              if($div_temp != $division) {
                  $query = "select sum(dm.meta) as meta from depto_meta dm, depto d where d.division = '$division' and dm.depto = d.depto1 and dm.periodo = '$periodo'";

                  $result = $ventas->query($query);

                  $meta_div = 0;

                  while ($fila = mysqli_fetch_assoc($result))
                      $meta_div = $fila['meta'];

                  $query = "select d.depto1 as depto1 from depto_meta dm, depto d where d.division = '$division' and dm.depto = d.depto1 and dm.periodo = '$periodo'";

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

                  if($division == 'OTROS'){
                    $otros = $division."-".$mingresoneto."-".$meta_div."-".$cump;
                  }else{
                    echo '<tr><td><h5><a href="#" style="text-decoration: none;" onclick="mostrar'; echo "('.$division'); return false;"; echo '"><b>' . $division . '</b> <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span></h5></a></td>';
                    echo "<td class='text-center'><h5>$mingresoneto</h5></td>";
                    echo "<td class='text-center'><h5>$meta_div</h5></td>";
                    echo "<td class='text-center' style='font-size: 15px;'><h5 class='$label'>$cump</h5></td></tr>";
                  }
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

              if($depto == 706 || $depto == 732){
                $deptos[$k] = $depto."-".$nomdepto."-".$mingresoneto."-".$meta."-".$cump;
                $k++;
              }
              else{
                echo "<tr><td class='$division' style='display:none;'><h5>$depto - $nomdepto</h5></td>";
                echo "<td class='$division' style='display:none;'><h5 class='text-center'>$mingresoneto</h5></td>";
                echo "<td class='$division' style='display:none;'><h5 class='text-center'>$meta</h5></td>";
                echo "<td class='$division text-center' style='display:none; font-size: 15px;'><h5 class='$label'>$cump</h5></td></tr>";
              }
              $div_temp = $division;
            }
// cambio de OTROS para el final de la lista
            $otros_aux      = explode("-", $otros);
            $division       = $otros_aux[0];
            $mingresoneto   = $otros_aux[1];
            $meta_div       = $otros_aux[2];
            $cump           = $otros_aux[3];
            echo '<tr><td><h5><a href="#" style="text-decoration: none;" onclick="mostrar'; echo "('.$division'); return false;"; echo '"><b>' . $division . '</b> <span class="glyphicon glyphicon-collapse-down" aria-hidden="true"></span></h5></a></td>';
            echo "<td class='text-center'><h5>$mingresoneto</h5></td>";
            echo "<td class='text-center'><h5>$meta_div</h5></td>";
            echo "<td class='text-center' style='font-size: 15px;'><h5 class='$label'>$cump</h5></td></tr>";

            for($i = 0; $i < count($deptos); $i++){
              $deptos_aux   = $deptos[$i];
              $deptos_aux   = explode("-", $deptos_aux);
              $depto        = $deptos_aux[0];
              $nom_depto    = $deptos_aux[1];
              $mingresoneto = $deptos_aux[2];
              $meta         = $deptos_aux[3];
              $cump         = $deptos_aux[4];
              echo "<tr><td class='$division' style='display:none;'><h5>$depto - $nomdepto</h5></td>";
              echo "<td class='$division' style='display:none;'><h5 class='text-center'>$mingresoneto</h5></td>";
              echo "<td class='$division' style='display:none;'><h5 class='text-center'>$meta</h5></td>";
              echo "<td class='$division text-center' style='display:none; font-size: 15px;'><h5 class='$label'>$cump</h5></td></tr>";
            }
          ?>
        </table>
      </div>


<!-- INICIO FOOTER -->
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
