<?php

/**
 * Administrador de tareas / Clase para conexiones a base de datos.
 *
 * @author John Mejia (jjmejia@yahoo.com)
 * @since 1.0 Creado en Marzo 2023
 */

class BDDTareas
{
	private $config = array();
	private $pdo = null; // Recurso PDO asociado

	public function __construct()
	{
		$this->config = [
			'servidor' => '?',
			'bdd' => '?',
			'usuario' => '?',
			'password' => '?'
		];
	}

	/**
	 * Carga datos de conexión registrados en un archivo .ini.
	 * El archivo debe contener la siguiente información:
	 * - servidor: Path o nombre del servidor donde se encuentra el motor de base de datos.
	 * - bdd: Nombre de la base de datos a usar.
	 * - usuario: Nombre del usuario autorizado para consultas.
	 * - password: Contraseña.
	 *
	 * @param string $filename Path del archivo .ini.
	 * @return bool TRUE si el archivo existe y todos los datos requeridos fueron encontrados.
	 * 				FALSE en otro caso.
	 */
	public function cargarDatosIni(string $filename)
	{
		$retornar = false;
		if (file_exists($filename)) {
			$datos = parse_ini_file($filename, false, INI_SCANNER_RAW);
			foreach ($this->config as $param => $value) {
				if (isset($datos[$param])) {
					$this->config[$param] = $datos[$param];
				}
			}
			// TRUE si inicializó todos los valores
			$retornar = !in_array('?', $this->config);
		}

		return $retornar;
	}

	/**
	 * Conecta a la base de datos.
	 * Revise el log de errores de PHP en caso que ocurra algún error.
	 * Referencia:
	 * https://mariadb.com/resources/blog/developer-quickstart-php-data-objects-and-mariadb/
	 *
	 * @return object Objeto PDO o FALSE si ocurre algún error.
	 */
	public function bddConectar()
	{
		$this->pdo = null;

		$dsn = "mysql:host={$this->config['servidor']};dbname={$this->config['bdd']};charset=utf8mb4";

		$options = [
			PDO::ATTR_EMULATE_PREPARES   => false, // Disable emulation mode for "real" prepared statements
			PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Disable errors in the form of exceptions
			PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Make the default fetch be an associative array
		];

		try {
			$this->pdo = new PDO($dsn, $this->config['usuario'], $this->config['password'], $options);
		} catch (Exception $e) {
			error_log(__METHOD__ . ': ' . $e->getMessage());
			$this->pdo = null;
		}

		return $this->pdo;
	}

	/**
	 * Valida si hay una conexión activa con la base de datos.
	 *
	 * @return bool TRUE si hay una conexión activa, FALSE en otro caso.
	 */
	private function bddConectada()
	{
		return (!is_null($this->pdo));
	}

	/**
	 * Ejecuta un query SQL y retorna el arreglo de datos encontrados.
	 * Se recomienda no incluir valores directamente en el query sino indicarlos usando "?" y pasando
	 * los valores en el arreglo $args, en el mismo orden en que se requieren en el query.
	 * Revise el log de errores de PHP en caso que ocurra algún error.
	 *
	 * @param string $query Sentencia SQL.
	 * @param array $args Valores (solamente si se requieren en el SQL).
	 * @return array|bool Arreglo de datos (registros y columnas) o FALSE si no hay conexión con la
	 *                    base de datos o si se presenta algún error.
	 */
	public function bddQuery(string $query, ?array $args = null)
	{
		$datos = false;
		if ($this->bddConectada()) {
			try {
				if (is_null($args) || count($args) <= 0) {
					$datos = $this->pdo->query($query)->fetchAll();
				} else {
					// La lista de argumentos DEBE coincidir con los "?" en el query
					$res = $this->pdo->prepare($query);
					$res->execute(array_values($args));
					$datos = $res->fetchAll();
				}
			} catch (Exception $e) {
				error_log(__METHOD__ . ': ' . $e->getMessage());
				$datos = false;
			}
		}

		return $datos;
	}

