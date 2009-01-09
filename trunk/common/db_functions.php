<?
/**
 * Mejorado por JGG - 2006.05.25
 * - Se le agrega la opcion para trabajar con PostgreSQL
 */
function db_get_preferences($req) {
	$preferences = array (
		"type" 		=> _DB_DRIVER_,
		"server" 	=> _DB_HOST_,
		"username" 	=> _DB_USER_,
		"password" 	=> _DB_PASSWORD_,
		"database" 	=> _DB_NAME_	
	);
	switch ($req) {
		case "type":
			if (strlen($preferences['type']) > 0) 
				return $preferences['type'];
			else 
				die("db_get_preferences: No se ha configurado el tipo de base de datos a usar");
			break;
		case "server":
			if (strlen($preferences['server']) > 0) 
				return $preferences['server'];
			else 
				die("db_get_preferences: No se ha configurado el servidor a conectar");
			break;
		case "username":
			return $preferences['username'];
			break;
		case "password":
			return $preferences['password'];
			break;
		case "database":
			if (($preferences['type'] == "mysql" || $preferences['type'] == "mssql") ? (strlen($preferences['database']) > 0) : TRUE) 
				return $preferences['database'];
			else
				die("db_get_preferences: No se ha configurado la base de datos con la cual se trabajará");
			break;
		default:
			die("db_get_preferences: Consulta inválida");
	}
}

function db_connect() {
	$db_type = db_get_preferences("type");
	$db_server = db_get_preferences("server");
	$db_username = db_get_preferences("username");
	$db_password = db_get_preferences("password");
	$db_database = db_get_preferences("database");
	switch ($db_type) {
		case "mysql":
			$dbh = mysql_connect($db_server, $db_username, $db_password)
				or die("db_connect: No se pudo conectar con la base de datos" . db_error());
			mysql_select_db($db_database)
				or die("db_connect: No se pudo conectar con la base de datos" . db_error());
			return $dbh;
			break;
		case "mssql":
			$dbh = mssql_connect($db_server, $db_username, $db_password)
				or die("db_connect: No se pudo conectar con la base de datos" . db_error());
			mssql_select_db($db_database, $dbh)
				or die("db_connect: No se pudo conectar con la base de datos" . db_error());
			return $dbh;
			break;
		case "pgsql":
			$dbh = pg_connect("host=$db_server dbname=$db_database user=$db_username password=$db_password")
				or die("db_connect: No se pudo conectar con la base de datos" . db_error());
			return $dbh;
			break;
		case "odbc":
			$dbh = odbc_connect($db_server, $db_username, $db_password)
				or die("db_connect: No se pudo conectar con la base de datos" . db_error());
			return $dbh;
			break;
		default:
			die("db_connect: No se ha configurado correctamente la base de datos a usar");
	}
}

function db_disconnect($dbh) {
	if (isset($dbh) ? (strlen($dbh) < 1) : TRUE)
		die("db_disconnect: Error desconectando de la base de datos");
	else {
		$db_type = db_get_preferences("type");
		switch ($db_type) {
			case "mysql":
				mysql_close($dbh)
					or die("db_disconnect: Error desconectando de la base de datos" . db_error());
				break;
			case "mssql":
				mssql_close($dbh)
					or die("db_disconnect: Error desconectando de la base de datos" . db_error());
				break;
			case "pgsql":
				pg_close($dbh)
					or die("db_disconnect: Error desconectando de la base de datos" . db_error());
				break;
			case "odbc":
				odbc_close($dbh)
					or die("db_disconnect: Error desconectando de la base de datos" . db_error());
				break;
			default:
				die("db_disconnect: No se ha configurado correctamente la base de datos a usar");
		}
	}
}

function db_query($query, $dbh) {
	if (isset($dbh) ? (strlen($dbh) < 1) : TRUE)
		die("db_query: No se ha definido el handler de la base de datos");
	elseif (isset($query) ? (strlen($query) < 1) : TRUE)
		die("db_query: No se ha definido la consulta");
	else {
		$db_type = db_get_preferences("type");
		switch ($db_type) {
			case "mysql":
				$result = mysql_query($query, $dbh);
				return $result;
				break;
			case "mssql":
				$result = mssql_query($query, $dbh);
				return $result;
				break;
			case "pgsql":
				$result = pg_query($dbh, $query) or die("<br />" . pg_last_error() . "<br /><br />$query<hr />");
				return $result;
				break;
			case "odbc":
				$result = odbc_exec($dbh, $query);
				return $result;
				break;
			default:
				die("db_query: No se ha configurado correctamente la base de datos a usar");
		}
	}
}

function db_num_rows($result) {
	if (isset($result) ? gettype($result) != "resource" : TRUE)
		return -1;
	else {
		$db_type = db_get_preferences("type");
		switch ($db_type) {
			case "mysql":
				$num = mysql_num_rows($result);
				return $num;
				break;
			case "mssql":
				$num = mssql_num_rows($result);
				return $num;
				break;
			case "pgsql":
				$num = pg_num_rows($result);
				return $num;
				break;
			case "odbc":
				$num = odbc_num_rows($result);
				return $num;
				break;
			default:
				die("db_num_rows: No se ha configurado correctamente la base de datos a usar");
		}
	}
}

