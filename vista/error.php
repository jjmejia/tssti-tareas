<?php
/**
 * Administrador de tareas / Vista para visualización de errores.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

?>

<h1>Ups! Algo no anda bien...</h1>
<div class="errores">
	<p><b>Ha ocurrido un error y no es posible continuar:</b></p>
	<p><?= $this->getRaw('mensaje-error') ?></p>
</div>

<p style="margin-top:30px"><b>Sugerencias:</b></p>

<?= retornar_listado($this->accion) ?>
