<?php
/* @var $dbConnection mysqli */
/* @var $query string */
/* @var $clave string */
/* @var $nombre string */
/* @var $erroresValidacion array */

// Activando reporte de errores fatales y en tiempo de compilacion
error_reporting(E_ERROR | E_COMPILE_ERROR);
// Activando el uso de sesiones
session_start();

// Inicializacion de variables
$dbConnection = null;
$query = '';
$clave = '';
$nombre = '';
$erroresValidacion = array();

// Conectandonos a la base de datos
$dbConnection = new mysqli('localhost', 'root', '', 'control_oficialias');
// Comprobando si hubo un error al conectarse a la base de datos
if( $dbConnection->connect_error ){
	// Almacenando mensaje de error en la session
	$_SESSION['errorInesperado'] = 'Base de datos no disponible, favor de revisar';
	// Redireccionando a pagina web para mostrar errores
	header('Location: error.php');
	exit();
}else{
	if( isset($_POST['clave'], $_POST['nombre']) ){
		$clave = $_POST['clave'];
		$nombre = $_POST['nombre'];
		// Validando los campos antes de usarlos en el query de insercion de registro
		if( empty($clave) ){
			array_push($erroresValidacion, 'Olvidaste proporcionar la clave de la Region');
		}
		if( !is_numeric($clave) ){
			array_push($erroresValidacion, 'La clave de la Region debe ser numerica');
		}
		if( empty($nombre) ){
			array_push($erroresValidacion, 'Olvidaste proporcionar el nombre de la Region');
		}
		if ( empty($erroresValidacion) ){
			// Escapamos los valores para evitar ataques como la inyeccion de sql, Javascript, etc.
			$clave = $dbConnection->real_escape_string($clave);
			$nombre = $dbConnection->real_escape_string($nombre);
			// Preparando el query para obtener los registro de regiones
			$query = "INSERT INTO region(clave, nombre) VALUES ($clave, '$nombre')";
			if( $dbConnection->query($query) ){
				// Redireccionando a pagina de regiones
				header('Location: regiones.php');
				exit();
			}else{
				array_push($erroresValidacion, 'Fallo la ejecucion de la consulta, favor de revisar');
			}
		}
	}
}
?>
<!DOCTYPE html>
<html><!-- Este documento usa etiquetas semanticas de HTML5 -->
	<head>
		<!-- Codificacion de caracteres -->
		<meta charset="utf-8" >
		<!-- Titulo del documento -->
		<title>Control de Oficialias</title>
		<!-- Icono del documento (Favicon) -->
		<link href="img/favicon.png" rel="icon" type="image/png" >
	</head>
	<body>
		
		<header><!-- Cabecera de la pagina  -->
			<h1>Sistema de Control de Oficialias</h1>
			<nav><!-- Vinculos de navegacion -->
				<ul>
					<li><a href="index.php">Inicio</a></li>
					<li><a href="regiones.php">Regiones </a></li>
				</ul>
			</nav>
		</header>
		
		<div><!-- Contenido principal de la pagina web -->
			<h2>Crear region</h2>
			
			<ul>
				<li><a href="regiones.php">Regresar</a></li>
			</ul>
			
			<!-- Mostramos errores de validacion, si los hay -->
			<?php if( !empty($erroresValidacion) ){ ?>
				<p><b>Por favor corrija los siguientes problemas</b></p>
				<ul>
					<?php foreach($erroresValidacion as $error){ ?>
						<li><?= $error ?></li>
					<?php } ?>
				</ul>
			<?php } ?>
			
			<form method="post"><!-- Formulario para crear region -->
				<div>
					<label for="clave">Clave</label>
					<input id="clave" name="clave" type="text" value="<?= $clave ?>" >
				</div>
				<div>
					<label for="nombre">Nombre</label>
					<input id="nombre" name="nombre" type="text" value="<?= $nombre ?>" >
				</div>
				<div>
					<button type="submit">Guardar</button>
				</div>
			</form>
		</div>
		
		<footer><!-- Pie de pagina -->
			<p>Derechos Reservados 2015</p>
			<p>74-71-32-86-90<br>registrocivil@guerrero.gob.mx</p>
		</footer>
		
	</body>
</html>