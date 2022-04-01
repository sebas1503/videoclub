<?php include("../template/cabecera.php"); ?>
<?php
include("../config/bd.php");

$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtIdCliente = (isset($_POST['txtIdCliente'])) ? $_POST['txtIdCliente'] : "";
$txtIdPelicula = (isset($_POST['txtIdPelicula'])) ? $_POST['txtIdPelicula'] : "";
$txtFechaInicial = (isset($_POST['txtFechaInicial'])) ? $_POST['txtFechaInicial'] : "";
$txtFechaFinal = (isset($_POST['txtFechaFinal'])) ? $_POST['txtFechaFinal'] : "";
$txtTipoPelicula = (isset($_POST['txtTipoPelicula'])) ? $_POST['txtTipoPelicula'] : "";
$txtValorUnitario = (isset($_POST['txtValorUnitario'])) ? $_POST['txtValorUnitario'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";
$txtDias = determinarNumDias($txtFechaInicial, $txtFechaFinal);
$txtValorFinal = calcularPrecio($txtDias, $txtTipoPelicula, $txtValorUnitario);
echo " Valor final: " . $txtValorFinal;


switch ($accion) {
    case "Agregar":

        $error = array();
        if ($txtIdCliente == "") {
            $error[0] = "Error: el ID del cliente no puede estar vacio";
        }
        if ($txtIdPelicula == "") {
            $error[1] = "Error: debe seleccionar una pelicula para alquilar";
        }
        if ($txtFechaInicial == "") {
            $error[2] = "Error: la fecha inicial no puede estar vacía";
        }
        if ($txtFechaFinal == "") {
            $error[3] = "Error: la fecha final no puede estar vacía";
        }
        if (count($error) == 0) {
            $sentenciaSQL = $conexion->prepare("INSERT INTO alquileres(idCliente,idPelicula,valorTotal,fechaInicial,fechaFinal) VALUES (:idCliente,:idPelicula,:valorFinal,:fechaInicial,:fechaFinal);");
            $sentenciaSQL->bindParam(':idCliente', $txtIdCliente);
            $sentenciaSQL->bindParam(':idPelicula', $txtIdPelicula);
            $sentenciaSQL->bindParam(':valorFinal', $txtValorFinal);
            $sentenciaSQL->bindParam(':fechaInicial', $txtFechaInicial);
            $sentenciaSQL->bindParam(':fechaFinal', $txtFechaFinal);
            $sentenciaSQL->execute();

            header("Location:alquileres.php");
        }
        break;

    case "Cancelar":
        header("Location:alquileres.php");
        break;

    case "Borrar":

        $sentenciaSQL = $conexion->prepare("DELETE FROM alquileres WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();

        header("Location:alquileres.php");
        break;
}
$sentenciaSQL = $conexion->prepare("SELECT * FROM peliculas");
$sentenciaSQL->execute();
$listaPeliculas = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

$sentenciaSQL = $conexion->prepare("SELECT * FROM alquileres");
$sentenciaSQL->execute();
$listaAlquileres = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

function determinarNumDias($fechaInicial, $fechaFinal)
{
    $txtDias = (strtotime($fechaInicial) - strtotime($fechaFinal)) / (60 * 60 * 24);
    $txtDias = abs($txtDias);
    $txtDias = floor($txtDias);
    return $txtDias;
}

function calcularPrecio($dias, $tipoPelicula, $valorUnitario)
{
    $valor = 0;
    if ($tipoPelicula == "nuevo lanzamiento" || $tipoPelicula == "Nuevo lanzamiento") {
        $valor = $valorUnitario * $dias;
    } else {
        if ($tipoPelicula == "normal" || $tipoPelicula == "Normal") {
            if ($dias >= 0 && $dias <= 3) {
                $valor = $valorUnitario * $dias;
            } else {
                $diasRestantes = $dias - 3;
                $valor = ($valorUnitario * 3) + (($valorUnitario * $diasRestantes) + ($valorUnitario * $diasRestantes * 0.15));
            }
        } else {
            if ($tipoPelicula == "antigua" || $tipoPelicula == "Antigua") {
                if ($dias >= 0 && $dias <= 5) {
                    $valor = $valorUnitario * $dias;
                } else {
                    $diasRestantes = $dias - 5;
                    $valor = ($valorUnitario * 5) + (($valorUnitario * $diasRestantes) + ($valorUnitario * $diasRestantes * 0.10));
                }
            }
        }
        return $valor;
    }
}

?>


<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            Datos de Alquiler
        </div>
        <div class="card-body">
            <form method="POST">

                <div class="form-group">
                    <input type="hidden" required readonly class="form-control" value="<?php echo $txtID ?>" name="txtID" id="txtID" placeholder="ID" />
                </div>

                <div class="form-group">
                    <label for="txtIdCliente">ID Cliente</label>
                    <input type="text" class="form-control" value="<?php echo $txtIdCliente ?>" name="txtIdCliente" id="txtIdCliente" placeholder="ID Cliente" />
                    <?php if (isset($error[0])) print "<p class='error'>" . $error[0] . "</p>"; ?>
                </div>

                </br>
                <div class="form-group">
                    <label for="txtIdPelicula">ID Pelicula</label>
                    <?php if (isset($error[1])) print "<p class='error'>" . $error[1] . "</p>"; ?><input type="text" required readonly class="form-control" value="<?php echo $txtIdPelicula ?>" name="txtIdPelicula" id="txtIdPelicula" placeholder="ID Pelicula" />
                </div>

                </br>
                <div class="form-group">
                    <label for="txtFechaInicial">Fecha de inicio</label>
                    <input type="text" class="form-control" value="<?php echo $txtFechaInicial ?>" name="txtFechaInicial" id="txtFechaInicial" placeholder="AAAA-MM-DD" />
                    <?php if (isset($error[2])) print "<p class='error'>" . $error[2] . "</p>"; ?>
                </div>

                </br>
                <div class="form-group">
                    <label for="txtFechaFinal">Fecha final</label>
                    <input type="text" class="form-control" value="<?php echo $txtFechaFinal ?>" name="txtFechaFinal" id="txtFechaFinal" placeholder="AAAA-MM-DD" />
                    <?php if (isset($error[3])) print "<p class='error'>" . $error[3] . "</p>"; ?>
                </div>

                </br>
                <div class="form-group">
                    <input type="hidden" class="form-control" value="<?php echo $txtTipoPelicula ?>" name="" id="txtTipoPelicula" placeholder="Tipo pelicula" />
                </div>

                </br>
                <div class="form-group">
                    <input type="hidden" class="form-control" value="<?php echo $txtValorUnitario ?>" name="" id="txtValorUnitario" placeholder="Valor unitario" />
                </div>

                </br>
                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" value="Agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" value="Cancelar" class="btn btn-info">Cancelar</button>
                </div>
            </form>
        </div>

        </br></br>
        <!--Tabla de peliculas -->
        <table class="table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Sinopsis</th>
                    <th>Precio unitario</th>
                    <th>Tipo de pelicula</th>
                    <th>Fecha de estreno</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($listaPeliculas as $pelicula) { ?>
                    <tr>
                        <td><?php echo $pelicula['id']; ?></td>
                        <td><?php echo $pelicula['nombre']; ?></td>
                        <td><?php echo $pelicula['sinopsis']; ?></td>
                        <td><?php echo $pelicula['precioUnitario']; ?></td>
                        <td><?php echo $pelicula['tipoPelicula']; ?></td>
                        <td><?php echo $pelicula['fechaEstreno']; ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="txtIdPelicula" id="txtIdPelicula" value="<?php echo $pelicula['id']; ?>">
                                <input type="hidden" name="txtTipoPelicula" id="txtTipoPelicula" value="<?php echo $pelicula['tipoPelicula']; ?>">
                                <input type="hidden" name="txtValorUnitario" id="txtValorUnitario" value="<?php echo $pelicula['precioUnitario']; ?>">
                                <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary" />
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <!-- Tabla de alquileres -->
        </br></br></br>
        <table class="table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Cliente</th>
                    <th>ID Pelicula</th>
                    <th>Valor total</th>
                    <th>Fecha inicial</th>
                    <th>Fecha final</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            </br>

            <tbody>
                <?php foreach ($listaAlquileres as $alquiler) { ?>
                    <tr>
                        <td><?php echo $alquiler['id']; ?></td>
                        <td><?php echo $alquiler['idCliente']; ?></td>
                        <td><?php echo $alquiler['idPelicula']; ?></td>
                        <td><?php echo $alquiler['valorTotal']; ?></td>
                        <td><?php echo $alquiler['fechaInicial']; ?></td>
                        <td><?php echo $alquiler['fechaFinal']; ?></td>
                        <td>
                            <form method="post">
                                <input type="submit" name="accion" value="Borrar" class="btn btn-danger" />
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    </br></br></br>
</div>

<?php include("../template/pie.php"); ?>