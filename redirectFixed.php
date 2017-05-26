<?
define("ADMIN_SECTION",false);
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");
global $APPLICATION;


//CУDIGO DE LA CONTRAMEDIDA, DESPLIEGA UNA ADVERTENCIA SI SE VA 
//A REDIRECCIONAR A UNA PAGINA EXTERNA
if(preg_match("'^(http://|https://|ftp://)'i", $goto))
{
        $url = str_replace("&amp;", "&", $goto);
	$url = str_replace(array("\r", "\n"), "", $url);
	if(!defined("BX_UTF") && defined("LANG_CHARSET"))
	$url = CharsetConverter::ConvertCharset($url, LANG_CHARSET, "UTF-8");

	echo <<< HTML
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<meta name="robots" content="noindex, nofollow" />
<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/adminstyles.css" />
<link rel="stylesheet" type="text/css" href="/bitrix/themes/.default/404.css" />
</head>
<body>

<div class="error-404">
<table class="error-404" border="0" cellpadding="0" cellspacing="0" align="center">
	<tbody><tr class="top">
		<td class="left"><div class="empty"></div></td>
		<td><div class="empty"></div></td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr>
		<td class="left"><div class="empty"></div></td>
		<td class="content">
			<div class="description">
				<table cellpadding="0" cellspacing="0">
					<tbody><tr>
						<td><div class="icon"></div></td>
						<td>Attention! You are about to be redirected to a different site. Click this link to open it: <nobr><a href="$url">$url</a></nobr></td>
					</tr>
				</tbody></table>
			</div>
		</td>
		<td class="right"><div class="empty"></div></td>
	</tr>
	<tr class="bottom">
		<td class="left"><div class="empty"></div></td>
		<td><div class="empty"></div></td>
		<td class="right"><div class="empty"></div></td>
	</tr>
</tbody></table>
</div>
</body>
</html>
HTML;
}
else
{
if(CModule::IncludeModule("statistic"))
{
	if(strlen($_REQUEST["site_id"]) <= 0)
	{
		$site_id = false;
		$referer_url = strlen($_SERVER["HTTP_REFERER"]) <= 0? $_SESSION["SESS_HTTP_REFERER"]: $_SERVER["HTTP_REFERER"];
		if(strlen($referer_url))
		{
			$url = @parse_url($referer_url);
			if($url)
			{
				$rs = CSite::GetList($v1="LENDIR", $v2="DESC", Array("ACTIVE"=>"Y", "DOMAIN"=> "%".$url["host"], "IN_DIR"=>$url["path"]));
				if($arr = $rs->Fetch())
					$site_id = $arr["ID"];
			}
		}
	}
	else
	{
		$site_id = $_REQUEST["site_id"];
	}
	$goto = preg_replace("/#EVENT_GID#/i", urlencode(CStatEvent::GetGID($site_id)), $_REQUEST["goto"]);
	CStatEvent::AddCurrent($_REQUEST["event1"], $_REQUEST["event2"], $_REQUEST["event3"], $_REQUEST["money"], $_REQUEST["currency"], $goto, $_REQUEST["chargeback"], $site_id);
}
else
{
	$goto = preg_replace("/#EVENT_GID#/i", "", $_REQUEST["goto"]);
}
LocalRedirect($goto);
}
?>