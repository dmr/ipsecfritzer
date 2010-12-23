#!/usr/bin/env php
<?php
/*
IPSEC FRITZER 

a tool for generating a mac os x and ios native ipsec client compatible vpn config
by Martin Brüggemann <martin@brgmn.de>

Copyright (c) 2010 Martin Brüggemann

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

include('config.inc.php');

echo "############################################################################\n".
	 "# IPSEC FRITZER v0.1 by Martin Brüggemann <blog@brgmn.de>                  #\n".
	 "# http://github.com/brgmn/ipsecfritzer                                     #\n".
	 "############################################################################\n";

$filename = 'vpn_config_'.date('Y-m-d-H-i').'.txt';
$headerTemplate = '/*
 * AVM FRITZBOX VPN CONFIG ('.$filename.')
 * generated by IPSEC FRITZER v0.1
 *
';

foreach ($accounts as $accountName => $accountData){
	$filler = null;
	if (strlen($accountName) < 15){
		for($i=15; $i > strlen($accountName); $i--){ $filler.= ' '; }
	}
	$headerTemplate.= ' * '.$filler.$accountName.' | '.$accountData['secret'].' | '.$accountData['password']."\n";
}

$headerTemplate.=' */

vpncfg {
	connections';

$accountTemplate = ' {
			name = "###ACCOUNT_NAME###@###DOMAIN###";
			enabled = yes;
            conn_type = conntype_user;                
			remoteip = 0.0.0.0;
			remote_virtualip = ###LOCAL_NETWORK###.###ACCOUNT_LOCAL_IP###;
			phase2remoteid {
				ipaddr = ###LOCAL_NETWORK###.###ACCOUNT_LOCAL_IP###;
			}
			remoteid {
                    key_id = "###ACCOUNT_NAME###@###DOMAIN###";
            }
            accesslist = "permit ip any ###ACCESSLIST_IP### 255.255.255.255";
			key = "###ACCOUNT_SECRET###";
            always_renew = no;
            reject_not_encrypted = no;
            dont_filter_netbios = yes;
            localip = 0.0.0.0;
            local_virtualip = 0.0.0.0;
            mode = phase1_mode_aggressive;
            phase1ss = "all/all/all";
            keytype = connkeytype_pre_shared;
            cert_do_server_auth = no;
			use_nat_t = yes;
            use_xauth = yes;
			use_cfgmode = no;
			xauth {
				valid=yes;
				username="###ACCOUNT_NAME###";
				passwd="###ACCOUNT_PASSWORD###";
			}
            phase2ss = "esp-all-all/ah-none/comp-all/no-pfs";
			phase2localid {
				ipnet {
					ipaddr = 0.0.0.0; 
					mask = 0.0.0.0;
				}
			}
    }';

$footerTemplate = "\n".'
    ike_forward_rules = "udp 0.0.0.0:500 0.0.0.0:500",
                        "udp 0.0.0.0:4500 0.0.0.0:4500";
}
// EOF';

//generate config
$markers = array();
$markers['###LOCAL_NETWORK###'] = $config['localNetwork'];
$markers['###DOMAIN###'] = $config['domain'];

$configData = $headerTemplate;
foreach ($accounts as $accountName => $accountData){
	//fill account markers
	$markers['###ACCOUNT_NAME###'] = $accountName;
	$markers['###ACCOUNT_PASSWORD###'] = $accountData['password'];
	$markers['###ACCOUNT_LOCAL_IP###'] = $accountData['localIP'];
	$markers['###ACCOUNT_SECRET###'] = $accountData['secret'];
	
	//check if traffic should be routed over the ipsec gateway
	$markers['###ACCESSLIST_IP###'] = $config['localNetwork'].'.0';
	if ($config['routeAllTrafficTroughVPN']) $markers['###ACCESSLIST_IP###'] = $config['localNetwork'].'.'.$accountData['localIP'];
	
	//parse template
	$findArray = array();
	$replaceArray = array();
	foreach ($markers as $markerKey => $markerValue){
		$findArray[] = $markerKey;
		$replaceArray[] = $markerValue;
	}
	$configData.= str_replace($findArray,$replaceArray,$accountTemplate);
}
$configData.= $footerTemplate;

//write config file
$configFileHandle = @fopen('output/'.$filename, "w") or die("Error while creating the config file");
@fwrite($configFileHandle,$configData) or die("Error while writing the config file");
@fclose(configFileHandle);

echo 'FritzBox!-VPN-Config succesfully written.'."\n\n";

