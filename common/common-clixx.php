<?php
session_start();
/**
 *	Archivo de configuracion global del sistema
 *
 */

// Datos de acceso a la base
define("_DB_DRIVER_"	, "pgsql");
define("_DB_HOST_"		, "localhost");
define("_DB_USER_"		, "spgm_delva");
define("_DB_PASSWORD_"	, "spgm_delva");
define("_DB_NAME_"		, "spgm_delva");
define("_EMPRESA_"			, "Delva" );
define("_SIGLA_PROVEEDOR_"	, "DLV");
define("_INTEGRADOR_"		, "Movistar" );
define("_REENVIO_MAIL_"		, 5);

 // El path absoluto de la aplicacion.
define( "_PATH_ABSOLUTO_", "/var/www/spgm" );

// El path relativo, esta representado en la forma "../../../.."
for($i=0, $str=''; $i<substr_count($_SERVER['SCRIPT_NAME'], '/')-1; $str.='../',$i++);
$path = (substr($str, 0, strlen($str)-1))!="" ? substr($str, 0, strlen($str)-1) : ".";
define( "_PATH_RELATIVO_", _PATH_ABSOLUTO_ );

/*----------------------------------------------------------------
						Conexion ADODB
----------------------------------------------------------------*/
require( _PATH_RELATIVO_ . "/common/adodb/adodb.inc.php");
require( _PATH_RELATIVO_ . "/common/adodb/tohtml.inc.php");

$objDb =& NewADOConnection(_DB_DRIVER_);
$objDb->Connect(_DB_HOST_, _DB_USER_, _DB_PASSWORD_, _DB_NAME_);
/*----------------------------------------------------------------
----------------------------------------------------------------*/

/*-------------------------------------------------------------
				Configuracion del Mailer (Envio)
--------------------------------------------------------------*/
require( _PATH_RELATIVO_ . "/common/phpmailer/class.phpmailer.php" );

$mail 					= new PHPMailer();
$mail->PluginDir 		= _PATH_RELATIVO_ . "/common/phpmailer/";
$mail->Mailer 			= "smtp";
$mail->Host 			= "200.55.221.18";
$mail->SMTPAuth 		= true;
$mail->Username 		= "spgm"; 
$mail->Password 		= "delvaspgm";
$mail->From 			= "spgm@delva.cl";
$mail->FromName 		= "SPGM - " . _EMPRESA_;
$mail->Timeout			= 30;
$mail->SetLanguage( "es", _PATH_RELATIVO_ . "/common/phpmailer/language/" );
$mail->AddAddress( "atix@telefonicamoviles.cl" );
$mail->AddBCC( "claudio.cifuentes@telefonicamoviles.cl" );
//$mail->AddAddress("aramos@nsoft.cl");
/*-------------------------------------------------------------
--------------------------------------------------------------*/

/*---------------------------------------------------------------------------------------------------
		Aqui se configura el objeto para acceder a una cuenta de correo POP3 (Recepcion)
---------------------------------------------------------------------------------------------------*/
/*
Name:   mail.manquehue.net
Address: 201.238.246.101

Name:   mail.manquehue.net
Address: 201.238.246.102

Name:   mail.manquehue.net
Address: 201.238.246.103

Name:   mail.manquehue.net
Address: 201.238.246.104
*/
require( _PATH_RELATIVO_ . "/common/pop3.class.php" );

$pop3 				= new pop3_class;
$pop3->hostname		= "200.55.221.18";      		/* POP 3 server host name                      */
$pop3->port			= 110;                         	/* POP 3 server host port                      */
$user				= "spgm";     /* Authentication user name                    */
$password			= "delvaspgm";                    	/* Authentication password                     */
$pop3->realm		= "";                         	/* Authentication realm or domain              */
$pop3->workstation	= "";                   		/* Workstation for NTLM authentication         */
$apop				= 0;                            /* Use APOP authentication                     */
$pop3->authentication_mechanism	= "USER";			/* SASL authentication mechanism               */

$pop3->Open();										/* Apertura de la conexion hacia el servidor   */
$pop3->Login($user, $password, $apop);				/* Loguin en la cuenta de correo               */
$pop3->debug		= 0;
$pop3->html_debug	= 1;
/*---------------------------------------------------------------------------------------------------
---------------------------------------------------------------------------------------------------*/

