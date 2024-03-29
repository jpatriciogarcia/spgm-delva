<?php


echo "
/*
Menu related selectors
*/
.jsdomenudiv {
	background-color: #10A8D1;
	/*background-image: url(" . _PATH_RELATIVO_ . "/common/js/menu/img/office_xp_menu_left.png);*/
	background-repeat: repeat-y;
	border: 1px solid #DCDDDF;
	cursor: default;
	padding-bottom: 1px;
	padding-top: 1px;
	position: absolute; /* Do not alter this line! */
	visibility: hidden;
	z-index: 10;
}

.jsdomenuitem {
	background: transparent;
	border: none;
	color: #DCDDDF;
	font-family: Tahoma, Helvetica, sans, Arial, sans-serif;
	font-size: 12px;
	padding-bottom: 3px;
	padding-left: 30px;
	padding-right: 15px;
	padding-top: 3px;
	position: relative; /* Do not alter this line! */
}

.jsdomenuitemover {
	background-color: #DCDDDF;
	border: 1px solid #316AC5;
	color: #000000;
	font-family: Tahoma, Helvetica, sans, Arial, sans-serif;
	font-size: 12px;
	margin-left: 1px;
	margin-right: 1px;
	padding-bottom: 2px;
	padding-left: 28px;
	padding-right: 15px;
	padding-top: 2px;
	position: relative; /* Do not alter this line! */
}

.jsdomenuarrow {
	background-image: url(" . _PATH_RELATIVO_ . "/common/js/menu/img/office_xp_arrow.png);
	background-repeat: no-repeat; /* Do not alter this line! */
	height: 7px;
	position: absolute; /* Do not alter this line! */
	right: 8px;
	width: 4px;
}

.jsdomenuarrowover {
	background-image: url(" . _PATH_RELATIVO_ . "/common/js/menu/img/office_xp_arrow_o.png);
	background-repeat: no-repeat; /* Do not alter this line! */
	height: 7px;
	position: absolute; /* Do not alter this line! */
	right: 8px;
	width: 4px;
}

.jsdomenusep {
	padding-left: 28px;
}

.jsdomenusep hr {
}

/*
Menu bar related selectors
*/
.jsdomenubardiv {
	background-color: #10A8D1;
	/*background-image: url(" . _PATH_RELATIVO_ . "/common/js/menu/img/office_xp_divider.png);*/
	background-position: left;
	background-repeat: no-repeat;
	border: 1px outset;
	cursor: default;
	padding-bottom: 3px;
	padding-left: 1px;
	padding-right: 1px;
	padding-top: 3px;
	position: absolute; /* Do not alter this line! */
	visibility: visible;
}

.jsdomenubardragdiv {
	cursor: move;
	display: inline;
	font-family: Tahoma, Helvetica, sans, Arial, sans-serif;
	font-size: 12px;
	padding-bottom: 2px;
	padding-left: 5px;
	padding-right: 5px;
	padding-top: 2px;
	position: relative; /* Do not alter this line! */
	visibility: hidden;
	width: 9px;
}

.jsdomenubaritem {
	background-color: #10A8D1;
	border: none;
	color: #DCDDDF;
	display: inline;
	font-family: Tahoma, Helvetica, sans, Arial, sans-serif;
	font-size: 12px;
	padding-bottom: 2px;
	padding-left: 24px;
	padding-right: 10px;
	padding-top: 2px;
	position: relative; /* Do not alter this line! */
}

.jsdomenubaritemover {
	background-color: #DCDDDF;
	border: 1px solid #316AC5;
	color: #000000;
	display: inline;
	font-family: Tahoma, Helvetica, sans, Arial, sans-serif;
	font-size: 12px;
	padding-bottom: 2px;
	padding-left: 23px;
	padding-right: 9px;
	padding-top: 2px;
	position: relative; /* Do not alter this line! */
}

.jsdomenubaritemclick {
	background-color: #DCDDDF;
	border: 1px solid #316AC5;
	color: #000000;
	display: inline;
	font-family: Tahoma, Helvetica, sans, Arial, sans-serif;
	font-size: 12px;
	padding-bottom: 2px;
	padding-left: 23px;
	padding-right: 9px;
	padding-top: 2px;
	position: relative; /* Do not alter this line! */
}

/*
Example of selectors for icons. Change the height and width to match the actual 
height and width of the icon image.
*/
.icon1 {
	background-image: url(" . _PATH_RELATIVO_ . "/common/js/menu/img/icon1.png);
	background-repeat: no-repeat; /* Do not alter this line! */
	height: 16px;
	left: 4px;
	position: absolute; /* Do not alter this line! */
	width: 16px;
}

.icon2 {
	background-image: url(" . _PATH_RELATIVO_ . "/common/js/menu/img/icon2.png);
	background-repeat: no-repeat; /* Do not alter this line! */
	height: 16px;
	left: 4px;
	position: absolute; /* Do not alter this line! */
	width: 16px;
}

.icon3 {
	background-image: url(" . _PATH_RELATIVO_ . "/common/js/menu/img/icon3.png);
	background-repeat: no-repeat; /* Do not alter this line! */
	height: 16px;
	left: 4px;
	position: absolute; /* Do not alter this line! */
	width: 16px;
}
";

?>