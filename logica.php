<?php
require_once 'config.php';
require_once 'mensaje.php';


/**
 * Conecta a la base de datos carga los datos de los alumnos y los devuelve en un array
 * @return array
 * @param mysqli $conn  Conexión a la base de datos
 */
function cargarMensajes($conn) {
  $mensajes = [];
  //$sql = "SELECT id, correo_electronico, nombre, telefono, mensaje as id, email, nombre, telefono, mensaje FROM mensaje";
  $sql = "SELECT * FROM mensaje";

  $result = $conn->query($sql);
  
  if ($result && $result->num_rows > 0) {
      // Convertir cada fila en un objeto Alumno
      while($row = $result->fetch_assoc()) {
          $mensajes[] = new Mensaje($row['id'],$row['email'], $row['nombre'], $row['telefono'], $row['mensaje']);
      }
      //ordena el array de alumnos por nombre
      usort($mensajes, function($a, $b) {
          return $a->getNombre() <=> $b->getNombre();
      });
  }
  return $mensajes;
}


/**
 * Filtra los alumnos por nombre, apellido, telefono, correo_electronico, nota1, nota2, nota3, asistencia, finales
 */
function filtrarAlumnos($conn, $filter) {
    $alumnos = [];
    $fields = ["nombre", "apellido", "telefono", "correo_electronico", "nota1", "nota2", "nota3", "asistencia", "finales"];
    
    $conditions = [];
    foreach ($fields as $field) {
        $conditions[] = "$field LIKE '%$filter%'";
    }
    $sqlCondition = implode(" OR ", $conditions);

    $sql = "SELECT id, nombre, apellido, telefono, correo_electronico as email, nota1, nota2, nota3, asistencia, finales as examenFinal FROM alumno WHERE $sqlCondition";

    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            $alumnos[] = new Alumno($row['id'],$row['nombre'], $row['apellido'], $row['telefono'], $row['email'], $row['nota1'], $row['nota2'], $row['nota3'], $row['asistencia'], $row['examenFinal']);
        }
        
        usort($alumnos, function($a, $b) {
            return $a->getNombre() <=> $b->getNombre();
        });
    }
    return $alumnos;
}

/**
 * Agrega un alumno a la base de datos
 * @param array $data  Datos del alumno a agregar
 * @param mysqli $conn  Conexión a la base de datos
 */
function agregarMensaje($data, $conn) {
  $nombre = $data['nombre'];
  $telefono = $data['telefono'];
  $email = $data['email'];
  $mensaje = $data['mensaje'];


  $stmt = $conn->prepare("INSERT INTO mensaje (nombre, telefono, email, mensaje) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssss", $nombre, $telefono, $email, $mensaje);
  
  if($stmt->execute()) {
      return true;
  } else {
      return "Error: " . $stmt->error;
  }
  $stmt->close();
}


/**
 * Modifica un alumno en la base de datos
 * @param array $data  Datos del alumno a modificar
 * @param mysqli $conn  Conexión a la base de datos
 */
function modificarMensaje($data, $conn) {
  $id = intval($data['id']); 
  $nombre = $data['nombre'];
  $telefono = $data['telefono'];
  $email = $data['email'];
  $mensaje = $data['mensaje'];
var_dump($data);
  $stmt = $conn->prepare("UPDATE mensaje SET nombre = ?, telefono = ?, email = ?, mensaje = ? WHERE id = ?");
  $stmt->bind_param("ssssi", $nombre, $telefono, $email, $mensaje, $id);
  
  if($stmt->execute()) {
      return true;
  } else {
      return "Error: " . $stmt->error;
  }
  $stmt->close();
}


/**
 * Elimina un alumno de la base de datos
 * @param int $id  ID del alumno a eliminar
 * @param mysqli $conn  Conexión a la base de datos
 */
function eliminarMensaje($id, $conn) {
  $stmt = $conn->prepare("DELETE FROM mensaje WHERE id = ?");
  $stmt->bind_param("i", $id);
  
  if($stmt->execute()) {
      $stmt->close();
      return true;
  } else {
      $error = "Error al eliminar: " . $stmt->error;
      $stmt->close();
      return $error;
  }
}


/**
 * Elimina un alumno de la base de datos
 * @param int $id  ID del alumno a eliminar
 * @param mysqli $conn  Conexión a la base de datos
 */
function editarMensaje($id, $conn) {
  $stmt = $conn->prepare("SELECT * FROM mensaje WHERE id = ?");
  $stmt->bind_param("i", $id);
  
  if($stmt->execute()) {
      $result = $stmt->get_result();
      $stmt->close();

      if ($result && $result->num_rows > 0) {
          $row = $result->fetch_assoc();
          $mensajeToEdit = new Mensaje($row['id'], $row['nombre'], $row['telefono'], $row['email'], $row['mensaje']);
          return $mensajeToEdit;
      } else {
          return "No se encontró el registro a editar";
      }
  } else {
      $error = "Error al consultar: " . $stmt->error;
      $stmt->close();
      return $error;
  }
}

?>