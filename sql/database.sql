CREATE DATABASE security_hub;
USE security_hub;

CREATE TABLE eventos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(100),
  descripcion TEXT,
  fecha DATE,
  hora TIME,
  tipo VARCHAR(50),
  color VARCHAR(20)
);

CREATE TABLE password_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  password_hash VARCHAR(255),
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_evento_calendario DATE
);
