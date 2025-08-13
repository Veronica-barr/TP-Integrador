-- Creación de la base de datos
CREATE DATABASE IF NOT EXISTS sis_facturacionbd;
USE sis_facturacionbd;

-- Tabla de Provincias
CREATE TABLE provincias (
    provincia_id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL
);

-- Tabla de Localidades
CREATE TABLE localidades (
    localidad_id INT AUTO_INCREMENT PRIMARY KEY,
    provincia_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    codigo_postal VARCHAR(10),
    FOREIGN KEY (provincia_id) REFERENCES provincias(provincia_id)
);

-- Tabla de Clientes
CREATE TABLE clientes (
  idCliente int unsigned NOT NULL AUTO_INCREMENT,
  nombre varchar(20) NOT NULL,
  apellido varchar(20) NOT NULL,
  cuil char(11) NOT NULL,
  email varchar(80) NOT NULL,
  fechaRegistro timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  fechaActualizacion timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (idCliente),
  UNIQUE KEY cuil (cuil)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb4


-- Tabla de Teléfonos
CREATE TABLE telefonoscliente (
  idTelefono int unsigned NOT NULL AUTO_INCREMENT,
  idCliente int unsigned NOT NULL,
  telefono varchar(25) NOT NULL,
  tipoTelefono enum('celular','fijo','otro') DEFAULT 'celular',
  PRIMARY KEY (idTelefono),
  KEY fk_telefonoCliente (idCliente),
  CONSTRAINT fk_telefonoCliente FOREIGN KEY (idCliente) REFERENCES clientes (idCliente) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla de Direcciones
CREATE TABLE direccionescliente (
  idDireccion int unsigned NOT NULL AUTO_INCREMENT,
  idCliente int unsigned NOT NULL,
  calle varchar(40) NOT NULL,
  numero varchar(10) NOT NULL,
  piso varchar(10) DEFAULT NULL,
  dpto varchar(10) DEFAULT NULL,
  ciudad varchar(30) NOT NULL,
  provincia varchar(30) DEFAULT NULL,
  cp varchar(10) DEFAULT NULL,
  tipoDireccion enum('fiscal','envio','otro') DEFAULT 'envio',
  PRIMARY KEY (idDireccion),
  KEY fk_direccionCliente (idCliente),
  CONSTRAINT fk_direccionCliente FOREIGN KEY (idCliente) REFERENCES clientes (idCliente) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Tabla de Productos
CREATE TABLE productos (
    producto_id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_unitario DECIMAL(10,2) NOT NULL,
    porcentaje_impuesto DECIMAL(5,2) DEFAULT 21.00,
    stock INT DEFAULT 0,
    activo BOOLEAN DEFAULT TRUE
);

-- Tabla de Facturas
CREATE TABLE facturas (
    factura_id INT AUTO_INCREMENT PRIMARY KEY,
    cliente_id INT NOT NULL,
    numero_factura VARCHAR(20) NOT NULL UNIQUE,
    fecha DATE NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    impuesto DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    estado ENUM('Pendiente', 'Pagada', 'Cancelada') DEFAULT 'Pendiente',
    FOREIGN KEY (cliente_id) REFERENCES clientes(cliente_id)
);

-- Tabla de Líneas de Factura
CREATE TABLE lineas_factura (
    linea_id INT AUTO_INCREMENT PRIMARY KEY,
    factura_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    porcentaje_impuesto DECIMAL(5,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    monto_impuesto DECIMAL(10,2) NOT NULL,
    total_linea DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (factura_id) REFERENCES facturas(factura_id) ON DELETE CASCADE,
    FOREIGN KEY (producto_id) REFERENCES productos(producto_id)
);