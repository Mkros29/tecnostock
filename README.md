Nombre: Lucas Bastian Fuentes Reyes / seccón 411 / Sistema De Informaciones 

# TecnoStock - Sistema de inventario
Aplicación web para gestionar productos y movimientos de stock.
Stack: PHP 8 + PDO, MySQL/MariaDB, HTML5, Bootstrap 5 y JavaScript.
## Instalación
1. Instalar XAMPP e iniciar Apache junto a MySQL.
2. Copiar la carpeta exactamente en (htdocs/tecnostock).
3. En phpMyAdmin, importar (base_datos.sql) y luego (datos_prueba.sql).
4. Abrir (http://localhost/tecnostock/login.php).
5. Usuario de prueba: (admin@tecnostock.cl) / (Admin1234).
## Actor
Encargado de inventario.
## Trazabilidad
 Requerimiento / Archivo / Prueba /
 RF1 Iniciar sesión / login.php, auth.php, logout.php / P01-P03 /
 RF2 Registrar productos / producto_form.php, producto_guardar.php / P04-P07 /
 RF3 Listar productos / productos.php / P04 /
 RF4 Buscar / productos.php / P08 /
 RF5 Modificar producto / producto_form.php, producto_guardar.php / P09 /
 RF6 Desactivar / producto_estado.php / P10 /
 RF7 Entradas y salidas / movimiento_form.php, movimiento_guardar.php, movimientos.php / P11-P14 /
 RF8 Alerta de stock bajo / productos.php / P15 /
## Reglas de negocio
 Regla / Dónde se cumple /
 RN1 Código único / UNIQUE en BD + manejo de error en PHP /
 RN2 Sin valores negativos / CHECK en BD + validación JS y PHP /
 RN3 Movimiento con fecha, tipo, cantidad, producto y usuario / Tabla movimientos (NOT NULL + FK) /
 RN4 Salida no deja stock bajo cero / Validación dentro de la transacción /
 RN5 No se elimina, se desactiva / FK RESTRICT + campo estado /
 RN6 Stock automático / UPDATE dentro de la transacción /
## Seguridad
Consultas preparadas con PDO, (password_hash()), (htmlspecialchars()),
validación en cliente y servidor, sesiones protegidas con (auth.php).