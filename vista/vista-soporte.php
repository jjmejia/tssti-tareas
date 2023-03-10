<?php
/**
 * Administrador de tareas / Librería de Soporte para vistas.
 * Estas funciones son invocadas desde los scripts de vistas, que son ejecutados al interior de la
 * clase LocalApp.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

/**
 * Visualización de menús principales.
 *
 * @param string $menus Arreglo de menús.
 * @return string Texto HTML.
 */
function mostrar_menus(array $menus) {

	$salida = '';
	if (count($menus) > 0) {
		$salida = '<p class="botones">';
		foreach ($menus as $accion => $titulo) {
			$accion = urlencode($accion);
			$salida .= "<a href=\"?accion={$accion}\">{$titulo}</a> ";
		}
		$salida .= "</p>";
	}

	return $salida;
}

/**
 * Visualiza arreglo de datos.
 * El arreglo de enlaces (si se recibe como argumento) debe ser del tipo [llave]=>[valor]
 * donde la "llave" corresponde a la acción a invocar y valor al "nombre" del menú.
 * Si existe un icono que corresponda al nombre dado (en el directorio `recursos/imagenes`),
 * se usará dicho icono en lugar del nombre textual.
 *
 * @param array $titulos Arreglo de títulos (cabecera de cada columna de datos).
 * @param array $data Arreglo de datos (filas y columnas)
 * @param array $enlaces Arreglo de enlaces a incluir por cada fila.
 * @return string Texto HTML.
 */
function mostrar_data(array $titulos, array $data, array $enlaces = null) {

	$salida = '<table border="0" cellspacing="0">';
	$codejs = array();
	$primervez = true;

	foreach ($data as $k => $fila) {
		if ($primervez) {
			// Incluye el encabezado con el título de las columnas
			$salida .= "<tr>";
			foreach ($fila as $columna => $info) {
				if (isset($titulos[$columna])) {
					$columna = trim($titulos[$columna]);
					if ($columna == '') { continue; }
				}
				$salida .= "<th>{$columna}</th>";
			}
			// Adiciona enlaces asociados a la fila
			if (is_array($enlaces)) {
				$salida .= str_repeat("<th>&nbsp;</th>", count($enlaces));
			}
			$salida .= "</tr>";
			$primervez = false;
		}

		// Valida si aplica clase a la fila
		$clase = '';
		if (isset($fila['clase']) && $fila['clase'] != '') {
			$clase = ' class="' . $fila['clase'] . '"';
			unset($fila['clase']);
		}

		$salida .= "<tr{$clase}>";

		foreach ($fila as $columna => $info) {
			if ($columna == 'tareas_asunto') {
				// Caso especial: Reune en una soloa tareas_asunto y tareas_descripcion
				if (trim($fila['tareas_descripcion']) != '') {
					$info = "<b>{$info}</b><br \\>{$fila['tareas_descripcion']}";
				}
			}
			elseif ($columna == 'retraso' && $info > 0) {
				$info = "<b>{$info}</b>";
			}
			if (isset($titulos[$columna])) {
				$columna = trim($titulos[$columna]);
				if ($columna == '') { continue; }
			}
			$info = nl2br($info);
			$salida .= "<td>{$info}</td>";
		}

		// Adiciona enlaces asociados a la fila
		if (is_array($enlaces)) {
			foreach ($enlaces as $accion => $titulo) {
				$codejs_local = '';
				if (substr($accion, 0, 1) == '!') {
					$accion = substr($accion, 1);
					$llave = md5($accion);
					// Adiciona código Javascript para validar la ejecución de la acción
					$codejs[$llave] = "
function evaluarEnlace_{$llave}(event, info) {
	let respuesta;
	if (confirm('¿Está seguro que desea {$titulo} el registro ' + info + '?') !== true) {
		event.preventDefault()
		return false;
	}
}";
					// Adiciona ejecución al enlace
					$codejs_local = " onclick=\"javascript:evaluarEnlace_{$llave}(event, '{$fila['id']}')\"";
				}
				// Adiciona iconos en lugar de texto si la imagen existe en disco
				$filename = 'recursos/imagenes/' . htmlspecialchars(strtolower($titulo)) . '.svg';
				if (file_exists(__DIR__ . '/../' . $filename)) {
					$alterno = htmlspecialchars($titulo);
					$titulo = "<img src=\"{$filename}\" alt=\"{$alterno}\" title=\"{$alterno}\">";
				}
				if ($accion == 'actividades/cerrar' && $fila['estado-raw'] > 0) {
					// Caso especial: Cierre de actividades. Ya está cerrada
					$salida .= "<td>&nbsp;</td>";
				}
				else {
					$accion = urlencode($accion);
					$salida .= "<td><a href=\"?accion={$accion}&item={$fila['id']}\"{$codejs_local}>{$titulo}</a></td>";
				}
			}
		}
		$salida .= "</tr>";
	}

	$salida .= "</table>". PHP_EOL;

	// Incluye código Javascript en línea
	if (count($codejs) > 0) {
		$salida .= '<script>' . implode(PHP_EOL, $codejs) . PHP_EOL . '</script>' . PHP_EOL;
	}

	return $salida;
}

/**
 * Visualiza formulario para edición/adición de datos.
 *
 * @param array $titulos Arreglo de títulos.
 * @param array $fila Arreglo correspondientes a una fila (registro) de datos.
 * @param array $formatos Arreglo con el formato asociado a cada dato.
 * @return string Texto HTML.
 */