$bgcolors = array(	999 => "#90BF1E",
					1	=> "#FC0101",
					2	=> "#FC0101",
					3	=> "#FC0101",
					4	=> "#FC0101",
					5	=> "#FC0101",
					6	=> "#FC0101",
					7	=> "#FC0101",
					8	=> "#FC0101");

$codigos_error = array(
					999 => "Mail Recibido OK por "._INTEGRADOR_,
					1	=> "Emplazamiento NO Existe",
					2	=> "Error Aplicación en Sistema del Proveedor",
					3	=> "Error Aplicación en Sistema "._INTEGRADOR_,
					4	=> "Folio ATIX no Existe",
					5	=> "Valor Proveniente de Proveedor es NULO",
					6	=> "Subject fuera de formato",
					7	=> "Acción No permitida, Falta acción previa",
					8	=> "Error en el cuerpo del mensaje");


$_TIPOS_DE_TRABAJOS = array(
						"TP" => "Trabajo Preventivo",
						"TC" => "Trabajo Correctivo",
						"TE" => "Trabajo Excepcional"
						);
/*
	E (ETAPA)	A (ACCION)
	
	El array esta compuesto de la siguiente forma:
	Array( "TipoTrabajo" => Array("CodigoAccion" => "FormatoMail") )
*/
$_CODIGOS	= array("TC"	=>	array(	// Formatos para recibir correo.
										"AB"	=> "F1",	// Abierta (E)
										"CMV"	=> "F4",	// Comentarios Integrista (A)
										"CR"	=> "F7",	// Cierre Conforme
										"CRO"	=> "F7",	// Cierre con Observaciones
										"CRR"	=> "F7",	// Cierre por Rechazo
										
										// Formatos para enviar correo.
										"EP"	=> "F2",	// En proceso
										"RC"	=> "F3",	// Rechazada (E)
										"CPV"	=> "F5",	// Comentarios Movistar (A)
										"SL"	=> "F6"		// Solucionado
									),
					"TE"	=>	array(	"AB"	=> "F1",	// Abierta
										"CTZ"	=> "FF2",	// Cotizado 1
										"SCTZ"	=> "FF1",	// Solicitud Recotizacion
										"TAC"	=> "FF3",	// Acepta
										"TRC"	=> "FF3",	// Rechaza
										"CPV"	=> "F5",	// Comentarios Integrista (A)
										"CR"	=> "F7",	// Cierre Conforme
										"CRO"	=> "F7",	// Cierre con Observaciones
										"CRR"	=> "F7"		// Cierre por Rechazo
											)
				);


$estados = array(	"TC" => array(
							// Formatos para recibir correo.
							"FBK"	=> "Feedback",
							"ERR"	=> "Error",
							"AB"	=> "Abierta",
							"CPV"	=> "Comentarios " . _EMPRESA_ ,
							"CR"	=> "Cierre Conforme",
							"CRO"	=> "Cierre con Observaciones",
							"CRR"	=> "Cierre por Rechazo",
													
							// Formatos para enviar correo.
							"EP"	=> "En proceso",
							"RC"	=> "Rechazada",
							"CMV"	=> "Comentarios " . _INTEGRADOR_ ,
							"SL"	=> "Solucionado"
							),
					"TE"=>	array(	
							"FBK"	=> "Feedback",
							"ERR"	=> "Error",
							"AB"	=> "Abierta",
							"CTZ"	=> "Cotizado 1",
							"SCTZ"	=> "Solicitud Recotizacion",
							"CTZ2"	=> "Recotizado",
							"TAC"	=> "Acepta",
							"TRC"	=> "Rechaza",
							"PRG"	=> "Programado",
							"CMV"	=> "Comentarios " . _INTEGRADOR_ ,
							"CPV"	=> "Comentarios " . _EMPRESA_ ,
							"SL"	=> "Solucionado",
							"CR"	=> "Cierre Conforme",
							"CRO"	=> "Cierre con Observaciones",
							"CRR"	=> "Cierre por Rechazo"
							)
			);


