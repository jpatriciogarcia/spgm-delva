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
 <title>..:: SPGM - <?=_EMPRESA_?> ::.. (<?= $titulo ?>)</title>
 <style type="text/css">@import url("<?=_PATH_RELATIVO_;?>/common/style.css");</style>
 <style type="text/css">@import url("<?=_PATH_RELATIVO_;?>/common/js/jscalendar-1.0/calendar-brown.css");</style>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_;?>/common/js/jscalendar-1.0/calendar.js"></script>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_;?>/common/js/jscalendar-1.0/lang/calendar-es.js"></script>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_;?>/common/js/jscalendar-1.0/calendar-setup.js"></script>


 <link rel="stylesheet" type="text/css" href="<?=_PATH_RELATIVO_?>/common/js/mygosuMenu.1.5.3/1.1/example1.css" />
 <script type="text/javascript" src="<?=_PATH_RELATIVO_?>/common/js/mygosuMenu.1.5.3/ie5.js"></script>
 <script type="text/javascript" src="<?=_PATH_RELATIVO_?>/common/js/mygosuMenu.1.5.3/1.1/DropDownMenuX.js"></script>


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
</head>

<body onload="initjsDOMenu()">

<table align="center" border="0" cellspacing="0">
 <tr>
  <td><img src="<?=_PATH_RELATIVO_?>/img/logo.gif" height="62px" /></td>
  <td style="background-color:#D9B66F; width: 500px; height: 62px; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px; color:#FFFFFF; text-align:right; "><a href="http://www.nsoft.cl" target="_blank"><img src="<?=_PATH_RELATIVO_?>/img/logo_nsoft.gif" border="0px" align="right" height="31px" /></a>Sistema de Control de Radiobases 2006.</td>
 </tr>
<tr>
  <td colspan="2" style="background-image:url(<?=_PATH_RELATIVO_?>/img/masthead_separator.gif); width: 901px; height: 5px; "></td>
 </tr>
 <tr>
  <td colspan="2" style="background-color: #D9B66F; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:14px; color:#FFFFFF; text-align:right; "><br /><br />
  <div align="right" style="position:static; background-color:#D9B66F; font-family: Geneva, Arial, Helvetica, sans-serif; font-size:20px; color:#FFFFFF; text-align:left; "><?php echo trim($titulos[0]); ?><? isset($titulos[1]) ? print("<br>({$titulos[1]}") : FALSE; ?></div>
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
