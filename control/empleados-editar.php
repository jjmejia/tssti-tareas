<?php

/**
 * Administrador de tareas / Soporte para Edición de empleados.
 * Puede ser invocado desde "empleados-adicionar.php" para adicionar registros nuevos.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$empleado_id = 1 * $this->post('item', 0);

if ($empleado_id <= 0 && $this->accion != 'empleados/adicionar') {
	mostrar_error('Los datos para el Empleado indicado no pudieros ser recuperados.');
}

// Títulos a usar para las columnas a editar
$titulos = array(
	'empleado_nombre' => 'Nombre',
	'empleado_estado' => 'Estado'
);

// Errores encontrados
$this->errores = array();

// Valida si recibió valores para guardar
// La validación con $this->recuperarLlaveEdicion() previene errores al recargar el navegador luego de guardar.
// NOTA: Previene validar como positivo el valor de vacio tanto en Post y llave de control
$post_check = $this->post('M' . md5('_ok'), '');
if ($post_check !== '' && $post_check === $this->recuperarLlaveEdicion('empleado', $empleado_id)) {
	$guardar = array();
	foreach ($titulos as $columna => $nombre) {

		// Ignora elementos sin nombre
		if ($nombre == '') {
			continue;
		}

		$valor = $this->post('M' . md5($columna), false);
		if ($valor !== false) {
			// Validaciones especiales
			if ($columna == 'empleado_estado') {
				if ($valor > 0) {
					$valor = 1;
				} else {
					$valor = 0;
				}
			}
			if ($valor !== '') {
				$guardar[$columna] = $valor;
			}
		}
		// Requeridos
		if (($valor == '' || $valor == 0) && strpos('.empleado_nombre.', $columna) !== false) {
			$this->errores[] = 'Valor requerido para <b>' . $nombre . '</b>';
		}
	}

	// print_r($guardar); echo "<hr>$empleado_id<hr>";

	if (count($this->errores) <= 0) {
		// Guarda en base de datos
		$info_respuesta = '';
		if ($empleado_id <= 0) {
			// Valida limite
			$total_empleados = $this->bdd->count("empleado");
			if ($total_empleados >= MAX_EMPLEADOS) {
				$this->errores[] = "Adición no permitida: Se alcanzó el límite de empleados a registrar (" . MAX_EMPLEADOS . ")";
			}
			// Actualizar datos
			elseif (!$this->bdd->bddAdicionar('empleado', $guardar)) {
				$this->errores[] = 'No pudo adicionar empleado con los datos recibidos.';
			} else {
				// Actualización exitosa. Recarga página
				$info_respuesta = 'Empleado <b>' . htmlspecialchars($guardar['empleado_nombre']) . '</b> adicionado con éxito';
			}
		} else {
			// Editar datos
			if (!$this->bdd->bddEditar('empleado', $guardar, $empleado_id)) {
				$this->errores[] = 'No pudo actualizar empleado con los datos recibidos.';
			} else {
				$info_respuesta = 'Empleado <b>' . htmlspecialchars($guardar['empleado_nombre']) . '</b> actualizado con éxito';
			}
		}
		if ($info_respuesta != '') {
			// Actualización exitosa. Recarga página
			$this->removerLlaveEdicion('empleado', $empleado_id);
			$this->setVista('empleados/resultado');
			$this->set('mensaje-ok', $info_respuesta);
		}
	}
}

// Consulta actividad solicitada
$empleado_raw = array();
if ($empleado_id > 0) {
	$empleado_raw = $this->bdd->bddPrimerRegistro(
		"SELECT empleado_id, empleado_nombre
		FROM empleado
		WHERE empleado_id = ?",
		[$empleado_id]
	);
}

$formatos = array(
	// 'item' => 'hidden',
	'empleado_nombre' => 'text:100',
	'empleado_estado' => [1 => 'Activo', 0 => 'Retirado'],
	'_ok' => 'hidden'
);

// Filtra las actividades para los titulos dados
$empleados = array();
foreach ($titulos as $t => $nombre) {
	$valor = '';
	if (isset($guardar[$t])) {
		$valor = trim("{$guardar[$t]}");
	} elseif (isset($empleado_raw[$t])) {
		$valor = trim("{$empleado_raw[$t]}");
	}
	$empleados[$t] = $valor;
}

// Fija valor para el campo de llave-edición
$empleados['_ok'] = $this->crearLlaveEdicion('empleado', $empleado_id);

// Preserva valores para uso de la vista
$this->set('tarea-id', $empleado_id);
$this->set('actividad', $empleados);
$this->set('titulos', $titulos);
$this->set('formatos', $formatos);
