-- Crear base de datos
CREATE DATABASE IF NOT EXISTS gestion_operativa CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE gestion_operativa;

-- Tabla de ubigeo
CREATE TABLE ubigeo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    codigo_postal VARCHAR(10) ,
    dependencia_id INT ,
    estado BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (dependencia_id) REFERENCES ubigeo(id) ON DELETE RESTRICT
);

-- Tabla de categorias
CREATE TABLE categorias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);



-- Tabla de unidades de medida
CREATE TABLE unidades_medida (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);



-- Tabla de cargos
CREATE TABLE cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de areas
CREATE TABLE areas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de empleados
CREATE TABLE empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cargo_id INT NOT NULL,
    area_id INT NOT NULL,
    nombre VARCHAR(50) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    dni CHAR(8) UNIQUE NOT NULL,
    licencia VARCHAR(20),
    telefono VARCHAR(15),
    direccion VARCHAR(200),
    ubigeo_id INT,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cargo_id) REFERENCES cargos(id) ON DELETE RESTRICT,
    FOREIGN KEY (area_id) REFERENCES areas(id) ON DELETE RESTRICT,
    FOREIGN KEY (ubigeo_id) REFERENCES ubigeo(id) ON DELETE RESTRICT 
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    empleado_id INT ,
    usuario VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    perfil ENUM('admin', 'supervisor', 'tecnico') NOT NULL DEFAULT 'tecnico',
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE RESTRICT
);

-- Tabla de materiales
CREATE TABLE materiales (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_id INT NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    descripcion VARCHAR(255) ,
    unidad_medida_id INT NOT NULL,
    precio_unitario DECIMAL(10,3) ,
    stock_minimo INT NOT NULL,
    codigo_material VARCHAR(50) UNIQUE,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE RESTRICT,
    FOREIGN KEY (unidad_medida_id) REFERENCES unidades_medida(id) ON DELETE RESTRICT
);

-- Tabla de proveedores
CREATE TABLE proveedores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    razon_social VARCHAR(100) NOT NULL, 
    ruc CHAR(11) UNIQUE NOT NULL,     
    contacto VARCHAR(100),     
    email VARCHAR(100),
    telefono VARCHAR(15),
    direccion VARCHAR(200),    
    ubigeo_id INT,             
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ubigeo_id) REFERENCES ubigeo(id) ON DELETE RESTRICT 
);

-- Tabla de cuadrillas
CREATE TABLE cuadrillas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL, -- autogenerar nombre con el formato: "CUADRILLA-#-periodo", donde # es el número de cuadrilla
    fecha_inicio DATE,
    fecha_fin DATE ,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de cuadrillas_empleados
CREATE TABLE cuadrillas_empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuadrilla_id INT NOT NULL,
    empleado_id INT NOT NULL,
    fecha_asignacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cuadrilla_id) REFERENCES cuadrillas(id) ON DELETE RESTRICT,
    FOREIGN KEY (empleado_id) REFERENCES empleados(id) ON DELETE RESTRICT
);

CREATE TABLE tipo_combustible (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de vehiculos
CREATE TABLE vehiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    year INT ,
    modelo VARCHAR(100) NOT NULL,
    color VARCHAR(50) NOT NULL, 
    placa VARCHAR(20) UNIQUE NOT NULL, 
    tipo_combustible_id INT NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tipo_combustible_id) REFERENCES tipo_combustible(id) ON DELETE RESTRICT
);

-- Tabla de soats
CREATE TABLE soats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehiculo_id INT NOT NULL,
    proveedor_id INT  NOT NULL,
    numero_soat VARCHAR(200) NOT NULL,
    fecha_emision DATE NOT NULL,
    fecha_vencimiento DATE NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id) ON DELETE RESTRICT,
    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE RESTRICT 
);

-- Tabla de asignacion_vehiculo
CREATE TABLE asignacion_vehiculo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuadrilla_id INT NOT NULL,
    vehiculo_id INT NOT NULL,
    fecha_asignacion TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cuadrilla_id) REFERENCES cuadrillas(id) ON DELETE RESTRICT,
    FOREIGN KEY (vehiculo_id) REFERENCES vehiculos(id) ON DELETE RESTRICT
);

-- Tabla de papeletas
CREATE TABLE papeletas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asignacion_vehiculo_id INT NOT NULL,
    fecha DATE DEFAULT CURRENT_DATE NOT NULL,
    destino VARCHAR(255) NOT NULL,
    motivo TEXT NOT NULL,
    km_salida DECIMAL(10,3),
    km_llegada DECIMAL(10,3),
    fecha_hora_salida DATETIME,
    fecha_hora_llegada DATETIME,
    estado BOOLEAN DEFAULT TRUE,
    fecha_anulacion DATETIME,
    motivo_anulacion VARCHAR(200),
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (asignacion_vehiculo_id) REFERENCES asignacion_vehiculo(id) ON DELETE RESTRICT
);

