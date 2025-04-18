<?php

/**
 * Administrador de tareas / Soporte para Listado de empleados.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$tareas = $this->bdd->bddQuery(
	"SELECT
		empleado_id, empleado_nombre, empleado_estado, count(tareas_id) as actividades
	FROM empleado
		LEFT JOIN tareas ON (tareas_responsable = empleado_id)
	GROUP BY empleado_id, empleado_nombre, empleado_estado
	ORDER BY empleado_nombre ASC"
);

// $empleados = array();

$estados = array();
$titulos = array(
	'id' => 'ID',
	'empleado_nombre' => 'Nombre',
	'empleado_estado' => '',
	'estado' => 'Estado',
	'actividades' => 'Actividades'
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
	$actividades[$k]['id'] = $info['empleado_id'];
	$actividades[$k]['estado'] = 'Retirado';
	if ($actividades[$k]['empleado_estado'] > 0) {
		$actividades[$k]['estado'] = 'Activo';
	}
}

// Mensaje previo
$mensaje = $this->get('mensaje-ok', '');
if ($mensaje !== '') {
	$mensaje .= '<br />' . PHP_EOL;
}
// Menús disponibles para esta vista
$menus = array(
	'empleados/adicionar' => 'Nuevo empleado',
	'actividades/listar' => 'Lista de actividades'
);

$total_empleados = $this->bdd->count("empleado");
if ($total_empleados >= MAX_EMPLEADOS) {
	unset($menus['empleados/adicionar']);
	$this->set('mensaje-ok', $mensaje . "Se alcanzó el límite de empleados a registrar (" . MAX_EMPLEADOS . ").");
}

// Preserva valores para uso de la vista
$this->set('actividades', $actividades);
$this->set('titulos', $titulos);
$this->set('menus', $menus);