$estados_color = array(	"TC" => array(
							"AB"	=> "#00FFFF",
							"EP"	=> "#FFFF00",
							"RC"	=> "#FF0000",
							"CMV"	=> "#C0C0C0",
							"CPV"	=> "#C0C0C0",
							"SL"	=> "#00FF00",
							"CR"	=> "#00FF00",
							"CRO"	=> "#FF0000",
							"CRR"	=> "#FF0000",

							"FBK"	=> "#0000FF",
							"ERR"	=> "#FF0000"
							),
					"TE"=>	array(	
							"AB"	=> "#00FFFF",
							"CTZ"	=> "#FFFF00",
							"SCTZ"	=> "#FF0000",
							"CTZ2"	=> "#FFFF00",
							"TAC"	=> "#FF0000",
							"TRC"	=> "#FF0000",
							"PRG"	=> "#FFFF00",
							"CMV"	=> "#C0C0C0",
							"CPV"	=> "#C0C0C0",
							"SL"	=> "#00FF00",
							"CR"	=> "#00FF00",
							"CRO"	=> "#FF0000",
							"CRR"	=> "#FF0000",

							"FBK"	=> "#0000FF",
							"ERR"	=> "#FF0000"
							)
			);

/*
Este array contiene los campos de los distintos formatos de mails
esta dado de la siguiente manera
Array(	"Formato" => Array("Campo"=>"campo_db"),
		"FormatoTipo" => Array("Campo"=>"tipo") )
*/
$_FORMATOS_MAILS= array(
					"F1" =>	array(	"FAT"	=> "folioctc", 
									"EMPLZ"	=> "numero_mnemonico", 
									"F"		=> "fecha", 
									"AR"	=> "arearesponsabletelefonica", 
									"NGN"	=> "responsabletelefonica", 
									"CRT"	=> "calidad", 
									"OBS"	=> "ctcdetalle", 
									"ACT"	=> "actividad", 
									"SACT"	=> "subactividad", 
									"FAR"	=> "folio_atix_relacionado", 
									"FPR"	=> "folio_proveedor_relacionado"
									),
					"F1_TIPO"	=> array(	"FAT"	=> "char", 
											"EMPLZ"	=> "num", 
											"F"		=> "date", 
											"AR"	=> "char", 
											"NGN"	=> "char", 
											"CRT"	=> "char", 
											"OBS"	=> "char", 
											"ACT"	=> "num_enum", 
											"SACT"	=> "num_enum", 
											"FAR"	=> "num", 
											"FPR"	=> "num"
											),

					"FF1"=>	array(	"FAT"	=> "folioctc", 
									"FII"	=> "id", 
									"NCTZ"	=> "valor", 
									"F"		=> "fecha", 
									"AR"	=> "arearesponsabletelefonica", 
									"NGN"	=> "responsabletelefonica", 
									"OBS"	=> "ctcdetalle", 
									"ACT"	=> "actividad", 
									"SACT"	=> "subactividad"
									),
									
					"FF1_TIPO"	=> array(	"FAT"	=> "char", 
											"FII"	=> "num", 
											"NCTZ"	=> "num", 
											"F"		=> "date", 
											"AR"	=> "char", 
											"NGN"	=> "char", 
											"OBS"	=> "char", 
											"ACT"	=> "num", 
											"SACT"	=> "num"
											),

					"F2" =>	array(	"FAT"	=> "folioctc AS FAT", 
									"FII"	=> "ord.id AS FII", 
									"FR"	=> "to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR", 
									"ARPCII"=> "arearesponsableintegrista AS ARPCII", 
									"NGT"	=> "tecnico AS NGT", 
									"DUR"	=> "plazo AS DUR", 
									"FIT"	=> "to_char(fechaejecucion, 'DD-MM-YYYY ') || to_char(cast(horaejecucion as TIME), 'HH24:MI') AS FIT", 
									"ACT"	=> "actividad AS ACT", 
									"SACT"	=> "subactividad AS SACT", 
									"TRS"	=> "tarea AS TRS", 
									"OBS"	=> "observaciones AS OBS"),
					"F2_TIPO"	=> array(	"FAT"	=> "num", 
											"FII"	=> "num", 
											"FR"	=> "date", 
											"ARPCII"=> "char", 
											"NGT"	=> "char", 
											"DUR"	=> "char", 
											"FIT"	=> "date", 
											"ACT"	=> "num", 
											"SACT"	=> "num", 
											"TRS"	=> "num", 
											"OBS"	=> "char"),

					"FF2" =>array(	"FAT"	=> "folioctc AS FAT", 
									"FII"	=> "ord.id AS FII", 
									"FR"	=> "to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR", 
									"ARPCII"=> "arearesponsableintegrista AS ARPCII", 
									"NGT"	=> "tecnico AS NGT", 
									"DUR"	=> "plazo AS DUR", 
									"FIT"	=> "to_char(fechaejecucion, 'DD-MM-YYYY ') || to_char(cast(horaejecucion as TIME), 'HH24:MI') AS FIT", 
									"ACT"	=> "actividad AS ACT", 
									"SACT"	=> "subactividad AS SACT", 
									"TRS"	=> "tarea AS TRS", 
									"OBS"	=> "observaciones AS OBS",
									"UFT"	=> "precio AS UFT",
									"NCTZ"	=> "valor AS NCTZ"),
					"FF2_TIPO"	=> array(	"FAT"	=> "num", 
											"FII"	=> "num", 
											"FR"	=> "date", 
											"ARPCII"=> "char", 
											"NGT"	=> "char", 
											"DUR"	=> "char", 
											"FIT"	=> "date", 
											"ACT"	=> "num", 
											"SACT"	=> "num", 
											"TRS"	=> "num", 
											"OBS"	=> "char",
											"UFT"	=> "num",
											"NCTZ"	=> "num"),

					"F3" =>	array(	"FAT"	=> "folioctc AS FAT", 
									"FR"	=> "to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR", 
									"ARPCII"=> "arearesponsableintegrista AS ARPCII", 
									"NGT"	=> "tecnico AS NGT", 
									"OBS"	=> "observaciones AS OBS"),
					"F3_TIPO"	=> array(	"FAT"	=> "num", 
											"FR"	=> "date", 
											"ARPCII"=> "char", 
											"NGT"	=> "char", 
											"OBS"	=> "char"),

					"FF3"=>	array(	"FAT"	=> "folioctc", 
									"FII"	=> "id", 
									"FR"	=> "fecha", 
									"AR"	=> "arearesponsabletelefonica",
									"NAM" 	=> "nombreautorizadormovistar",
									"FIT"	=> "plazo",
									"NCTZ"	=> "valor",
									"UFT"	=> "precio",
									"OBS"	=> "observaciones"),
					"FF3_TIPO"	=> array(	"FAT"	=> "num", 
											"FII"	=> "num", 
											"FR"	=> "date", 
											"AR"	=> "char",
											"NAM" 	=> "char",
											"FIT"	=> "date",
											"NCTZ"	=> "num",
											"UFT"	=> "num",
											"OBS"	=> "char"),

					"F4" =>	array(	"FAT"	=> "folioctc", 
									"FII"	=> "id", 
									"FR"	=> "ctcfechaactualizacion", 
									"AR"	=> "arearesponsabletelefonica", 
									"NUM"	=> "responsabletelefonica", 
									"OBS"	=> "ctcdetalle"),
					"F4_TIPO"	=> array(	"FAT"	=> "num", 
											"FII"	=> "num", 
											"FR"	=> "date", 
											"AR"	=> "char", 
											"NUM"	=> "char", 
											"OBS"	=> "char"),

					"FF4"=>	array(	"FAT"	=> "folioctc AS FAT", 
									"FR"	=> "to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR", 
									"ARPCII"=> "arearesponsableintegrista AS ARPCII", 
									"NGT"	=> "tecnico AS NGT", 
									"FIT"	=> "to_char(fechaejecucion, 'DD-MM-YYYY ') || to_char(cast(horaejecucion as TIME), 'HH24:MI') AS FIT", 
									"DUR"	=> "plazo AS DUR", 
									"OBS"	=> "observaciones AS OBS"),
					"FF4_TIPO"	=> array(	"FAT"	=> "num", 
											"FR"	=> "date", 
											"ARPCII"=> "char", 
											"NGT"	=> "char", 
											"FIT"	=> "date", 
											"DUR"	=> "char", 
											"OBS"	=> "char"),

					"F5" =>	array(	"FAT"	=> "folioctc AS FAT", 
									"FII"	=> "ord.id AS FII", 
									"FR"	=> "to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR", 
									"ARPCII"=> "arearesponsableintegrista AS ARPCII", 
									"NGT"	=> "tecnico AS NGT", 
									"OBS"	=> "observaciones AS OBS"),

					"F6" =>	array(	"FAT"	=> "folioctc AS FAT", 
									"FII"	=> "ord.id AS FII", 
									"FR"	=> "to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR", 
									"ARPCII"=> "arearesponsableintegrista AS ARPCII", 
									"NGT"	=> "tecnico AS NGT", 
									"FSOL"	=> "to_char(fechasolucion, 'DD-MM-YYYY ') || to_char(cast(horasolucion as TIME), 'HH24:MI') AS FSOL", 
									"OBS"	=> "observaciones AS OBS"),

					"F7" =>	array(	"FAT"	=> "folioctc", 
									"FII"	=> "id", 
									"FR"	=> "fechasolucion",
									"NGN"	=> "responsabletelefonica", 
									"AR"	=> "arearesponsabletelefonica", 
									"OBS"	=> "ctcdetalle"),
					"F7_TIPO"	=> array(	"FAT"	=> "char", 
											"FII"	=> "num", 
											"FR"	=> "date",
											"NGN"	=> "char", 
											"AR"	=> "char", 
											"OBS"	=> "char"),

					);


