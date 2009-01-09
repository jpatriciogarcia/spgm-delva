<?php
/**
 * read_mail.php
 *
 * @autor nSoft - JGG
 * @date 2006.06.15
 * @version 2.1
 */
require( "../common/common.php" );

include( _PATH_RELATIVO_ . "/common/header.php" );

$directorio_mails = _PATH_RELATIVO_ . "/integracion/mails/";

$html = "\n<script languaje='javascript'>
function abrir( archivo ) {
\tvar URL = '$directorio_mails' + archivo;
\twindow.open('$directorio_mails' + archivo, 'Mail', 'width=450, height=350, resizable=1, scrollbars=1');
}
</script>
\n<table width='98%' align='center'>\n <tr>";
if ($gestor = opendir($directorio_mails)) {
	$i=-1;
	while (false !== ($archivo = readdir($gestor))) {
		if ($archivo != "." && $archivo != "..") {
			$html .= "\n  <td><a href=\"javascript:abrir('$archivo');\">$archivo</a></td>";
		}

		if( $i > 2 ) { $html .= "\n </tr>\n <tr>"; $i=0; }
		$i++;
	}
	closedir($gestor);
}
$html .= "\n</td>\n </tr>\n</table>";
echo $html;


include( _PATH_RELATIVO_ . "/common/footer.php" );
?>