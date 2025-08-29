<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar p-0">
            <nav class="nav flex-column">
                <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="index.php">
                    <i class="bi bi-house-door"></i> Inicio
                </a>
                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'clientes/list.php') !== false) ? 'active' : ''; ?>" href="../clientes/listar.php">
                    <i class="bi bi-people"></i> Clientes
                </a>
                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'productos/') !== false) ? 'active' : ''; ?>" href="../productos/listar.php">
                    <i class="bi bi-box"></i> Productos
                </a>
                <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'facturas/') !== false) ? 'active' : ''; ?>" href="../facturas/listar.php">
                    <i class="bi bi-receipt"></i> Facturas
                </a>
            </nav>
        </div>
        <div class="col-md-10">