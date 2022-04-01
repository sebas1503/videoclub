<?php include("template/cabecera.php"); ?>

<?php 
include("administrador/config/bd.php"); 

$sentenciaSQL = $conexion->prepare("SELECT * FROM peliculas");
$sentenciaSQL->execute();
$listaPeliculas = $sentenciaSQL->fetchAll(PDO::FETCH_ASSOC);

?>

<?php foreach($listaPeliculas as $pelicula) {?>

<div class="col-md-2">
    <div class="card">
        <img class="card-img-top" src="./img/<?php echo $pelicula['imagen']; ?>" width="50"  alt="">
        <div class="card-body">
            <h4 class="card-title"><?php echo $pelicula['nombre']; ?> </h4>
            <a name="" id="" class="btn btn-primary" href="https://www.imdb.com/" role="button">Ver más </a>
        </div>
    </div>
</div>

<hr>
<?php }?>



<?php include("template/pie.php"); ?>