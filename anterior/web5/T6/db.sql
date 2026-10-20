-- crear la base de datos
CREATE DATABASE crud_app;
USE crud_app;
DROP TABLE IF EXISTS usuarios;
-- crear latabla
CREATE TABLE usuarios(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR (150) NOT NULL,
    email VARCHAR (150) NOT NULL,
    telefono VARCHAR (15) NOT NULL
);

INSERT INTO usuarios (nombre, email, telefono) VALUES ('Alexander Villegas', 'alexander@gmail.com', '5512345678');
INSERT INTO usuarios (nombre, email, telefono) VALUES ('Zoe Martinez', 'zoe@gmail.com', '5587654321');
INSERT INTO usuarios (nombre, email, telefono) VALUES ('Carlos Ochoa', 'carlos@gmail.com', '5512345777');
INSERT INTO usuarios (nombre, email, telefono) VALUES ('Maria Gonzalez', 'maria@gmail.com', '5512345888');
DELETE FROM usuarios WHERE id = 4;
