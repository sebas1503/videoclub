<?php include("../template/cabecera.php"); ?>
<?php
$txtID = (isset($_POST['txtID'])) ? $_POST['txtID'] : "";
$txtNombre = (isset($_POST['txtNombre'])) ? $_POST['txtNombre'] : "";
$txtSinopsis = (isset($_POST['txtSinopsis'])) ? $_POST['txtSinopsis'] : "";
$txtPrecioUnitario = (isset($_POST['txtPrecioUnitario'])) ? $_POST['txtPrecioUnitario'] : "";
$txtTipoPelicula = (isset($_POST['txtTipoPelicula'])) ? $_POST['txtTipoPelicula'] : "";
$txtFechaEstreno = (isset($_POST['txtFechaEstreno'])) ? $_POST['txtFechaEstreno'] : "";
$txtImagen = (isset($_FILES['txtImagen']['name'])) ? $_FILES['txtImagen']['name'] : "";
$accion = (isset($_POST['accion'])) ? $_POST['accion'] : "";

include("../config/bd.php");

switch ($accion) {

    case "Agregar":

        $error = array();
        if ($txtNombre == "") {
            $error[0] = "Error: el nombre de la pelicula no puede estar vacío";
        }
        if ($txtSinopsis == "") {
            $error[1] = "Error: la sinopsis no puede estar vacía";
        }
        if ($txtPrecioUnitario == "") {
            $error[2] = "Error: El precio unitario es requerido";
        }
        if (!is_numeric($txtPrecioUnitario)) {
            $error[3] = "Error: El precio unitario debe ser de tipo numerico";
        }
        if ($txtTipoPelicula == "") {
            $error[4] = "Error: el tipo de pelicula es requerido";
        }
        if (!($txtTipoPelicula == "nuevo lanzamiento" || $txtTipoPelicula == "Nuevo lanzamiento"
            || $txtTipoPelicula == "normal" || $txtTipoPelicula == "Normal"
            || $txtTipoPelicula == "antigua" || $txtTipoPelicula == "Antigua")) {
            $error[5] = "Error: el tipo de pelicula es incorrecto";
        }
        if ($txtFechaEstreno == "") {
            $error[6] = "Error: la fecha de estreno es requerida";
        }

        if ($txtImagen == "") {
            $error[7] = "Error: la imagen es requerida";
        }

        if (count($error) == 0) {
            $sentenciaSQL = $conexion->prepare("INSERT INTO peliculas(nombre,sinopsis,precioUnitario,tipoPelicula,fechaEstreno,imagen) VALUES (:nombre,:sinopsis,:precioUnitario,:tipoPelicula,:fechaEstreno,:imagen);");
            $sentenciaSQL->bindParam(':nombre', $txtNombre);
            $sentenciaSQL->bindParam(':sinopsis', $txtSinopsis);
            $sentenciaSQL->bindParam(':precioUnitario', $txtPrecioUnitario);
            $sentenciaSQL->bindParam(':tipoPelicula', $txtTipoPelicula);
            $sentenciaSQL->bindParam(':fechaEstreno', $txtFechaEstreno);

            $fecha = new DateTime();
            $nombreArchivo = ($txtImagen != "") ? $fecha->getTimestamp() . "_" . $_FILES["txtImagen"]["name"] : "imagen.jpg";
            $tmpImagen = $_FILES["txtImagen"]["tmp_name"];

            if ($tmpImagen != "") {
                move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);
            }

            $sentenciaSQL->bindParam(':imagen', $nombreArchivo);
            $sentenciaSQL->execute();
            header("Location:peliculas.php");
        }
        break;

    case "Modificar":

        $sentenciaSQL = $conexion->prepare("UPDATE peliculas SET nombre=:nombre, sinopsis=:sinopsis, precioUnitario=:precioUnitario, tipoPelicula=:tipoPelicula, fechaEstreno=:fechaEstreno  WHERE id=:id");
        $sentenciaSQL->bindParam(':nombre', $txtNombre);
        $sentenciaSQL->bindParam(':sinopsis', $txtSinopsis);
        $sentenciaSQL->bindParam(':precioUnitario', $txtPrecioUnitario);
        $sentenciaSQL->bindParam(':tipoPelicula', $txtTipoPelicula);
        $sentenciaSQL->bindParam(':fechaEstreno', $txtFechaEstreno);
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();

        if ($txtImagen != "") {
            $fecha = new DateTime();
            $nombreArchivo = ($txtImagen != "") ? $fecha->getTimestamp() . "_" . $_FILES["txtImagen"]["name"] : "imagen.jpg";
            $tmpImagen = $_FILES["txtImagen"]["tmp_name"];
            move_uploaded_file($tmpImagen, "../../img/" . $nombreArchivo);

            $sentenciaSQL = $conexion->prepare("SELECT imagen FROM peliculas WHERE id=:id");
            $sentenciaSQL->bindParam(':id', $txtID);
            $sentenciaSQL->execute();
            $pelicula = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

            if (isset($pelicula["imagen"]) && ($pelicula["imagen"] != "imagen.jpg")) {
                if (file_exists("../../img/" . $pelicula["imagen"])) {
                    unlink(("../../img/" . $pelicula["imagen"]));
                }
            }

            $sentenciaSQL = $conexion->prepare("UPDATE peliculas SET imagen=:imagen WHERE id=:id");
            $sentenciaSQL->bindParam(':imagen', $nombreArchivo);
            $sentenciaSQL->bindParam(':id', $txtID);
            $sentenciaSQL->execute();
        }
        header("Location:peliculas.php");
        break;

    case "Cancelar":
        header("Location:peliculas.php");
        break;

    case "Seleccionar":
        $sentenciaSQL = $conexion->prepare("SELECT * FROM peliculas WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        $pelicula = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

        $txtNombre = $pelicula['nombre'];
        $txtSinopsis = $pelicula['sinopsis'];
        $txtPrecioUnitario = $pelicula['precioUnitario'];
        $txtTipoPelicula = $pelicula['tipoPelicula'];
        $txtFechaEstreno = $pelicula['fechaEstreno'];
        $txtImagen = $pelicula['imagen'];
        break;

    case "Borrar":
        $sentenciaSQL = $conexion->prepare("SELECT imagen FROM peliculas WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        $pelicula = $sentenciaSQL->fetch(PDO::FETCH_LAZY);

        if (isset($pelicula["imagen"]) && ($pelicula["imagen"] != "imagen.jpg")) {
            if (file_exists("../../img/" . $pelicula["imagen"])) {
                unlink(("../../img/" . $pelicula["imagen"]));
            }
        }

        $sentenciaSQL = $conexion->prepare("DELETE FROM peliculas WHERE id=:id");
        $sentenciaSQL->bindParam(':id', $txtID);
        $sentenciaSQL->execute();
        header("Location:peliculas.php");
        break;
} //fin switch

$sentenciaSQL = $conexion->prepare("SELECT * FROM peliculas");
$sentenciaSQL->execute();
$listaPeliculas = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>

<div class="col-md-12">
    <div class="card">
        <div class="card-header">
            Datos de Pelicula
        </div>
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">

                <div class="form-group">
                    <label for="txtID">ID</label>
                    <input type="text" required readonly class="form-control" value="<?php echo $txtID ?>" name="txtID" id="txtID" placeholder="ID" />
                </div>

                <div class="form-group">
                    <label for="txtNombre">Nombre</label>
                    <input type="text" class="form-control" value="<?php echo $txtNombre ?>" name="txtNombre" id="txtNombre" placeholder="Nombre" />
                    <?php if (isset($error[0])) print "<p class='error'>" . $error[0] . "</p>"; ?>
                </div>

                <div class="form-group">
                    <label for="txtSinopsis">Sinopsis</label>
                    <textarea class="form-control" value="<?php echo $txtSinopsis ?>" name="txtSinopsis" id="txtSinopsis" placeholder="Sinopsis"></textarea>
                    <?php if (isset($error[1])) print "<p class='error'>" . $error[1] . "</p>"; ?>
                </div>

                <div class="form-group">
                    <label for="txtPrecioUnitario">Precio unitario</label>
                    <input type="text" class="form-control" value="<?php echo $txtPrecioUnitario ?>" name="txtPrecioUnitario" id="txtPrecioUnitario" placeholder="Precio unitario" />
                    <?php if (isset($error[2])) print "<p class='error'>" . $error[2] . "</p>"; ?>
                    <?php if (isset($error[3])) print "<p class='error'>" . $error[3] . "</p>"; ?>
                </div>

                <br />
                <div class="form-group">
                    <label for="txtTipoPelicula">Tipo de pelicula</label>
                    <input type="text" class="form-control" value="<?php echo $txtTipoPelicula ?>" name="txtTipoPelicula" id="txtTipoPelicula" placeholder=' "nuevo lanzamiento" ó "normal" ó "antigua"' />
                    <?php if (isset($error[4])) print "<p class='error'>" . $error[4] . "</p>"; ?>
                    <?php if (isset($error[5])) print "<p class='error'>" . $error[5] . "</p>"; ?>
                </div>
                </br>

                <div class="form-group">
                    <label for="txtFechaEstreno">Fecha de estreno</label>
                    <input type="text" class="form-control" value="<?php echo $txtFechaEstreno ?>" name="txtFechaEstreno" id="txtFechaEstreno" placeholder="AAAA-MM-DD" />
                    <?php if (isset($error[6])) print "<p class='error'>" . $error[6] . "</p>"; ?>
                </div>

                <div class="form-group">
                    <label for="txtImagen">Imagen: </label>

                    </br>
                    <?php if ($txtImagen != "") { ?>
                        <img class="img-thumbnail rounded" src="../../img/<?php echo $txtImagen; ?>" width="50" alt="">
                    <?php  } ?>

                    <input type="file" class="form-control" name="txtImagen" id="txtImagen" />
                    <?php if (isset($error[7])) print "<p class='error'>" . $error[7] . "</p>"; ?>
                </div>

                <br />
                <div class="btn-group" role="group" aria-label="">
                    <button type="submit" name="accion" <?php echo ($accion == "Seleccionar") ? "disabled" : ""; ?> value="Agregar" class="btn btn-success">Agregar</button>
                    <button type="submit" name="accion" <?php echo ($accion != "Seleccionar") ? "disabled" : ""; ?> value="Modificar" class="btn btn-warning">Modificar</button>
                    <button type="submit" name="accion" <?php echo ($accion != "Seleccionar") ? "disabled" : ""; ?> value="Cancelar" class="btn btn-info">Cancelar</button>
                </div>
            </form>
        </div>


        <!--Tabla para mostrar datos -->
        <br />
        <table class="table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Sinopsis</th>
                    <th>Precio unitario</th>
                    <th>Tipo de pelicula</th>
                    <th>Fecha de estreno</th>
                    <th>Imagen</th>
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
                            <img class="img-thumbnail rounded" src="../../img/<?php echo $pelicula['imagen']; ?>" width="50" alt="">
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="txtID" id="txtID" value="<?php echo $pelicula['id']; ?>">
                                <input type="submit" name="accion" value="Seleccionar" class="btn btn-primary" />
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