$_ERRORES	= array( 
				"5" 	=> array("ERR", "Valor Proveniente de Proveedor es NULO"),
				"4" 	=> array("ERR", "Folio ATIX no Existe"),
				"1" 	=> array("ERR", "Emplazamiento NO Existe"),
				"2" 	=> array("ERR", "Error Aplicación en Sistema del Proveedor"),
				"3" 	=> array("ERR", "Error Aplicación en Sistema ATIX Movistar"),
				"999"	=> array("ERR", "Mail Recibido OK por Proveedor")
			);



$_UPLOADS_PERMITIDOS= array("image/png",
							"image/gif",
							"image/pjpeg",
							"image/jpeg",
							"application/vnd.ms-excel",
							"application/msword"
							);



//require( _PATH_RELATIVO_ . "/common/db_functions.php");

//$dbh = db_connect();


setlocale(LC_ALL, "es_ES", "esp");

function is_date($mydate) {
	if (preg_match("/\d+\/\d+\/\d\d\d\d/", $mydate) == 0) {
		return FALSE;
	} else {
		list($d, $m, $y) = split("/", $mydate);
		return checkdate($m, $d, $y);
	}
}

function is_time($mytime) {
	if (preg_match("/\d+:\d+/", $mytime) == 0) {
		return FALSE;
	} else {
		return TRUE;
	}
}

