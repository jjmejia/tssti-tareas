<?php
/**
 * Administrador de tareas / Vista para Edición de actividades.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

// Recupera datos generados por el controlador.
$actividad = $this->get('actividad', array());
$titulos = $this->get('titulos', array());
$formatos = $this->get('formatos', array());
$tarea_id = $this->get('tarea-id', 0);
$titulo_pagina = $this->get('titulo-pagina', 'Editar Actividad');
$enlace_cancelar = $this->get('enlace-cancelar', 'actividades/listar');

?>

<h1><?= $titulo_pagina ?></h1>

<?php

if (count($this->errores) > 0) {
	echo '<div class="errores"><p>Se encontraron algunos errores, confirme los valores indicados e intente de nuevo.</p>' .
		'<ul><li>' .
		implode('</li><li>', $this->errores) .
		'</li></ul></div>';
}

?>

<form method="POST" action="?accion=<?= $this->accion ?>">

<?= editar_data($titulos, $actividad, $formatos) ?>

<?php

if ($tarea_id > 0) {
	echo editar_control('hidden', 'item', $tarea_id);
}

?>

<p class="botones">
	<input type="submit" value="Guardar"> <a href="?accion=<?= $enlace_cancelar ?>">Cancelar</a>
</p>

</form>