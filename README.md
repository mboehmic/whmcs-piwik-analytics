# whmcs-piwik-analytics
Addon for Piwik / Matomo analytics in WHMCS


Piwik / Matomo Analytics

Author: [Biteno GmbH] (https://www.biteno.com), M. Boehmichen 

https://github.com/mboehmic/
 *
## Description:
Addon Module for whmcs to integrate Piwik/Matomo ecommerce tracking in whmcs

Tested with: 

Matomo 4.2.1 

whmcs: 8.3.2

Multibrand for whmcs 2.9.1 by ModulesGarden (you do not need to use Multibrand)

## Concept:
The addon allows up to 6 different brands or domains out of whmcs to be tracked individually to piwik/Matomo. 

The idea behind the plugin: Use a piwik template (rather a fixed piece of javascript code) and insert piwik Domain-ID and variables as needed.

The file "hooks.php" in the addon directory ensures that every checkout witnhin whmcs is tracked as an ecommcerce sales. Tthe file clientare_hook.php delivers the appropriate variables for your piwik url and piwik id through the template and
ensures the piwik tracking mechanism is fired for every page in the clientarea.


## Installation
Copy the folder "piwik_analytics" with  "piwik_analytics.php" and "hooks.php" to your WHMCS\modules\addons\ directory.

Copy the file "clientarea_hook.php" to WHMCS\include\hooks\ . Make sure all files have appropriate ownerships (e.g. www-data.www-data on Debian)

Copy the file "footer-piwik.tpl" into your template include directory -> WHMCS\template\include\ 

Make sure this file is picked up by your chosen template by including the file in 
WHMCS\template\footer.tpl. To achieve this:

Open "footer.tpl" in your preferred template and find the line

{include file="$template/includes/generate-password.tpl"}

Add the following line after 

{include file="$template/includes/footer-piwik.tpl}


## Setup inside whmcs
Inside whmcs go to -> System -> Addon Modules and enable "Piwik Analytics"
 
Click -> configure and enter at least the following
Your Piwik Hostname **without http:// or https:// ** at the beginning in the form of host.domain.tld/ with an "/" at the end.

The Hostname (fqdn) of your first Domain in whmcs (usually host.domain.tld) that you want to track with Matomo / Piwik

The Piwik ID of this Domain (usually a numeric value)
Domain / brands 2 to 6 are optional and can be left empty
 save your settings
 
## Disclaimer
I have tested the addon but I am by no means a well trained software developper 
Use at your own risk ;-) 

## Improvements / Feedback
Feel free to improse the addon. If you have any suggestions for improvement, please let me know.
