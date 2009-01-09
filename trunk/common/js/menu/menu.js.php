<?php
/**
 * El menu para el sitio.
 *
 * @date 2006.06.08
 * @version 1
 * @author JGG
 */

echo '
function createjsDOMenu() {

absoluteMenu1 = new jsDOMenu(150, "absolute");
with (absoluteMenu1) {';

// Para hacer la distincion en el menu de acuerdo al tipo de usuario
if( $_SESSION['perfil'] == "0" || $_SESSION['perfil'] == "1" ) {
	echo '
	addMenuItem(new menuItem("Preventivas", "item1", ""));
	addMenuItem(new menuItem("Correctivas", "item2", ""));
	addMenuItem(new menuItem("Extraordinarias", "item3", ""));
	addMenuItem(new menuItem("Buscar", "", "' . _PATH_RELATIVO_ . '/ordenes/buscador.php"));
	addMenuItem(new menuItem("Ver Log", "", "' . _PATH_RELATIVO_ . '/ordenes/log.php"));
	addMenuItem(new menuItem("Resumen", "", "' . _PATH_RELATIVO_ . '/admin/reporte_totales.php"));
	addMenuItem(new menuItem("Exportar a Excel", "", "' . _PATH_RELATIVO_ . '/admin/reporte_folios.php"));
	';
} else {
	echo '	addMenuItem(new menuItem("Visualizar Ordenes", "", "' . _PATH_RELATIVO_ . '/ordenes/listado.php"));';
}
//--

echo '}

absoluteMenu1_1 = new jsDOMenu(250, "absolute");
with (absoluteMenu1_1) {
addMenuItem(new menuItem("Generar Orden", "", "' . _PATH_RELATIVO_ . '/ordenes/preventivas/generacion.php"));
addMenuItem(new menuItem("Informe Equipos de Clima", "", "' . _PATH_RELATIVO_ . '/ordenes/preventivas/informes/clima/informe.php"));
addMenuItem(new menuItem("Informe Grupos Electrogenos", "", "' . _PATH_RELATIVO_ . '/ordenes/preventivas/informes/electrogeno/informe.php"));
}

absoluteMenu1_2 = new jsDOMenu(200, "absolute");
with (absoluteMenu1_2) {
addMenuItem(new menuItem("Abierta", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=AB"));
addMenuItem(new menuItem("En Proceso", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=EP"));
addMenuItem(new menuItem("Rechazada", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=RC"));
addMenuItem(new menuItem("Comentarios desde ' . _INTEGRADOR_ . '", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=CMV"));
addMenuItem(new menuItem("Comentarios desde ' . _EMPRESA_ . '", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=CPV"));
addMenuItem(new menuItem("Solucionado", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=SL"));
addMenuItem(new menuItem("Cierre Conforme", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=CR"));
addMenuItem(new menuItem("Cierre con Observaciones", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=CRO"));
addMenuItem(new menuItem("Cierre por Rechazo", "", "' . _PATH_RELATIVO_ . '/ordenes/correctivas/listado.php?estado=CRR"));
}

absoluteMenu1_3 = new jsDOMenu(200, "absolute");
with (absoluteMenu1_3) {
addMenuItem(new menuItem("Abierta", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=AB"));
addMenuItem(new menuItem("Cotizado", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=CTZ"));
addMenuItem(new menuItem("Solicitud Recotizacion", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=SCTZ"));
addMenuItem(new menuItem("Recotizado", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=CTZ2"));
addMenuItem(new menuItem("Acepta", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=TAC"));
addMenuItem(new menuItem("Rechaza", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=TRC"));
addMenuItem(new menuItem("Programado", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=PRG"));
addMenuItem(new menuItem("Comentarios desde '._INTEGRADOR_.'", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=CMV"));
addMenuItem(new menuItem("Comentarios desde '._EMPRESA_.'", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=CPV"));
addMenuItem(new menuItem("Solucionado", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=SL"));
addMenuItem(new menuItem("Cierre Conforme", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=CR"));
addMenuItem(new menuItem("Cierre con Observaciones", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=CRO"));
addMenuItem(new menuItem("Cierre por Rechazo", "", "' . _PATH_RELATIVO_ . '/ordenes/extraordinarias/listado.php?estado=CRR"));
}


absoluteMenu2 = new jsDOMenu(300, "absolute");
with (absoluteMenu2) {';
if( $_SESSION['perfil'] == "0" || $_SESSION['perfil'] == "1" ) {
	echo '
	addMenuItem(new menuItem("Visualizar Ordenes", "", "' . _PATH_RELATIVO_ . '/ordenes/listado.php"));
	addMenuItem(new menuItem("Visualizar Estaciones", "", "' . _PATH_RELATIVO_ . '/estaciones/buscar.php"));
	addMenuItem(new menuItem("Estaciones Atendidas en un Periodo", "", "' . _PATH_RELATIVO_ . '/estaciones/atendidas.php"));
	addMenuItem(new menuItem("Estaciones No Atendidas en un Periodo", "", "' . _PATH_RELATIVO_ . '/estaciones/no-atendidas.php"));
	addMenuItem(new menuItem("Bitacora", "", "' . _PATH_RELATIVO_ . '/listados/bitacora.php"));
	addMenuItem(new menuItem("Generar Estado de Pago", "", "' . _PATH_RELATIVO_ . '/facturacion/generacion.php"));
	addMenuItem(new menuItem("Resumen de Indisponibilidades", "", "' . _PATH_RELATIVO_ . '/indisponibilidades/resumen.php"));';
} else {
	echo '
	addMenuItem(new menuItem("Instalaciones", "", "' . _PATH_RELATIVO_ . '/estaciones/listado.php"));
	addMenuItem(new menuItem("Contratistas", "", "' . _PATH_RELATIVO_ . '/listados/contratistas.php"));
	addMenuItem(new menuItem("Planilla Maxima", "", "' . _PATH_RELATIVO_ . '/listados/maxima.php"));
	addMenuItem(new menuItem("Procedimientos de Mantencion", "", "' . _PATH_RELATIVO_ . '/listados/manuales.php"));';
}
echo '
}


absoluteMenu3 = new jsDOMenu(300, "absolute");
with (absoluteMenu3) {';

if( $_SESSION['perfil'] == "0" || $_SESSION['perfil'] == "1" ) {
	echo '
	addMenuItem(new menuItem("Generar Calendario", "", "' . _PATH_RELATIVO_ . '/itinerario/generacion.php"));
	addMenuItem(new menuItem("Ver Calendario", "", "' . _PATH_RELATIVO_ . '/itinerario/calendario.php"));
	addMenuItem(new menuItem("Generacion de Items de Consumo", "", "' . _PATH_RELATIVO_ . '/listados/consumo.php"));';
} else {
	echo 'addMenuItem(new menuItem("Calendario de Itinerarios de Mantencion", "", "' . _PATH_RELATIVO_ . '/itinerario/calendario.php"));';
}
echo '
}

';

if( $_SESSION['perfil'] == "0" || $_SESSION['perfil'] == "1" ) {
	echo '
	absoluteMenu4 = new jsDOMenu(150, "absolute");
	with (absoluteMenu4) {
	addMenuItem(new menuItem("Mantenedores", "item1", ""));
	}
	
	absoluteMenu4_1 = new jsDOMenu(200, "absolute");
	with (absoluteMenu4_1) {
	addMenuItem(new menuItem("Estaciones", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/estaciones.php"));
	addMenuItem(new menuItem("Clima", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/climas.php"));
	addMenuItem(new menuItem("Electrogenos", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/electrogenos.php"));
	addMenuItem(new menuItem("-", "", ""));
	addMenuItem(new menuItem("Actividades", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/generico_select.php?tabla=actividades"));
	addMenuItem(new menuItem("Sub Actividades", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/generico_select.php?tabla=sub_actividades"));
	addMenuItem(new menuItem("Tareas", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/generico_select.php?tabla=tareas"));
	addMenuItem(new menuItem("-", "", ""));
	addMenuItem(new menuItem("Contratistas", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/generico_select.php?tabla=contratistas"));
	addMenuItem(new menuItem("Tecnicos", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/generico_select.php?tabla=tecnicos"));
	addMenuItem(new menuItem("Usuarios", "", "' . _PATH_RELATIVO_ . '/admin/mantenedores/generico_select.php?tabla=usuarios"));
	}
	
	absoluteMenu1.items.item1.setSubMenu(absoluteMenu1_1);
	absoluteMenu1.items.item2.setSubMenu(absoluteMenu1_2);
	absoluteMenu1.items.item3.setSubMenu(absoluteMenu1_3);
	absoluteMenu4.items.item1.setSubMenu(absoluteMenu4_1);
	';
}

// Esto es solo para cambiar el nombre del menu dependiendo del tipo de usuario.
if( $_SESSION['perfil'] == "0" || $_SESSION['perfil'] == "1" )
$menu_3 = "Itinerario";
else
$menu_3 = "Otros";
//--

echo '
absoluteMenuBar = new jsDOMenuBar();
with (absoluteMenuBar) {
addMenuBarItem(new menuBarItem("Inicio", null, "Inicio", true, "' . _PATH_RELATIVO_ . '/menu.php"));
addMenuBarItem(new menuBarItem("Ordenes", absoluteMenu1));
addMenuBarItem(new menuBarItem("Listados", absoluteMenu2));
';
if( $_SESSION['perfil'] == "0" ) {
	echo 'addMenuBarItem(new menuBarItem("Administracion", absoluteMenu4));';
}
echo '
addMenuBarItem(new menuBarItem("Ayuda", null, "Ayuda", true, "' . _PATH_RELATIVO_ . '/ayuda/"));
addMenuBarItem(new menuBarItem("Salir", null, "Salir", true, "' . _PATH_RELATIVO_ . '/."));
var x = screen.width==1280?580:452;
moveBy(x, 70);
//"absolute" and "fixed"
//setMode("absolute");
//"click" and "over"
//setActivateMode("over");
setDraggable(true);
}
}
';
?>