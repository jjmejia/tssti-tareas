<?php
/**
 * Administrador de tareas / Soporte para borrado de empleados.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$empleado_id = 1 * $this->post('item', 0);

if ($empleado_id <= 0) {
	mostrar_error('Empleado indicado no es valido.');
}

if (!$this->bdd->bddRemover('empleado', [ $empleado_id ])) {
	$this->set('mensaje-error', 'No pudo eliminar el empleado indicada.');
}
else {
	$this->set('mensaje-ok', 'Empleado <b>' . $empleado_id . '</b> eliminado con éxito.');
}

// Prefija script alterno como vista de salida
$this->setVista('empleados/resultado');
