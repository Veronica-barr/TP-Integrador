<?php
require_once '../includes/conexion.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $conexion->begin_transaction();
    
    try {
        // Guardar datos básicos del cliente
        $id = $_POST['cliente_id'] ?: 0;
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $cuil = $_POST['cuil'];
        
        if ($id > 0) {
            $query = "UPDATE clientes SET nombre = ?, apellido = ?, cuil = ? WHERE cliente_id = ?";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("sssi", $nombre, $apellido, $cuil, $id);
        } else {
            $query = "INSERT INTO clientes (nombre, apellido, cuil) VALUES (?, ?, ?)";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("sss", $nombre, $apellido, $cuil);
        }
        
        $stmt->execute();
        $cliente_id = $id > 0 ? $id : $conexion->insert_id;
        
        // Procesar teléfonos
        if (isset($_POST['telefono_tipo'])) {
            $telefono_ids = $_POST['telefono_id'];
            $tipos = $_POST['telefono_tipo'];
            $codigos_area = $_POST['telefono_codigo_area'];
            $numeros = $_POST['telefono_numero'];
            
            for ($i = 0; $i < count($tipos); $i++) {
                $telefono_id = $telefono_ids[$i];
                $tipo = $tipos[$i];
                $codigo_area = $codigos_area[$i];
                $numero = $numeros[$i];
                
                if ($telefono_id > 0) {
                    // Actualizar teléfono existente
                    $query = "UPDATE telefonos SET tipo = ?, codigo_area = ?, numero = ? WHERE telefono_id = ?";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("sssi", $tipo, $codigo_area, $numero, $telefono_id);
                } else {
                    // Insertar nuevo teléfono
                    $query = "INSERT INTO telefonos (cliente_id, tipo, codigo_area, numero) VALUES (?, ?, ?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("isss", $cliente_id, $tipo, $codigo_area, $numero);
                }
                $stmt->execute();
            }
        }
        
        // Procesar direcciones
        if (isset($_POST['direccion_calle'])) {
            $direccion_ids = $_POST['direccion_id'];
            $calles = $_POST['direccion_calle'];
            $numeros = $_POST['direccion_numero'];
            $pisos = $_POST['direccion_piso'];
            $departamentos = $_POST['direccion_departamento'];
            $localidad_ids = $_POST['direccion_localidad_id'];
            $codigos_postales = $_POST['direccion_codigo_postal'];
            
            for ($i = 0; $i < count($calles); $i++) {
                $direccion_id = $direccion_ids[$i];
                $calle = $calles[$i];
                $numero = $numeros[$i];
                $piso = $pisos[$i];
                $departamento = $departamentos[$i];
                $localidad_id = $localidad_ids[$i];
                $codigo_postal = $codigos_postales[$i];
                
                if ($direccion_id > 0) {
                    // Actualizar dirección existente
                    $query = "UPDATE direcciones SET calle = ?, numero = ?, piso = ?, departamento = ?, localidad_id = ?, codigo_postal = ? WHERE direccion_id = ?";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("ssssisi", $calle, $numero, $piso, $departamento, $localidad_id, $codigo_postal, $direccion_id);
                } else {
                    // Insertar nueva dirección
                    $query = "INSERT INTO direcciones (cliente_id, calle, numero, piso, departamento, localidad_id, codigo_postal) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conexion->prepare($query);
                    $stmt->bind_param("issssis", $cliente_id, $calle, $numero, $piso, $departamento, $localidad_id, $codigo_postal);
                }
                $stmt->execute();
            }
        }
        
        $conexion->commit();
        header("Location: listar.php?success=1");
    } catch (Exception $e) {
        $conexion->rollback();
        header("Location: editar.php?id=$id&error=1");
    }
    exit;
}

header("Location: listar.php");
?>