function dateconv($mydate) {
		$d = $m = $y = $h = $mi = $s = 0;
		
		list($fecha, $hora) = split("[ ]", $mydate);
		list($d, $m, $y) 	= split("[/-]", trim($fecha));
		list($h, $mi, $s) 	= split("[:]", trim($hora));
		
		if($fecha && $hora)
			return date("Y-m-d H:i:s", mktime( ($h*1), ($mi*1), ($s*1), ($m*1), ($d*1), ($y*1) ) );
		elseif($fecha)
			return date("Y-m-d", mktime( 0, 0, 0, ($m*1), ($d*1), ($y*1) ) );
		elseif($hora)
			return date("H:i:s", mktime( ($h*1), ($mi*1), ($s*1), 0, 0, 0 ) );
}

function InvertirFecha($fecha="0000/00/00", $sep="") {
	$fecha = $fecha!="" ? $fecha : "0000/00/00";
	ereg("[/.-]", $fecha, $separador);
	$separador = $sep!="" ? $sep : $separador[0];
	
	$array_fecha = split("[/.-]", $fecha);
	
	if ( $array_fecha[0] > 31 ) {
		$dia = $array_fecha[2];
		$mes = $array_fecha[1];
		$ano = $array_fecha[0];
		return $dia . $separador . $mes . $separador . $ano;
	}
	else {
		$dia = $array_fecha[0];
		$mes = $array_fecha[1];
		$ano = $array_fecha[2];
		return $ano . $separador . $mes . $separador . $dia;
	}
}


function error_handler($message, $action) {
	$_SESSION['mensaje'] = $message;
	$_SESSION['accion'] = $action;
	redirect("/error.php");
}

