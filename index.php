<?php

ini_set('display_errors', 1); // Mostrar errores en pantalla
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); // Reportar todos los errores de PHP



require_once 'mensaje.php';// Incluir la clase Persona
require_once 'config.php'; // Incluir la configuración de la base de datos
require_once 'logica.php'; // Incluir la lógica del programa

// Inicializar variables
$isEditing = false; // maneja el estado de edición y agregar
$filter = null; // Almacena el filtro de búsqueda

// inicion de los controladores de las acciones del formulario

//if (isset($_GET['filter'])) {
    //$filter = $_GET['filter'];
   // $alumnos = filtrarAlumnos($conn, $filter); // Puede que necesites crear esta función.
//} else {
   $mensajes = cargarMensajes($conn); // cargar todos los alumnos
//}


// controller para el metodo post del formulario, se recibe el action del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    //echo "entro al post";
    switch ($_POST['action']) {
        case 'add':
    //echo "entro al add";

            $result = agregarMensaje($_POST, $conn);
            if ($result === true) {
                header("Location: index.php");
            } else {
                echo $result;
            }
            break;
        case 'update':
            if (isset($_POST['id'])) {
                $result = modificarMensaje($_POST, $conn);
                if ($result === true) {
                    //echo "Alumno modificado correctamente";
                    header("Location: index.php");
                } else {
                    echo $result;
                }
            }
            break;
    }
}

// controller para editar un alumno, se recibe el id del alumno a editar
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
    $isEditing = true; // cambiar el estado de edición
    $idToEdit = intval($_GET['id']); // id del alumno a editar
    $result = editarMensaje($idToEdit, $conn);

    if ($result instanceof Mensaje) {
        $mensajeToEdit = $result;
    } else {
        echo $result;
        header("Location: index.php");
    }
}

// controller para eliminar un alumno, se recibe el id del alumno a eliminar
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['index'])) {
    $result = eliminarMensaje(intval($_GET['index']), $conn);
    if ($result === true) {
        header("Location: index.php");
        exit(); // detener la ejecución del script  
    } else {
        echo $result;
    }
}

?>

<!-- formulario y la tabla de listado de alumnos. -->

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Estilos CSS de Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <title>CRUD Alumnos</title>
</head>
<body>

<!-- Navegación -->
<header class=" bg-primary d-flex justify-content-center">
    <img id="logo" src="./img/logo-stationary" class="mx-auto d-block" width="200" height="80" alt="png">
    <!-- <img src="/img/logo-stationary.png" class="img-fluid rounded mx-auto d-block" alt="..."> -->
    <!-- <p class="mb-0 p-2 fs-6 text-white Letter-sppacing">• ♡ STATIONARY WITH LOVE ♡ •</p> -->
  </header>
  <nav class="navbar navbar-expand-lg navbar-primary bg-primary p-3 bg-body-primary rounded-bottom" id="menu">
    <div class="container-fluid">
      <a class="navbar-brand" href="index.html">
        <span class="text-white fs-5 fw-bold">• ♡ •</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false"aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto mb-2 mb-lg-0">
          <li class="nav-item">
            <a class="nav-link active text-white" aria-current="page" href="index.html">INICIO</a>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="#equipo">EQUIPO</a>
          </li>
          
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#seccion-servicios" role="button" data-bs-toggle="dropdown" aria-expanded="false">SERVICIOS</a>
            <ul class="dropdown-menu ">
              <li><a class="dropdown-item text-primary" href="Branding.html">Branding</a></li>
              <li><a class="dropdown-item text-primary" href="Premadebrands.html">Premade brands</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-primary" href="fotografia.html">Fotografía</a></li>
              <li><a class="dropdown-item text-primary" href="Diseño.html">Diseño</a></li>
              <li><a class="dropdown-item text-primary" href="Asesoria.html">Asesorías</a></li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item text-primary" href="Contenido.html">Contenido</a></li>
              <li><a class="dropdown-item text-primary" href="Productos.html">Productos</a></li>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link text-white" href="#seccion-contacto">CONTACTO</a>
          </li>
        </ul>
      
        <form class="d-flex">
          <input class="form-control me-2" type="email" placeholder="correo electronico" aria-label="email">
          <button class="btn btn-Secondary btn-primary-outline-success" type="button">suscribete</button>
        </form>
        <div class="hm-icon-cart">
      </div>
      </div>
    </div>
  </nav>


<!-- seccion de la vista -->
<div class="container mt-4">
    <h2 class="text-center mb-4"><?= $isEditing ? 'Editar Mensaje' : 'Mensaje' ?></h2> 
