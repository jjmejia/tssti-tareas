<?php
/**
 * Administrador de tareas / Vista para Listado de empleados.
 * Reutiliza la vista de actividades/listar.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$this->set('titulo-pagina', 'Empleados');
$this->set('enlaces-fila', [ 'empleados/editar' => 'Editar', '!empleados/remover' => 'Eliminar' ]);

include_once 'actividades-listar.php';
