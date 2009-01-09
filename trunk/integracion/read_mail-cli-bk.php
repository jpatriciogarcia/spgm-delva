<?php
/**
 * read_mail.php
 *
 * Este archivo se ejecuta por consola (crontab).
 * Hay que tener la precaucion de dar todos los paths de manera absoluta.
 *
 * @author nSoft - JGG <jgarcigo@yahoo.es>
 * @copyright 2006.06.15
 * @version 2.1
 */

require("/var/www/spgm/common/common-cli.php");

$pop3->Open();							/* Apertura de la conexion hacia el servidor	*/
$pop3->Login($user, $password, $apop);	/* Login en la cuenta de correo					*/

$objDb->debug = false;
$debug = 0;

$formato = array(
"F1" => array(
"FAT"	=> "^[0-9]{1,10}$", 									// NUM(10)
"EMPLZ"	=> "^[0-9]{1,5}$", 										// NUM(5)
"F"		=> "^[0-9]{2}-[0-9]{2}-[0-9]{4} [0-9]{2}:[0-9]{2}$", 	// DATE(DD-MM-YYYY HH:MM)
"AR"	=> "^[[[:alnum:][:space:]]{1,80}|]$", 					// CHAR(80)
"NGN"	=> "^[[:alnum:][:space:]]{1,120}$", 					// CHAR(120)
"CRT"	=> "^[0-9]{1,2}$",										// CHAR(2)
"OBS"	=> "^.{1,255}$",						 					// CHAR(255)
"ACT"	=> "^[0-9\|]{1,}$", 									// NUM(4)
"SACT"	=> "^[0-9\|]{1,}$", 									// NUM(4)
"FAR"	=> "^[[0-9]{0,10}|]$", 									// NUM(10)
"FPR"	=> "^[[0-9]{0,10}|]$" 									// NUM(10)
),
"FF1" => array(
"FAT"	=> "^[0-9]{1,10}$", 									// NUM(10)
"FII"	=> "^[0-9]{1,10}$",										// NUM(10)
"FR"	=> "^[0-9]{2}-[0-9]{2}-[0-9]{4} [0-9]{2}:[0-9]{2}$", 	// DATE(DD-MM-YYYY HH:MM)
"AR"	=> "^[[[:alnum:][:space:]]{1,80}|]$", 					// CHAR(80)
"NGN"	=> "^[[:alnum:][:space:]]{1,120}$", 					// CHAR(120)
"OBS"	=> "^.{1,255}$"						 					// CHAR(255)
),
"FF3" => array(
"FAT"	=> "^[0-9]{1,10}$", 									// NUM(10)
"FII"	=> "^[0-9]{1,10}$",										// NUM(10)
"FR"	=> "^[0-9]{2}-[0-9]{2}-[0-9]{4} [0-9]{2}:[0-9]{2}$", 	// DATE(DD-MM-YYYY HH:MM)
"AR"	=> "^[[[:alnum:][:space:]]{1,80}|]$", 					// CHAR(80)
"NAM"	=> "^[[:alnum:][:space:]]{1,120}$", 					// CHAR(120)
"FIT"	=> "^[0-9]{2}-[0-9]{2}-[0-9]{4} [0-9]{2}:[0-9]{2}$", 	// DATE(DD-MM-YYYY HH:MM)
"NCTZ"	=> "^[0-9]{1,10}$",										// NUM(10)
"UFT"	=> "^[0-9]{1,7}[|,[0-9]{1,2}|.[0-9]{1,2}]$",			// NUM(7,2) => (1234567,89)
"OBS"	=> "^.{1,255}$"						 					// CHAR(255)
),
"F4" => array(
"FAT"	=> "^[0-9]{1,10}$", 									// NUM(10)
"FII"	=> "^[0-9]{1,10}$",										// NUM(10)
"FR"	=> "^[0-9]{2}-[0-9]{2}-[0-9]{4} [0-9]{2}:[0-9]{2}$", 	// DATE(DD-MM-YYYY HH:MM)
"AR"	=> "^[[[:alnum:][:space:]]{1,80}|]$", 					// CHAR(80)
"NUMV"	=> "^[[:alnum:][:space:]]{1,120}$", 					// CHAR(120)
"OBS"	=> "^.{1,255}$"						 					// CHAR(255)
),
"F7" => array(
"FAT"	=> "^[0-9]{1,10}$", 									// NUM(10)
"FII"	=> "^[0-9]{1,10}$",										// NUM(10)
"FR"	=> "^[0-9]{2}-[0-9]{2}-[0-9]{4} [0-9]{2}:[0-9]{2}$", 	// DATE(DD-MM-YYYY HH:MM)
"NGN"	=> "^[[:alnum:][:space:]]{1,120}$", 					// CHAR(120)
"AR"	=> "^[[[:alnum:][:space:]]{1,80}|]$", 					// CHAR(80)
"OBS"	=> "^.{1,255}$"						 					// CHAR(255)
)
);

