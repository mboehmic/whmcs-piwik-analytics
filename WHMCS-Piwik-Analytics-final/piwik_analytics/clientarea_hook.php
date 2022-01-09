<?php
/*
 * clientarea_hooks.php 
 * belongs to -> piwik_analytics 
 * this file needs to be copied to $WHMCS\includes\hooks\
 * 
 * Source / Inspiration: 
 * https://whmcs.community/topic/297137-let-a-hook-work-for-all-pages-on-whmcs/
 * https://docs.whmcs.com/Templates_and_Custom_PHP_Logic
 * 
 */

function hook_template_variables($vars) {
	global $CONFIG;

	$modulevars = array();
	$result = select_query( 'tbladdonmodules', '', array( 'module' => 'piwik_analytics' ) );

	while ($data = mysql_fetch_array( $result )) {
		$value = $data['value'];
		$value = explode( '|', $value );
		$value = trim( $value[0] );
		$modulevars[$data['setting']] = $value;
	}

	if (!$modulevars['piwikSite1ID'] || !$modulevars['piwikUrl']) {
		return false;
	}

	$mypiwikUrl = $modulevars['piwikUrl'] ;
	
	
	// das funktioniert
	$servername = $_SERVER['SERVER_NAME'] ;
	$domain = getHost2($servername);
	
	//$code .= "referer var is " . $domain ."</br>";

	if ($domain == $modulevars['piwikSite6Hostname']) {
		$piwik_id = $modulevars['piwikSite6ID'];
		}
	elseif ($domain == $modulevars['piwikSite5Hostname']) {
		$piwik_id = $modulevars['piwikSite5ID'];
		}
	elseif ($domain == $modulevars['piwikSite4Hostname']) {
		$piwik_id = $modulevars['piwikSite4ID'];
		}
	elseif ($domain == $modulevars['piwikSite3Hostname']) {
		$piwik_id = $modulevars['piwikSite3ID'];
		}
	elseif ($domain == $modulevars['piwikSite2Hostname']) {
		$piwik_id = $modulevars['piwikSite2ID'];
		}
	else {
		$piwik_id = $modulevars['piwikSite1ID'];
		}

    $extraTemplateVariables = array();
 
    // set a fixed value
    $extraTemplateVariables['PiwikID'] = $piwik_id ;
    $extraTemplateVariables['PiwikURL'] = $mypiwikUrl ;
 
    return $extraTemplateVariables;
}

function getHost2($Address) { 
   $parseUrl = parse_url(trim($Address)); 
   return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2))); 
   // Source: https://stackoverflow.com/questions/276516/parsing-domain-from-a-url
} 

if (!defined( 'WHMCS' )) {
	exit( 'This file cannot be accessed directly' );
}

add_hook( 'ClientAreaPage', 1, 'hook_template_variables' );

?>
