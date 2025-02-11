<?php
/**
 * Module WHMCS-DNA
 * @package DomainNameApi
 * @version 2.2.3
 */

use \WHMCS\Domain\TopLevel\ImportItem;
use \WHMCS\Domains\DomainLookup\ResultsList;
use \WHMCS\Domains\DomainLookup\SearchResult;
use \WHMCS\Module\Registrar\Registrarmodule\ApiClient;
use \WHMCS\Database\Capsule;

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once __DIR__ . '/lib/Services/Language.php';
new DomainNameApi\Services\Language();

function domainnameapi_version(): string
{
    return '2.2.3';
}

function domainnameapi_getConfigArray($params) {
    $configarray = [];

    $customfields = domainnameapi_parse_cache('config_settings', 512, function () {

        $customfields['no_field'] = 'There is no such as field in my system';

        $fields = Illuminate\Database\Capsule\Manager::table('tblcustomfields')
                                                     ->where('type', 'client')
                                                     ->pluck('fieldname', 'id');

        foreach ($fields as $k => $v) {
            $customfields[$k] = $v;
        }
        return $customfields;
    });

    if (class_exists("SoapClient")) {

        $sysMsg ='';

        if(strlen($params['API_UserName'])<1 ||  strlen($params['API_Password'])<1) {
            $sysMsg = 'Please enter your username and password';
        }else{

            $dna = getDNAApi($params);
            $username = $params["API_UserName"];
            $password = $params["API_Password"];
            $testmode = $params["API_TestMode"];


            $sysMsg = domainnameapi_parse_cache('user_'.$username.md5($password).$testmode, 100, function () use ($dna) {

                $details = $dna->GetResellerDetails();

                $sysMsg='';

                if ($details['result'] != 'OK') {
                     $sysMsg = "Username and password combination not correct";
                } else {
                    $balances = [];
                     $sysMsg = "User: <b>{$details['name']}({$details['id']})</b> , Balance: ";
                    foreach ($details['balances'] as $k => $v) {
                        $balances[]= "<b>{$v['balance']}{$v['symbol']}</b>";
                     }
                    $sysMsg .= implode(' | ', $balances);

                }

                return $sysMsg;

            });

        }

        $configarray = [
            "FriendlyName" => [
                "Type"  => "System",
                "Value" => "DomainNameAPI"
            ],
            "Description"  => [
                "Type"  => "System",
                "Value" => $sysMsg ."<br>Don't have an Domain Name API account yet? Get one here: <a href='https://www.domainnameapi.com/become-a-reseller' target='_blank'>https://www.domainnameapi.com/become-a-reseller</a>"
            ],
            "API_UserName" => [
                "FriendlyName" => "UserName",
                "Type"         => "text",
                "Size"         => "20",
                "Default"      => "ownername"
            ],
            "API_Password" => [
                "FriendlyName" => "Password",
                "Type"         => "password",
                "Size"         => "20",
                "Default"      => "ownerpass"
            ],
            "API_TestMode" => [
                "FriendlyName" => "Test Mode",
                "Type"         => "yesno",
                "Default"      => "yes",
                "Description"  => "Check for using test platform!"
            ],
            'TrIdendity'   => [
                'FriendlyName' => 'Turkish Identity',
                'Type'         => 'dropdown',
                'Options'      => $customfields,
                'Description'  => 'Turkish Identity Custom Field , required only .tr tld',
            ],
            'TrTaxOffice'  => [
                'FriendlyName' => 'Turkish Tax Office',
                'Type'         => 'dropdown',
                'Options'      => $customfields,
                'Description'  => 'Turkish Tax Office Custom Field , required only .tr tld',
            ],
            'TrTaxNumber'  => [
                'FriendlyName' => 'Turkish TaxNumber',
                'Type'         => 'dropdown',
                'Options'      => $customfields,
                'Description'  => 'Turkish TaxNumber Custom Field , required only .tr tld',
            ],
            'basecurrency' => [
                'FriendlyName' => 'Exchange Convertion For TLD Sync',
                'Type'         => 'dropdown',
                'Options'      => [
                    'no'  => 'Do Not Convert',
                    'TRY' => 'to TRY',
                    'EUR' => 'to EUR',
                    'IRR' => 'to IRR',
                    'INR' => 'to INR',
                    'PKR' => 'to PKR',
                    'CNY' => 'to CNY',
                    'AED' => 'to AED',
                ],
                'Description'  => 'Base Currency Convertion. <br><b>Strongly advice to not use this feature</b>. Using this feature means that you have read and fully understood the  <a href="https://github.com/domainreseller/whmcs-dna/blob/main/DISCLAIMER.md" target="_blank">DISCLAIMER AND WAIVER OF LIABILITY</a>'
            ],
        ];
    } else {
        return [
            "FriendlyName" => [
                "Type"  => "System",
                "Value" => "Domain Name API - ICANN Accredited Domain Registrar from TURKEY"
            ],
            "Description"  => [
                "Type"  => "System",
                "Value" => "<span style='color:red'>Your server does not support SOAPClient. Please install and activate it. <a href='http://php.net/manual/en/class.soapclient.php' target='_blank'>Detailed informations</a></span>"
            ],
        ];
    }

    return $configarray;
}

