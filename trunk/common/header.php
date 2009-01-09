<?php
if ( ! isset($_SESSION['validacion']) ) header("Location: "._PATH_RELATIVO_."/index.php");

if (isset($_REQUEST['ori']) ? trim($_REQUEST['ori']) != "" : FALSE) {
	$ori = trim($_REQUEST['ori']);
} elseif (isset($_SERVER['HTTP_REFERER']) ? trim($_SERVER['HTTP_REFERER']) != "" : FALSE) {
	$ori = trim($_SERVER['HTTP_REFERER']);
} else {
	$ori = "/menu.php";
}

$titulos = explode('(', $titulo);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="es">
<head>
 <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
 <script type="text/javascript" language="javascript" src="<?=_PATH_RELATIVO_;?>/common/js/scriptaculous/lib/prototype.js"></script>
<script type="text/javascript" language="JavaScript" src="<?=_PATH_RELATIVO_;?>/common/js/scriptaculous/src/scriptaculous.js"></script>
<script type="text/javascript" language="JavaScript" src="<?=_PATH_RELATIVO_;?>/common/js/_dialog.js"></script>
 <title>..:: SPGM - <?=_EMPRESA_?> ::.. (<?= $titulo ?>)</title>
 <style type="text/css">@import url("<?=_PATH_RELATIVO_;?>/common/style.css");</style>
 <style type="text/css">@import url("<?=_PATH_RELATIVO_;?>/common/js/jscalendar-1.0/calendar-blue2.css");</style>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_;?>/common/js/jscalendar-1.0/calendar.js"></script>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_;?>/common/js/jscalendar-1.0/lang/calendar-es.js"></script>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_;?>/common/js/jscalendar-1.0/calendar-setup.js"></script>

 <style type="text/css"><?php include(_PATH_RELATIVO_ . "/common/js/menu/style.css.php");?></style>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_;?>/common/js/menu/jsdomenu.js"></script>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_;?>/common/js/menu/jsdomenubar.js"></script>
 <script type="text/javascript">
 <?php
 if ( isset($_SESSION['validacion']) ) {
 	if( $_SESSION['validacion'] != 0) {
 		include(_PATH_RELATIVO_ . "/common/js/menu/menu.js.php");
 	}
 }
 ?>
</script>

<script language="javascript" type="text/javascript">
// Aprendiendo AJAX :-)

function objAJAX() {
	try {
		_objAJAX = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			_objAJAX = new ActiveXObject("Microsoft.XMLHTTP");
		} catch (E) {
			_objAJAX = false;
		}
	}
	if (! _objAJAX && typeof XMLHttpRequest != 'undefined') {
		_objAJAX = new XMLHttpRequest();
	}
	return _objAJAX
}

_objAJAX = objAJAX();

function objetus() {
	try {
		objetus = new ActiveXObject("Msxml2.XMLHTTP");
	} catch (e) {
		try {
			objetus= new ActiveXObject ("Microsoft.XMLHTTP");
		} catch (E) {
			objetus= false;
		}
	}
	if (! objetus && typeof XMLHttpRequest!= 'undefined') {
		objetus = new XMLHttpRequest();
	}
	return objetus
}

_objetus=objetus()

function busca_tecnico( id ) {

	_values_send="rut=" + document.getElementById(id).value;
	_URL_="busca_tecnico.php?"
	_objetus.open("GET",_URL_+"&"+_values_send,true);

	//una vez enviado los valores inmediatamente llamamos a la propiedad onreadystatechange
	_objetus.onreadystatechange=function() {
		//dentro de la funcion comprobamos el estado del proceso si es 4 (terminado) pedimos lo que nos han mandado
		if (_objetus.readyState==4 && _objetus.status==200) {
			//usamos la propiedad responseText para recibir en una cadena lo que nos mandaron
			var vari=_objetus.responseXML;
			var contratista = vari.getElementsByTagName('contratista').item(0).firstChild.data;
			var razon_social = vari.getElementsByTagName('razon_social').item(0).firstChild.data;
			var direccion_contratista = vari.getElementsByTagName('direccion_contratista').item(0).firstChild.data;
			var telefono = vari.getElementsByTagName('telefono').item(0).firstChild.data;
			var fax = vari.getElementsByTagName('fax').item(0).firstChild.data;
			var mail = vari.getElementsByTagName('mail').item(0).firstChild.data;

			document.getElementById('contratista').innerHTML = contratista
			document.getElementById('razon_social').innerHTML = razon_social
			document.getElementById('direccion_contratista').innerHTML = direccion_contratista
			document.getElementById('telefono').innerHTML = telefono
			document.getElementById('fax').innerHTML = fax
			document.getElementById('mail').innerHTML = mail

		}

	}
	//obligatorio .... luego explicarè el porque
	_objetus.send(null);
}
</script>

</head>

<body onload="initjsDOMenu()">

<table align="center" border="0" cellspacing="0">
 <tr>
  <td><img src="<?=_PATH_RELATIVO_?>/img/logo.jpg" height="62px" /></td>
  <td style="background-color:#FFF; width: 500px; height: 62px; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px; color:#000; text-align:right; "><a href="http://www.nsoft.cl" target="_blank"><img src="<?=_PATH_RELATIVO_?>/img/logo_nsoft.gif" border="0px" align="right" height="31px" /></a>Sistema de Control de Radiobases 2006.</td>
 </tr>
<tr>
  <td colspan="2" style="background-image:url(<?=_PATH_RELATIVO_?>/img/masthead_separator.gif); width: 901px; height: 5px; "></td>
 </tr>
 <tr>
  <td colspan="2" style="background-color: #10A8D1; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px; color:#FFFFFF; text-align:right; "><br /><br />
  <div align="right" style="position:static; background-color:#10A8D1; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:20px; color:#FFFFFF; text-align:left; "><?php echo trim($titulos[0]); ?><? isset($titulos[1]) ? print("<br>({$titulos[1]}") : FALSE; ?></div>
<?php 
if ( isset($_SESSION['validacion']) ) {
	if( $_SESSION['validacion'] != 0) {
?>
<!--
  <ul class="menu">
   <?php if ($_SERVER['REQUEST_URI'] != "$str/menu.php") { ?>
    <li class="menu"><a id="origen" href="<?= $ori ?>">Anterior</a></li>
    <li class="menu"><a href="<?=_PATH_RELATIVO_?>/menu.php">Menú</a></li>
    <li class="menu"><a href="<?=_PATH_RELATIVO_?>/index.php">Salir</a></li>
<?php 
   }
}
}
?>
   </ul>-->
 </td>
</tr>
</table>

<table align="center" border="0" cellspacing="0">
 <td style="background-image:url(<?=_PATH_RELATIVO_?>/img/masthead_separator.gif); width: 901px; height:5px; background-repeat:repeat-x;">
 <br />