-- Tabla de dotacion_combustible
CREATE TABLE dotacion_combustible (
    id INT AUTO_INCREMENT PRIMARY KEY,
    papeleta_id INT NOT NULL,
    cantidad_gl DECIMAL(10,3) NOT NULL,
    precio_unitario DECIMAL(10,3),
    fecha_carga DATE DEFAULT CURRENT_DATE NOT NULL,
    numero_vale VARCHAR(200),
    tipo_combustible_id INT NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (tipo_combustible_id) REFERENCES tipo_combustible(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (papeleta_id) REFERENCES papeletas(id) ON DELETE RESTRICT
);

-- Tabla de tipos_actividad
CREATE TABLE tipos_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    dependencia_id INT ,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (dependencia_id) REFERENCES tipos_actividad(id) ON DELETE RESTRICT
);

-- Tabla de medidores
CREATE TABLE medidores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    serie VARCHAR(50) UNIQUE NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    capacidad_amperios VARCHAR(10),
    año_fabricacion CHAR(4),
    marca VARCHAR(50),
    numero_hilos INT,
    material_id INT,
    fm VARCHAR(50),
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla de suministros
CREATE TABLE suministros (
    id INT AUTO_INCREMENT PRIMARY KEY,
    codigo VARCHAR(50) UNIQUE NOT NULL,
    nombre TEXT NOT NULL,
    ruta VARCHAR(50),
    direccion TEXT,
    ubigeo_id INT,
    referencia TEXT,
    caja VARCHAR(50),
    tarifa VARCHAR(50),
    latitud VARCHAR(50),
    longitud VARCHAR(50),
    serie VARCHAR(50),
    medidor_id INT NULL,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medidor_id) REFERENCES medidores(id) ON DELETE RESTRICT,
    FOREIGN KEY (ubigeo_id) REFERENCES ubigeo(id) ON DELETE RESTRICT
);



CREATE TABLE tipos_propiedad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE construcciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE usos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE situaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE servicios_electrico (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE fichas_actividades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_actividad_id INT NOT NULL,
    suministro_id INT NOT NULL,
    tipo_propiedad_id INT,
    construccion_id INT,
    servicio_electrico_id INT,
    uso_id INT,
    numero_piso VARCHAR(10),
    situacion_id INT,
    situacion_detalle VARCHAR(100),
    suministro_derecho VARCHAR(50),
    suministro_izquierdo VARCHAR(50),
    latitud VARCHAR(50),
    longitud VARCHAR(50),
    observacion TEXT,
    documento VARCHAR(100),
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (servicio_electrico_id) REFERENCES servicios_electrico(id) ON DELETE RESTRICT,
    FOREIGN KEY (tipo_actividad_id) REFERENCES tipos_actividad(id) ON DELETE RESTRICT,
    FOREIGN KEY (suministro_id) REFERENCES suministros(id) ON DELETE RESTRICT,
    FOREIGN KEY (tipo_propiedad_id) REFERENCES tipos_propiedad(id) ON DELETE RESTRICT,
    FOREIGN KEY (construccion_id) REFERENCES construcciones(id) ON DELETE RESTRICT,
    FOREIGN KEY (uso_id) REFERENCES usos(id) ON DELETE RESTRICT,
    FOREIGN KEY (situacion_id) REFERENCES situaciones(id) ON DELETE RESTRICT
);

-- Tabla de medidor_suministro
CREATE TABLE medidor_suministro (
    id INT AUTO_INCREMENT PRIMARY KEY,
    suministro_id INT NOT NULL,
    medidor_id INT NOT NULL,
    fecha_cambio TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
    observaciones TEXT ,
    ficha_actividad_id INT ,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (suministro_id) REFERENCES suministros(id) ON DELETE RESTRICT,
    FOREIGN KEY (medidor_id) REFERENCES medidores(id) ON DELETE RESTRICT,
    FOREIGN KEY (ficha_actividad_id) REFERENCES fichas_actividades(id) ON DELETE RESTRICT
);

CREATE TABLE fichas_actividades_empleados (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ficha_actividad_id INT NOT NULL,
    cuadrilla_empleado_id INT NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (ficha_actividad_id) REFERENCES fichas_actividades(id) ON DELETE CASCADE,
    FOREIGN KEY (cuadrilla_empleado_id) REFERENCES cuadrillas_empleados(id) ON DELETE RESTRICT
);

-- 20. medidores_ficha_actividad
CREATE TABLE medidores_ficha_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ficha_actividad_id INT NOT NULL,
    medidor_id INT NOT NULL,
    tipo ENUM('nuevo','retirado','existente') NOT NULL ,
    digitos_enteros INT,
    digitos_decimales INT,
    lectura INT,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ficha_actividad_id) REFERENCES fichas_actividades(id) ON DELETE RESTRICT,
    FOREIGN KEY (medidor_id) REFERENCES medidores(id) ON DELETE RESTRICT
);

