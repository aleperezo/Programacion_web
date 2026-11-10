CREATE DATABASE escuela;

USE escuela;

CREATE TABLE alumnos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO alumnos (nombre, email) VALUES ('Ana López', 'ana@ejemplo.com');

SELECT * FROM alumnos;

SELECT * FROM alumnos WHERE id = 5;