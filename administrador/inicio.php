<?php include('template/cabecera.php') ?>

<div class="col-md-11">
    <div class="jumbotron">
        <h1 class="display-3">Bienvenido <?php echo $nombreUsuario;?></h1>
        <p class="lead">La mejor plataforma de alquiler de peliculas</p>
        <hr class="my-2">
        <p class="lead">
            <a class="btn btn-primary btn-lg" href="seccion/peliculas.php" role="button">Administrar peliculas</a>
        </p>
    </div>
</div>

<?php include('template/pie.php') ?>