function domainnameapi_GetNameservers($params) {
    $dna = getDNAApi($params);

    $result = $dna->GetDetails($params["sld"].".".$params["tld"]);
    $values=[];

    if($result["result"] == "OK") {
        if(is_array($result["data"]["NameServers"])) {
            foreach ([0,1,2,3,4] as $k => $v) {
                if (isset($result["data"]["NameServers"][$v])) {
                    $values["ns".($v+1)] = $result["data"]["NameServers"][$v];
                }
            }
        }
        else {
            // Only one nameserver
            if(isset($result["data"]["NameServers"])) { $values["ns1"] = $result["data"]["NameServers"]; }
        }
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_SaveNameservers($params) {

    $dna = getDNAApi($params);

    $values=$nsList=[];

    foreach ([1,2,3,4,5] as $k => $v) {
        if (isset($params["ns{$v}"]) && is_string($params["ns{$v}"]) && strlen(trim($params["ns{$v}"])) > 0) {
            $nsList[] = $params["ns{$v}"];
        }
    }

    // Process request
    $result = $dna->ModifyNameserver($params["sld"].".".$params["tld"], $nsList);

    if($result["result"] == "OK") {

        foreach ([0, 1, 2, 3, 4] as $k => $v) {
            if (isset($result["data"]["NameServers"][0][$v])) {
                $values["ns" . ($v + 1)] = $result["data"]["NameServers"][0][$v];
            }
        }
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_GetRegistrarLock($params) {

    $dna = getDNAApi($params);

    $values=[];


    // Process request
    $result = $dna->GetDetails($params["sld"].".".$params["tld"]);

    if ($result["result"] == "OK") {
        if (isset($result["data"]["LockStatus"])) {

            if ($result["data"]["LockStatus"] == "true") {
                $values = "locked";
            } else {
                $values = "unlocked";
            }

        }
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request

    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );
    return $values;
}

function domainnameapi_SaveRegistrarLock($params) {

    $dna = getDNAApi($params);

    $values=[];

    // Get current lock status from registrar, Process request
    $result = $dna->GetDetails($params["sld"].".".$params["tld"]);


    if($result["result"] == "OK") {
        if(isset($result["data"]["LockStatus"])) {
            if($result["data"]["LockStatus"] == "true")
            {
                $kilit = "locked";
            } else {
                $kilit = "unlocked";
            }

            if($kilit == "unlocked") {
                // Process request
                $result = $dna->EnableTheftProtectionLock($params["sld"].".".$params["tld"]);
            } else {
                // Process request
                $result = $dna->DisableTheftProtectionLock($params["sld"].".".$params["tld"]);
            }

            if($result["result"] == "OK") {
                $values = ["success" => true];
            } else {
                $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
            }

        }
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request

    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_RegisterDomain($params) {

    $dna = getDNAApi($params);

    $values=[];



    $nameServers = [];
    $period = 1;
    $privacyProtection = false;

    // Set nameservers
    foreach ([1,2,3,4,5] as $k => $v) {
        if (isset($params["ns{$v}"]) && trim($params["ns{$v}"]) != "") {
            $nameServers[] = $params["ns{$v}"];
        }
    }

    // Set period
    if (isset($params["regperiod"]) && is_numeric($params["regperiod"])) {
        $period = intval($params["regperiod"]);
    }
    if (isset($params["idprotection"]) && ($params["idprotection"] == true || trim($params["idprotection"]) == "1")) {
        $privacyProtection = true;
    }

    $addionalfields = [];

    if($dna->isTrTLD($params["sld"] . "." . $params["tld"])){
        $addionalfields=domainnameapi_parse_trcontact($params);
    }

    // Register Domain
    $result = $dna->RegisterWithContactInfo(
        // Domain name
        $params["sld"] . "." . $params["tld"],
        // Years
        $period,
        // Contact informations
        [
            // Administrative contact
            "Administrative" => domainnameapi_parse_clientinfo($params),
            // Billing contact
            "Billing" => domainnameapi_parse_clientinfo($params),
            // Technical contact
            "Technical" => domainnameapi_parse_clientinfo($params),
            // Registrant contact
            "Registrant" => domainnameapi_parse_clientinfo($params),
        ],
        // Nameservers
        $nameServers,
        // Theft protection lock enabled
        false,
        // Privacy Protection enabled
        $privacyProtection,
        //Adddionalattributes
        $addionalfields
    );

    if($result["result"] == "OK") {
        $values = ["success" => true];
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );
    return $values;
}

function domainnameapi_TransferDomain($params) {

    $dna = getDNAApi($params);

    $values=[];

    // Process request
    $result = $dna->Transfer($params["sld"].".".$params["tld"], $params["transfersecret"],$params['regperiod']);

    if($result["result"] == "OK") {
       $values = ["success" => true];
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_RenewDomain($params) {

    $dna = getDNAApi($params);

    $values=[];


    // Process request
    $result = $dna->Renew($params["sld"].".".$params["tld"], $params["regperiod"]);

    if($result["result"] == "OK") {
        $values = ["success" => true];
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_GetContactDetails($params) {

    $dna = getDNAApi($params);

    $values=[];


    // Process request
    $result = $dna->GetContacts($params["sld"].".".$params["tld"]);

    if($result["result"] == "OK")
    {
        $values = [
            'RegistrantContact'=>domainnameapi_parse_contact($result["data"]["contacts"]["Registrant"]),
            'AdministrativeContact'=>domainnameapi_parse_contact($result["data"]["contacts"]["Administrative"]),
            'BillingContact'=>domainnameapi_parse_contact($result["data"]["contacts"]["Billing"]),
            'TechnicalContact'=>domainnameapi_parse_contact($result["data"]["contacts"]["Technical"]),
        ];

    }
    else
    {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_SaveContactDetails($params) {

    $dna = getDNAApi($params);

    $values=[];

    // Process request
    $result = $dna->SaveContacts(

    // DOMAIN NAME
        $params["sld"].".".$params["tld"],
        [
            "Administrative" => domainnameapi_parse_clientinfo($params["contactdetails"]["AdministrativeContact"]),
            "Billing" =>domainnameapi_parse_clientinfo($params["contactdetails"]["BillingContact"]),
            "Technical" =>domainnameapi_parse_clientinfo($params["contactdetails"]["TechnicalContact"]),
            "Registrant" => domainnameapi_parse_clientinfo($params["contactdetails"]["RegistrantContact"])
        ]
    );

    if($result["result"] == "OK") {
        $values = ["success" => true];
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );
    return $values;
}

function domainnameapi_GetEPPCode($params) {

    $dna = getDNAApi($params);

    $values=[];

    // Process request
    $result = $dna->GetDetails($params["sld"].".".$params["tld"]);

    if($result["result"] == "OK")
    {
        if(isset($result["data"]["AuthCode"]))
        {
            $values["eppcode"] = $result["data"]["AuthCode"];
        }
        else
        {
            $values["error"] = "EPP Code can not reveived from registrar!";
        }
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );
    return $values;
}

function domainnameapi_RegisterNameserver($params) {
    $dna = getDNAApi($params);

    $values=[];

    // Process request
    $result = $dna->AddChildNameServer($params["sld"].".".$params["tld"], $params["nameserver"], $params["ipaddress"]);

    if ($result["result"] == "OK") {
        $values["success"] = true;
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );
    return $values;
}

function domainnameapi_ModifyNameserver($params) {

    $dna = getDNAApi($params);

    $values=[];

    // Process request
    $result = $dna->ModifyChildNameServer(
        $params["sld"].".".$params["tld"],
        $params["nameserver"],
        $params["newipaddress"]
    );

    if ($result["result"] == "OK") {
        $values["success"] = true;
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );
    return $values;
}

function domainnameapi_DeleteNameserver($params) {
    $dna = getDNAApi($params);

    $values=[];

    // Process request
    $result = $dna->DeleteChildNameServer($params["sld"].".".$params["tld"], $params["nameserver"]);

    if ($result["result"] == "OK") {
        $values["success"] = true;
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_IDProtectToggle($params) {
    $dna = getDNAApi($params);

    $values=[];

    if($params["protectenable"]) {
        // Process request
        $result = $dna->ModifyPrivacyProtectionStatus($params["sld"].".".$params["tld"], true, "Owner\'s request");
    } else {
        // Process request
        $result = $dna->ModifyPrivacyProtectionStatus($params["sld"].".".$params["tld"], false, "Owner\'s request");
    }

    if ($result["result"] == "OK") {
        $values = ["success" => true];
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );
    return $values;
}

function domainnameapi_GetDNS($params)
{
    $values["error"] = "DNS Management does not supported by Domain Name API.";

    return $values;
}

function domainnameapi_SaveDNS($params)
{
    $values["error"] = "DNS Management does not supported by Domain Name API!!!";

    return $values;
}

function domainnameapi_CheckAvailability($params) {
    $dna = getDNAApi($params);

    $values=[];

    if($params['isIdnDomain']){
        $label = empty($params['punyCodeSearchTerm']) ? strtolower($params['searchTerm']) : strtolower($params['punyCodeSearchTerm']);
    }else{
        $label = strtolower($params['searchTerm']);
    }

    $tldslist = $params['tldsToInclude'];
    $premiumEnabled = (bool) $params['premiumEnabled'];
    $domainslist = [];
    $results = new \WHMCS\Domains\DomainLookup\ResultsList();

    $all_tlds = [];
    foreach ($tldslist as $k => $v) {
        $all_tlds[]=ltrim($v,'.');
    }


    //$tld=str_replace(".","",$domain['tld']);
    $result = $dna->CheckAvailability([$label],$all_tlds,"1","create");

    $exchange_rates = domainnameapi_exchangerates();


    foreach ($result as $k => $v) {
        $searchResult = new SearchResult($label, '.'.$v['TLD']);

        $register_price = $v['Price'];
        $renew_price = $v['Price'];

        if(strpos($v['TLD'],'.tr' ) !== false){
            $register_price = $register_price / $exchange_rates['TRY'];
            $renew_price = $renew_price / $exchange_rates['TRY'];
        }

        if ($v['Status'] == 'available') {

            $status = SearchResult::STATUS_NOT_REGISTERED;
            $searchResult->setStatus($status);

            if ($v['IsFee'] == '1') {
                $searchResult->setPremiumDomain(true);
                $searchResult->setPremiumCostPricing([
                        'register'     => $register_price,
                        'renew'        => $renew_price,
                        'CurrencyCode' => 'USD',
                    ]);
            }

        }else{
            $status = SearchResult::STATUS_REGISTERED;
            $searchResult->setStatus($status);
        }
      $results->append($searchResult);
    }

    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );


    return $results;
}

function domainnameapi_GetDomainSuggestions($params) {
    $dna = getDNAApi($params);

    $values=[];


    if($params['isIdnDomain']){
        $label = empty($params['punyCodeSearchTerm']) ? strtolower($params['searchTerm']) : strtolower($params['punyCodeSearchTerm']);
    }else{
        $label = strtolower($params['searchTerm']);
    }

    $tldslist = $params['tldsToInclude'];
    $premiumEnabled = (bool) $params['premiumEnabled'];
    $domainslist = [];
    $results = new \WHMCS\Domains\DomainLookup\ResultsList();

    $result=null;

    $all_tlds = [];
    foreach ($tldslist as $k => $v) {
        $all_tlds[]=ltrim($v,'.');
    }


    //$tld=str_replace(".","",$domain['tld']);
    $result = $dna->CheckAvailability([$label],$all_tlds,"1","create");

    $exchange_rates = domainnameapi_exchangerates();


    foreach ($result as $k => $v) {
        $searchResult = new SearchResult($label, '.'.$v['TLD']);

        $register_price = $v['Price'];
        $renew_price = $v['Price'];

        if(strpos($v['TLD'],'.tr' ) !== false){
            $register_price = $register_price / $exchange_rates['TRY'];
            $renew_price = $renew_price / $exchange_rates['TRY'];
        }



        if ($v['Status'] == 'available') {

            $status = SearchResult::STATUS_NOT_REGISTERED;
            $searchResult->setStatus($status);

            if ($v['IsFee'] == '1') {
                $searchResult->setPremiumDomain(true);
                $searchResult->setPremiumCostPricing([
                        'register'     => $register_price,
                        'renew'        => $renew_price,
                        'CurrencyCode' => 'USD',
                    ]);
            }

        }else{
            $status = SearchResult::STATUS_REGISTERED;
            $searchResult->setStatus($status);
        }
      $results->append($searchResult);
    }

    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );


    return $results;
}

function domainnameapi_GetTldPricing($params) {
    // Perform API call to retrieve extension information
    // A connection error should return a simple array with error key and message
    // return ['error' => 'This error occurred',];

    $dna = getDNAApi($params);

    $values = [];

    $tldlist = $dna->GetTldList(1200);

    $convertable_currencies = domainnameapi_exchangerates();

    $results = new ResultsList;

    if ($tldlist['result'] == 'OK') {
        foreach ($tldlist['data'] as $extension) {
            if(strlen($extension['tld'])>1){

                $price_registration = $extension['pricing']['registration'][1];
                $price_renew        = $extension['pricing']['renew'][1];
                $price_transfer     = $extension['pricing']['transfer'][1];
                $current_currency   = $extension['currencies']['registration'];

                if($current_currency=='TL'){
                    $current_currency='TRY';
                }


                if (in_array($params["basecurrency"],array_keys($convertable_currencies) )) {

                    $exchange_rate     = $convertable_currencies[$params["basecurrency"]];
                    $exchange_rate_rev = $convertable_currencies['TRY'];

                    if ($current_currency == 'USD') {
                        $exchange_rate_rev = 1;
                    }

                    $price_registration = $price_registration * $exchange_rate / $exchange_rate_rev;
                    $price_renew        = $price_renew * $exchange_rate / $exchange_rate_rev;
                    $price_transfer     = $price_transfer * $exchange_rate / $exchange_rate_rev;

                    $current_currency   = $params["basecurrency"];
                }


                $tlds[] = $extension['tld'];

                $item      = (new ImportItem)->setExtension(trim($extension['tld']))
                                             ->setMinYears($extension['minperiod'])
                                             ->setMaxYears($extension['maxperiod'])
                                             ->setRegisterPrice($price_registration)
                                             ->setRenewPrice($price_renew)
                                             ->setTransferPrice($price_transfer)
                                             ->setCurrency($current_currency);

                $results[] = $item;

            }
        }
    }

    return $results;
}

function domainnameapi_Sync($params) {

    $dna = getDNAApi($params);

    $values=[];

    // Process request
    $result = $dna->SyncFromRegistry($params["sld"].".".$params["tld"]);

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    if($result["result"] == "OK") {
        // Process request

        //Active
        //ConfirmationEmailSend
        //WaitingForDocument
        //WaitingForIncomingTransfer
        //WaitingForOutgoingTransfer
        //WaitingForRegistration
        //PendingDelete
        //PreRegistiration
        //PendingHold
        //MigrationPending
        //ModificationPending



        $result2 = $dna->GetDetails($params["sld"].".".$params["tld"]);

        if ($result2["result"] == "OK") {
            $active       ='';
            $expired      ='';
            $expiration   ='';
            $transferaway ='';

            // Check results
            if (preg_match("/\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}/", $result2["data"]["Dates"]["Expiration"])) {
                $expiration = substr($result2["data"]["Dates"]["Expiration"], 0, 10);
            }
            if ($result2["data"]["Status"] == "Active") {
                $active  = true;
                $expired = false;
                $transferaway=false;
            }
            if (in_array($result2["data"]["Status"],['PendingDelete','Deleted'])) {
                $expired = true;
                $active  = false;
                $transferaway=false;
            }
            if ($result2["data"]["Status"] == "TransferredOut") {
                $expired = true;
                $active  = false;
                $transferaway=true;
            }


            // If result is valid set it to WHMCS
            if (is_bool($active) && is_bool($expired)&& trim($expiration) != ""&&is_bool($transferaway) ) {
                $values["active"]     = $active;
                $values["expired"]    = $expired;
                $values["expirydate"] = $expiration;
                //$values["success"] = true;
            } else {
                $values["error"] = "Unexpected result returned from registrar" . "\nActive: " . $active . "\nExpired: " . $expired . "\nExpiryDate: " . $expiration;
            }

        } else {
            $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
        }

    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        "GetDetails_FROM_".substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_TransferSync($params) {

    $dna = getDNAApi($params);

    $values=[];

    // Process request
    $result = $dna->SyncFromRegistry($params["sld"].".".$params["tld"]);

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    if($result["result"] == "OK") {

        $result2 = $dna->GetDetails($params["sld"].".".$params["tld"]);

        if ($result2["result"] == "OK") {

            // Check results
            if (preg_match("/\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}/", $result2["data"]["Dates"]["Expiration"])) {
                $values['expirydate'] = date('Y-m-d',strtotime($result2["data"]["Dates"]["Expiration"]));
            }
            if ($result2["data"]["Status"] == "Active") {
                $values['completed']=true;
                $values['failed']=false;
            }
            if (in_array($result2["data"]["Status"],['PendingDelete','Deleted'])) {
                $expired = true;
                $active  = false;
                $transferaway=false;
            }
            if ($result2["data"]["Status"] == "TransferCancelledFromClient") {
                $values['completed']=true;
                $values['failed']=false;
                $values['reason']='Transfer Cancelled From Client';
            }

        } else {
            $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
        }

    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }


    // Log request
    logModuleCall("domainnameapi",
        "GetDetails_FROM_".substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;
}

function domainnameapi_AdminCustomButtonArray() {
    return [
        "Cancel Transfer" => "canceltransfer",
    ];
}

function domainnameapi_canceltransfer($params) {

    $dna = getDNAApi($params);

    $values=[];

    $result = $dna->CancelTransfer($params["sld"] . "." . $params["tld"]);

    if ($result["result"] == "OK") {
        $values["message"] = "Successfully cancelled the domain transfer";
    } else {
        $values["error"] = $result["error"]["Message"] . " - " . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $values
    );

    return $values;

}

function domainnameapi_AdminDomainsTabFields($params){

    $regs = Illuminate\Database\Capsule\Manager::table('tblregistrars')->where('registrar', 'domainnameapi')->get();

    foreach ($regs as $k => $v) {
        $results = localAPI('DecryptPassword', ['password2'=>$v->value]);
        $params[$v->setting] = $results['password'];
    }

    $dna = getDNAApi($params);


    // Process request
    $result = $dna->GetDetails($params["sld"].".".$params["tld"]);

    $addionals = $nameservers='';

    foreach ($result['data']['Additional'] as $k => $v) {
        $addionals.= $k.' : '.$v.'<br>';
    }
    foreach ($result['data']['ChildNameServers'] as $k => $v) {
        $nameservers.= '['.$v['ip'].'] : '.$v['ns'].'<br>';
    }

    logModuleCall("domainnameapi",
        substr(__FUNCTION__, 14),
        $dna->getRequestData(),
        $dna->getResponseData(),
        $result
    );

    return [
        'Current State'      => $result['data']['Status'],
        'Start'              => $result['data']['Dates']['Start'],
        'Expiring'           => $result['data']['Dates']['Expiration'],
        'Remaining Days'     => $result['data']['Dates']['RemainingDays'],
        'Child Nameservers'  => $nameservers,
        'AddionalParameters' => $addionals,
    ];

}

function domainnameapi_ClientAreaCustomButtonArray($params)
{
    global $_LANG;
    return [
        $_LANG['dna']['childhostmanagement'] => "childhosts",
    ];
}

function domainnameapi_ClientArea($params)
{
}

function domainnameapi_ChildHosts($params)
{
    global $_LANG;
    $dna = getDNAApi($params);

    $process_success = $process_error = false;

    if(isset($_POST['subaction'])) {

        $dom = $params['domainname'];
        $cns = $_POST['nameserver'].'.'.$params['domainname'];

        if ($_POST['subaction'] == 'addchildhost') {
            $result = $dna->AddChildNameServer($dom, $cns, $_POST['ipaddress']);

            if ($result["result"] == "OK") {
                $process_success = true;
            } else {
                $process_error = $result["error"]["Message"] . " - " . $result["error"]["Details"];
            }
        }
        if ($_POST['subaction'] == 'deletechildhost') {
            $result = $dna->DeleteChildNameServer($dom, $cns);
            if ($result["result"] == "OK") {
                $process_success = true;
            } else {
                $process_error = $result["error"]["Message"] . " - " . $result["error"]["Details"];
            }
        }
        if ($_POST['subaction'] == 'modifychildhost') {
            $result = $dna->ModifyChildNameServer($dom,$cns, $_POST['newipaddress']);
            if ($result["result"] == "OK") {
                $process_success = true;
            } else {
                $process_error = $result["error"]["Message"] . " - " . $result["error"]["Details"];
            }
        }
    }

    $result = $dna->GetDetails($params['domainname']);

    $domain      = $result['data']['DomainName'];
    $nameservers = [];
    foreach ($result['data']['ChildNameServers'] as $k => $v) {
        $v['ns'] = str_replace('.'.$params['domainname'],'',$v['ns']);
        $nameservers[] = ['name' => $v['ns'], 'ip' => $v['ip']];
    }

    return [
        'templatefile' => 'childns',
        'breadcrumb'   => [
            'clientarea.php?action=domaindetails&id=' . $params['domainid'] . '&modop=custom&a=ChildHosts' => $_LANG['dna']['childhostmanagement']
        ],
        'vars'         => [
            'nameservers'     => $nameservers,
            'domain'          => $domain,
            'process_success' => $process_success,
            'process_error'   => $process_error,
        ],
    ];
}

/**
 * Gets DNA API instance
 * @param array $params
 * @return \DomainNameApi\DomainNameAPI_PHPLibrary
 */
function getDNAApi($params){
    require_once __DIR__.'/lib/dna.php';

    $username = $params["API_UserName"];
    $password = $params["API_Password"];
    $testmode = $params["API_TestMode"];

    return new \DomainNameApi\DomainNameAPI_PHPLibrary($username,$password);
}

function domainnameapi_parse_contact($params) {

    return [
        "First Name"         => $params["FirstName"],
        "Last Name"          => $params["LastName"],
        "Company Name"       => $params["Company"],
        "Email"              => $params["EMail"],
        "Phone Country Code" => $params["Phone"]["Phone"]["CountryCode"],
        "Phone"              => $params["Phone"]["Phone"]["Number"],
        "Fax Country Code"   => $params["Phone"]["Fax"]["CountryCode"],
        "Fax"                => $params["Phone"]["Fax"]["Number"],
        "Address 1"          => $params["Address"]["Line1"],
        "Address 2"          => $params["Address"]["Line2"],
        "Address 3"          => $params["Address"]["Line3"],
        "State"              => $params["Address"]["State"],
        "City"               => $params["Address"]["City"],
        "Country"            => $params["Address"]["Country"],
        "ZIP Code"           => $params["Address"]["ZipCode"],
    ];

}

function domainnameapi_parse_clientinfo($params) {


    $firstname   = $params["First Name"] ?? $params["firstname"];
    $lastname    = $params["Last Name"] ?? $params["lastname"];
    $compantname = $params["Company Name"] ?? $params["companyname"];
    $email       = $params["Email"] ?? $params["email"];
    $address1    = $params["Address 1"] ?? $params["address1"];
    $address2    = $params["Address 2"] ?? $params["address2"];
    $city        = $params["City"] ?? $params["city"];
    $country     = $params["Country"] ?? $params["countrycode"];
    $fax         = $params["Fax"] ?? $params["phonenumber"];
    $faxcc       = $params["Fax Country Code"] ?? $params["phonecc"];
    $phonecc     = $params["Phone Country Code"] ?? $params["phonecc"];
    $phone       = $params["Phone"] ?? $params["phonenumber"];
    $postcode    = $params["ZIP Code"] ?? $params["postcode"];
    $state       = $params["State"] ?? $params["state"];



    $arr_client= [
        "FirstName"        => mb_convert_encoding($firstname, "UTF-8", "auto"),
        "LastName"         => mb_convert_encoding($lastname, "UTF-8", "auto"),
        "Company"          => mb_convert_encoding($compantname, "UTF-8", "auto"),
        "EMail"            => mb_convert_encoding($email, "UTF-8", "auto"),
        "AddressLine1"     => mb_convert_encoding($address1, "UTF-8", "auto"),
        "AddressLine2"     => mb_convert_encoding($address2, "UTF-8", "auto"),
        "State"            => mb_convert_encoding($state, "UTF-8", "auto"),
        "City"             => mb_convert_encoding($city, "UTF-8", "auto"),
        "Country"          => mb_convert_encoding($country, "UTF-8", "auto"),
        "Fax"              => mb_convert_encoding($fax, "UTF-8", "auto"),
        "FaxCountryCode"   => mb_convert_encoding($faxcc, "UTF-8", "auto"),
        "Phone"            => mb_convert_encoding($phone, "UTF-8", "auto"),
        "PhoneCountryCode" => mb_convert_encoding($phonecc, "UTF-8", "auto"),
        "Type"             => mb_convert_encoding("Contact", "UTF-8", "auto"),
        "ZipCode"          => mb_convert_encoding($postcode, "UTF-8", "auto"),
        "Status"           => mb_convert_encoding("", "UTF-8", "auto")
    ];

    foreach (['Fax','Phone'] as $k => $v) {
        //if phone number contains . remove before .
        if (strpos($arr_client[$v], '.') !== false) {
            $arr_client[$v] = substr($arr_client[$v],strpos($arr_client[$v], '.') + 1);
        }
    }


    if(isset($params['FirstName'])){
        unset($arr_client['status']);
    }

    return $arr_client;
}

function domainnameapi_parse_trcontact($contactDetails) {
    $cf = [];
    foreach ($contactDetails['customfields'] as $k => $v) {
        $cf[$v['id']] = $v['value'];
    }

    $tr_domain_fields = [
        'TRABISDOMAINCATEGORY' => strlen($contactDetails['companyname']) > 0 ? '0' : '1',
        'TRABISNAMESURNAME'    => $contactDetails['firstname'] . ' ' . $contactDetails['lastname'],
        'TRABISCOUNTRYID'      => 215,
        'TRABISCITYID'        => 34,
        'TRABISCOUNTRYNAME'    => $contactDetails['countrycode'],
        'TRABISCITYNAME'       => $contactDetails['city'],
    ];

    $tr_domain_fields['TRABISORGANIZATION'] = $contactDetails['companyname'];
    $tr_domain_fields['TRABISTAXOFFICE']    = is_numeric($contactDetails['TrTaxOffice']) ? $cf[$contactDetails['TrTaxOffice']] : 'Kadikoy V.D.';
    $tr_domain_fields['TRABISTAXNUMBER']    = is_numeric($contactDetails['TrTaxNumber']) ? $cf[$contactDetails['TrTaxNumber']] : '1111111111';
    $tr_domain_fields['TRABISCITIZIENID']   = is_numeric($contactDetails['TrIdendity']) ? $cf[$contactDetails['TrIdendity']] : '11111111111';

    if (strlen($contactDetails['companyname'])<1 ) {
        unset($tr_domain_fields['TRABISORGANIZATION']);
        unset($tr_domain_fields['TRABISTAXOFFICE']);
        unset($tr_domain_fields['TRABISTAXNUMBER']);
    } else {
        unset($tr_domain_fields['TRABISNAMESURNAME']);
        unset($tr_domain_fields['TRABISCITIZIENID']);
    }

    return $tr_domain_fields;
}

function domainnameapi_parse_cache($key,$ttl,$callback){

    //Long usernames can cause issues with the tblconfiguration table setting column
    $cache_key = "domainnameapi_".md5($key); //Exact 46 character

    $token_row = Capsule::table('tblconfiguration')
                        ->where('setting', $cache_key)
                        ->first();

    //if module newly installed, create token row
    //token row could be object pattern or empty, not false
    if (!isset($token_row->setting)) {
        try {
            Capsule::table('tblconfiguration')
                   ->insert([
                       'setting' => $cache_key,
                       'value'   => ''
                   ]);
        } catch (\Exception $e) {
            //throw new Exception('Error: Record enumaration failed.');
        }
    }

    if (strtotime($token_row->updated_at) < (time() - 600)) {

        $data = $callback();

        Capsule::table('tblconfiguration')
               ->where('setting', $cache_key)
               ->update([
                   'value'      => serialize($data),
                   'updated_at' => date('Y-m-d H:i:s', strtotime("+{$ttl} seconds"))
               ]);

        return $data;

    }else{
        return unserialize($token_row->value);
    }

}

function domainnameapi_exchangerates() {
    $rates = [];

    $rates = domainnameapi_parse_cache('currency_data', 1800, function () {

        $url = 'https://open.er-api.com/v6/latest/USD';
        $ch  = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $json = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($json);

        if (!isset($data->rates)) {
            throw new Exception('Error: Exchange service is not available . Please Wait few minutes and try again. ');
        }

        return $data->rates;
    });


    $rates = json_decode(json_encode($rates), true);


    return $rates;
}

