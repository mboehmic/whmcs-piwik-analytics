<?php
/*
 * hooks.php 
 * belongs to -> piwik_analytics 
 * 
 * this version requires the Piwik/Matomo Snippet to exist in $template\includes\footer-piwik.tpl
 * 
 * 
 */

function piwik_analytics_hook_checkout_tracker($vars) {
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

	$orderid = $vars['orderid'];
	$ordernumber = $vars['ordernumber'];
	$invoiceid = $vars['invoiceid'];
	$ispaid = $vars['ispaid'];
	$amount = $subtotal = $vars['amount'];
	$paymentmethod = $vars['paymentmethod'];
	$clientdetails = $vars['clientdetails'];
	$result = select_query( 'tblorders', 'renewals', array( 'id' => $orderid ) );
	$data = mysql_fetch_array( $result );
	$renewals = $data['renewals'];
	
	$code = "<script type='text/javascript'>
		  var _paq = window._paq = window._paq || [];
		  _paq.push(['trackPageView']);
		  _paq.push(['enableLinkTracking']);
		  ";

	if ($invoiceid) {
		$result = select_query( 'tblinvoices', 'subtotal,tax,tax2,total', array( 'id' => $invoiceid ) );
		$data = mysql_fetch_array( $result );
		$subtotal = $data['subtotal'];
		$tax = $data['tax'] + $data['tax2'];
		$total = $data['total'];
	}

	$code .= "_paq.push(['trackEcommerceOrder','{$orderid}','{$total}','{$subtotal}','{$tax}']);
			";
	
	$result = select_query( 'tblhosting", "tblhosting.id,tblproducts.id AS pid,tblproducts.name,tblproductgroups.name AS groupname,tblhosting.firstpaymentamount', array( 'orderid' => $orderid ), '', '', '', 'tblproducts ON tblproducts.id=tblhosting.packageid INNER JOIN tblproductgroups ON tblproductgroups.id=tblproducts.gid' );

	while ($data = mysql_fetch_array( $result )) {
		$serviceid = $data['id'];
		$itempid = $data['pid'];
		$name = $data['name'];
		$groupname = $data['groupname'];
		$itemamount = $data['firstpaymentamount'];
		$code .= "_paq.push(['addEcommerceItem','PID{$itempid}','{$name}','{$groupname}','{$itemamount}',1]);
		";
	}

	$result = select_query( 'tblhostingaddons', 'tblhostingaddons.id,tblhostingaddons.addonid,tbladdons.name,tblhostingaddons.setupfee,tblhostingaddons.recurring', array( 'orderid' => $orderid ), '', '', '', 'tbladdons ON tbladdons.id=tblhostingaddons.addonid' );

	while ($data = mysql_fetch_array( $result )) {
		$aid = $data['id'];
		$addonid = $data['addonid'];
		$name = $data['name'];
		$groupname = $data['groupname'];
		$itemamount = $data['setupfee'] + $data['recurring'];
		$code .= "_paq.push(['addEcommerceItem','AID{$addonid}','{$name}','Addons','{$itemamount}',1]);
		";
	}

	$result = select_query( 'tbldomains', 'tbldomains.id,tbldomains.type,tbldomains.domain,tbldomains.firstpaymentamount', array( 'orderid' => $orderid ) );

	while ($data = mysql_fetch_array( $result )) {
		$did = $data['id'];
		$regtype = $data['type'];
		$domain = $data['domain'];
		$itemamount = $data['firstpaymentamount'];
		$domainparts = explode( '.', $domain, 2 );
		$code .= "_paq.push(['addEcommerceItem','TLD". strtoupper( $domainparts[1] ) ."','{$regtype}','Domain','{$itemamount}',1]);
		";
	}

	if ($renewals) {
		$renewals = explode( ',', $renewals );
		foreach ($renewals as $renewal) {
			$renewal = explode( '=', $renewal );
			$domainid = $renewal[0];
			$registrationperiod = $renewal[1];
			$result = select_query( 'tbldomains', 'id,domain,recurringamount', array( 'id' => $domainid ) );
			$data = mysql_fetch_array( $result );
			$did = $data['id'];
			$domain = $data['domain'];
			$itemamount = $data['recurringamount'];
			$domainparts = explode( '.', $domain, 2 );
			$code .= "_paq.push(['addEcommerceItem','TLD". strtoupper( $domainparts[1] ) ."','Renewal','Domain','{$itemamount}',1]);
			";
		}
	}

	// das funktioniert
	$servername = $_SERVER['SERVER_NAME'] ;
	$domain = getHost($servername);
	
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

/** 
* auskommentiert, damit der komplette Piwik COde nicht zweimal erscheint
*
	$code .= "(function() {
			var u='//$mypiwikUrl';
			_paq.push(['setTrackerUrl', u+'matomo.php']);
			_paq.push(['setSiteId','$piwik_id']);
			var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
			g.type='text/javascript'; g.async=true; g.src=u+'matomo.js'; s.parentNode.insertBefore(g,s);
			})();
			
**/
	$code .="</script>
			";
			

/** nur zum Debuggen:
	$code .= "the db value of piwikUrl " .$modulevars['piwikUrl'] ."</br>";
	$code .= "the db value of piwikSite1ID " .$modulevars['piwikSite1ID'] ."</br>";
	$code .= "the db value of piwikSite1Hostname " .$modulevars['piwikSite1Hostname'] ."</br>";
	$code .= "</br>";
	$code .= "the db value of piwikSite2ID " .$modulevars['piwikSite2ID'] ."</br>";
	$code .= "the db value of piwikSite2Hostname " .$modulevars['piwikSite2Hostname'] ."</br>";
	$code .= "</br>";
	$code .= "the referer domain was ". $domain ."</br>";
	$code .= "the computed piwik_id was ". $piwik_id ."</br>";
**/	
			
	return $code;
}

function getHost($Address) { 
   $parseUrl = parse_url(trim($Address)); 
   return trim($parseUrl['host'] ? $parseUrl['host'] : array_shift(explode('/', $parseUrl['path'], 2))); 
   // Source: https://stackoverflow.com/questions/276516/parsing-domain-from-a-url
} 

if (!defined( 'WHMCS' )) {
	exit( 'This file cannot be accessed directly' );
}

add_hook( 'ShoppingCartCheckoutCompletePage', 1, 'piwik_analytics_hook_checkout_tracker' );

?>
