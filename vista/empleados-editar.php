<?php
/**
 * Administrador de tareas / Vista para Edición de empleados.
 * Reutiliza la vista de actividades/editar.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$this->set('titulo-pagina', 'Editar Empleado');
$this->set('enlace-cancelar', 'empleados/listar');

include_once 'actividades-editar.php';