-- 21. precintos_ficha_actividad
CREATE TABLE precintos_ficha_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    medidor_ficha_actividad_id INT NOT NULL,
    tipo ENUM('tapa','caja','bornera') NOT NULL ,
    numero_precinto VARCHAR(50) NOT NULL,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (medidor_ficha_actividad_id) REFERENCES medidores_ficha_actividad(id) ON DELETE RESTRICT
);

-- 22. fotos_ficha_actividad
CREATE TABLE fotos_ficha_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ficha_actividad_id INT NOT NULL,
    url TEXT NOT NULL,
    descripcion TEXT,
    fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ficha_actividad_id) REFERENCES fichas_actividades(id) ON DELETE RESTRICT
);

-- 23. materiales_ficha_actividad
CREATE TABLE materiales_ficha_actividad (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ficha_actividad_id INT NOT NULL,
    material_id INT NOT NULL,
    cantidad DECIMAL(10,3) NOT NULL,
    observacion TEXT,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (ficha_actividad_id) REFERENCES fichas_actividades(id) ON DELETE RESTRICT,
    FOREIGN KEY (material_id) REFERENCES materiales(id)
);


-- Tabla maestra para tipo de documento de NEA
CREATE TABLE tipos_documento_nea (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE,  -- 'factura', 'boleta', 'guia', 'otros', movimiento de mercancia
    estado BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Tabla principal NEA
CREATE TABLE neas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    proveedor_id INT NOT NULL,
    fecha DATE DEFAULT CURRENT_DATE NOT NULL,
    nro_documento VARCHAR(50) UNIQUE NOT NULL,
    tipo_documento_id INT NOT NULL,
    observaciones TEXT,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    FOREIGN KEY (proveedor_id) REFERENCES proveedores(id) ON DELETE RESTRICT,
    FOREIGN KEY (tipo_documento_id) REFERENCES tipos_documento_nea(id) ON DELETE RESTRICT
);


CREATE TABLE nea_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nea_id INT NOT NULL,
    material_id INT NOT NULL,
    cantidad DECIMAL(10,3) NOT NULL,
    precio_unitario DECIMAL(10,3) ,
    incluye_igv BOOLEAN DEFAULT FALSE,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (nea_id) REFERENCES neas(id) ON DELETE RESTRICT,
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT
);

CREATE TABLE pecosa (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cuadrilla_empleado_id INT NOT NULL,
    fecha DATE DEFAULT CURRENT_DATE NOT NULL,
    nro_documento VARCHAR(50) UNIQUE NOT NULL,
    observaciones TEXT,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (cuadrilla_empleado_id) REFERENCES cuadrillas_empleados(id) ON DELETE RESTRICT
);

CREATE TABLE pecosa_detalle (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pecosa_id INT NOT NULL,
    nea_detalle_id INT NOT NULL, -- referencia al lote de ingreso (trazabilidad)
    cantidad DECIMAL(10,3) NOT NULL,
    precio_unitario DECIMAL(10,3) ,
    incluye_igv BOOLEAN DEFAULT FALSE,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (pecosa_id) REFERENCES pecosa(id) ON DELETE RESTRICT,
    FOREIGN KEY (nea_detalle_id) REFERENCES nea_detalle(id) ON DELETE RESTRICT
);

CREATE TABLE movimientos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    material_id INT NOT NULL,
    tipo_movimiento ENUM('entrada', 'salida') NOT NULL,
    nea_detalle_id INT,
    pecosa_detalle_id INT,
    cantidad DECIMAL(10,3) NOT NULL,
    precio_unitario DECIMAL(10,3) ,
    incluye_igv BOOLEAN DEFAULT FALSE,
    fecha DATE DEFAULT CURRENT_DATE,
    estado BOOLEAN DEFAULT TRUE,
    usuario_creacion_id INT,
    usuario_actualizacion_id INT,
    FOREIGN KEY (usuario_creacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    FOREIGN KEY (usuario_actualizacion_id) REFERENCES usuarios(id) ON DELETE RESTRICT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (material_id) REFERENCES materiales(id) ON DELETE RESTRICT,
    FOREIGN KEY (nea_detalle_id) REFERENCES nea_detalle(id) ON DELETE RESTRICT,
    FOREIGN KEY (pecosa_detalle_id) REFERENCES pecosa_detalle(id) ON DELETE RESTRICT

);

CREATE INDEX idx_material_id ON movimientos(material_id);
CREATE INDEX idx_suministro_id ON fichas_actividades(suministro_id);



CREATE VIEW vista_stock_actual AS
SELECT 
    m.material_id,
    mat.nombre AS nombre_material,
    mat.codigo_material,
    SUM(
        CASE WHEN m.tipo_movimiento = 'entrada' THEN m.cantidad
             WHEN m.tipo_movimiento = 'salida' THEN -m.cantidad
        END
    ) AS stock_actual
FROM movimientos m
JOIN materiales mat ON mat.id = m.material_id
GROUP BY m.material_id, mat.nombre, mat.codigo_material
HAVING stock_actual >= 0
ORDER BY mat.nombre;


