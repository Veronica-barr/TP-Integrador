<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <nav class="nav flex-column">
                <?php
                echo "<!-- DEBUG INFO:\n";
                echo "SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME'] . "\n";
                echo "REQUEST_URI: " . $_SERVER['REQUEST_URI'] . "\n";
                echo "PHP_SELF: " . $_SERVER['PHP_SELF'] . "\n";
                echo "-->";
                ?>
                
                <a class="nav-link" href="/sis_facturacion/index.php">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
                <a class="nav-link" href="/sis_facturacion/views/clientes/list.php">
                    <i class="bi bi-people"></i> Clientes
                </a>
                <a class="nav-link" href="/sis_facturacion/views/productos/list.php">
                    <i class="bi bi-box"></i> Productos
                </a>
                <a class="nav-link" href="/sis_facturacion/views/facturas/list.php">
                    <i class="bi bi-receipt"></i> Facturas
                </a>
            </nav>
        </div>
        <div class="col-md-10">