<?php
/**
 * Administrador de tareas / Vista para Adición de empleados.
 * Reutiliza la vista de actividades/adicionar.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$this->set('titulo-pagina', 'Nuevo Empleado');
$this->set('enlace-cancelar', 'empleados/listar');

include_once 'actividades-editar.php';