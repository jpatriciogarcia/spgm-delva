<?php
/**
 * send_mail-cli.php.php
 *
 * Este archivo se ejecuta por consola (crontab).
 * Hay que tener la precaucion de dar todos los paths de manera absoluta.
 *
 * @author nSoft - JGG <jgarcigo@yahoo.es>
 * @copyright 2006.06.15
 * @version 2.1
 */

require("/var/www/spgm/common/common-cli.php");

$objDb->debug = false;
$debug = 0;

function cleanString( $str ) {
	return preg_replace("((\r\n)+)", " ", trim($str));
}

$sql = "select trim(ord.tipo) as tipo, trim(ord.estado) as estado, trim(ord.ctcestado) as ctcestado,
			trim(ord.folioctc) as folioctc, ord.id, ord.fechaultimoenvio
		from ordenes ord
		where ord.estado in('FBK') and ord.tipo in('TE', 'TC')
		group by ord.tipo, ord.estado, ord.ctcestado, ord.folioctc, ord.id, ord.fechaultimoenvio
		order by ord.id
		";
$rs_folios = $objDb->Execute($sql);

while ( ! $rs_folios->EOF ) {
	list( $date, $time ) = split( "[ ]", $rs_folios->fields['fechaultimoenvio'] );
	list( $year, $mon, $day ) = split( "[-]", $date );
	list( $hour, $min, $sec ) = split( "[:|.]", $time, 3 );
	@$minutos_trancurridos = ( mktime(date("H"), date("i"), 0, date("m"), date("d"), date("Y")) - mktime($hour, $min, 0, $mon, $day, $year) ) / 60;

	// AQUI REENVIAMOS EL CORREO SI EL TIEMPO DE ESPERA DEL FEEDBACK FUE MAYOR O IGUAL A _REENVIO_MAIL_
	

	if ( $minutos_trancurridos >= _REENVIO_MAIL_ ) {

		switch ( $rs_folios->fields['tipo'] ) {

			// TRABAJOS CORRECTIVOS
			case 'TC':
			switch ( $rs_folios->fields['ctcestado']) {

				// FORMATO MAIL F2
				case "EP":
				$sql = "SELECT
							folioctc AS FAT, ord.id AS FII, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
							est.areatecnica AS ARPCII, tec.nombre AS NGT, plazo AS DUR,
							to_char(fechaejecucion, 'DD-MM-YYYY ') || to_char(cast(horaejecucion as TIME), 'HH24:MI') AS FIT,
							observaciones AS OBS
						FROM ordenes ord
							join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
							join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
						WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$sql = "select orden, actividad, sub_actividad, tarea
				from reparaciones rep
				where estado = 'EP' and orden=" . $rs_folios->fields['id'];
				$rs_reparaciones = $objDb->Execute( $sql );

				$actividades 	= array();
				$subactividades = array();
				$tareas 		= array();

				while( ! $rs_reparaciones->EOF ) {
					$actividades[] 		= $rs_reparaciones->fields["actividad"];
					$subactividades[] 	= $rs_reparaciones->fields["sub_actividad"];
					$tareas[] 			= $rs_reparaciones->fields["tarea"];
					$rs_reparaciones->MoveNext();
				}

				$mail->Subject = _SIGLA_PROVEEDOR_ . "-TC-" . trim($rs_body->fields["fat"]) . "-EP-";
				$mail->Body = "#########################################\n"
				."FAT&" . trim($rs_body->fields["fat"]) . "\n"
				."FII&" . trim($rs_body->fields["fii"]) . "\n"
				."FR&" . trim($rs_body->fields["fr"]) . "\n"
				."ARPCII&" . substr(cleanString($rs_body->fields["arpcii"]), 0, 80) . "\n"
				."NGT&" . substr(cleanString($rs_body->fields["ngt"]), 0, 120) . "\n"
				."DUR&" . trim($rs_body->fields["dur"]) . "\n"
				."FIT&" . trim($rs_body->fields["fit"]) . "\n"
				."ACT&" . implode("|", array_unique($actividades)) . "\n"
				."SACT&" . implode("|", array_unique($subactividades)) . "\n"
				."TRS&" . implode("|", array_unique($tareas)) . "\n"
				."OBS&" . substr(cleanString($rs_body->fields["obs"]), 0, 255) . "\n"
				."#########################################\n";
				break;
				// case "EP"

				// FORMATO MAIL F3
				case "RC":
				$sql = "SELECT
					folioctc AS FAT, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
						est.areatecnica AS ARPCII, tec.nombre AS NGT,
						observaciones AS OBS
				FROM ordenes ord
					join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
					join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
						WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$mail->Subject = _SIGLA_PROVEEDOR_ . "-TC-" . trim($rs_body->fields["fat"]) . "-RC-";
				$mail->Body = "#########################################\n"
				."FAT&" . trim($rs_body->fields["fat"]) . "\n"
				."FR&" . trim($rs_body->fields["fr"]) . "\n"
				."ARPCII&" . substr(cleanString($rs_body->fields["arpcii"]), 0, 80) . "\n"
				."NGT&" . substr(cleanString($rs_body->fields["ngt"]), 0, 120) . "\n"
				."OBS&" . substr(cleanString($rs_body->fields["obs"]), 0, 255) . "\n"
				."#########################################\n";
				break;
				// case "RC"

				// FORMATO MAIL F5
				case "CPV":
			$sql = "SELECT
			folioctc AS FAT, ord.id AS FII, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
				est.areatecnica AS ARPCII, tec.nombre AS NGT,
				observaciones AS OBS
			FROM ordenes ord
			join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
			join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
			WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$mail->Subject = _SIGLA_PROVEEDOR_ . "-TC-" . trim($rs_body->fields["fat"]) . "-CPV-";
				$mail->Body = "#########################################\n"
				."FAT&" . trim($rs_body->fields["fat"]) . "\n"
				."FII&" . trim($rs_body->fields["fii"]) . "\n"
				."FR&" . trim($rs_body->fields["fr"]) . "\n"
				."ARPCII&" . substr(cleanString($rs_body->fields["arpcii"]), 0, 80) . "\n"
				."NGT&" . substr(cleanString($rs_body->fields["ngt"]), 0, 120) . "\n"
				."OBS&" . substr(cleanString($rs_body->fields["obs"]), 0, 255) . "\n"
				."#########################################\n";
				break;
				// case "CPV"

				// FORMATO MAIL F6
				case "SL":
				$sql = "SELECT
							folioctc AS FAT, ord.id AS FII, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
							est.areatecnica AS ARPCII, tec.nombre AS NGT, to_char(FechaSolucion, 'DD-MM-YYYY HH24:MI') AS FSOL,
							observaciones AS OBS
						FROM ordenes ord
							join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
							join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
						WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$mail->Subject = _SIGLA_PROVEEDOR_ . "-TC-" . trim($rs_body->fields["fat"]) . "-EP-";
				$mail->Body = "#########################################\n"
				."FAT&" . trim($rs_body->fields["fat"]) . "\n"
				."FII&" . trim($rs_body->fields["fii"]) . "\n"
				."FR&" . trim($rs_body->fields["fr"]) . "\n"
				."ARPCII&" . substr(cleanString($rs_body->fields["arpcii"]), 0, 80) . "\n"
				."NGT&" . substr(cleanString($rs_body->fields["ngt"]), 0, 120) . "\n"
				."FSOL&" . trim($rs_body->fields["fsol"]) . "\n"
				."OBS&" . substr(cleanString($rs_body->fields["obs"]), 0, 255) . "\n"
				."#########################################\n";
				break;
				// case "SL"
			} // switch ( $rs_folios->fields['ctcestado'])
			break;
			// case 'TC'

			// TRABAJOS EXCEPCIONALES
			case 'TE':


			switch ( $rs_folios->fields['ctcestado']) {

				case "CTZ":

				$sql = "SELECT
					folioctc AS FAT, ord.id AS FII, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
					est.areatecnica AS ARPCII, tec.nombre AS NGT, plazo AS DUR,
					to_char(fechaejecucion, 'DD-MM-YYYY ') || to_char(cast(horaejecucion as TIME), 'HH24:MI') AS FIT,
					observaciones AS OBS, valor AS UFT, numero_cotizacion as NCTZ
				FROM ordenes ord
				join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
				join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
				WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$sql = "select orden, actividad, sub_actividad, tarea, numero_cotizacion, valor_cotizacion
				from reparaciones rep
				where estado = 'CTZ' and orden=" . $rs_folios->fields['id'];
				$rs_reparaciones = $objDb->Execute( $sql );

				$actividades 			= array();
				$subactividades 		= array();
				$tareas 				= array();
				$cotizaciones_numero 	= array();
				$cotizaciones_valor		= array();

				while( ! $rs_reparaciones->EOF ) {
					$cotizaciones_numero[]	= $rs_reparaciones->fields["numero_cotizacion"];
					$cotizaciones_valor[]	= $rs_reparaciones->fields["valor_cotizacion"];
					$rs_reparaciones->MoveNext();
				}

				$cotizaciones_numero = array_unique($cotizaciones_numero);
				$cotizaciones_valor = array_unique($cotizaciones_valor);

				$cantidad_cotizaciones=0;
				foreach( $cotizaciones_numero as $k => $v ) {
					if(!$v)continue;
					else $cantidad_cotizaciones++;
				}

				$www=1;

				foreach( $cotizaciones_numero as $k => $v ) {
					if(!$v)continue;
					$sql = "select orden, actividad, sub_actividad, tarea
					from reparaciones rep
					where estado = 'CTZ' and orden=" . $rs_folios->fields['id'] . " and numero_cotizacion=" . $v;
					$rs_reparaciones = $objDb->Execute( $sql );
					while( ! $rs_reparaciones->EOF ) {
						$actividades[]		= $rs_reparaciones->fields["actividad"];
						$subactividades[]	= $rs_reparaciones->fields["sub_actividad"];
						$tareas[]			= $rs_reparaciones->fields["tarea"];
						$rs_reparaciones->MoveNext();
					}
					$mail->Subject = _SIGLA_PROVEEDOR_ . "-TE-" . trim($rs_body->fields["fat"]) . "-CTZ-" . ($www++) . "-" . $cantidad_cotizaciones;
					$mail->Body = "#########################################\n"
					."FAT&" . trim($rs_body->fields["fat"]) . "\n"
					."FII&" . trim($rs_body->fields["fii"]) . "\n"
					."FR&" . trim($rs_body->fields["fr"]) . "\n"
					."ARPCII&" . trim($rs_body->fields["arpcii"]) . "\n"
					."NGT&" . trim($rs_body->fields["ngt"]) . "\n"
					."DUR&" . trim($rs_body->fields["dur"]) . "\n"
					."FIT&" . trim($rs_body->fields["fit"]) . "\n"
					."ACT&" . implode("|", array_unique($actividades)) . "\n"
					."SACT&" . implode("|", array_unique($subactividades)) . "\n"
					."TRS&" . implode("|", array_unique($tareas)) . "\n"
					."OBS&" . substr(trim($rs_body->fields["obs"]), 0, 255) . "\n"
					."UFT&" . number_format(trim($cotizaciones_valor[$k]), 2, ",", "") . "\n"
					."NCTZ&" . trim($cotizaciones_numero[$k]) . "\n"
					."#########################################\n";
					$exito = $mail->Send();
					$actividades 	= array();
					$subactividades = array();
					$tareas 		= array();
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
				} // Fin foreach

				break;

				case "CTZ2":

				$sql = "SELECT
					folioctc AS FAT, ord.id AS FII, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
					est.areatecnica AS ARPCII, tec.nombre AS NGT, plazo AS DUR,
					to_char(fechaejecucion, 'DD-MM-YYYY ') || to_char(cast(horaejecucion as TIME), 'HH24:MI') AS FIT,
					observaciones AS OBS, valor AS UFT, numero_cotizacion as NCTZ
				FROM ordenes ord
				join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
				join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
				WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$sql = "select orden, actividad, sub_actividad, tarea, numero_cotizacion, valor_cotizacion
				from reparaciones rep
				where estado = 'CTZ2' and orden=" . $rs_folios->fields['id'];
				$rs_reparaciones = $objDb->Execute( $sql );

				$actividades 			= array();
				$subactividades 		= array();
				$tareas 				= array();
				$cotizaciones_numero 	= array();
				$cotizaciones_valor		= array();

				while( ! $rs_reparaciones->EOF ) {
					$cotizaciones_numero[]	= $rs_reparaciones->fields["numero_cotizacion"];
					$cotizaciones_valor[]	= $rs_reparaciones->fields["valor_cotizacion"];
					$rs_reparaciones->MoveNext();
				}

				$cotizaciones_numero = array_unique($cotizaciones_numero);
				$cotizaciones_valor = array_unique($cotizaciones_valor);

				$cantidad_cotizaciones=0;
				foreach( $cotizaciones_numero as $k => $v ) {
					if(!$v)continue;
					else $cantidad_cotizaciones++;
				}

				$www=1;
				foreach( $cotizaciones_numero as $k => $v ) {
					if(!$v)continue;
					$sql = "select orden, actividad, sub_actividad, tarea
					from reparaciones rep
					where estado = 'CTZ2' and orden=" . $rs_folios->fields['id'] . " and numero_cotizacion=" . $v;
					$rs_reparaciones = $objDb->Execute( $sql );
					while( ! $rs_reparaciones->EOF ) {
						$actividades[]		= $rs_reparaciones->fields["actividad"];
						$subactividades[]	= $rs_reparaciones->fields["sub_actividad"];
						$tareas[]			= $rs_reparaciones->fields["tarea"];
						$rs_reparaciones->MoveNext();
					}
					$mail->Subject = _SIGLA_PROVEEDOR_ . "-TE-" . trim($rs_body->fields["fat"]) . "-CTZ2-" . ($www++) . "-" . $cantidad_cotizaciones;
					$mail->Body = "#########################################\n"
					."FAT&" . trim($rs_body->fields["fat"]) . "\n"
					."FII&" . trim($rs_body->fields["fii"]) . "\n"
					."FR&" . trim($rs_body->fields["fr"]) . "\n"
					."ARPCII&" . trim($rs_body->fields["arpcii"]) . "\n"
					."NGT&" . trim($rs_body->fields["ngt"]) . "\n"
					."DUR&" . trim($rs_body->fields["dur"]) . "\n"
					."FIT&" . trim($rs_body->fields["fit"]) . "\n"
					."ACT&" . implode("|", array_unique($actividades)) . "\n"
					."SACT&" . implode("|", array_unique($subactividades)) . "\n"
					."TRS&" . implode("|", array_unique($tareas)) . "\n"
					."OBS&" . substr(trim($rs_body->fields["obs"]), 0, 255) . "\n"
					."UFT&" . number_format(trim($cotizaciones_valor[$k]), 2, ".", "") . "\n"
					."NCTZ&" . trim($cotizaciones_numero[$k]) . "\n"
					."#########################################\n";
					$exito = $mail->Send();
					$actividades 	= array();
					$subactividades = array();
					$tareas 		= array();
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
				} // Fin foreach

				break;

				case "PRG":

				$sql = "SELECT
					folioctc AS FAT, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
					est.areatecnica AS ARPCII, tec.nombre AS NGT, plazo AS DUR,
					to_char(fechaejecucion, 'DD-MM-YYYY ') || to_char(cast(horaejecucion as TIME), 'HH24:MI') AS FIT,
					observaciones AS OBS
				FROM ordenes ord
				join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
				join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
				WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$mail->Subject = _SIGLA_PROVEEDOR_ . "-TE-" . trim($rs_body->fields["fat"]) . "-PRG-";
				$mail->Body = "#########################################\n"
				."FAT&" . trim($rs_body->fields["fat"]) . "\n"
				."FR&" . trim($rs_body->fields["fr"]) . "\n"
				."ARPCII&" . trim($rs_body->fields["arpcii"]) . "\n"
				."NGT&" . trim($rs_body->fields["ngt"]) . "\n"
				."FIT&" . trim($rs_body->fields["fit"]) . "\n"
				."DUR&" . trim($rs_body->fields["dur"]) . "\n"
				."OBS&" . substr(trim($rs_body->fields["obs"]), 0, 255) . "\n"
				."#########################################\n";
				break;

				case "CPV":
				case "AB":

				$sql = "SELECT
					folioctc AS FAT, ord.id AS FII, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
					est.areatecnica AS ARPCII, tec.nombre AS NGT, observaciones AS OBS
				FROM ordenes ord
				join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
				join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
				WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$mail->Subject = _SIGLA_PROVEEDOR_ . "-TE-" . trim($rs_body->fields["fat"]) . "-PRG-";
				$mail->Body = "#########################################\n"
				."FAT&" . trim($rs_body->fields["fat"]) . "\n"
				."FII&" . trim($rs_body->fields["fii"]) . "\n"
				."FR&" . trim($rs_body->fields["fr"]) . "\n"
				."ARPCII&" . trim($rs_body->fields["arpcii"]) . "\n"
				."NGT&" . trim($rs_body->fields["ngt"]) . "\n"
				."OBS&" . substr(trim($rs_body->fields["obs"]), 0, 255) . "\n"
				."#########################################\n";
				break;

				case "SL":

				$sql = "SELECT
					folioctc AS FAT, ord.id AS FII, to_char(current_timestamp, 'DD-MM-YYYY HH24:MI') AS FR,
					est.areatecnica AS ARPCII, tec.nombre AS NGT, plazo AS DUR,
					to_char(fechaejecucion, 'DD-MM-YYYY ') || to_char(cast(horaejecucion as TIME), 'HH24:MI') AS FIT,
					to_char(fechasolucion, 'DD-MM-YYYY ') || to_char(cast(horasolucion as TIME), 'HH24:MI') AS FSOL,
					observaciones AS OBS
				FROM ordenes ord
				join estaciones est on (ord.numero_mnemonico=est.numero_mnemonico)
				join tecnicos tec on (ord.tecnico=tec.rut or ord.contratista=tec.contratista)
				WHERE ord.id=" . $rs_folios->fields['id'];
				$rs_body = $objDb->Execute($sql);

				$mail->Subject = _SIGLA_PROVEEDOR_ . "-TE-" . trim($rs_body->fields["fat"]) . "-SL-";
				$mail->Body = "#########################################\n"
				."FAT&" . trim($rs_body->fields["fat"]) . "\n"
				."FII&" . trim($rs_body->fields["fii"]) . "\n"
				."FR&" . trim($rs_body->fields["fr"]) . "\n"
				."ARPCII&" . trim($rs_body->fields["arpcii"]) . "\n"
				."NGT&" . trim($rs_body->fields["ngt"]) . "\n"
				."FSOL&" . trim($rs_body->fields["fsol"]) . "\n"
				."OBS&" . substr(trim($rs_body->fields["obs"]), 0, 255) . "\n"
				."#########################################\n";
				break;

			} // switch ( $rs_folios->fields['ctcestado'])
			break;
			// case 'TE'

		} // switch ( $rs_folios->fields['tipo'] )
	} // if ( $minutos_trancurridos >= 20 )

	// ENVIO DEL MAIL
	if ( $mail->Subject && $mail->Body && ($rs_folios->fields['ctcestado']!='CTZ') ) {

		echo "enviando mail...\n";

		$exito = $mail->Send();
		$intentos=1;
		while( (!$exito) && ($intentos < 3) ) {
			sleep(5);
			$exito = $mail->Send();
			$intentos = $intentos++;
		}
	}

	$mail->Subject = $mail->Body = "";
	$rs_folios->MoveNext();
} // while ( ! $rs_folios->EOF )

?>