<!-- seccion de busqueda -->
   <!-- 
    <form action="index.php" method="get" class="d-flex custom-search-form" role="search">
        <div class="row">    
            <div class="col-md-10">
                <input class="form-control me-2 custom-search-input" type="search" placeholder="Buscar" name="filter" id="filter" aria-label="filter" value="<?= $filter ?>">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-success custom-search-button" type="submit">Buscar</button>
            </div>
        </div>
    </form>
    -->
    <!-- Formulario -->
    <div class="border-4 border-4  border-secudary" id="seccion-contacto">
  <div class="container  " style="max-width: 500px" id="contenedor-formulario">
    <div class="text-center mb-4" id="titulo-formulario">
      <div>
        <img src="./img/logo-contactanos-05.png" alt="contacto" class="img-fluid ps-5">
      </div>
      <p class="fs-5 mx-2 fw-light text-dark">Estamos aqui para hacer realidad tus proyectos</p>
    </div>

    <form id="formMensaje" onsubmit="return validarNombre()" action="index.php" method="post" class="border-1">   
    <?php if ($isEditing): ?>
            <input type="hidden" name="id" value="<?= $mensajeToEdit->getId() ?>">
            <?php endif; ?>
      <div class= "mb-3 mx-4"> 
        <input type="email" class="form-control" id="email" name="email" placeholder="nombre@ejemplo.com"  value="<?= $isEditing ? $mensajeToEdit->getEmail() : '' ?>" required>
      </div>
      <div class="mb-3 mx-4">    
        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre" value="<?= $isEditing ? $mensajeToEdit->getNombre() : '' ?>" required>
      </div>
      <div class="mb-3 mx-4">
        <input type="tel" class="form-control" id="telefono" name="telefono" placeholder="000-000-000"  value="<?= $isEditing ? $mensajeToEdit->getTelefono() : '' ?>" required>
      </div>
      <div class="mb-3 mx-4">       
      <textarea class="form-control" name="mensaje" id="mensaje" placeholder="Escribe tu mensaje" rows="3" required>
    <?= $isEditing ? $mensajeToEdit->getMensaje() : '' ?>
      </textarea>

      </div>
      <div class="d-grid gap-2 col-10 mx-auto">
        <button type="submit" class="btn btn-primary mb-4 fs-5" name="action" value="<?= $isEditing ? 'update' : 'add' ?>">
            <?= $isEditing ? 'Modificar Mensaje' : 'Enviar Mensaje' ?>
        </button> 
      </div>
    </form>
  </div>
</div>

    <!-- Fin de Formulario -->
</div>
<!-- Tabla de Mensajes -->
<div class="pt-5">
    <table class="table container">
        <thead>
            <tr class="text-center">
                <th>Acciones</th>
                <th>Email</th>
                <th>Nombre</th>
                <th>Telefono</th>
                <th>Mensaje</th>
                <!-- Otros encabezados de tabla... -->
            </tr>
        </thead>
        <tbody>
            <?php foreach ($mensajes as $index => $mensaje) : ?>
                <tr>
                    <td>
                        <!-- Enlaces para Editar, Eliminar y presentarse-->
                        <a href="index.php?action=edit&id=<?= $mensaje->getId()?>">
                            <img src="imgs/file-edit-line.png" alt="Editar" width="24px">
                        </a>
                        <a href="index.php?action=delete&index=<?= $mensaje->getId() ?>" onclick="return confirm('¿Estás seguro de querer eliminar este registro?')">
                            <img src="imgs/delete-bin-line.png" alt="Eliminar" width="24px">
                        </a>
                    </td>
                    <td><?= $mensaje->getEmail() ?></td>
                    <td><?= $mensaje->getNombre() ?></td>
                    <td><?= $mensaje->getTelefono() ?></td>
                    <td><?= $mensaje->getMensaje() ?></td>
                </tr>
                <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Modal Acerca de -->
<div class="modal modal-xl fade" id="acercaDeModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Acerca de</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <h3 class="">Curso : Desarrollo web en entorno servidor</h3>
        <h3 class="">Desarrollado por: Juan Carlos Sulbaran</h3>
        <h3 class="">Profesor :  Victor Rodriguez</h3>
        <h3 class="">Practica final primer corte</h3>
        <h3 class="">Version : 0.1</h3>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
    </div>
  </div>
</div>

<!-- Modal Acerca Presentarse -->
<!--
<div class="modal modal-xl fade" id="acercaDeModa2" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalLabel">Acerca de</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
      </div>
      <div class="modal-body text-center">
        <h3 class="">Presentarse</h3>
        <?= $alumno->calificar("te informo que he ",$alumno->getNotaAcumulada())?>
        <h3 class=""></h3>

      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
        </div>
    </div>
  </div>
</div>
-->
<!-- Scripts JS de Bootstrap 5 (incluye Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="./scripts.js"></script>
</body>
</html>
