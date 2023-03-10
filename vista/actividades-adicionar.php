<?php
/**
 * Administrador de tareas / Vista para Adición de actividades.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

$this->set('titulo-pagina', 'Nueva Actividad');

// Reutiliza vista para editar. No se redirecciona directamente en control porque
// esta es una decisión netamente de la vista. Un modelo diferente podría usar presentaciones
// diferentes al adicionar y editar, de forma que este script no enrutaría al de editar.
include_once 'actividades-editar.php';