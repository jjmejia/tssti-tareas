<?php
/**
 * Administrador de tareas / Vista para Listado de empleados.
 * Reutiliza la vista de actividades/listar.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$menus = array(
	'empleados/adicionar' => 'Nuevo empleado',
	'actividades/listar' => 'Lista de actividades'
);

$this->set('titulo-pagina', 'Empleados');
$this->set('menus', $menus);
$this->set('enlaces-fila', [ 'empleados/editar' => 'Editar', '!empleados/remover' => 'Eliminar' ]);

include_once 'actividades-listar.php';
