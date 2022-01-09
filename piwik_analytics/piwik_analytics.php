<?php
/*
 * piwik_analytics.php
 *
 * Piwik / Matomo Analytics
 * Version: 0.8
 * Author: Biteno GmbH , M. Boehmichen 
 * https://github.com/mboehmic/
 *
 * Description:
 * Addon Module for whmcs to integrate Piwik/Matomo ecommerce tracking in whmcs
 * 
 * Tested with: 
 * Matomo 4.2.1 
 * whmcs: 8.3.2
 * Multibrand for whmcs 2.9.1 by ModulesGarden (you do not need to use Multibrand)
 * 
 * 'Concept:
 * the addon allows up to 6 different brands or domains out of whmcs to be tracked individually to piwik/Matomo
 * The idea behind the plugin: Use a piwik template (rather a fixed piece of javascript code) and insert piwik Domain-ID and variables as needed.
 * the file "hooks.php" in the addon directory ensures that every checkout witnhin whmcs is tracked as an ecommcerce sales
 * the file clientare_hook.php delivers the appropriate variables for your piwik url and piwik id through the template and
 * ensures the piwik tracking mechanism is fired for every page in the clientarea
 * 
 * # Installation
 * copy the folder piwik_analytics with  "piwik_analytics.php" and "hooks.php" to WHMCS\modules\addons\
 * copy the file "clieantare_hook.php" to WHMCS\include\hooks\
 * make sure all files habe appropriate ownerships (e.g. www-data.www-data on Debian)
 * Copy the file "footer-piwik.tpl" into your template include directory -> WHMCS\template\include\ 
 * Make sure this file is picked up by your chosen template by including the file in 
 * WHMCS\template\footer.tpl
 * 
 * Open "footer.tpl" in your preferred template and find the line
 * {include file="$template/includes/generate-password.tpl"}
 * Add the following line after 
 * {include file="$template/includes/footer-piwik.tpl}
 *  
 *  In whmcs go to -> System -> Addon Modules and enable "Piwik Analytics"
 *  Click -> configure and enter at least the following
 *  Your Piwik Hostname without http:// or https:// at the beginning in the form of host.domain.tld/ with an "/" at the end.
 *  The Hostname (fqdn) of your first Domain in whmcs (usually host.domain.tld) that you want to track with Matomo / Piwik
 *  the Piwik ID of this Domain (usually a numeric value)
 *  Domain / brands 2 to 6 are optional and can be left empty
 *  save your settings
 *  
 * # Disclaimer
 * I have tested the addon but I am by no means a well trained software developper 
 * Use at your own risk ;-) 
 *  
 */

function piwik_analytics_config() {
	$configarray = array( 
		'name' => 'Piwik Analytics', 
		'description' => 'This module provides a quick and rather dirty way to integrate Piwik / Matomo Analytics tracking into your WHMCS installation.', 
		'version' => '0.8', 
		'author' => '<a href="https://github.com/mboehmic/">Biteno GmbH</a>', 
		'fields' => array( 
			'piwikUrl' => array( 
				'FriendlyName' => 'Piwik URL', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'hostname and path of your Matomo/Piwik Installation without http(s), e.g. site.domain.tld/piwik/ with / at the end' 
			),
			'piwikSite1Hostname' => array( 
				'FriendlyName' => 'Piwik Site 1 fqdn Hostname', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'The fully qualified hostname of your first brand (must not be empty)' 
			),
			'piwikSite1ID' => array( 
				'FriendlyName' => 'Piwik Domain 1 ID', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'Numeric value of your first brand/domain in Matomo/Piwik (must not be empty)'
			),
			'piwikSite2Hostname' => array( 
				'FriendlyName' => 'Piwik Site 2 fqdn Hostname', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'The fully qualified hostname of your second brand (can be empty)' 
			),
			'piwikSite2ID' => array( 
				'FriendlyName' => 'Piwik Domain 2 ID', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'This is the numeric value of your second  brand domain in Matomo / Piwik (can be empty)'
			),
			'piwikSite3Hostname' => array( 
				'FriendlyName' => 'Piwik Site 3 fqdn Hostname', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'The fully qualified hostname of your third brand(can be empty)' 
			),
			'piwikSite3ID' => array( 
				'FriendlyName' => 'Piwik Domain 3 ID', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'This is the numeric value of your third domain in Matomo/Piwik (can be empty)'
			),
			'piwikSite4Hostname' => array( 
				'FriendlyName' => 'Piwik Site 4 fqdn Hostname', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'The fully qualified hostname of your fourth brand (can be empty)' 
			),
			'piwikSite4ID' => array( 
				'FriendlyName' => 'Piwik Domain 4 ID', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'This is the numeric value of your fourth brand domain in Matomo/Piwik (can be empty)'
			),
			'piwikSite5Hostname' => array( 
				'FriendlyName' => 'Piwik Site 5 fqdn Hostname', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'The fully qualified hostname of your fifth brand (can be empty)'
			),
			'piwikSite5ID' => array( 
				'FriendlyName' => 'Piwik Domain 5 ID', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'This is the numeric value of your fifth  brand domain in Matomo/Piwik (can be empty)'
			),
			'piwikSite6Hostname' => array( 
				'FriendlyName' => 'Piwik Site 6 fqdn Hostname', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'The fully qualified hostname of your sixth brand (can be empty)'
			),
			'piwikSite6ID' => array( 
				'FriendlyName' => 'Piwik Domain 6 ID', 
				'Type' => 'text', 
				'Size' => '25', 
				'Description' => 'This is the numeric value of your sixth  brand domain in Matomo/Piwik (can be empty)'
			)
			
		) 
	);
	return $configarray;
}



if (!defined( 'WHMCS' )) {
	exit( 'This file cannot be accessed directly' );
}

?>
