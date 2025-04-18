<?php

/**
 * Administrador de tareas / Clase para manejo de contexto de la aplicación.
 * Inicia una sesión PHP y captura toda salida a pantalla.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

class LocalApp
{
	private $accion = '';				// Acción asociada al archivo controlador.
	private $vista = '';				// Acción asociada al archivo de visualización (vista).
	private $bdd = null;				// Objeto BDDTareas.
	private $filename = '';				// Archivo de control o vista en uso.
	private $data = array();			// Datos a usar en las vistas.
	private $mensaje_sesion = '';		// Mensaje recuperado de $_SESSION.
	private $errores = array();			// Errores registrados en alguna de las consultas.

	/**
	 * Constructor.
	 * Inicia una sesión PHP y captura toda salida a pantalla.
	 */
	public function __construct()
	{
		session_start();
		ob_start();
	}

	/**
	 * Conecta con la base de datos.
	 * Los parámetros de conexión son tomados del archivo .ini indicado.
	 * El archivo debe contener la siguiente información:
	 * - servidor: Path o nombre del servidor donde se encuentra el motor de base de datos.
	 * - bdd: Nombre de la base de datos a usar.
	 * - usuario: Nombre del usuario autorizado para consultas.
	 * - password: Contraseña.
	 *
	 * En caso de error se interrumple la sesión y se muestra la información en pantalla.
	 *
	 * @param string $filename Path del archivo .ini.
	 */
	public function conectarBDD(string $filename)
	{
		$this->bdd = new BDDTareas();

		if ($this->bdd->cargarDatosIni($filename)) {
			if (!$this->bdd->bddConectar()) {
				mostrar_error('No pudo conectar a la base de datos');
			}
		} else {
			mostrar_error('No pudo cargar configuracion .ini de la base de datos');
		}
	}

	/**
	 * Recupera valor de variable recibida por formulario.
	 *
	 * @param string $param Nombre de la variable a buscar en el arreglo $_REQUEST.
	 * @param mixed $defecto Valor a usar si la variable no existe en el arreglo $_REQUEST.
	 * @return mixed Valor.
	 */
	public function post(string $param, mixed $defecto = '')
	{
		if (isset($_REQUEST[$param])) {
			$defecto = $_REQUEST[$param];
		}

		return $defecto;
	}

	/**
	 * Modifica valor de $this->accion.
	 *
	 * @param string $accion Acción asociada.
	 */
	public function setAccion(string $accion)
	{
		$this->accion = $accion;
	}

	/**
	 * Modifica valor de $this->vista.
	 *
	 * @param string $accion Acción asociada a la vista.
	 */
	public function setVista(string $accion)
	{
		$this->vista = $accion;
	}

	/**
	 * Interpreta acción e invoca el respectivo script.
	 *
	 * @param string $tipo Tipo asociado a la acción, puede ser "control" (controlador) o
	 *    				   "vista" (visualización a pantalla). En cada script puede accederse a las
	 * 					   propiedades y/o métodos de esta clase mediante "$this".
	 */
	public function ejecutarAccion(string $tipo)
	{
		$accion = $this->accion;
		if ($tipo != 'control' && $this->vista != '') {
			$accion = $this->vista;
		}

		// Ejecuta archivo de control asociado
		$basename = str_replace(array('.', '/', "\\", '>', '<'), '-', $accion);
		if ($basename != '') {
			// Busca el script de interes asociado a la accion indicada
			$this->filename = __DIR__ . '/../../' . $tipo . '/' . $basename . '.php';
			if (!file_exists($this->filename)) {
				mostrar_error("No fue posible encontrar soporte para el recurso solicitado: <b>{$tipo}/{$basename}</b>");
			}
			$this->incluir();
		}
	}

	/**
	 * Ejecuta script indicado por $this->filename.
	 * Puede acceder a los recursos de esta clase desde $filename usando "$this".
	 * Se ejecuta desde una función aislada para reducir inclusión de variables ajenas.
	 */
	private function incluir()
	{
		include_once $this->filename;
	}

	/**
	 * Códifica el valor recibido para usarlo de manera segura en páginas HTML.
	 * Si el dato es un arreglo, codifica cada elemento del arreglo.
	 *
	 * @param mixed $data Dato a codificar.
	 * @return mixed Dato codificado.
	 */
	private function encode(mixed $data)
	{
		if (is_array($data)) {
			foreach ($data as $k => $v) {
				$data[$k] = $this->encode($v);
			}
		} elseif (is_string($data) && $data != '') {
			$data = htmlspecialchars($data);
		}

		return $data;
	}

	/**
	 * Recupera un valor particular registrado en $this->data.
	 * El valor recuperado se retorna codificado para usarlo de manera segura en páginas HTML.
	 *
	 * @param string $param Nombre del elemento a recuperar.
	 * @param mixed $defecto Valor a usar si el elemento requerido no existe en $this->data.
	 * @return mixed Dato recuperado codificado.
	 */
	public function get(string $param, mixed $defecto = '')
	{
		return $this->encode($this->getRaw($param, $defecto));
	}

	/**
	 * Recupera un valor particular registrado en $this->data.
	 * El valor recuperado se retorna tal cuál sin codificar.
	 *
	 * @param string $param Nombre del elemento a recuperar.
	 * @param mixed $defecto Valor a usar si el elemento requerido no existe en $this->data.
	 * @return mixed Dato recuperado sin codificar.
	 */
	public function getRaw(string $param, mixed $defecto = '')
	{
		if (isset($this->data[$param])) {
			$defecto = $this->data[$param];
		}

		return $defecto;
	}

	/**
	 * Asigna valor a un elemento registrado en $this->data.
	 *
	 * @param string $param Nombre del elemento a guardar.
	 * @param mixed $valor Valor a guardar. Puede ser un entero, string, arreglo, etc.
	 */
	public function set(string $param, mixed $valor)
	{
		$this->data[$param] = $valor;
	}

	/**
	 * Despliega vista a usar para mostrar pantallas de errores críticos.
	 * Este método termina la ejecución del script luego de generar la salida a pantalla de la
	 * página de error.
	 *
	 * @param string $mensaje_error Mensaje de error a mostrar.
	 * @param string $fun Nombre de la función usada para construir la página (contenedor o layout sobre
	 *   			      el que se publica el mensaje de error).
	 */
	public function mostrarError(string $mensaje_error, string $fun = '')
	{
		$this->data['mensaje-error'] = $mensaje_error;
		$this->vista = 'error';
		$this->ejecutarAccion('vista');
		$this->exit($fun);
	}

	/**
	 * Termina ejecución y recarga la página actual.
	 *
	 * @param string $accion Acción a invocar. Si no se indica, redirecciona al index.
	 * @param string $mensaje Mensaje a mostrar en la nueva página. Este mensaje se almacena temporalmente en
	 *   	 	 	   	      la sesión PHP y se recupera de ahí por la nueva página.
	 */
	public function recargarIndex(string $accion = '', string $mensaje = '')
	{
		$protocolo = 'http';
		// https://stackoverflow.com/questions/1175096/how-to-find-out-if-youre-using-https-without-serverhttps
		if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
			|| $_SERVER['SERVER_PORT'] == 443
		) {
			$protocolo = 'https';
		}

		$url = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
		if ($accion != '') {
			$url .= '?accion=' . $accion;
		}

		if ($mensaje != '') {
			$_SESSION['reload-mensaje'] = $mensaje;
		}

		echo "<script>window.location='{$protocolo}://{$_SERVER['SERVER_NAME']}{$url}';</script>";

		$this->exit();
	}

	/**
	 * Recupera mensaje preservado al recargar la página.
	 * Una vez recuperado, el mensaje es eliminado de la sesión.
	 */
	public function mensajeSesionAnterior()
	{

		if (isset($_SESSION['reload-mensaje'])) {
			$this->mensaje_sesion = $_SESSION['reload-mensaje'];
			unset($_SESSION['reload-mensaje']);
		}

		return $this->mensaje_sesion;
	}

	/**
	 * Crea llave única para autenticar un proceso de edición/adición de registros.
	 * Esto se hace con el fin de prevenir (especialmente en casos de adición) que una potencial recarga
	 * de la página de respuesta, genere un nuevo registro no intencionado.
	 *
	 * @param string $tabla Nombre de la tabla u otra palabra clave a usar para generar la llave.
	 * @param int $tarea_id Identificador del registro asociado (por defecto usa valor cero). Usado como
	 *   	  				identificador de la llave a crear y evitar crear elementos indiscriminadamente
	 *   					cuando el usuario recarga muchas veces la página de ingreso de datos.
	 * @return string Llave de edición creada.
	 */
	public function crearLlaveEdicion(string $tabla, int $tarea_id = 0)
	{
		$llave = uniqid('E');
		$_SESSION['edit-' . $tabla . '-' . $tarea_id] = $llave;

		return $llave;
	}

	/**
	 * Recupera la llave de edición asociada.
	 * Usualmente usado para validar que el proceso de modificación en curso corresponda al iniciado.
	 *
	 * @param string $tabla Nombre de la tabla u otra palabra clave a usar para generar la llave.
	 * @param int $tarea_id Identificador del registro asociado (por defecto usa valor cero).
	 * @return string Llave de edición.
	 */
	public function recuperarLlaveEdicion(string $tabla, int $tarea_id = 0)
	{
		$llave = '';
		if (isset($_SESSION['edit-' . $tabla . '-' . $tarea_id])) {
			$llave = $_SESSION['edit-' . $tabla . '-' . $tarea_id];
		}

		return $llave;
	}

	/**
	 * Elimina llave de edición.
	 * Usualmente se realiza este proceso luego que se termina la edición/adición de registros, para
	 * liberar memoria que de otra forma queda ocupada con valores que ya no serán útiles.
	 *
	 * @param string $tabla Nombre de la tabla u otra palabra clave a usar para generar la llave.
	 * @param int $tarea_id Identificador del registro asociado (por defecto usa valor cero).
	 */
	public function removerLlaveEdicion(string $tabla, int $tarea_id = 0)
	{
		if (isset($_SESSION['edit-' . $tabla . '-' . $tarea_id])) {
			unset($_SESSION['edit-' . $tabla . '-' . $tarea_id]);
		}
	}

	/**
	 * Termina la captura de texto y genera salida a pantalla.
	 * El proceso de acabado final del texto a pantalla se realiza en la función $fun.
	 *
	 * @param string $fun Función a usar para generar el texto final a pantalla, usualmente usada para
	 * 		 	 		  "incrustar" el texto previamente capturado en un contenedor o layout.
	 */
	public function renderSalida(string $fun = '')
	{
		$buffer = '';

		while (ob_get_level() > 0) {
			$buffer .= ob_get_contents();
			ob_end_clean();
		}

		if ($fun != '' && function_exists($fun)) {
			$fun($this, $buffer);
		}

		echo $buffer;
	}

	/**
	 * Termina la ejecución del script.
	 * Se usa este método para asegurarse que se dé correcta salida al texto previamente capturado.
	 *
	 * @param string $fun Función a usar para generar el texto final a pantalla
	 */
	public function exit(string $fun = '')
	{
		$this->renderSalida($fun);

		exit;
	}
}
