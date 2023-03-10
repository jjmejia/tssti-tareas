<?php
/**
 * Administrador de tareas / Soporte para borrado de actividades.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$tarea_id = 1 * $this->post('item', 0);

if ($tarea_id <= 0) {
	mostrar_error('Actividad indicada no es valida.');
}

if (!$this->bdd->bddRemover('tareas', [ $tarea_id ])) {
	$this->set('mensaje-error', 'No pudo eliminar la actividad indicada.');
}
else {
	$this->set('mensaje-ok', 'Actividad <b>' . $tarea_id . '</b> eliminada con éxito.');
}

// Prefija script alterno como vista de salida
$this->setVista('actividades/resultado');