function redirect($url) {
	/*
	if (preg_match("/http:\/\/.+/", $url) > 0) {
		header("Location: $url");
		# header("Refresh: 0; url=$url");
	} elseif (preg_match("/\/.+/", $url) > 0) {
		redirect("http://{$_SERVER['HTTP_HOST']}$url");
	} else {
		redirect("http://{$_SERVER['HTTP_HOST']}" . dirname($_SERVER['PHP_SELF']) . "$url");
	}
	*/
	header("Location: ". _PATH_RELATIVO_ . "$url");
}

function check_post($key, $value, $trim) {
	if (isset($_POST[$key])) {
		if ($trim != 0) {
			return trim($_POST[$key]) == $value;
		} else {
			return $_POST[$key] == $value;
		}
	} else {
		return FALSE;
	}
}

function check_get($key, $value, $trim) {
	if (isset($_GET[$key])) {
		if ($trim != 0) {
			return trim($_GET[$key]) == $value;
		} else {
			return $_GET[$key] == $value;
		}
	} else {
		return FALSE;
	}
}

function check_request($key, $value, $trim) {
	if (isset($_REQUEST[$key])) {
		if ($trim != 0) {
			return trim($_REQUEST[$key]) == $value;
		} else {
			return $_REQUEST[$key] == $value;
		}
	} else {
		return FALSE;
	}
}

function check_session($key, $value, $trim) {
	if (isset($_SESSION[$key])) {
		if ($trim != 0) {
			return trim($_SESSION[$key]) == $value;
		} else {
			return $_SESSION[$key] == $value;
		}
	} else {
		return FALSE;
	}
}

function download($path) {
	if (is_file($path)) {
		if (ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');

		$info = pathinfo($path);
		$name = $info['basename'];
		$ext = $info['extension'];
		$size = filesize($path);
		header("Pragma: public");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		switch (strtolower($ext)){
		case "txt":
			$type = "text/plain";
			break;
		case "xls":
			$type = "application/vnd.ms-excel";
			break;
		case "doc":
			$type = "application/msword";
			break;
		case "pdf":
			$type = "application/pdf";
			break;
		case "zip":
			$type = "application/x-zip-compressed";
			break;
		case "default":
			$type = "application/octet-stream";
		}
		header("Content-type: $type");
		header("Content-Length: $size");
		header("Content-Disposition: inline; filename=$name");
		header("Content-Transfer-Encoding: binary");
		$fp = fopen($path, 'rb');
		$buffer = fread($fp, $size);
		print ($buffer);
		return TRUE;
	} else {
	    return FALSE;
	}
}

function rmdirr($dirname)
{
    // Sanity check
    if (!file_exists($dirname)) {
        return false;
    }

    // Simple delete for a file
    if (is_file($dirname)) {
        return unlink($dirname);
    }

    // Loop through the folder
    $dir = dir($dirname);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }

        // Deep delete directories      
        if (is_dir("$dirname/$entry")) {
            rmdirr("$dirname/$entry");
        } else {
            unlink("$dirname/$entry");
        }
    }

    // Clean up
    $dir->close();
    return rmdir($dirname);
}

function xml2html($xml) {
	$xh = xslt_create();

	$arguments = array(
		'/_xml' => $xml
	);

	$html = xslt_process($xh, 'arg:/_xml', 'file://' . getcwd() . '/listado.xsl', NULL, $arguments);
	xslt_free($xh);
	
	return $html;
}

/**
 * Retorna un JSCalendar
 */
function getJSCalendar ( $inputField, $ifFormat="%d/%m/%Y" ) {
        $html = "<IMG id=\"img-$inputField\" src=\""._PATH_RELATIVO_."/img/calendario.jpg\" title='CALENDARIO'"
                        ." width=\"15px\" height=\"15px\" style=\"cursor:pointer; \" />"
                        ."\n<script type=\"text/javascript\">"
                        ."\n Calendar.setup ({"
                        ."\n  inputField   : \"$inputField\","
                        ."\n  ifFormat     : \"$ifFormat\","
                        ."\n  button       : \"img-$inputField\","
                        ."\n  weekNumbers  : false,"
						."\n  showsTime    : true"
                        ."\n });"
                        ."\n</script>";

        return $html;
}


?>
