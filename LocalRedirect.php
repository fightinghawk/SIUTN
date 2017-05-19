<?php 
/**
 * Bitrix Framework
 * @package bitrix
 * @subpackage main
 * @copyright 2001-2013 Bitrix
 */

//    LocalRedirect()
//    /bitrix/modules/main/tools.php:3447
function LocalRedirect($url, $skip_security_check=false, $status="302 Found")
{
	/** @global CMain $APPLICATION */
	global $APPLICATION;

	if(defined("DEMO") && DEMO=="Y" && (!defined("SITEEXPIREDATE") || !defined("OLDSITEEXPIREDATE") || strlen(SITEEXPIREDATE) <= 0 || SITEEXPIREDATE != OLDSITEEXPIREDATE))
		die(GetMessage("TOOLS_TRIAL_EXP"));

	//doubtful
	$url = str_replace("&amp;", "&", $url);

	if(function_exists("getmoduleevents"))
	{
		foreach(GetModuleEvents("main", "OnBeforeLocalRedirect", true) as $arEvent)
			ExecuteModuleEventEx($arEvent, array(&$url, $skip_security_check));
	}

	// http response splitting defence
	$url = str_replace(array("\r", "\n"), "", $url);

	CHTTP::SetStatus($status);

	if(preg_match("'^(http://|https://|ftp://)'i", $url))
	{
		if(!defined("BX_UTF") && defined("LANG_CHARSET"))
			$url = CharsetConverter::ConvertCharset($url, LANG_CHARSET, "UTF-8");

		header("Request-URI: ".$url);
		header("Content-Location: ".$url);
		header("Location: ".$url);
	}
	else
	{
		//store cookies for next hit (see CMain::GetSpreadCookieHTML())
		$APPLICATION->StoreCookies();

		if(strpos($url, "/") !== 0)
			$url = str_replace(array("\r", "\n"), "", $APPLICATION->GetCurDir()).$url;

		if(!defined("BX_UTF") && defined("LANG_CHARSET"))
			$url = CharsetConverter::ConvertCharset($url, LANG_CHARSET, "UTF-8");

		$host = $_SERVER['HTTP_HOST'];
		if($_SERVER['SERVER_PORT'] <> 80 && $_SERVER['SERVER_PORT'] <> 443 && $_SERVER['SERVER_PORT'] > 0 && strpos($_SERVER['HTTP_HOST'], ":") === false)
			$host .= ":".$_SERVER['SERVER_PORT'];

		$protocol = (CMain::IsHTTPS() ? "https" : "http");

		header("Request-URI: ".$protocol."://".$host.$url);
		header("Content-Location: ".$protocol."://".$host.$url);
		header("Location: ".$protocol."://".$host.$url);
	}

	if(function_exists("getmoduleevents"))
	{
		foreach(GetModuleEvents("main", "OnLocalRedirect", true) as $arEvent)
			ExecuteModuleEventEx($arEvent);
	}

	$_SESSION["BX_REDIRECT_TIME"] = time();
	exit;
}

