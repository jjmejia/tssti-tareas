<?php
/**
 * Administrador de tareas / Soporte para Edición de actividades.
 * Permite marcar una actividad como "Realizada" con un solo click. La fecha de cierre
 * asignada es la del momento en que se actualiza el registro.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$tarea_id = 1 * $this->post('item', 0);

if ($tarea_id > 0) {
	$tareas_cerrar = $this->bdd->bddPrimerRegistro("SELECT tareas_id, tareas_fecha_cierre, tareas_asunto
		FROM tareas
		WHERE tareas_id = ?",
		[ $tarea_id ]
		);
	if (isset($tareas_cerrar['tareas_id']) && trim("{$tareas_cerrar['tareas_fecha_cierre']}") == '') {
		$guardar = [ 'tareas_fecha_cierre' => date('Y-m-d H:i:s') ];
		if ($this->bdd->bddEditar('tareas', $guardar, $tarea_id)) {
			$this->set('mensaje-ok', 'Actividad <b>' . htmlspecialchars($tareas_cerrar['tareas_asunto']) . '</b> actualizada con éxito');
		}
	}
}

$this->setVista('actividades/listar');

// Carga nuevamente el listado de actividades
include_once 'actividades-listar.php';