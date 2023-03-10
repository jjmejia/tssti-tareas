<?php
/**
 * Administrador de tareas.
 * Emplea modelo básico sin usar URLs amigables para no requerir ajustes en .htaccess.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

require_once __DIR__ . '/lib/funciones.php';
require_once __DIR__ . '/vista/vista-soporte.php';

$app = new LocalApp();

try {
	// Conectarse a la base de datos
	$app->conectarBDD(__DIR__ . '/lib/data/bdd.ini');

	// Validar acción solicitada
	// (Acción por defecto: Listar actividades)
	$accion = $app->post('accion');
	if ($accion == '') { $accion = 'actividades/listar'; }

	$app->setAccion($accion);

	// Ejecuta archivo de controlador asociado a la acción.
	$app->ejecutarAccion('control');

	// Ejecuta archivo de vista asociada ($app->accion puede ser modificado en la acción anterior).
	$app->ejecutarAccion('vista');

	// Genera salida a pantalla con el texto capturado hasta este punto.
	$app->renderSalida('render_salida');
}
catch (Exception $e) {
	$app->mostrarError($e->getMessage(), 'render_salida');
}