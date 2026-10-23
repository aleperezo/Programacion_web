use gymico_db

-- 1. Tabla de Usuarios (Socios del gimnasio)
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    correo VARCHAR(150) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    edad INT DEFAULT NULL,
    peso DECIMAL(5,2) DEFAULT NULL,
    tipo_membresia VARCHAR(50) DEFAULT 'Basica',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_vencimiento DATE DEFAULT NULL,
    estado VARCHAR(20) DEFAULT 'Activo',
    foto VARCHAR(255) DEFAULT NULL
);

-- 2. Tabla de Asistencias (Registro de entradas vinculadas al usuario)
CREATE TABLE IF NOT EXISTS asistencias (
    id_asistencia INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    fecha_hora TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- 3. Tabla de Mantenimiento (Maquinaria y Salas fuera de servicio)
CREATE TABLE IF NOT EXISTS mantenimiento (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_equipo VARCHAR(100) NOT NULL,
    tipo VARCHAR(50) NOT NULL,
    motivo VARCHAR(255) NOT NULL
);

-- 4. Tabla de Ventas (Para el módulo de reportes y Excel)
CREATE TABLE IF NOT EXISTS ventas (
    id_venta INT AUTO_INCREMENT PRIMARY KEY,
    concepto VARCHAR(100) NOT NULL,
    monto DECIMAL(10,2) NOT NULL,
    fecha_venta TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 5. Tabla de Empleados (El staff de GYMICO)
CREATE TABLE IF NOT EXISTS empleados (
    id_empleado INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    rol VARCHAR(50) NOT NULL,
    fecha_contratacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Tabla de Reportes (Incidencias reportadas por usuarios y admin)
CREATE TABLE IF NOT EXISTS reportes (
    id_reporte INT AUTO_INCREMENT PRIMARY KEY,
    tipo_reporte VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    reportado_por VARCHAR(100) NOT NULL,
    fecha_reporte TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS visitas (
    id_visita INT AUTO_INCREMENT PRIMARY KEY,
    nombre_visitante VARCHAR(150) NOT NULL,
    correo_visitante VARCHAR(150) NOT NULL,
    telefono VARCHAR(20) DEFAULT NULL,
    fecha_visita DATE NOT NULL,
    hora_visita TIME NOT NULL,
    motivo VARCHAR(100) DEFAULT 'Tour / Visita al Gym',
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS visitas;

CREATE TABLE visitas (
    id_visita INT AUTO_INCREMENT PRIMARY KEY,
    nombre_visitante VARCHAR(150) NOT NULL,
    correo_visitante VARCHAR(150) NOT NULL,
    telefono VARCHAR(20) NOT NULL,
    fecha_visita DATE NOT NULL,
    horario VARCHAR(50) NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);