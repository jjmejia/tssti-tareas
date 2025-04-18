<?php

/**
 * Administrador de tareas / Vista para Listado de actividades.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$actividades = $this->get('actividades', array());
$titulos = $this->get('titulos', array());
$menus = $this->get('menus', array());
$titulo_pagina = $this->get('titulo-pagina', 'Actividades');

$enlaces_fila = $this->get('enlaces-fila', [
	'actividades/cerrar' => 'Cerrar',
	'actividades/editar' => 'Editar',
	'!actividades/remover' => 'Eliminar' // El signo "!" indica que debe validar la acción antes de ejecutarla
]);

$mensaje = $this->mensajeSesionAnterior();
if ($this->getRaw('mensaje-ok') != '') {
	if ($mensaje != '') {
		$mensaje .= '<br />';
	}
	$mensaje .= $this->getRaw('mensaje-ok');
}
if ($mensaje != '') {
	$mensaje = "<p class=\"aviso\">{$mensaje}</p>";
}

?>

<h1><?= $titulo_pagina ?></h1>

<?= $mensaje ?>

<?= mostrar_menus($menus) ?>

<?= mostrar_data($titulos, $actividades, $enlaces_fila) ?>