<?php
/**
 * Administrador de tareas / Vista para visualizar resultados de modificaciones a la tabla de actividades.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$titulo_pagina = $this->get('titulo-pagina', 'Actividades');
$clase = 'info';
$mensaje = $this->getRaw('mensaje-ok');

if ($this->getRaw('mensaje-error') != '') {
	$clase = 'errores';
	$mensaje .= '<p><b>Ha ocurrido un error al procesar la solicitud deseada</b></p><p>' .
		$this->get('mensaje-error') .
		'</p>';
}
else {
	// Redirecciona a nueva página.
	// De nuevo, se hace aquí y no en el controlador porque es una decisión netamente de presentación.
	// Si se comenta este bloque, se presentará el mensaje en esta vista y el enlace para ir a la página
	// a la que aquí se redirecciona.
	$url_retorno = '';
	if ($this->accion != '' && strpos($this->accion, 'empleados/') !== false) {
		$url_retorno = 'empleados/listar';
	}
	$this->recargarIndex($url_retorno, $mensaje);
}

?>

<h1><?= $titulo_pagina ?></h1>

<?php

if (count($this->errores) > 0) {
	echo '<ul class="errores"><li>' . implode('</li><li>', $this->errores) . '</li></ul>';
}

?>

<div class="<?= $clase ?>"><?= $mensaje ?></div>

<?= retornar_listado($this->accion) ?>