function inArrayReplace( $search, $replace, &$arr ) {
	foreach( $arr as $k => $v ) {
		$arr[$k] = trim(str_replace($search, $replace, $v));
	}
	return $arr;
}

function ValidaFormato( $arr_valores, $arr_formato) {
	if( array_diff_assoc( array_keys($arr_formato), array_keys($arr_valores) ) ) {
		if($GLOBALS['debug']) echo "\n(".$_SERVER['PHP_SELF']." línea 81):  \n";
		return 8;
	}
	foreach( $arr_valores as $k => $v ) {
		if( array_key_exists($k, $arr_formato) ) {
			if( ! ereg($arr_formato[$k], trim($v)) ) {
				if($GLOBALS['debug']) echo "\n(".$_SERVER['PHP_SELF']." línea 87):  [$k] => $v\n";
				return 8;
			}
		} else {
			if($GLOBALS['debug']) echo "\n(".$_SERVER['PHP_SELF']." línea 91):  [$k] => $v\n";
			return 8;
		}
	}
	return false;
}

function GrabarArchivo( $archivo, $contenido='' ) {
	if ($contenido!='') {
		$fp = fopen( $archivo, "w" );
		fwrite( $fp, $contenido );
		fclose( $fp );
	}
	else {
		if (isset($GLOBALS['headers']) && isset($GLOBALS['body'])) {

			foreach ($GLOBALS['headers'] as $k => $v) {
				$contenido .= $k . ":" . $v . "\r\n";
			}

			$contenido .= "#########################################\r\n";

			foreach ($GLOBALS['body'] as $k => $v) {
				$contenido .= $k . "&" . $v . "\r\n";
			}

			$contenido .= "#########################################\r\n";

			$fp = fopen( $archivo, "w" );
			fwrite( $fp, $contenido );
			fclose( $fp );
		}
	}
}

$mensajes = $pop3->ListMessages("",0);
$procesados_correctivos		= 0;
$procesados_excepcionales	= 0;
$MailSubject				= "";

