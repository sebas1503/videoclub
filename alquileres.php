<?php include("template/cabecera.php"); ?>
<?php
include("administrador/config/bd.php");

$sentenciaSQL = $conexion->prepare("SELECT * FROM alquileres");
$sentenciaSQL->execute();
$listaAlquileres = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>



<div class="col-md-12">
    <div class="card">

        <table class="table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>ID Cliente</th>
                    <th>ID Pelicula</th>
                    <th>Valor total</th>
                    <th>Fecha inicial</th>
                    <th>Fecha final</th>
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
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>


<?php include("template/pie.php"); ?>