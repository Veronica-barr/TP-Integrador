<?php
require_once 'includes/header.php';
?>

<div class="jumbotron bg-light p-5 rounded">
    <h1 class="display-4">Sistema de Facturación</h1>
    <p class="lead">Bienvenido al sistema de gestión de facturas, clientes y productos.</p>
    <hr class="my-4">
    <p>Utilice el menú superior para acceder a las diferentes secciones del sistema.</p>
    <div class="row mt-4">
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary h-100">
                <div class="card-body">
                    <h5 class="card-title">Clientes</h5>
                    <p class="card-text">Gestión de clientes y sus datos.</p>
                    <a href="clientes/listar.php" class="btn btn-light">Administrar</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success h-100">
                <div class="card-body">
                    <h5 class="card-title">Productos</h5>
                    <p class="card-text">Gestión de productos y precios.</p>
                    <a href="productos/listar.php" class="btn btn-light">Administrar</a>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info h-100">
                <div class="card-body">
                    <h5 class="card-title">Facturas</h5>
                    <p class="card-text">Gestión de facturas y ventas.</p>
                    <a href="facturas/listar.php" class="btn btn-light">Administrar</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>