function editar_data(array $titulos, array $fila, array $formatos) {

	$salida = '<table border="0" cellspacing="0" class="nobordes">';
	$ocultos = '';

	// echo "<pre>"; print_r($formatos); print_r($fila); print_r($titulos); echo "</pre>";
	foreach ($fila as $columna => $info) {
		$nombre = 'M' . md5($columna); // La "M" previene que PHP pueda interpretarlo como numero
		$control = editar_control('', $columna, $info);
		if (isset($formatos[$columna])) {
			if (!is_array($formatos[$columna])) {
				$control = editar_control($formatos[$columna], $nombre, $info);
				if ($formatos[$columna] == 'hidden') {
					$ocultos .= $control;
					continue;
				}
			}
			else {
				$control = editar_control('select', $nombre, $info, $formatos[$columna]);
			}
		}
		if (isset($titulos[$columna])) {
			$columna = $titulos[$columna];
		}

		$salida .= "<tr><th>{$columna}</th><td>{$control}</td></tr>";
	}

	$salida .= "</table>";
	$salida .= $ocultos;

	return $salida;
}

/**
 * Genera el código HTML asociado a alguno de los tipos (formatos) soportados.
 * Los tipos disponibles son:
 * - text: Edición de texto de una sola línea. Puede recibir la cantidad de caracteres a aceptar
 * 		   indicado despues de ".", ejemplo: [text:50] indica que acepta hasta 50 carácteres.
 * - textarea: Edición de texto de múltiples líneas.
 * - date: Edición de campos fecha (fecha y hora). Se soporta en el tipo "datetime-local" que puede
 *   	   tener un comportamiento diferente dependiendo del navegador o no ser soportado del todo,
 * 		   en cuyo caso se visualizará como un campo texto regular.
 * - hidden: Campo de datos oculto.
 * - select: Campo para selección de uno entre un listado de opciones.
 *
 * @param string $tipo Nombre del tipo asociado.
 * @param string $nombre Identificador del elemento a crear.
 * @param string $valor Valor actual del elemento.
 * @param array $listado Arreglo a usar para elementos de tipo "select".
 * @return string Texto HTML.
 */
function editar_control(string $tipo, string $nombre, string $valor, array $listado = null) {

	$control = '';
	$adicionales = false;

	if (strpos($tipo, ':') !== false) {
		$adicionales = explode(':', $tipo);
		$tipo = trim($adicionales[0]);
		unset($adicionales[0]);
	}

	switch ($tipo) {

		case 'text':
			// Valida si definió longitud máxima
			if (isset($adicionales[1]) && $adicionales[1] > 0) {
				// Longitud maxima del control
				$adicionales[1] = 1 * $adicionales[1];
				$control = "<input type=\"text\" id=\"{$nombre}\" name=\"{$nombre}\" value=\"{$valor}\" maxlength=\"{$adicionales[1]}\">";
			}
			break;

		case "textarea":
			$control = "<textarea id=\"{$nombre}\" name=\"{$nombre}\" cols=\"30\" rows=\"5\">{$valor}</textarea>";
			break;

		case 'date':
			// https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/datetime-local
			// In browsers with no support, these degrade gracefully to simple <input type="text"> controls.
			if ($valor > 0) {
				$segundos = strtotime($valor);
				$valor = str_replace(' ', 'T', date('Y-m-d H:i', $segundos));
			}
			$control = "<input type=\"datetime-local\" id=\"{$nombre}\" name=\"{$nombre}\" value=\"{$valor}\">";
			break;

		case 'hidden':
			$control = "<input type=\"hidden\" id=\"{$nombre}\" name=\"{$nombre}\" value=\"{$valor}\">";
			break;

		case 'select':
			$control = "<select id=\"{$nombre}\" name=\"{$nombre}\">";
			foreach ($listado as $llave => $info) {
				$selecto = '';
				if ($valor == $llave) {
					$selecto = ' selected';
				}
				$control .= "<option value=\"{$llave}\"{$selecto}>{$info}</option>";
			}
			$control .= '</select>';
			break;

		default:
			// Una caja texto por defecto
			$control = "<input type=\"text\" id=\"{$nombre}\" name=\"{$nombre}\" value=\"{$valor}\">";

	}

	return $control;
}

/**
 * Presenta enlace de retorno al listado de actividades o empleados, según la acción en curso.
 *
 * @param string $accion Acción asociada.
 * @return string Texto HTML.
 */
function retornar_listado(string $accion) {

	$salida = '<ul class="sugerencias">';
	if ($accion != '' && strpos($accion, 'empleados/') !== false) {
		$salida = '<li><a href="index.php?accion=empleados/listar">Regresar al listado de empleados</a></li>';
	}
	else {
		$salida = '<li><a href="index.php">Regresar al listado de actividades</a></li>';
	}
	$salida .= '</ul>';

	return $salida;
}

/**
 * Complementa el texto capturado para generar una salida a pantalla.
 *
 * @param LocalApp $app Objeto LocalApp.
 * @param string $buffer Texto previamente capturado.
 * @return string Texto HTML.
 */
function render_salida(LocalApp $app, string &$buffer) {

	$fecha = date('Y');
	$correo = 'jjmejia@yahoo.com';
	$buffer = "<!doctype html>
<html>
	<head>
		<title>Gestión de actividades</title>
	</head>
	<link href=\"recursos/estilos/tareas.css\" rel=\"stylesheet\">
<body>
	{$buffer}
<p class=\"pie\">Prueba técnica TS/STI {$fecha} realizada por <b>John Mejía</b> (<a href=\"mailto:{$correo}\">{$correo}</a>)</p>
</body>
</html>";

	echo $buffer;

	exit();
}
