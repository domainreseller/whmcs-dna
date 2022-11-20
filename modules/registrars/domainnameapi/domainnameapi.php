<?php



use WHMCS\Domains\DomainLookup\ResultsList;
use WHMCS\Domains\DomainLookup\SearchResult;
use WHMCS\Module\Registrar\Registrarmodule\ApiClient;
use WHMCS\Database\Capsule;





function api($UserName = "ownername", $Password = "ownerpass", $TestMode = "on")
{
    if(isset($_SERVER["api"]) && 1==1) { }
    else
    {
        // Create an instance of API
        $_SERVER["api"] = new DomainNameAPI_PHPLibrary();
    }

    // Set test mode
    if($TestMode == "on")
    { $_SERVER["api"]->useTestMode(true); }
    else
    { $_SERVER["api"]->useTestMode(false); }

    // Set Username and password
    $_SERVER["api"]->setUser($UserName, $Password);

    // Enable domain details caching
    $_SERVER["api"]->useCaching(true);

    return $_SERVER["api"];
}
















// OK
function domainnameapi_getConfigArray()
{
    if(class_exists("SoapClient"))
    {
        $configarray = array(
            "FriendlyName" => array("Type" => "System", "Value" => "Domain Name API - ICANN Accredited Domain Registrar from TURKEY"),
            "Description" => array("Type" => "System", "Value" => "Don't have an Domain Name API account yet? Get one here: <a href='https://www.domainnameapi.com/become-a-reseller' target='_blank'>https://www.domainnameapi.com/become-a-reseller</a>"),

            "API_UserName" => array("FriendlyName" => "UserName", "Type" => "text", "Size" => "20", "Default" => "ownername"),
            "API_Password" => array("FriendlyName" => "Password", "Type" => "password", "Size" => "20", "Default" => "ownerpass"),
            "API_TestMode" => array("FriendlyName" => "Test Mode", "Type" => "yesno", "Default" => "yes", "Description" => "Check for using test platform!")
        );
    }
    else
    {
        return array(
            "FriendlyName" => array("Type" => "System", "Value" => "Domain Name API - ICANN Accredited Domain Registrar from TURKEY"),
            "Description" => array("Type" => "System", "Value" => "<span style='color:red'>Your server does not support SOAPClient. Please install and activate it. <a href='http://php.net/manual/en/class.soapclient.php' target='_blank'>Detailed informations</a></span>"),
        );
    }

    return $configarray;
}



