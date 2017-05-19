<?
/**
 * Análisis del Código
 * @package SEGURIDAD INFORMATICA
 * @package UTN FRBA
 * @package 2017
* @package VULNERABILIDAD EN LINEAS 63-66
 */


define("ADMIN_SECTION",false);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;
/**
*@package INCLUYE UN MODULO DE ESTADISTICAS PARA REENVIOS A PAGINAS INTERNAS
*/
if(CModule::IncludeModule("statistic"))
{
	/**
	*@package PARA LAS PAGINAS INTERNAS USA COOKIES, TRATA DE MAPEAR UNA DIRECCION CON UN ID
	*/
	if(strlen($_REQUEST["site_id"]) <= 0)
	{
		/**
		*@package DESDE LA LINEA 26 A LA 35, TRATA DE ASIGNA EL ID DE LA PAGINA ALMACENADA EN LA COOKIE
		*/
		$site_id = false;
		$referer_url = strlen($_SERVER["HTTP_REFERER"]) <= 0? $_SESSION["SESS_HTTP_REFERER"]: $_SERVER["HTTP_REFERER"];
		if(strlen($referer_url))
		{
			$url = @parse_url($referer_url);
			if($url)
			{
				$rs = CSite::GetList($v1="LENDIR", $v2="DESC", Array("ACTIVE"=>"Y", "DOMAIN"=> "%".$url["host"], "IN_DIR"=>$url["path"]));
				if($arr = $rs->Fetch())
					/**
					*@package ASIGNA ID
					*/
					$site_id = $arr["ID"];
			}
		}
	}
	else
	{
		/**
		*@package CASO CONTRARIO LA INFORMACION DE ID DEL SITIO ES CONOCIDO
		*/
		$site_id = $_REQUEST["site_id"];
	}
	/**
	*@package ASIGNA EN GOTO LA REDIRECCION CORRESPONDIENTE + EVENTOS
	*/
	$goto = preg_replace("/#EVENT_GID#/i", urlencode(CStatEvent::GetGID($site_id)), $_REQUEST["goto"]);
	CStatEvent::AddCurrent($_REQUEST["event1"], $_REQUEST["event2"], $_REQUEST["event3"], $_REQUEST["money"], $_REQUEST["currency"], $goto, $_REQUEST["chargeback"], $site_id);
}
else
{
	/**
	*@package ASIGNA EN GOTO LA REDIRECCION CORRESPONDIENTE
	*/
	$goto = preg_replace("/#EVENT_GID#/i", "", $_REQUEST["goto"]);
}
/**
*@package HECHAS LAS VALIDACIONES ANTERIORES SE LLAMA A LA FUNCION GENERAL LOCALREDIRECT
*/
LocalRedirect($goto);
?>
