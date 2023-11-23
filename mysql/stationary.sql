drop database IF EXISTS stationary;
CREATE DATABASE IF NOT EXISTS stationary;
USE stationary; 

CREATE TABLE mensaje (
    id INT AUTO_INCREMENT PRIMARY KEY,
    correo_electronico VARCHAR(255) NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(15) NOT NULL,
    mensaje VARCHAR(1000) NOT NULL
);

