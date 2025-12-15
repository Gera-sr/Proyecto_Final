CREATE DATABASE IF NOT EXISTS proyecto;
USE proyecto;

CREATE TABLE Cascos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(100) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    tipo VARCHAR(50),
    certificacion VARCHAR(100),
    descripcion TEXT,
    precio_aprox DECIMAL(10, 2) NOT NULL,
    imagen VARCHAR(255),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE Accidentes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE,
    lugar VARCHAR(255),
    descripcion TEXT,
    causa VARCHAR(255),
    lesionados INT,
    uso_casco BOOLEAN,
    nivel_gravedad VARCHAR(50),
    imagen_evidencia VARCHAR(255) COMMENT 'Path to image e.g. /img/accidente.jpg'
);

CREATE TABLE FAQ (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pregunta TEXT NOT NULL,
    respuesta TEXT NOT NULL,
    categoria VARCHAR(100),
    orden INT
);

CREATE TABLE Usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(50) NOT NULL,
    nombres VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    fecha_nacimiento DATE NOT NULL,
    contrasena VARCHAR(255) NOT NULL,
    numero_telefono VARCHAR(12) NOT NULL
);

CREATE TABLE Admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES Usuarios(id) ON DELETE CASCADE
);

CREATE TABLE Mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    asunto VARCHAR(150),
    mensaje TEXT NOT NULL,
    fecha_envio TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);