function db_data_seek($result, $row_number) {
	if (isset($result) ? !$result : TRUE)
		die("db_data_seek: Resultado inválido");
	elseif (isset($row_number) ? ($row_number < 0) : TRUE)
		die("db_data_seek: Número de fila inválido");
	else {
		$db_type = db_get_preferences("type");
		switch ($db_type) {
			case "mysql":
				$resp = mysql_data_seek($result, $row_number)
					or die("db_data_seek: Error buscando resultado" . db_error());
				return $resp;
				break;
			case "mssql":
				$resp = mssql_data_seek($result, $row_number)
					or die("db_data_seek: Error buscando resultado" . db_error());
				return $resp;
				break;
			case "pgsql":
				$resp = pg_lo_seek($result, $row_number)
					or die("db_data_seek: Error buscando resultado" . db_error());
				return $resp;
				break;
			case "odbc":
				$resp = odbc_fetch_row($result, $row_number)
					or die("db_data_seek: Error buscando resultado" . db_error());
				return $resp;
				break;
			default:
				die("db_data_seek: No se ha configurado correctamente la base de datos a usar");
		}		
	}
}

function db_fetch_row($result) {
	if (isset($result) ? !$result : TRUE)
		die("db_fetch_row: Resultado inválido");
	else {
		$db_type = db_get_preferences("type");
		switch ($db_type) {
			case "mysql":
				$row = mysql_fetch_row($result);
				return $row;
				break;
			case "mssql":
				$row = mssql_fetch_row($result);
				return $row;
				break;
			case "pgsql":
				$row = pg_fetch_row($result);
				return $row;
				break;
			case "odbc":
				$rc = odbc_fetch_into($result, $row);
				if($rc)
					return FALSE;
				else
					return $row;
				break;
			default:
				die("db_fetch_row: No se ha configurado correctamente la base de datos a usar");
		}
	}
}

function db_fetch_array($result) {
	if (isset($result) ? !$result : TRUE)
		die("db_fetch_array: Resultado inválido");
	else {
		$db_type = db_get_preferences("type");
		switch ($db_type) {
			case "mysql":
				$row = mysql_fetch_array($result);
				return $row;
				break;
			case "mssql":
				$row = mssql_fetch_array($result);
				return $row;
				break;
			case "pgsql":
				$row = pg_fetch_array($result);
				return $row;
				break;
			case "odbc":
				$row = odbc_fetch_array($result);
				return $row;
				break;
			default:
				die("db_fetch_array: No se ha configurado correctamente la base de datos a usar");
		}
	}
}

function db_fetch_assoc($result) {
	if (isset($result) ? !$result : TRUE)
		die("db_fetch_array: Resultado inválido");
	else {
		$db_type = db_get_preferences("type");
		switch ($db_type) {
			case "mysql":
				$row = mysql_fetch_assoc($result);
				return $row;
				break;
			case "mssql":
				$row = mssql_fetch_assoc($result);
				return $row;
				break;
			case "pgsql":
				$row = pg_fetch_assoc($result);
				return $row;
				break;
			case "odbc":
				$row = odbc_fetch_array($result);
				return $row;
				break;
			default:
				die("db_fetch_array: No se ha configurado correctamente la base de datos a usar");
		}
	}
}

function db_free_result($result) {
	if (isset($result) ? !$result : TRUE)
		die("db_free_result: Resultado inválido");
	else {
		$db_type = db_get_preferences("type");
		switch ($db_type) {
			case "mysql":
				$resp = mysql_free_result($result)
					or die("db_free_result: Error liberando resultado" . db_error());
				return $resp;
				break;
			case "mssql":
				$resp = mssql_free_result($result)
					or die("db_free_result: Error liberando resultado" . db_error());
				return $resp;
				break;
			case "pgsql":
				$resp = pg_free_result($result)
					or die("db_free_result: Error liberando resultado" . db_error());
				return $resp;
				break;
			case "odbc":
				$resp = odbc_free_result($result)
					or die("db_free_result: Error liberando resultado" . db_error());
				return $resp;
				break;
			default:
				die("db_free_result: No se ha configurado correctamente la base de datos a usar");
		}
	}
}

function db_error() {
	$db_type = db_get_preferences("type");
	switch ($db_type) {
		case "mysql":
			return "<br>" . mysql_errno() . ": " . mysql_error();
			break;
		case "mssql":
			return "<br> Error en SQL Server";
			break;
		case "pgsql":
			return "<br>" . pg_last_error();
			break;
		case "odbc":
			return "<br>". odbc_error() . ": " . odbc_errormsg();
			break;
		default:
			die("db_error: No se ha configurado correctamente la base de datos a usar");
	}
}


?>