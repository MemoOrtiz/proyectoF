<?php
// Configuración de conexión
$host = '10.10.0.4';         // IP privada de tu VM Linux/MySQL
$user = 'appuser';          // Usuario que creaste en MySQL
$pass = '12345678';     // Su contraseña
$db   = 'practica';         // Tu base de datos

// Conectar a MySQL
$mysqli = new mysqli($host, $user, $pass, $db);
if ($mysqli->connect_error) {
    die("Error de conexión: " . $mysqli->connect_error);
}

// Handle: Eliminar último registro
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_last'])) {
    // Borra el registro con el ID más alto
    $mysqli->query("DELETE FROM personas ORDER BY id DESC LIMIT 1");
}

// Crear tabla si no existe
$mysqli->query("
  CREATE TABLE IF NOT EXISTS personas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    estado_civil ENUM('Soltero','Casado','Divorciado','Viudo') NOT NULL
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
");

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $mysqli->real_escape_string($_POST['nombre'] ?? '');
    $estado = $mysqli->real_escape_string($_POST['estado_civil'] ?? '');
    if ($nombre && $estado) {
      $mysqli->query("
        INSERT INTO personas (nombre, estado_civil)
        VALUES ('$nombre', '$estado')
      ");
    }
}

// Leer registros
$result = $mysqli->query("SELECT * FROM personas ORDER BY id ASC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Registro de Personas</title>
  <style>
    body { font-family: sans-serif; max-width: 600px; margin: 2rem auto; }
    form, table { width: 100%; margin-bottom: 2rem; }
    input, select, button { padding: .5rem; font-size: 1rem; width: 100%; margin-top: .5rem; }
    table { border-collapse: collapse; width: 100%; }
    th, td { padding: .5rem; border: 1px solid #ccc; }
  </style>
</head>
<body>
  <h1>Registro de Personas</h1>
	<!-- Botón para eliminar el último registro -->
  	<form method="post" onsubmit="return confirm('¿Eliminar el último registro?');">
    	<button type="submit" name="delete_last" class="btn-delete">Eliminar último registro</button>
  	</form>

  <form method="post">
    <label>Nombre:
      <input type="text" name="nombre" required>
    </label>
    <label>Estado Civil:
      <select name="estado_civil" required>
        <option value="">-- Selecciona --</option>
        <option value="Soltero">Soltero</option>
        <option value="Casado">Casado</option>
        <option value="Divorciado">Divorciado</option>
        <option value="Viudo">Viudo</option>
      </select>
    </label>
    <button type="submit">Guardar</button>
  </form>

  <h2>Listado de Personas</h2>
  <table>
    <thead>
      <tr><th>ID</th><th>Nombre</th><th>Estado Civil</th></tr>
    </thead>
    <tbody>
      <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $row['id'] ?></td>
        <td><?= htmlspecialchars($row['nombre']) ?></td>
        <td><?= $row['estado_civil'] ?></td>
      </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</body>
</html>
