<?php
/**
 * Librería con funciones de soporte.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

/**
 * Registra función para autoload de clases.
 * Referencia: https://www.php.net/manual/en/language.oop5.autoload.php
 */
spl_autoload_register(function ($class_name) {
    include __DIR__ . '/classes/' . $class_name . '.php';
});

/**
 * Genera una Excepción PHP.
 */
function mostrar_error(string $mensaje_error) {

	throw new Exception($mensaje_error);
}
