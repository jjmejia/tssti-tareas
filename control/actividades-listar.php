<?php

/**
 * Administrador de tareas / Soporte para Listado de actividades.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$tareas = $this->bdd->bddQuery(
	"SELECT
		tareas.*, empleado_nombre, empleado_estado
	FROM tareas
	LEFT JOIN empleado ON (empleado_id = tareas_responsable)
	ORDER BY tareas_fecha_cierre ASC, tareas_fecha_deseada DESC"
);

$estados = array();
$titulos = array(
	'id' => 'ID',
	'tareas_asunto' => 'Asunto',
	'tareas_descripcion' => '',
	'estado-raw' => '',
	'estado' => 'Estado',
	'empleado_nombre' => 'Responsable',
	'fecha_deseada' => 'Fecha entrega',
	'retraso' => 'Días retraso'
);

// Filtra las actividades para los titulos dados
$actividades = array();
foreach ($tareas as $k => $info) {
	foreach ($titulos as $t => $nombre) {
		$valor = '';
		if (isset($info[$t])) {
			$valor = trim("{$info[$t]}");
		}
		$actividades[$k][$t] = $valor;
	}
	// Complementa valores
	$actividades[$k]['id'] = $tareas[$k]['tareas_id'];
	$actividades[$k]['estado'] = 'Pendiente';
	$actividades[$k]['estado-raw'] = 0; // Pendiente
	$actividades[$k]['retraso'] = 0;

	if ($actividades[$k]['empleado_nombre'] == '') {
		$actividades[$k]['empleado_nombre'] = '(No asignado)';
	} elseif ($tareas[$k]['empleado_estado'] <= 0) {
		$actividades[$k]['empleado_nombre'] .= ' (R)';
	}
	$seg_deseada = strtotime($tareas[$k]['tareas_fecha_deseada']);
	$actividades[$k]['fecha_deseada'] = date('Y-m-d', $seg_deseada);

	if ($tareas[$k]['tareas_fecha_cierre'] != '') {
		$actividades[$k]['estado-raw'] = 1; // Cerrada
		$actividades[$k]['estado'] = 'Realizada';
		$actividades[$k]['clase'] = 'realizada';
	} else {
		// Dias de retraso
		$dias = floor((time() - $seg_deseada) / 86400);
		if ($dias > 0) {
			$actividades[$k]['retraso'] = $dias;
			$actividades[$k]['estado'] = 'Vencida';
			$actividades[$k]['clase'] = 'vencida';
		}
	}
}

// Mensaje previo
$mensaje = $this->get('mensaje-ok', '');
if ($mensaje !== '') {
	$mensaje .= '<br />' . PHP_EOL;
}
// Menús disponibles para esta vista
$menus = array(
	'actividades/adicionar' => 'Nueva actividad',
	'empleados/listar' => 'Lista de empleados'
);

$total_actividades = $this->bdd->count("tareas");
$total_empleados = $this->bdd->count("empleado");
if ($total_empleados <= 0) {
	unset($menus['actividades/adicionar']);
	$this->set('mensaje-ok', $mensaje . 'Recuerde adicionar empleados antes de poder ingresar Actividades');
} elseif ($total_actividades >= MAX_TAREAS) {
	unset($menus['actividades/adicionar']);
	$this->set('mensaje-ok', $mensaje . "Se alcanzó el límite de actividades a registrar (" . MAX_TAREAS . "). Elimine alguna de las actividades actuales para poder adicionar nuevas.");
}

// Preserva valores para uso de la vista
$this->set('actividades', $actividades);
$this->set('titulos', $titulos);
$this->set('menus', $menus);
