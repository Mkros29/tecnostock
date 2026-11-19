 ID / Caso / Datos / Resultado esperado / Resultado obtenido /

 P01 / Login inválido / admin@tecnostock.cl / clave incorrecta / Mensaje de error / 
 P02 / Login Valido / Admin1234 / se inicio sesión / 
 P03 / Acceso sin sesión / abrir productos.php sin login / Redirige a login / 
 P03.5 / Acceso sin sesión / volver al login / Si se redirige al login con los datos / 
 P04 / Registro válido / TEC-001, Mouse, 9990, stock 2, mín. 5 / Aparece en el listado / 
 P05 / Código duplicado / TEC-001 otra vez / "El código ya existe" / 
 P06 / Precio negativo / -100 / Bloqueado / 
 P07 / Campo faltante / nombre vacío / Bloqueado / 
 P08 / Búsqueda / "mouse", "tec", categoría / Filtra el listado / 
 P09 / Edición / cambiar precio y categoría / Se actualizan / 
 P10 / Desactivar / producto con movimientos / Queda gris, sigue en historial / 
 P11 / Entrada válida / +10 / Stock sube 10 / 
 P12 / Salida válida / -9 / Stock baja 9 / 
 P13 / Salida excesiva / -50 con stock 3 / Rechazada, stock intacto / 
 P14 / Cantidad 0 o negativa / 0 y -5 / Bloqueada / 
 P15 / Stock bajo / stock menor al mínimo / Fila roja con badge / 
 P16 / Texto especial / nombre con (<script>) / Se muestra escapado / 