// OK
function domainnameapi_GetNameservers($params)
{
    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->GetDetails($params["sld"].".".$params["tld"]);

    if($result["result"] == "OK")
    {
        if(is_array($result["data"]["NameServers"][0]))
        {
            // Multiple nameserver?
            if(isset($result["data"]["NameServers"][0][0])) { $values["ns1"] = $result["data"]["NameServers"][0][0]; }
            if(isset($result["data"]["NameServers"][0][1])) { $values["ns2"] = $result["data"]["NameServers"][0][1]; }
            if(isset($result["data"]["NameServers"][0][2])) { $values["ns3"] = $result["data"]["NameServers"][0][2]; }
            if(isset($result["data"]["NameServers"][0][3])) { $values["ns4"] = $result["data"]["NameServers"][0][3]; }
            if(isset($result["data"]["NameServers"][0][4])) { $values["ns5"] = $result["data"]["NameServers"][0][4]; }
        }
        else
        {
            // Only one nameserver
            if(isset($result["data"]["NameServers"][0])) { $values["ns1"] = $result["data"]["NameServers"][0]; }
        }
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}



// OK
function domainnameapi_SaveNameservers($params) {

    $nsList = array();

    if(isset($params["ns1"]) && !is_null($params["ns1"]) && is_string($params["ns1"]) && strlen(trim($params["ns1"])) > 0) { array_push($nsList, $params["ns1"]); };

    if(isset($params["ns2"]) && !is_null($params["ns2"]) && is_string($params["ns2"]) && strlen(trim($params["ns2"])) > 0) { array_push($nsList, $params["ns2"]); };

    if(isset($params["ns3"]) && !is_null($params["ns3"]) && is_string($params["ns3"]) && strlen(trim($params["ns3"])) > 0) { array_push($nsList, $params["ns3"]); };

    if(isset($params["ns4"]) && !is_null($params["ns4"]) && is_string($params["ns4"]) && strlen(trim($params["ns4"])) > 0) { array_push($nsList, $params["ns4"]); };

    if(isset($params["ns5"]) && !is_null($params["ns5"]) && is_string($params["ns5"]) && strlen(trim($params["ns5"])) > 0) { array_push($nsList, $params["ns5"]); };

    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->ModifyNameserver($params["sld"].".".$params["tld"], $nsList);

    if($result["result"] == "OK")
    {
        if(isset($result["data"]["NameServers"][0])) { $values["ns1"] = $result["data"]["NameServers"][0]; }
        if(isset($result["data"]["NameServers"][1])) { $values["ns2"] = $result["data"]["NameServers"][1]; }
        if(isset($result["data"]["NameServers"][2])) { $values["ns3"] = $result["data"]["NameServers"][2]; }
        if(isset($result["data"]["NameServers"][3])) { $values["ns4"] = $result["data"]["NameServers"][3]; }
        if(isset($result["data"]["NameServers"][4])) { $values["ns5"] = $result["data"]["NameServers"][4]; }
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}



// OK
function domainnameapi_GetRegistrarLock($params) {


    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->GetDetails($params["sld"].".".$params["tld"]);

    if($result["result"] == "OK")
    {
        if(isset($result["data"]["LockStatus"]))
        {
            if($result["data"]["LockStatus"] == "true")
            { $values = "locked"; }
            else
            {  $values = "unlocked"; }

        }
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}



// OK
function domainnameapi_SaveRegistrarLock($params) {

    // Get current lock status from registrar, Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->GetDetails($params["sld"].".".$params["tld"]);

    // Log request
    logModuleCall("domainnameapi", "GetDetails_FROM_" . substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => null, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    if($result["result"] == "OK")
    {
        if(isset($result["data"]["LockStatus"]))
        {
            if($result["data"]["LockStatus"] == "true")
            { $kilit = "locked"; }
            else
            {  $kilit = "unlocked"; }

            if($kilit == "unlocked")
            {
                // Process request
                $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->EnableTheftProtectionLock($params["sld"].".".$params["tld"]);
            }
            else
            {
                // Process request
                $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->DisableTheftProtectionLock($params["sld"].".".$params["tld"]);
            }

            if($result["result"] == "OK")
            {
                $values = array("success" => true);
            }
            else
            {
                $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
            }

        }
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}



function domainnameapi_RegisterDomain($params) {

    $nameServers = array();
    $period = 1;
    $privacyProtection = false;

    // Set nameservers
    if(isset($params["ns1"]) && trim($params["ns1"]) != "") { array_push($nameServers, $params["ns1"]); }
    if(isset($params["ns2"]) && trim($params["ns2"]) != "") { array_push($nameServers, $params["ns2"]); }
    if(isset($params["ns3"]) && trim($params["ns3"]) != "") { array_push($nameServers, $params["ns3"]); }
    if(isset($params["ns4"]) && trim($params["ns4"]) != "") { array_push($nameServers, $params["ns4"]); }
    if(isset($params["ns5"]) && trim($params["ns5"]) != "") { array_push($nameServers, $params["ns5"]); }

    // Set period
    if(isset($params["regperiod"]) && is_numeric($params["regperiod"])) { $period = intval($params["regperiod"]); }
    if(isset($params["idprotection"]) && ($params["idprotection"] == true || trim($params["idprotection"]) == "1")) { $privacyProtection = true; }

    // Register Domain
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->RegisterWithContactInfo(

    // Domain name
        $params["sld"] . "." . $params["tld"],

        // Years
        $period,

        // Contact informations
        array(
            // Administrative contact
            "Administrative" => array(
                "FirstName" => mb_convert_encoding($params["firstname"], "UTF-8", "auto"),
                "LastName" => mb_convert_encoding($params["lastname"], "UTF-8", "auto"),
                "Company" => mb_convert_encoding($params["companyname"], "UTF-8", "auto"),
                "EMail" => mb_convert_encoding($params["email"], "UTF-8", "auto"),
                "AddressLine1" => mb_convert_encoding($params["address1"], "UTF-8", "auto"),
                "AddressLine2" => mb_convert_encoding($params["address2"], "UTF-8", "auto"),
                "State" => mb_convert_encoding($params["state"], "UTF-8", "auto"),
                "City" => mb_convert_encoding($params["city"], "UTF-8", "auto"),
                "Country" => mb_convert_encoding($params["countrycode"], "UTF-8", "auto"),
                "Fax" => mb_convert_encoding($params["phonenumber"], "UTF-8", "auto"),
                "FaxCountryCode" => mb_convert_encoding($params["phonecc"], "UTF-8", "auto"),
                "Phone" => mb_convert_encoding($params["phonenumber"], "UTF-8", "auto"),
                "PhoneCountryCode" => mb_convert_encoding($params["phonecc"], "UTF-8", "auto"),
                "Type" => mb_convert_encoding("Contact", "UTF-8", "auto"),
                "ZipCode" => mb_convert_encoding($params["postcode"], "UTF-8", "auto"),
                "Status" => mb_convert_encoding("", "UTF-8", "auto")
            ),

            // Billing contact
            "Billing" => array(
                "FirstName" => mb_convert_encoding($params["firstname"], "UTF-8", "auto"),
                "LastName" => mb_convert_encoding($params["lastname"], "UTF-8", "auto"),
                "Company" => mb_convert_encoding($params["companyname"], "UTF-8", "auto"),
                "EMail" => mb_convert_encoding($params["email"], "UTF-8", "auto"),
                "AddressLine1" => mb_convert_encoding($params["address1"], "UTF-8", "auto"),
                "AddressLine2" => mb_convert_encoding($params["address2"], "UTF-8", "auto"),
                "State" => mb_convert_encoding($params["state"], "UTF-8", "auto"),
                "City" => mb_convert_encoding($params["city"], "UTF-8", "auto"),
                "Country" => mb_convert_encoding($params["countrycode"], "UTF-8", "auto"),
                "Fax" => mb_convert_encoding($params["phonenumber"], "UTF-8", "auto"),
                "FaxCountryCode" => mb_convert_encoding($params["phonecc"], "UTF-8", "auto"),
                "Phone" => mb_convert_encoding($params["phonenumber"], "UTF-8", "auto"),
                "PhoneCountryCode" => mb_convert_encoding($params["phonecc"], "UTF-8", "auto"),
                "Type" => mb_convert_encoding("Contact", "UTF-8", "auto"),
                "ZipCode" => mb_convert_encoding($params["postcode"], "UTF-8", "auto"),
                "Status" => mb_convert_encoding("", "UTF-8", "auto")
            ),

            // Technical contact
            "Technical" => array(
                "FirstName" => mb_convert_encoding($params["firstname"], "UTF-8", "auto"),
                "LastName" => mb_convert_encoding($params["lastname"], "UTF-8", "auto"),
                "Company" => mb_convert_encoding($params["companyname"], "UTF-8", "auto"),
                "EMail" => mb_convert_encoding($params["email"], "UTF-8", "auto"),
                "AddressLine1" => mb_convert_encoding($params["address1"], "UTF-8", "auto"),
                "AddressLine2" => mb_convert_encoding($params["address2"], "UTF-8", "auto"),
                "State" => mb_convert_encoding($params["state"], "UTF-8", "auto"),
                "City" => mb_convert_encoding($params["city"], "UTF-8", "auto"),
                "Country" => mb_convert_encoding($params["countrycode"], "UTF-8", "auto"),
                "Fax" => mb_convert_encoding($params["phonenumber"], "UTF-8", "auto"),
                "FaxCountryCode" => mb_convert_encoding($params["phonecc"], "UTF-8", "auto"),
                "Phone" => mb_convert_encoding($params["phonenumber"], "UTF-8", "auto"),
                "PhoneCountryCode" => mb_convert_encoding($params["phonecc"], "UTF-8", "auto"),
                "Type" => mb_convert_encoding("Contact", "UTF-8", "auto"),
                "ZipCode" => mb_convert_encoding($params["postcode"], "UTF-8", "auto"),
                "Status" => mb_convert_encoding("", "UTF-8", "auto")
            ),

            // Registrant contact
            "Registrant" => array(
                "FirstName" => mb_convert_encoding($params["firstname"], "UTF-8", "auto"),
                "LastName" => mb_convert_encoding($params["lastname"], "UTF-8", "auto"),
                "Company" => mb_convert_encoding($params["companyname"], "UTF-8", "auto"),
                "EMail" => mb_convert_encoding($params["email"], "UTF-8", "auto"),
                "AddressLine1" => mb_convert_encoding($params["address1"], "UTF-8", "auto"),
                "AddressLine2" => mb_convert_encoding($params["address2"], "UTF-8", "auto"),
                "State" => mb_convert_encoding($params["state"], "UTF-8", "auto"),
                "City" => mb_convert_encoding($params["city"], "UTF-8", "auto"),
                "Country" => mb_convert_encoding($params["countrycode"], "UTF-8", "auto"),
                "Fax" => mb_convert_encoding($params["phonenumber"], "UTF-8", "auto"),
                "FaxCountryCode" => mb_convert_encoding($params["phonecc"], "UTF-8", "auto"),
                "Phone" => mb_convert_encoding($params["phonenumber"], "UTF-8", "auto"),
                "PhoneCountryCode" => mb_convert_encoding($params["phonecc"], "UTF-8", "auto"),
                "Type" => mb_convert_encoding("Contact", "UTF-8", "auto"),
                "ZipCode" => mb_convert_encoding($params["postcode"], "UTF-8", "auto"),
                "Status" => mb_convert_encoding("", "UTF-8", "auto")
            ),

        ),

        // Nameservers
        $nameServers,

        // Theft protection lock enabled
        false,

        // Privacy Protection enabled
        $privacyProtection

    );

    if($result["result"] == "OK")
    {
        $values = array("success" => true);
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}

// OK
function domainnameapi_TransferDomain($params) {

    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->Transfer($params["sld"].".".$params["tld"], $params["transfersecret"]);

    if($result["result"] == "OK")
    {
        $values = array("success" => true);
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}

// OK
function domainnameapi_RenewDomain($params) {

    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->Renew($params["sld"].".".$params["tld"], $params["regperiod"]);

    if($result["result"] == "OK")
    {
        $values = array("success" => true);
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}

// OK
function domainnameapi_GetContactDetails($params) {

    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->GetContacts($params["sld"].".".$params["tld"]);

    if($result["result"] == "OK")
    {
        $values = array();

        $values["Registrant Contact"]["First Name"] = $result["data"]["contacts"]["Registrant"]["FirstName"];
        $values["Registrant Contact"]["Last Name"] = $result["data"]["contacts"]["Registrant"]["LastName"];
        $values["Registrant Contact"]["Company Name"] = $result["data"]["contacts"]["Registrant"]["Company"];
        $values["Registrant Contact"]["Email"] = $result["data"]["contacts"]["Registrant"]["EMail"];
        $values["Registrant Contact"]["Phone Country Code"] = $result["data"]["contacts"]["Registrant"]["Phone"]["Phone"]["CountryCode"];
        $values["Registrant Contact"]["Phone"] = $result["data"]["contacts"]["Registrant"]["Phone"]["Phone"]["Number"];
        $values["Registrant Contact"]["Fax Country Code"] = $result["data"]["contacts"]["Registrant"]["Phone"]["Fax"]["CountryCode"];
        $values["Registrant Contact"]["Fax"] = $result["data"]["contacts"]["Registrant"]["Phone"]["Fax"]["Number"];
        $values["Registrant Contact"]["Address 1"] = $result["data"]["contacts"]["Registrant"]["Address"]["Line1"];
        $values["Registrant Contact"]["Address 2"] = $result["data"]["contacts"]["Registrant"]["Address"]["Line2"];
        $values["Registrant Contact"]["Address 3"] = $result["data"]["contacts"]["Registrant"]["Address"]["Line3"];
        $values["Registrant Contact"]["State"] = $result["data"]["contacts"]["Registrant"]["Address"]["State"];
        $values["Registrant Contact"]["City"] = $result["data"]["contacts"]["Registrant"]["Address"]["City"];
        $values["Registrant Contact"]["Country"] = $result["data"]["contacts"]["Registrant"]["Address"]["Country"];
        $values["Registrant Contact"]["ZIP Code"] = $result["data"]["contacts"]["Registrant"]["Address"]["ZipCode"];

        $values["Administrative Contact"]["First Name"] = $result["data"]["contacts"]["Administrative"]["FirstName"];
        $values["Administrative Contact"]["Last Name"] = $result["data"]["contacts"]["Administrative"]["LastName"];
        $values["Administrative Contact"]["Company Name"] = $result["data"]["contacts"]["Administrative"]["Company"];
        $values["Administrative Contact"]["Email"] = $result["data"]["contacts"]["Administrative"]["EMail"];
        $values["Administrative Contact"]["Phone Country Code"] = $result["data"]["contacts"]["Administrative"]["Phone"]["Phone"]["CountryCode"];
        $values["Administrative Contact"]["Phone"] = $result["data"]["contacts"]["Administrative"]["Phone"]["Phone"]["Number"];
        $values["Administrative Contact"]["Fax Country Code"] = $result["data"]["contacts"]["Administrative"]["Phone"]["Fax"]["CountryCode"];
        $values["Administrative Contact"]["Fax"] = $result["data"]["contacts"]["Administrative"]["Phone"]["Fax"]["Number"];
        $values["Administrative Contact"]["Address 1"] = $result["data"]["contacts"]["Administrative"]["Address"]["Line1"];
        $values["Administrative Contact"]["Address 2"] = $result["data"]["contacts"]["Administrative"]["Address"]["Line2"];
        $values["Administrative Contact"]["Address 3"] = $result["data"]["contacts"]["Administrative"]["Address"]["Line3"];
        $values["Administrative Contact"]["State"] = $result["data"]["contacts"]["Administrative"]["Address"]["State"];
        $values["Administrative Contact"]["City"] = $result["data"]["contacts"]["Administrative"]["Address"]["City"];
        $values["Administrative Contact"]["Country"] = $result["data"]["contacts"]["Administrative"]["Address"]["Country"];
        $values["Administrative Contact"]["ZIP Code"] = $result["data"]["contacts"]["Administrative"]["Address"]["ZipCode"];

        $values["Billing Contact"]["First Name"] = $result["data"]["contacts"]["Billing"]["FirstName"];
        $values["Billing Contact"]["Last Name"] = $result["data"]["contacts"]["Billing"]["LastName"];
        $values["Billing Contact"]["Company Name"] = $result["data"]["contacts"]["Billing"]["Company"];
        $values["Billing Contact"]["Email"] = $result["data"]["contacts"]["Billing"]["EMail"];
        $values["Billing Contact"]["Phone Country Code"] = $result["data"]["contacts"]["Billing"]["Phone"]["Phone"]["CountryCode"];
        $values["Billing Contact"]["Phone"] = $result["data"]["contacts"]["Billing"]["Phone"]["Phone"]["Number"];
        $values["Billing Contact"]["Fax Country Code"] = $result["data"]["contacts"]["Billing"]["Phone"]["Fax"]["CountryCode"];
        $values["Billing Contact"]["Fax"] = $result["data"]["contacts"]["Billing"]["Phone"]["Fax"]["Number"];
        $values["Billing Contact"]["Address 1"] = $result["data"]["contacts"]["Billing"]["Address"]["Line1"];
        $values["Billing Contact"]["Address 2"] = $result["data"]["contacts"]["Billing"]["Address"]["Line2"];
        $values["Billing Contact"]["Address 3"] = $result["data"]["contacts"]["Billing"]["Address"]["Line3"];
        $values["Billing Contact"]["State"] = $result["data"]["contacts"]["Billing"]["Address"]["State"];
        $values["Billing Contact"]["City"] = $result["data"]["contacts"]["Billing"]["Address"]["City"];
        $values["Billing Contact"]["Country"] = $result["data"]["contacts"]["Billing"]["Address"]["Country"];
        $values["Billing Contact"]["ZIP Code"] = $result["data"]["contacts"]["Billing"]["Address"]["ZipCode"];

        $values["Technical Contact"]["First Name"] = $result["data"]["contacts"]["Technical"]["FirstName"];
        $values["Technical Contact"]["Last Name"] = $result["data"]["contacts"]["Technical"]["LastName"];
        $values["Technical Contact"]["Company Name"] = $result["data"]["contacts"]["Technical"]["Company"];
        $values["Technical Contact"]["Email"] = $result["data"]["contacts"]["Technical"]["EMail"];
        $values["Technical Contact"]["Phone Country Code"] = $result["data"]["contacts"]["Technical"]["Phone"]["Phone"]["CountryCode"];
        $values["Technical Contact"]["Phone"] = $result["data"]["contacts"]["Technical"]["Phone"]["Phone"]["Number"];
        $values["Technical Contact"]["Fax Country Code"] = $result["data"]["contacts"]["Technical"]["Phone"]["Fax"]["CountryCode"];
        $values["Technical Contact"]["Fax"] = $result["data"]["contacts"]["Technical"]["Phone"]["Fax"]["Number"];
        $values["Technical Contact"]["Address 1"] = $result["data"]["contacts"]["Technical"]["Address"]["Line1"];
        $values["Technical Contact"]["Address 2"] = $result["data"]["contacts"]["Technical"]["Address"]["Line2"];
        $values["Technical Contact"]["Address 3"] = $result["data"]["contacts"]["Technical"]["Address"]["Line3"];
        $values["Technical Contact"]["State"] = $result["data"]["contacts"]["Technical"]["Address"]["State"];
        $values["Technical Contact"]["City"] = $result["data"]["contacts"]["Technical"]["Address"]["City"];
        $values["Technical Contact"]["Country"] = $result["data"]["contacts"]["Technical"]["Address"]["Country"];
        $values["Technical Contact"]["ZIP Code"] = $result["data"]["contacts"]["Technical"]["Address"]["ZipCode"];

    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}

// OK
function domainnameapi_SaveContactDetails($params) {

    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->SaveContacts(

    // DOMAIN NAME
        $params["sld"].".".$params["tld"],

        // CONTACTS
        array(

            // Administrative contact
            "Administrative" => array(
                "FirstName" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["First Name"], 'UTF-8', 'auto'),
                "LastName" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Last Name"], "UTF-8", "auto"),
                "Company" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Company Name"], "UTF-8", "auto"),
                "EMail" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Email"], "UTF-8", "auto"),
                "AddressLine1" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Address 1"], "UTF-8", "auto"),
                "AddressLine2" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Address 2"], "UTF-8", "auto"),
                "AddressLine3" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Address 3"], "UTF-8", "auto"),
                "City" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["City"], "UTF-8", "auto"),
                "Country" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Country"], "UTF-8", "auto"),
                "Fax" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Fax"], "UTF-8", "auto"),
                "FaxCountryCode" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Fax Country Code"], "UTF-8", "auto"),
                "Phone" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Phone"], "UTF-8", "auto"),
                "PhoneCountryCode" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["Phone Country Code"], "UTF-8", "auto"),
                "Type" => mb_convert_encoding("Contact", "UTF-8", "auto"),
                "ZipCode" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["ZIP Code"], "UTF-8", "auto"),
                "State" => mb_convert_encoding($params["contactdetails"]["Administrative Contact"]["State"], "UTF-8", "auto")
            ),

            // Billing contact
            "Billing" => array(
                "FirstName" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["First Name"], "UTF-8", "auto"),
                "LastName" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Last Name"], "UTF-8", "auto"),
                "Company" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Company Name"], "UTF-8", "auto"),
                "EMail" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Email"], "UTF-8", "auto"),
                "AddressLine1" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Address 1"], "UTF-8", "auto"),
                "AddressLine2" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Address 2"], "UTF-8", "auto"),
                "AddressLine3" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Address 3"], "UTF-8", "auto"),
                "City" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["City"], "UTF-8", "auto"),
                "Country" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Country"], "UTF-8", "auto"),
                "Fax" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Fax"], "UTF-8", "auto"),
                "FaxCountryCode" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Fax Country Code"], "UTF-8", "auto"),
                "Phone" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Phone"], "UTF-8", "auto"),
                "PhoneCountryCode" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["Phone Country Code"], "UTF-8", "auto"),
                "Type" => mb_convert_encoding("Contact", "UTF-8", "auto"),
                "ZipCode" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["ZIP Code"], "UTF-8", "auto"),
                "State" => mb_convert_encoding($params["contactdetails"]["Billing Contact"]["State"], "UTF-8", "auto")
            ),

            // Technical contact
            "Technical" => array(
                "FirstName" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["First Name"], "UTF-8", "auto"),
                "LastName" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Last Name"], "UTF-8", "auto"),
                "Company" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Company Name"], "UTF-8", "auto"),
                "EMail" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Email"], "UTF-8", "auto"),
                "AddressLine1" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Address 1"], "UTF-8", "auto"),
                "AddressLine2" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Address 2"], "UTF-8", "auto"),
                "AddressLine3" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Address 3"], "UTF-8", "auto"),
                "City" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["City"], "UTF-8", "auto"),
                "Country" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Country"], "UTF-8", "auto"),
                "Fax" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Fax"], "UTF-8", "auto"),
                "FaxCountryCode" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Fax Country Code"], "UTF-8", "auto"),
                "Phone" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Phone"], "UTF-8", "auto"),
                "PhoneCountryCode" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["Phone Country Code"], "UTF-8", "auto"),
                "Type" => mb_convert_encoding("Contact", "UTF-8", "auto"),
                "ZipCode" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["ZIP Code"], "UTF-8", "auto"),
                "State" => mb_convert_encoding($params["contactdetails"]["Technical Contact"]["State"], "UTF-8", "auto")
            ),

            // Registrant contact
            "Registrant" => array(
                "FirstName" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["First Name"], "UTF-8", "auto"),
                "LastName" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Last Name"], "UTF-8", "auto"),
                "Company" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Company Name"], "UTF-8", "auto"),
                "EMail" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Email"], "UTF-8", "auto"),
                "AddressLine1" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Address 1"], "UTF-8", "auto"),
                "AddressLine2" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Address 2"], "UTF-8", "auto"),
                "AddressLine3" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Address 3"], "UTF-8", "auto"),
                "City" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["City"], "UTF-8", "auto"),
                "Country" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Country"], "UTF-8", "auto"),
                "Fax" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Fax"], "UTF-8", "auto"),
                "FaxCountryCode" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Fax Country Code"], "UTF-8", "auto"),
                "Phone" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Phone"], "UTF-8", "auto"),
                "PhoneCountryCode" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["Phone Country Code"], "UTF-8", "auto"),
                "Type" => mb_convert_encoding("Contact", "UTF-8", "auto"),
                "ZipCode" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["ZIP Code"], "UTF-8", "auto"),
                "State" => mb_convert_encoding($params["contactdetails"]["Registrant Contact"]["State"], "UTF-8", "auto")
            )

        )
    );

    if($result["result"] == "OK")
    {
        $values = array("success" => true);
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}


