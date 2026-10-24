CREATE DATABASE IF NOT EXISTS tecnostock
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE tecnostock;

CREATE TABLE IF NOT EXISTS usuarios (
  id_usuario INT AUTO_INCREMENT PRIMARY KEY,
  nombre     VARCHAR(100) NOT NULL,
  correo     VARCHAR(120) NOT NULL UNIQUE,
  clave      VARCHAR(255) NOT NULL,
  estado     TINYINT(1)   NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS categorias (
  id_categoria INT AUTO_INCREMENT PRIMARY KEY,
  nombre       VARCHAR(60)  NOT NULL UNIQUE,
  descripcion  VARCHAR(255) NULL,
  estado       TINYINT(1)   NOT NULL DEFAULT 1
);

CREATE TABLE IF NOT EXISTS productos (
  id_producto    INT AUTO_INCREMENT PRIMARY KEY,
  codigo         VARCHAR(20)   NOT NULL UNIQUE,
  nombre         VARCHAR(100)  NOT NULL,
  descripcion    VARCHAR(255)  NULL,
  precio         DECIMAL(10,2) NOT NULL,
  stock_actual   INT NOT NULL DEFAULT 0,
  stock_minimo   INT NOT NULL DEFAULT 0,
  estado         TINYINT(1) NOT NULL DEFAULT 1,
  fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  id_categoria   INT NOT NULL,
  CONSTRAINT fk_producto_categoria
    FOREIGN KEY (id_categoria) REFERENCES categorias(id_categoria)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_precio       CHECK (precio >= 0),
  CONSTRAINT ck_stock_actual CHECK (stock_actual >= 0),
  CONSTRAINT ck_stock_minimo CHECK (stock_minimo >= 0)
);

CREATE TABLE IF NOT EXISTS movimientos (
  id_movimiento INT AUTO_INCREMENT PRIMARY KEY,
  fecha_hora    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tipo          ENUM('entrada','salida') NOT NULL,
  cantidad      INT NOT NULL,
  observacion   VARCHAR(255) NULL,
  id_producto   INT NOT NULL,
  id_usuario    INT NOT NULL,
  CONSTRAINT fk_mov_producto
    FOREIGN KEY (id_producto) REFERENCES productos(id_producto)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT fk_mov_usuario
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
    ON UPDATE CASCADE ON DELETE RESTRICT,
  CONSTRAINT ck_mov_cantidad CHECK (cantidad > 0)
);

INSERT IGNORE INTO categorias (nombre, descripcion) VALUES
  ('Accesorios',     'Mouse, teclados, fundas y similares'),
  ('Almacenamiento', 'Pendrives, discos y tarjetas'),
  ('Conectividad',   'Cables, adaptadores y redes');
  