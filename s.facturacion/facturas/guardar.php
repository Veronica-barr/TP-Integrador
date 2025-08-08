<?php
require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $cliente_id = $_POST['cliente_id'];
    $fecha = $_POST['fecha'];
    $productos = $_POST['producto_id'];
    $cantidades = $_POST['cantidad'];
    $precios = $_POST['precio'];
    
    // Calcular totales
    $subtotal = 0;
    $impuesto = 0;
    $total = 0;
    
    for ($i = 0; $i < count($productos); $i++) {
        if (!empty($productos[$i])) {
            $subtotal += $precios[$i] * $cantidades[$i];
        }
    }
    
    $impuesto = $subtotal * 0.21; // IVA 21%
    $total = $subtotal + $impuesto;
    
    // Generar número de factura
    $numero_factura = 'FAC-' . date('Ymd') . '-' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
    
    // Iniciar transacción
    $conexion->begin_transaction();
    
    try {
        // Insertar factura
        $query = "INSERT INTO facturas (cliente_id, numero_factura, fecha, subtotal, impuesto, total) 
                  VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("issddd", $cliente_id, $numero_factura, $fecha, $subtotal, $impuesto, $total);
        $stmt->execute();
        $factura_id = $conexion->insert_id;
        
        // Insertar líneas de factura
        $query = "INSERT INTO lineas_factura (factura_id, producto_id, cantidad, precio_unitario, porcentaje_impuesto, subtotal, monto_impuesto, total_linea) 
                  VALUES (?, ?, ?, ?, 21, ?, ?, ?)";
        $stmt = $conexion->prepare($query);
        
        for ($i = 0; $i < count($productos); $i++) {
            if (!empty($productos[$i])) {
                $producto_id = $productos[$i];
                $cantidad = $cantidades[$i];
                $precio = $precios[$i];
                $linea_subtotal = $precio * $cantidad;
                $linea_impuesto = $linea_subtotal * 0.21;
                $linea_total = $linea_subtotal + $linea_impuesto;
                
                $stmt->bind_param("iiidddd", $factura_id, $producto_id, $cantidad, $precio, $linea_subtotal, $linea_impuesto, $linea_total);
                $stmt->execute();
            }
        }
        
        $conexion->commit();
        header("Location: detalle.php?id=$factura_id");
    } catch (Exception $e) {
        $conexion->rollback();
        header("Location: nueva.php?error=1");
    }
    exit;
}

header("Location: listar.php");
exit;