<?php

/**
 * Administrador de tareas / Soporte para Edición de actividades.
 * Puede ser invocado desde "actividades-adicionar.php" para adicionar registros nuevos.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$tarea_id = 1 * $this->post('item', 0);

if ($tarea_id <= 0 && $this->accion != 'actividades/adicionar') {
	mostrar_error('Los datos para la Actividad indicada no pudieron ser recuperados.');
}

$empleados_raw = $this->bdd->bddQuery(
	"SELECT empleado_id, empleado_nombre
	FROM empleado
	WHERE empleado_estado > 0
	ORDER BY empleado_nombre ASC"
);

$total_actividades = $this->bdd->bddQuery("SELECT count(tareas_id) as TOTAL FROM tareas");
print_r($total_actividades);

// Organiza la lista de empleados asociada por empleado_id
$empleados = array(0 => '(No asignado)');
foreach ($empleados_raw as $k => $info) {
	$empleados[$info['empleado_id']] = $info['empleado_nombre'];
}

// Títulos a usar para las columnas a editar
$titulos = array(
	'tareas_asunto' => 'Asunto',
	'tareas_descripcion' => 'Descripción',
	'tareas_responsable' => 'Responsable',
	'tareas_fecha_deseada' => 'Fecha estimada',
	'tareas_fecha_cierre' => 'Fecha en que se realizó'
);

// Errores encontrados
$this->errores = array();

// Valida si recibió valores para guardar
// La validación con $this->recuperarLlaveEdicion() previene errores al recargar el navegador luego de guardar.
if ($this->post('M' . md5('_ok'), 0) == $this->recuperarLlaveEdicion('tareas', $tarea_id)) {
	$guardar = array();
	foreach ($titulos as $columna => $nombre) {
		$valor = $this->post('M' . md5($columna), false);
		if ($valor !== false) {
			// Validaciones especiales
			if ($columna == 'tareas_responsable' && ($valor <= 0 || !isset($empleados[$valor]))) {
				$valor = '';
			}
			if ($columna == 'tareas_fecha_deseada') {
				if ($valor != '') {
					// Se segura cumpla con el formato de fecha
					$segundos = strtotime($valor);
					if ($segundos !== false) {
						$valor = date('Y-m-d H:i:s', $segundos);
					} else {
						$this->errores[] = 'Formato fecha no valido para <b>' . $nombre . '</b>';
					}
				}
			}
			$guardar[$columna] = $valor;
			if ($columna == 'tareas_fecha_cierre' && $valor == '') {
				$guardar[$columna] = null;
			}
		}
		// Requeridos
		if (($valor == '' || $valor == 0) && strpos('.tareas_fecha_deseada.tareas_asunto.tareas_responsable.', $columna) !== false) {
			$this->errores[] = 'Valor requerido para <b>' . $nombre . '</b>';
		}
	}

	if (count($this->errores) <= 0) {
		// Guarda en base de datos
		$info_respuesta = '';
		if ($tarea_id <= 0) {
			// Valida limite
			$total_actividades = $this->bdd->count("tareas");
			if ($total_actividades >= MAX_TAREAS) {
				$this->errores[] = "Adición no permitida: Se alcanzó el límite de tareas a registrar (" . MAX_TAREAS . ")";
			}
			// Actualizar datos
			elseif (!$this->bdd->bddAdicionar('tareas', $guardar)) {
				$this->errores[] = 'No pudo adicionar actividad con los datos recibidos.';
			} else {
				// Actualización exitosa. Recarga página
				$info_respuesta = 'Actividad <b>' . htmlspecialchars($guardar['tareas_asunto']) . '</b> adicionada con éxito';
			}
		} else {
			// Editar datos
			if (!$this->bdd->bddEditar('tareas', $guardar, $tarea_id)) {
				$this->errores[] = 'No pudo actualizar actividad con los datos recibidos.';
			} else {
				$info_respuesta = 'Actividad <b>' . htmlspecialchars($guardar['tareas_asunto']) . '</b> actualizada con éxito';
			}
		}
		if ($info_respuesta != '') {
			// Actualización exitosa. Recarga página
			$this->removerLlaveEdicion('tareas', $tarea_id);
			$this->setVista('actividades/resultado');
			$this->set('mensaje-ok', $info_respuesta);
		}
	}
}

// Consulta actividad solicitada
$tareas = array();
if ($tarea_id > 0) {
	$tareas = $this->bdd->bddPrimerRegistro(
		"SELECT tareas.*, empleado_nombre
		FROM tareas
		LEFT JOIN empleado ON (empleado_id = tareas_responsable)
		WHERE tareas_id = ?",
		[$tarea_id]
	);
}

$formatos = array(
	// 'item' => 'hidden',
	'tareas_asunto' => 'text:100',
	'tareas_descripcion' => 'textarea',
	'tareas_responsable' => $empleados,
	'tareas_fecha_deseada' => 'date',
	'tareas_fecha_cierre' => 'date',
	'_ok' => 'hidden'
);

// Filtra las actividades para los titulos dados
$actividades = array();
foreach ($titulos as $t => $nombre) {
	$valor = '';
	if (isset($guardar[$t])) {
		$valor = trim("{$guardar[$t]}");
	} elseif (isset($tareas[$t])) {
		$valor = trim("{$tareas[$t]}");
	}
	// Consideraciones especiales
	if ($t == 'tareas_fecha_deseada') {
		if ($valor != '') {
			$segundos = strtotime($valor);
			if ($segundos !== false) {
				$valor = date('Y-m-d H:i:s', $segundos);
			}
		} else {
			$valor = date('Y-m-d H:i:s');
		}
	}

	$actividades[$t] = $valor;
}

// Fija valor para el campo de llave-edición
$actividades['_ok'] = $this->crearLlaveEdicion('tareas', $tarea_id);

// Preserva valores para uso de la vista
$this->set('tarea-id', $tarea_id);
$this->set('actividad', $actividades);
$this->set('titulos', $titulos);
$this->set('formatos', $formatos);
