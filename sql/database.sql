CREATE DATABASE security_hub;
USE security_hub;

CREATE TABLE eventos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(100),
  descripcion TEXT,
  fecha DATE,
  hora TIME,
  tipo ENUM('feriado','partido','efemeride') NOT NULL,
  color VARCHAR(20)
);

INSERT INTO eventos (titulo, descripcion, fecha, tipo, color) VALUES

('Año Nuevo', 'Feriado nacional', '2026-01-01', 'feriado', 'event-blue'),

('Carnaval', 'Feriado nacional', '2026-02-16', 'feriado', 'event-blue'),
('Carnaval', 'Feriado nacional', '2026-02-17', 'feriado', 'event-blue'),

('Día de la Memoria', 'Feriado nacional', '2026-03-24', 'feriado', 'event-blue'),

('Día del Veterano y de los Caídos en Malvinas', 'Feriado nacional', '2026-04-02', 'feriado', 'event-blue'),

('Día del Trabajador', 'Feriado nacional', '2026-05-01', 'feriado', 'event-blue'),

('Día de la Revolución de Mayo', 'Feriado nacional', '2026-05-25', 'feriado', 'event-blue'),

('Paso a la Inmortalidad de Güemes', 'Feriado nacional', '2026-06-17', 'feriado', 'event-blue'),

('Paso a la Inmortalidad de Belgrano', 'Feriado nacional', '2026-06-20', 'feriado', 'event-blue'),

('Día de la Independencia', 'Feriado nacional', '2026-07-09', 'feriado', 'event-blue'),

('Paso a la Inmortalidad de San Martín', 'Feriado trasladable', '2026-08-17', 'feriado', 'event-blue'),

('Día del Respeto a la Diversidad Cultural', 'Feriado trasladable', '2026-10-12', 'feriado', 'event-blue'),

('Día de la Soberanía Nacional', 'Feriado trasladable', '2026-11-20', 'feriado', 'event-blue'),

('Inmaculada Concepción de María', 'Feriado nacional', '2026-12-08', 'feriado', 'event-blue'),

('Navidad', 'Feriado nacional', '2026-12-25', 'feriado', 'event-blue');

INSERT INTO eventos (titulo, descripcion, fecha, tipo, color) VALUES

('Inicio de clases', 'Conmemoración o recuerdo de un hecho histórico notable, personaje destacado o fecha significativa en su aniversario.', '2026-03-01', 'efemeride', 'event-purple'),

('Día del Estudiante', 'Conmemoración o recuerdo de un hecho histórico notable, personaje destacado o fecha significativa en su aniversario.', '2026-09-21', 'efemeride', 'event-purple'),

('Fin de clases', 'Conmemoración o recuerdo de un hecho histórico notable, personaje destacado o fecha significativa en su aniversario.', '2026-12-10', 'efemeride', 'event-purple');

INSERT INTO eventos (titulo, descripcion, fecha, hora, tipo, color) VALUES

('Argentina vs Argelia', 'Partido de la selección', '2026-06-16', '22:00:00', 'partido', 'event-pink'),

('Argentina vs Austria', 'Partido de la selección', '2026-06-22', '14:00:00', 'partido', 'event-pink'),

('Argentina vs Jordania', 'Partido de la selección', '2026-06-27', '23:00:00', 'partido', 'event-pink');

CREATE TABLE password_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  password_hash VARCHAR(255),
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  fecha_evento_calendario DATE
);

-- Usuarios para login/registro
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(100) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