	/**
	 * Ejecuta un query SQL y retorna arreglo con los datos del primer registro encontrado.
	 * Se recomienda no incluir valores directamente en el query sino indicarlos usando "?" y pasando
	 * los valores en el arreglo $args, en el mismo orden en que se requieren en el query.
	 *
	 * @param string $query Sentencia SQL.
	 * @param array $args Valores (solamente si se requieren en el SQL).
	 * @return array|bool Arreglo de datos (columnas) o FALSE si no hay conexión con la
	 *                    base de datos o si se presenta algún error.
	 */
	public function bddPrimerRegistro(string $query, ?array $args = null)
	{
		$retornar = array();
		$datos = $this->bddQuery($query, $args);
		if (isset($datos[0])) {
			// Solamente retorna el registro solicitado
			$retornar = $datos[0];
		}

		return $retornar;
	}

	/**
	 * Modifica los valores de un registro en una tabla.
	 *
	 * @param string $tabla Nombre de la tabla.
	 * @param array $guardar Arreglo con los valores a actualizar. Las llaves en el arreglo corresponden
	 *     					 al nombre de la columna asociada.
	 * @param int $tabla_id Valor del campo en la columna {$tabla}_id (usada como PRIMARY KEY).
	 * @return bool TRUE si pudo modificar el registro, FALSE si ocurrió algún error.
	 */
	public function bddEditar(string $tabla, array $guardar, int $tabla_id)
	{
		$query = '';
		foreach ($guardar as $columna => $valor) {
			if ($query != '') {
				$query .= ',';
			}
			$query .= "{$columna} = ?";
		}
		$query = "UPDATE {$tabla} SET {$query} WHERE {$tabla}_id = ?";

		// Ultimo elemento es el id a actualizar
		$guardar[] = $tabla_id;

		$resultado = $this->ejecutarQuery($query, $guardar);

		return $resultado;
	}

	/**
	 * Adiciona un registro nuevo en una tabla.
	 *
	 * @param string $tabla Nombre de la tabla.
	 * @param array $guardar Arreglo con los valores a actualizar. Las llaves en el arreglo corresponden
	 *     					 al nombre de la columna asociada.
	 * @return bool TRUE si pudo adicionar el registro, FALSE si ocurrió algún error.
	 */
	public function bddAdicionar(string $tabla, array $guardar)
	{
		$llaves = implode(',', array_keys($guardar));
		$valores = substr(str_repeat(',?', count($guardar)), 1);
		$query = "INSERT INTO {$tabla} ({$llaves}) VALUES ({$valores})";

		$resultado = $this->ejecutarQuery($query, $guardar);

		return $resultado;
	}

	/**
	 * Elimina uno o varios registros en una tabla.
	 *
	 * @param string $tabla Nombre de la tabla.
	 * @param array $tabla_ids Arreglo con los valores de los registros a eliminar, asociados a la columna
	 *   					   {$tabla}_id (usada como PRIMARY KEY).
	 * @return bool TRUE si pudo eliminar los registros, FALSE si ocurrió algún error.
	 */
	public function bddRemover(string $tabla, array $tabla_ids)
	{
		$resultado = false;
		// Previene borre todos los datos con este método
		if (count($tabla_ids) > 0) {
			$valores = substr(str_repeat(',?', count($tabla_ids)), 1);
			$query = "DELETE FROM {$tabla} WHERE {$tabla}_id in ({$valores})";
			$resultado = $this->ejecutarQuery($query, $tabla_ids);
		}

		return $resultado;
	}

	/**
	 * Ejecuta un query SQL y retorna el recurso asociado.
	 * Se recomienda no incluir valores directamente en el query sino indicarlos usando "?" y pasando
	 * los valores en el arreglo $args, en el mismo orden en que se requieren en el query.
	 * Revise el log de errores de PHP en caso que ocurra algún error.
	 *
	 * @param string $query Sentencia SQL.
	 * @param array $args Valores asociados.
	 * @return array|bool Recurso asociado a la consulta o FALSE si se presenta algún error.
	 */
	private function ejecutarQuery(string $query, array $args)
	{
		$resultado = false;
		// La lista de argumentos DEBE coincidir con los "?" en el query
		try {
			$res = $this->pdo->prepare($query);
			$resultado = $res->execute(array_values($args));
		} catch (Exception $e) {
			error_log(__METHOD__ . ': ' . $e->getMessage());
			$resultado = false;
		}

		return $resultado;
	}

	public function count(string $tabla): int
	{
		$total = $this->bddPrimerRegistro("SELECT count({$tabla}_id) as total FROM {$tabla}");
		return $total['total'];
	}
}