// Recorremos los mensajes que se encuentran en el servidor.
for( $i=0; $i <= count($mensajes); $i++ ) {

	$msgError 				= "";
	$CantidadCotizaciones 	= 0;
	$TotalCotizaciones		= 0;
	$MailSubject			= "";
	$CodigoError 			= null;		// No hay errores
	$headers				= null;
	$body					= null;

	if( ($error=$pop3->RetrieveMessage($i,$headers,$body,100))=="" ) {
		$headers = $pop3->getArrayHeaders($i);

		@list($ProveedorCII, $TipoTrabajo, $Folio, $CodigoAccion, $CantidadCotizaciones, $TotalCotizaciones) = explode("-", $headers["Subject"]);

		$CantidadCotizaciones = $CantidadCotizaciones ? $CantidadCotizaciones : 0;
		$TotalCotizaciones = $TotalCotizaciones ? $TotalCotizaciones : 0;

		// Verificamos el Proveedor.
		if( $ProveedorCII == _SIGLA_PROVEEDOR_ ) {

			$body = $pop3->getArrayBody($i);
			if( $TipoTrabajo!="ERR" ) $body = inArrayReplace("<br>", "", $body);

			// Trabajos CORRECTIVOS
			if( $TipoTrabajo == "TC" ) {

				// Abierta
				// Formato Mail: F1
				if( $CodigoAccion == "AB" && !($CodigoError = ValidaFormato($body, $formato["F1"])) ) {
					$sub_actividades 	= explode("|", $body["SACT"]);
					list($fecha, $hora) = explode(" ", $body["F"], 2);

					// Verificamos si el folio existe.
					$sql = "SELECT ord.id FROM ordenes ord WHERE ord.folioctc=".$Folio;
					$rs_tmp = $objDb->Execute($sql);
					if( $rs_tmp->RecordCount( ) > 0 && !$CodigoError ) {
						$CodigoError = 9;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Validacion de la existencia de los folios relacionados.
					if( ($body["FAR"] || $body["FPR"]) && !$CodigoError ) {
						$sql = "SELECT ord.id, ord.folioctc
								FROM ordenes ord
								WHERE ord.folioctc=".$body["FAR"]." AND ord.id=".$body["FPR"];
						$rs_tmp = $objDb->Execute($sql);
						if( ! $rs_tmp->RecordCount( ) > 0 ) {
							$CodigoError = 4;
							// En este paso grabamos el mensaje en un archivo para tenerlo como backup
							$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
							GrabarArchivo($archivo);
							$procesados_correctivos++;
							$pop3->DeleteMessage($i);
						}
					}

					// Verificamos la existencia del emplzamiento.
					$sql = "SELECT est.id FROM estaciones est WHERE est.numero_mnemonico=".$body["EMPLZ"];
					$rs_tmp = $objDb->Execute($sql);
					if( ! $rs_tmp->RecordCount( ) > 0 && !$CodigoError ) {
						$CodigoError = 1;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Finalmente si no hay errores procesamos el mensaje.
					if( !$CodigoError ) {
						$sql = 	"INSERT INTO ordenes
								(folioctc, numero_mnemonico, fecha, hora,
								arearesponsabletelefonica, responsabletelefonica, calidad, observacionesctc,
								folio_atix_relacionado, folio_proveedor_relacionado, tipo, estado,
								mnemonico)
								VALUES
								(".$body["FAT"].", ".$body["EMPLZ"].", '".InvertirFecha($fecha)."', '".$hora."',
								'".$body["AR"]."', '".$body["NGN"]."', '".$body["CRT"]."', '".$body["OBS"]."',
								".($body["FAR"]?$body["FAR"]:0).", ".($body["FPR"]?$body["FPR"]:0).", 'TC', 'AB',
								(select mnemonico from estaciones where id=".$body["EMPLZ"]." or numero_mnemonico=".$body["EMPLZ"]."))";
						$objDb->Execute( $sql );

						$tmp_rs = $objDb->Execute("SELECT last_value as orden FROM ordenes_id_seq");

						foreach( $sub_actividades as $k => $v ) {
							$sql = "INSERT INTO reparaciones
									(orden, actividad, sub_actividad, estado)
									VALUES
									(".$tmp_rs->fields['orden'].", (select actividad from sub_actividades where id=".$v."), ".$v.", 'AB')";
							$objDb->Execute( $sql );
						}

						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
				}
				// Fin Abierta

				// Comentarios Movistar (A)
				// Este no cambia el estado.
				// Formato Mail: F4
				elseif( $CodigoAccion == "CMV" && !($CodigoError = ValidaFormato($body, $formato["F4"])) ) {
					list($fecha, $hora) = explode(" ", $body["FR"], 2);

					// Comprobamos la existencia de los folios
					$sql = "SELECT ord.id, ord.folioctc
							FROM ordenes ord
							WHERE ord.folioctc=".$body["FAT"]." AND ord.id=".$body["FII"];
					$rs_tmp = $objDb->Execute($sql);
					if( ! $rs_tmp->RecordCount( ) > 0 && !$CodigoError ) {
						$CodigoError = 4;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Finalmente procesamos el mensaje.
					if( !$CodigoError ) {
						$sql = 	"UPDATE ordenes
								SET ctcfechaactualizacion = '".InvertirFecha($fecha)."',
									ctchoraactualizacion = '".$hora."',
									arearesponsabletelefonica = '".$body["AR"]."',
									responsabletelefonica = '".$body["NUMV"]."',
									observacionesctc = '".$body["OBS"]."',
									detallesctc_acum = detallesctc_acum || '\n".$body["OBS"]."'
								WHERE folioctc = ".$body["FAT"]." AND id = ".$body["FII"];
						$objDb->Execute( $sql );

						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
				}
				// Fin Comentarios Movistar (A)

				// Cierre Conforme, Cierre con Observaciones, Cierre por Rechazo
				// Formato Mail: F7
				elseif( ($CodigoAccion == "CR" || $CodigoAccion == "CRO" || $CodigoAccion == "CRR") && !($CodigoError = ValidaFormato($body, $formato["F7"])) ) {
					list($fecha, $hora) = explode(" ", $body["FR"], 2);

					// Comprobamos la existencia de los folios
					$sql = "SELECT ord.id, ord.folioctc
							FROM ordenes ord
							WHERE ord.folioctc=".$body["FAT"]." AND ord.id=".$body["FII"];
					$rs_tmp = $objDb->Execute($sql);
					if( ! $rs_tmp->RecordCount( ) > 0 && !$CodigoError ) {
						$CodigoError = 4;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Finalmente procesamos el mensaje.
					if( !$CodigoError ) {
						$sql = 	"UPDATE ordenes
								SET ctcfechacierre = '".InvertirFecha($fecha)."',
									ctchoracierre = '".$hora."',
									responsabletelefonica = '".$body["NGN"]."',
									arearesponsabletelefonica = '".$body["AR"]."',
									observacionesctc = '".$body["OBS"]."',
									detallesctc_acum = detallesctc_acum || '\n".$body["OBS"]."',
									estado = '$CodigoAccion'
								WHERE folioctc = ".$body["FAT"]." AND id = ".$body["FII"];
						$objDb->Execute( $sql );

						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
				}
				// Fin Cierre Conforme, Cierre con Observaciones, Cierre por Rechazo


				// El codigo de Accion No existe !!!
				elseif( !$CodigoError ) {
					$CodigoError = 6;
					// En este paso grabamos el mensaje en un archivo para tenerlo como backup
					$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
					GrabarArchivo($archivo);
					$procesados_correctivos++;
					$pop3->DeleteMessage($i);
				}

			}
			// Fin Trabajos CORRECTIVOS

			// Trabajos EXCEPCIONALES
			elseif( $TipoTrabajo == "TE" ) {

				// Abierta
				// Formato Mail: F1
				if( $CodigoAccion == "AB" && !($CodigoError = ValidaFormato($body, $formato["F1"])) ) {
					$sub_actividades 	= explode("|", $body["SACT"]);
					list($fecha, $hora) = explode(" ", $body["F"], 2);

					// Verificamos si el folio existe.
					$sql = "SELECT ord.id FROM ordenes ord WHERE ord.folioctc=".$Folio;
					$rs_tmp = $objDb->Execute($sql);
					if( $rs_tmp->RecordCount( ) > 0 && !$CodigoError ) {
						$CodigoError = 9;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Validacion de la existencia de los folios relacionados.
					if( ($body["FAR"] || $body["FPR"]) && !$CodigoError ) {
						$sql = "SELECT ord.id, ord.folioctc
								FROM ordenes ord
								WHERE ord.folioctc=".$body["FAR"]." AND ord.id=".$body["FPR"];
						$rs_tmp = $objDb->Execute($sql);
						if( ! $rs_tmp->RecordCount( ) > 0 ) {
							$CodigoError = 4;
							// En este paso grabamos el mensaje en un archivo para tenerlo como backup
							$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
							GrabarArchivo($archivo);
							$procesados_correctivos++;
							$pop3->DeleteMessage($i);
						}
					}

					// Verificamos la existencia del emplzamiento.
					$sql = "SELECT est.id FROM estaciones est WHERE est.numero_mnemonico=".$body["EMPLZ"];
					$rs_tmp = $objDb->Execute($sql);
					if( ! $rs_tmp->RecordCount( ) > 0 && !$CodigoError ) {
						$CodigoError = 1;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Finalmente si no hay errores procesamos el mensaje.
					if( !$CodigoError ) {
						$sql = 	"INSERT INTO ordenes
								(folioctc, numero_mnemonico, fecha, hora,
								arearesponsabletelefonica, responsabletelefonica, calidad, ctcdetalle,
								folio_atix_relacionado, folio_proveedor_relacionado, tipo, estado)
								VALUES
								(".$body["FAT"].", ".$body["EMPLZ"].", '".InvertirFecha($fecha)."', '".$hora."',
								'".$body["AR"]."', '".$body["NGN"]."', '".$body["CRT"]."', '".$body["OBS"]."',
								".($body["FAR"]?$body["FAR"]:0).", ".($body["FPR"]?$body["FPR"]:0).", 'TE', 'AB')";
						$objDb->Execute( $sql );

						$tmp_rs = $objDb->Execute("SELECT last_value as orden FROM ordenes_id_seq");

						foreach( $sub_actividades as $k => $v ) {
							$sql = "INSERT INTO reparaciones
									(orden, actividad, sub_actividad, estado)
									VALUES
									(".$tmp_rs->fields['orden'].", (select actividad from sub_actividades where id=".$v."), ".$v.", 'AB')";
							$objDb->Execute( $sql );
						}

						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
				}
				// Fin Abierta

				// Solicitud Recotizacion
				// Formato Mail: FF1
				elseif( $CodigoAccion == "SCTZ" && !($CodigoError = ValidaFormato($body, $formato["FF1"])) ) {
					list($fecha, $hora) = explode(" ", $body["F"], 2);

					// Verificamos si el folio existe.
					/*
					$sql = "SELECT ord.id FROM ordenes ord WHERE ord.folioctc=".$Folio;
					$rs_tmp = $objDb->Execute($sql);
					if( $rs_tmp->RecordCount( ) > 0 && !$CodigoError ) {
						$CodigoError = 9;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
					*/
					// Comprobamos la existencia de los folios
					$sql = "SELECT ord.id, ord.folioctc
							FROM ordenes ord
							WHERE ord.folioctc=".$body["FAT"]." AND ord.id=".$body["FII"];
					$rs_tmp = $objDb->Execute($sql);
					if( ! $rs_tmp->RecordCount( ) > 0 ) {
						$CodigoError = 4;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Finalmente procesamos el mensaje.
					if( !$CodigoError ) {
						$sql = 	"UPDATE ordenes
								SET arearesponsabletelefonica = '".$body["AR"]."',
									responsabletelefonica = '".$body["NGN"]."',
									ctcdetalle = '".$body["OBS"]."',
									detallesctc_acum = ctcdetalle || '<br />' || detallesctc_acum,
									estado = 'SCTZ'
								WHERE folioctc = ".$body["FAT"]." AND id = ".$body["FII"];
						$objDb->Execute( $sql );

						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
				}
				// Fin Solicitud Recotizacion

				// Acepta, Rechaza
				// Formato Mail: FF3

		elseif( ($codigoAccion == "TAC" || $CodigoAccion == "TRC") && !($CodigoError = ValidaFormato($body, $formato["FF3"])) ) {
				
					list($fecha, $hora) = explode(" ", $body["FR"], 2);
					list($fecha_programada, $hora_programada) = explode(" ", $body["FIT"], 2);

					// Comprobamos la existencia de los folios
					$sql = "SELECT ord.id, ord.folioctc
							FROM ordenes ord
							WHERE ord.folioctc=".$body["FAT"]." AND ord.id=".$body["FII"];
					$rs_tmp = $objDb->Execute($sql);
					if( ! $rs_tmp->RecordCount( ) > 0 ) {
						$CodigoError = 4;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Finalmente procesamos el mensaje.
					if( !$CodigoError ) {
						$sql = 	"UPDATE ordenes
								SET ctcfechaactualizacion = '".InvertirFecha($fecha)."',
									ctchoraactualizacion = '".$hora."',
									arearesponsabletelefonica = '".$body["AR"]."',
									nombreautorizadormovistar = '".$body["NAM"]."',
									fechaejecucion = '".InvertirFecha($fecha_programada)."',
									horaejecucion = '".$hora_programada."',
									numero_cotizacion = ".$body["NCTZ"].",
									valor = '" . str_replace(",", ".", $body["UFT"]) . "',
									ctcdetalle = '".$body["OBS"]."',
									detallesctc_acum = ctcdetalle || '<br />' || detallesctc_acum,
									estado = '$CodigoAccion'
								WHERE folioctc = ".$body["FAT"]." AND id = ".$body["FII"];
						$objDb->Execute( $sql );

						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
				}
				// Fin Acepta

				// Comentarios Movistar (A)
				// Este no cambia el estado.
				// Formato Mail: F4
				elseif( $CodigoAccion == "CMV" && !($CodigoError = ValidaFormato($body, $formato["F4"])) ) {
					list($fecha, $hora) = explode(" ", $body["FR"], 2);

					// Comprobamos la existencia de los folios
					$sql = "SELECT ord.id, ord.folioctc
							FROM ordenes ord
							WHERE ord.folioctc=".$body["FAT"]." AND ord.id=".$body["FII"];
					$rs_tmp = $objDb->Execute($sql);
					if( ! $rs_tmp->RecordCount( ) > 0 ) {
						$CodigoError = 4;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Finalmente procesamos el mensaje.
					if( !$CodigoError ) {
						$sql = 	"UPDATE ordenes
								SET ctcfechaactualizacion = '".InvertirFecha($fecha)."',
									ctchoraactualizacion = '".$hora."',
									arearesponsabletelefonica = '".$body["AR"]."',
									responsabletelefonica = '".$body["NUMV"]."',
									ctcdetalle = '".$body["OBS"]."',
									detallesctc_acum = ctcdetalle || '<br />' || detallesctc_acum
								WHERE folioctc = ".$body["FAT"]." AND id = ".$body["FII"];
						$objDb->Execute( $sql );

						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
				}
				// Fin Comentarios Movistar (A)


				// Cierre Conforme, Cierre con Observaciones, Cierre por Rechazo
				// Formato Mail: F7
				elseif( ($CodigoAccion == "CR" || $CodigoAccion == "CRO" || $CodigoAccion == "CRR") && !($CodigoError = ValidaFormato($body, $formato["F7"])) ) {
					list($fecha, $hora) = explode(" ", $body["FR"], 2);

					// Comprobamos la existencia de los folios
					$sql = "SELECT ord.id, ord.folioctc
							FROM ordenes ord
							WHERE ord.folioctc=".$body["FAT"]." AND ord.id=".$body["FII"];
					$rs_tmp = $objDb->Execute($sql);
					if( ! $rs_tmp->RecordCount( ) > 0 && !$CodigoError ) {
						$CodigoError = 4;
						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}

					// Finalmente procesamos el mensaje.
					if( !$CodigoError ) {
						$sql = 	"UPDATE ordenes
								SET ctcfechacierre = '".InvertirFecha($fecha)."',
									ctchoracierre = '".$hora."',
									responsabletelefonica = '".$body["NGN"]."',
									arearesponsabletelefonica = '".$body["AR"]."',
									observacionesctc = '".$body["OBS"]."',
									detallesctc_acum = detallesctc_acum || '\n".$body["OBS"]."',
									estado = '$CodigoAccion'
								WHERE folioctc = ".$body["FAT"]." AND id = ".$body["FII"];
						$objDb->Execute( $sql );

						// En este paso grabamos el mensaje en un archivo para tenerlo como backup
						$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
						GrabarArchivo($archivo);
						$procesados_correctivos++;
						$pop3->DeleteMessage($i);
					}
				}
				// Fin Cierre Conforme, Cierre con Observaciones, Cierre por Rechazo

				// El codigo de Accion No existe !!!
				elseif( !$CodigoError ) {
					$CodigoError = 6;
					// En este paso grabamos el mensaje en un archivo para tenerlo como backup
					$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
					GrabarArchivo($archivo);
					$procesados_correctivos++;
					$pop3->DeleteMessage($i);
				}

			}
			// Fin Trabajos EXCEPCIONALES

			// El tipo de trabajo es un Error.
			elseif( $TipoTrabajo == "ERR" ) {
				$sql = "insert into log_sistema
						(origen, tipo, fecha, hora,
						folio, mensaje, comentario)

						values
						('$TipoTrabajo', '$CodigoAccion', '".date("Y-m-d H:i:s")."', '".date("H:i:s")."',
						'$Folio', '$CantidadCotizaciones', '".$headers["Subject"]."')";
				$objDb->Execute( $sql );
				$msgError .= "Se ha creado un log de errores.";

				if( $CodigoAccion==999 || $CodigoAccion==10 ) {
					//$ProveedorCII, $TipoTrabajo, $Folio, $CodigoAccion, $CantidadCotizaciones, $TotalCotizaciones, $
					//TEST           -ERR          -10371  -999           -EP                    -1                  -1
					$sql = "update ordenes set estado=ctcestado, ctcestado=''
							where folioctc=$Folio and estado='FBK' ";
					$objDb->Execute( $sql );
				} else {
					//$sql = "update ordenes set estado='ERR'
					//		where folioctc=$Folio and estado='FBK' ";
					//$objDb->Execute( $sql );
				}

				// En este paso grabamos el mensaje en un archivo para tenerlo como backup
				$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
				GrabarArchivo($archivo);
				$procesados_correctivos++;
				$pop3->DeleteMessage($i);
			}

			// El tipo de trabajo no corresponde.
			else {
				$CodigoError = 6;
				// En este paso grabamos el mensaje en un archivo para tenerlo como backup
				$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
				GrabarArchivo($archivo);
				$procesados_correctivos++;
				$pop3->DeleteMessage($i);
			}

		}
		// El proveedor no corresponde.
		elseif( $ProveedorCII!='ERI' && $ProveedorCII!='ISO' && $ProveedorCII!='TEST' ){
			$CodigoError = 6;
			// En este paso grabamos el mensaje en un archivo para tenerlo como backup
			$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
			GrabarArchivo($archivo);
			$procesados_correctivos++;
			$pop3->DeleteMessage($i);
		}

		switch( $CodigoError && $TipoTrabajo!="ERR") {
			case 1:
			case 2:
			case 4:
			case 6:
			case 7:
			case 8:
			case 9:
			$MailSubject = _SIGLA_PROVEEDOR_ . "-ERR-" . $Folio . "-" . $CodigoError . "-" . $CodigoAccion;
			$msgError .= "Se ha enviado una notificación a " . _INTEGRADOR_ . " con los errores.";
			// Grabamos el log correspondiente.
			$sql = "insert into log_sistema
 					(origen, tipo, fecha, hora, folio, mensaje, comentario)
 					values
 					('ERR', '$CodigoError', '".date("Y-m-d H:i:s")."', '".date("H:i:s")."', '$Folio', '$CodigoAccion', '$MailSubject')";
			$objDb->Execute( $sql );
			// En este paso grabamos el mensaje en un archivo para tenerlo como backup
			$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
			GrabarArchivo($archivo);
			$procesados_correctivos++;
			$pop3->DeleteMessage($i);
			break;
			// No hay Errores.
			default:
			$MailSubject = _SIGLA_PROVEEDOR_ . "-ERR-" . $Folio . "-" . 999 . "-" . $CodigoAccion;
			// Grabamos el log correspondiente.
			$sql = "insert into log_sistema
 					(origen, tipo, fecha, hora, folio, mensaje, comentario)
 					values
 					('ERR', '998', '".date("Y-m-d H:i:s")."', '".date("H:i:s")."', '$Folio', '$CodigoAccion', '$MailSubject')";
			$objDb->Execute( $sql );
			// En este paso grabamos el mensaje en un archivo para tenerlo como backup
			$archivo = _PATH_RELATIVO_ . "/integracion/mails/" . date("YmdHis") . "_" . $headers["Subject"];
			GrabarArchivo($archivo);
			$procesados_correctivos++;
			$pop3->DeleteMessage($i);
			break;
		}

		$mail->Subject = $MailSubject;

		if( $ProveedorCII==_SIGLA_PROVEEDOR_ && $TipoTrabajo!="ERR") {
			$exito = $mail->Send();

			$intentos=1;
			while( (!$exito) && ($intentos < 3) ) {
				sleep(5);
				//echo $mail->ErrorInfo;
				$exito = $mail->Send();
				$intentos = $intentos++;
			}

			if( !$exito ) {
				$msgError .= $mail->ErrorInfo;
			}
			else {
				$msgError .= "";
			}
		}

	} // If
} // For

// Descomentar para que tenga efecto la aliminacion de mensajes.
$pop3->Close();


$html = date("d/m/Y H:i:s");
$html .= "\t".$procesados_correctivos." mensajes Correctivos procesados.\t";
$html .= $msgError ? $msgError : "No se produjeron errores.";
$html .= "\n".date("d/m/Y H:i:s");
$html .= "\t".$procesados_excepcionales." mensajes Excepcionales procesados.\t";
$html .= $msgError ? $msgError : "No se produjeron errores.";
$html .= "\n";

$archivo = _PATH_RELATIVO_ . "/integracion/mails/crontab_" . date("Ymd") .".log";
$contenido = $html;
GrabarArchivo($archivo, $contenido);

if( date("i")>=0 && date("i")<2 ) {
	$mail->ClearAddresses();
	$mail->AddAddress( "jgarcia@nsoft.cl" );
	$mail->AddAttachment($archivo);

	$mail->Subject = "Crontab " . date("d/m/Y H:i");
	$mail->Body = "Se adjunta resultado de tarea crontab (crontab_" . date("Ymd") .".log)";
	$exito = $mail->Send();

	$intentos=1;
	while( (!$exito) && ($intentos < 3) ) {
		sleep(5);
		$exito = $mail->Send();
		$intentos = $intentos++;
	}

	if( !$exito ) {
		$msgError .= $mail->ErrorInfo;
	}
	else {
		$msgError .= "";
	}
}

$objDb->Close( );
?>