// OK
function domainnameapi_GetEPPCode($params) {

    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->GetDetails($params["sld"].".".$params["tld"]);

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
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", "GetDetails_FROM_" . substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}

// OK
function domainnameapi_RegisterNameserver($params)
{
    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->AddChildNameServer($params["sld"].".".$params["tld"], $params["nameserver"], array($params["ipaddress"]));

    if($result["result"] == "OK")
    {
        $values["success"] = true;
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}

// OK
function domainnameapi_ModifyNameserver($params)
{

    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->ModifyChildNameServer($params["sld"].".".$params["tld"], $params["nameserver"], array($params["newipaddress"]));

    if($result["result"] == "OK")
    {
        $values["success"] = true;
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}

// OK
function domainnameapi_DeleteNameserver($params)
{
    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->DeleteChildNameServer($params["sld"].".".$params["tld"], $params["nameserver"]);

    if($result["result"] == "OK")
    {
        $values["success"] = true;
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}





// OK
function domainnameapi_RequestDelete($params)
{
    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->Delete($params["sld"].".".$params["tld"]);

    if($result["result"] == "OK")
    {
        $values["success"] = true;
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}




// OK
function domainnameapi_IDProtectToggle($params)
{
    if($params["protectenable"])
    {
        // Process request
        $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->ModifyPrivacyProtectionStatus($params["sld"].".".$params["tld"], true, "Owner\'s request");
    }
    else
    {
        // Process request
        $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->ModifyPrivacyProtectionStatus($params["sld"].".".$params["tld"], false, "Owner\'s request");
    }

    if($result["result"] == "OK")
    {
        $values = array("success" => true);
    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}






// OK
function domainnameapi_GetDNS($params)
{
    $values["error"] = "DNS Management does not supported by Domain Name API.";

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}






// OK
function domainnameapi_SaveDNS($params)
{
    $values["error"] = "DNS Management does not supported by Domain Name API!!!";

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}


/**
 * @param $params
 * @return ResultsList
 */
function domainnameapi_CheckAvailability($params)
{
    if($params['isIdnDomain']){
        $label = empty($params['punyCodeSearchTerm']) ? strtolower($params['searchTerm']) : strtolower($params['punyCodeSearchTerm']);
    }else{
        $label = strtolower($params['searchTerm']);
    }

    $tldslist = $params['tldsToInclude'];
    $premiumEnabled = (bool) $params['premiumEnabled'];
    $domainslist = array();
    $results = new ResultsList();

    foreach($tldslist as $tld){
        if(!empty($tld[0])){
            if($tld[0] != '.'){
                $tld = ".".$tld;
            }
            $domain = $label.$tld;
            if(!in_array($domain, $domainslist["all"])){
                $domainslist["all"][] = $domain;
                $domainslist["list"][] = array("sld" => $label, "tld" => $tld);
            }
        }
    }

    foreach($domainslist["list"] as $domain){
        $tld=str_replace(".","",$domain['tld']);
        $result = api($params["API_UserName"], $params["API_Password"], false)->CheckAvailability(array($domain['sld']),array($tld),"1","create");
        $searchResult = new SearchResult($domain['sld'], $domain['tld']);
            if ($result[0]['Status'] == 'available') {
                $status = SearchResult::STATUS_NOT_REGISTERED;
                $searchResult->setStatus($status);
                if ($result[0]['IsFee']=='1') {
                    $searchResult->setPremiumDomain(true);
                    $searchResult->setPremiumCostPricing(
                        array(
                            'register' => $result[0]['Price'],
                            'renew' => $domain[0]['Price'],
                            'CurrencyCode' => 'USD',
                        )
                    );
                }

            }else{
                $status = SearchResult::STATUS_REGISTERED;
                $searchResult->setStatus($status);
            }
          $results->append($searchResult);

    }




    return $results;



}


function domainnameapi_GetDomainSuggestions($params){

    // user defined configuration values
    $userIdentifier = $params['API Username'];
    $apiKey = $params['API Key'];
    $testMode = $params['Test Mode'];
    $accountMode = $params['Account Mode'];
    $emailPreference = $params['Email Preference'];
    $additionalInfo = $params['Additional Information'];

    // availability check parameters
    $searchTerm = $params['searchTerm'];
    $punyCodeSearchTerm = $params['punyCodeSearchTerm'];

    $isIdnDomain = (bool) $params['isIdnDomain'];
    $premiumEnabled = (bool) $params['premiumEnabled'];
    $suggestionSettings = $params['suggestionSettings'];


    foreach (Capsule::table('tbldomainpricing')->get() as $client) {
        $tldsToInclude[]=str_replace(".","",$client->extension);
    }


    $results = new ResultsList();
    foreach ($tldsToInclude  as $tdl) {
        $result = api($params["API_UserName"], $params["API_Password"], false)->CheckAvailability(array($searchTerm),array($tdl),"1","create");
        $searchResult = new SearchResult($searchTerm,$tdl);
            foreach($result as $domain){
                if ($domain['Status'] == 'available') {
                    $status = SearchResult::STATUS_NOT_REGISTERED;
                    $searchResult->setStatus($status);
                        if ($domain['IsFee']=='1') {
                            $searchResult->setPremiumDomain(true);
                            $searchResult->setPremiumCostPricing(
                                array(
                                    'register' => $domain['Price'],
                                    'renew' => $domain['Price'],
                                    'CurrencyCode' => 'USD',
                                )
                            );
                        }
                    $results->append($searchResult);

                }




            }

        }

    return $results;




}

function registrarmodule_DomainSuggestionOptions() {
    return array(
        'includeCCTlds' => array(
            'FriendlyName' => 'Include Country Level TLDs',
            'Type' => 'yesno',
            'Description' => 'Tick to enable',
        ),
    );
}

//
function domainnameapi_Sync($params) {

    // Process request
    $result = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->SyncFromRegistry($params["sld"].".".$params["tld"]);

    // Log request
    logModuleCall("domainnameapi", substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));


    if($result["result"] == "OK" || 1 == 1)
    {
        // Process request
        $result2 = api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->GetDetails($params["sld"].".".$params["tld"]);

        if($result2["result"] == "OK")
        {
            $active = "";
            $expired = "";
            $expiration = "";

            // Check results
            if(preg_match("/\d{4}\-\d{2}\-\d{2}T\d{2}\:\d{2}\:\d{2}/", $result2["data"]["Dates"]["Expiration"]))
            {
                $expiration = substr($result2["data"]["Dates"]["Expiration"], 0, 10);
            }
            if($result2["data"]["Status"] == "Active") { $active = true; $expired = false; }
            if($result2["data"]["Status"] == "PendingDelete") { $expired= true; $active = false; }

            // If result is valid set it to WHMCS
            if(is_bool($active) && is_bool($expired) && trim($expiration) != "")
            {
                $values["active"] = $active;
                $values["expired"] = $expired;
                $values["expirydate"] = $expiration;
                //$values["success"] = true;
            }
            else
            {
                $values["error"] = "Unexpected result returned from registrar" . "\nActive: " . $active  . "\nExpired: " . $expired . "\nExpiryDate: " . $expiration;
            }

        }
        else
        {
            $values["error"] = $result["error"]["Message"] . "\n" . $result["error"]["Details"];
        }

    }
    else
    {
        $values["error"] = $result["error"]["Message"] . "<br />" . $result["error"]["Details"];
    }


    // Log request
    logModuleCall("domainnameapi", "GetDetails_FROM_" . substr(__FUNCTION__, 14), array("parameters" => $params, "request" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__REQUEST), array("values" => $values, "response" => api($params["API_UserName"], $params["API_Password"], $params["API_TestMode"])->__RESPONSE), $values, array($params["API_UserName"], $params["API_Password"]));

    return $values;
}




?>
