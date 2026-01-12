CREATE DATABASE IF NOT EXISTS Proyecto;
USE Proyecto;

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

INSERT INTO Usuarios (nombre_usuario, nombres, apellidos, email, fecha_nacimiento, contrasena, numero_telefono)
VALUES
("Isaacrml08", "Isaac", "Ramirez Lopez", "isaac@gmail.com", '2008-11-22', "1234", "12345"),
("asmith", "Alice", "Smith", "alice.smith@gmail.com", '2000-02-15', "alicepass", "1234567890");

INSERT INTO Admins (user_id) VALUES (2);

INSERT INTO Cascos (marca, modelo, tipo, certificacion, descripcion, precio_aprox, imagen)
VALUES 
("Rino", "Model A", "Integral", "DOT", "Casco integral de alta seguridad, ideal para motocross.", 150.00, "img/casco1.jpeg"),
("BELL", "Star", "Modular", "ECE", "Casco modular de la marca BELL, con ventilación avanzada.", 250.00, "img/casco2.webp"),
("Shoei", "GT-Air 2", "Integral", "Snell", "Casco integral con sistema de absorción de impacto y visibilidad mejorada.", 350.00, "img/casco3.jfif"),
("Arai", "RX-7V", "Integral", "ECE", "Casco de alta gama con excelente rendimiento en seguridad.", 500.00, "img/casco4.jpeg"),
("HJC", "RPHA 11", "Integral", "DOT", "Casco ligero y aerodinámico para carreras de alto rendimiento.", 450.00, "img/casco5.jpeg");

INSERT INTO Accidentes (fecha, lugar, descripcion, causa, lesionados, uso_casco, nivel_gravedad, imagen_evidencia)
VALUES 
('2023-11-01', 'Carretera 45', 'Accidente de moto en curva, el conductor perdió el control.', 'Exceso de velocidad', 2, TRUE, 'Alto', 'img/accidente1.jfif'),
('2023-09-15', 'Autopista 7', 'Accidente en autopista por colisión con otro vehículo.', 'Distracción al volante', 3, TRUE, 'Moderado', 'img/accidente2.jfif'),
('2023-08-20', 'Zona urbana', 'Caída de motocicleta por mal estado del pavimento.', 'Condiciones de la vía', 1, FALSE, 'Alto', 'img/accidente3.jfif'),
('2023-06-10', 'Carretera 2', 'Colisión entre motocicleta y camión.', 'Falta de visibilidad', 1, TRUE, 'Crítico', 'img/accidente4.jfif'),
('2023-05-25', 'Carretera 101', 'Accidente en semáforo, el motociclista fue golpeado por otro vehículo.', 'Falta de atención al semáforo', 1, TRUE, 'Bajo', 'img/accidente5.jpg');

INSERT INTO FAQ (pregunta, respuesta, categoria, orden)
VALUES 
("¿Cuáles son las normas básicas para conducir con casco?", "Siempre usa casco que cumpla con las normativas de seguridad, asegúrate de que esté ajustado correctamente y no lo uses para otro fin.", "Normativas", 1),
("¿Qué tipo de casco es mejor para motocross?", "Los cascos integrales ofrecen mayor protección en comparación con los cascos abiertos. Busca un casco con certificación DOT o Snell.", "Casco", 2),
("¿Cómo saber si un casco está certificado?", "Verifica el sello de certificación en el casco, como DOT, ECE o Snell, que garantiza que el casco cumple con los estándares de seguridad.", "Certificación", 3),
("¿Puedo usar un casco de moto que no esté homologado?", "No es recomendable, ya que un casco no homologado podría no ofrecer la protección adecuada en caso de accidente.", "Normativas", 4),
("¿Cómo mantener mi casco en buen estado?", "Límpialo regularmente con productos recomendados para cascos y revisa que no tenga daños visibles.", "Mantenimiento", 5);

INSERT INTO Mensajes (nombre, email, asunto, mensaje)
VALUES 
("Carlos Pérez", "carlos.perez@gmail.com", "Consulta sobre cascos", "¿Me pueden recomendar un casco para motocross? Estoy buscando algo con buena ventilación."),
("Laura González", "laura.gonzalez@yahoo.com", "Sugerencia de mejora", "Sería genial que pudieran ofrecer cascos con más opciones de colores."),
("Juan Martínez", "juan.martinez@hotmail.com", "Pedido de información", "Estoy interesado en comprar el casco Shoei GT-Air 2, ¿tienen stock?"),
("Sara López", "sara.lopez@gmail.com", "Consulta sobre accidentes", "¿Qué nivel de protección se recomienda para motociclistas principiantes?"),
("Pedro Ruiz", "pedro.ruiz@outlook.com", "Información sobre accidentes", "¿Tienen alguna estadística sobre accidentes recientes y el uso de casco?");