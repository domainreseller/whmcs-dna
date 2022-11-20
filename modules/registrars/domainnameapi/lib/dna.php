<?php
/**
 * Created by PhpStorm.
 * User: bunyaminakcay
 * Project name whmcs-dna
 * 20.11.2022 00:13
 * Bünyamin AKÇAY <bunyamin@bunyam.in>
 */
// BASE CONNECTION (ABSTRACT)
abstract class APIConnection
{

    abstract public function CheckAvailability($parameters);
    abstract public function ModifyNameServer($parameters);
    abstract public function GetList($parameters);
    abstract public function GetDetails($parameters);
    abstract public function EnableTheftProtectionLock($parameters);
    abstract public function DisableTheftProtectionLock($parameters);
    abstract public function ModifyPrivacyProtectionStatus($parameters);
    abstract public function AddChildNameServer($parameters);
    abstract public function DeleteChildNameServer($parameters);
    abstract public function ModifyChildNameServer($parameters);
    abstract public function GetContacts($parameters);
    abstract public function SaveContacts($parameters);
    abstract public function Transfer($parameters);
    abstract public function CancelTransfer($parameters);
    abstract public function RegisterWithContactInfo($parameters);
    abstract public function Renew($parameters);
    abstract public function Delete($parameters);
    abstract public function SyncFromRegistry($parameters);
}

// SOAP CONNECTION
class APIConnection_SOAP extends APIConnection
{
    // VARIABLES
    private $service = null;
    private $URL_SERVICE = "";

    // CONSTRUCTORS
    function __construct($Service_URL)
    {
        // Set settings
        $this->URL_SERVICE = $Service_URL;

        // Set WSDL caching enabled
        ini_set('soap.wsdl_cache_enabled', '1'); ini_set('soap.wsdl_cache_ttl', '86400');

        // Create unique connection
        $this->service = new SoapClient($this->URL_SERVICE . "?singlewsdl", array("cache_wsdl" => WSDL_CACHE_MEMORY,  "encoding"=>"UTF-8"));
    }
    function APIConnection_SOAP($Service_URL)
    {
        // Set settings
        $this->URL_SERVICE = $Service_URL;

        // Set WSDL caching enabled
        ini_set('soap.wsdl_cache_enabled', '1');  ini_set('soap.wsdl_cache_ttl', '86400');

        // Create unique connection
        $this->service = new SoapClient($this->URL_SERVICE . "?singlewsdl", array("cache_wsdl" => WSDL_CACHE_MEMORY,  "encoding"=>"UTF-8"));
    }

    // Convert object to array
    private function objectToArray($o)
    {
        try { $o = json_decode(json_encode($o), true); } catch(Exception $ex) { }
        return $o;
    }

    // Get error if exists
    private function parseError($response)
    {
        $result = false;

        if(is_null($response))
        {
            // Set error data
            $result = array();
            $result["Code"] = "INVALID_RESPONSE";
            $result["Message"] = "Invalid response or no response received from server!";
            $result["Details"] = "SOAP Connection returned null value!";
        }
        elseif(!is_array($response))
        {
            // Set error data
            $result = array();
            $result["Code"] = "INVALID_RESPONSE";
            $result["Message"] = "Invalid response or no response received from server!";
            $result["Details"] = "SOAP Connection returned non-array value!";
        }
        elseif(strtolower(key($response)) == "faultstring")
        {
            // Handle soap fault

            $result = array();
            $result["Code"] = "";
            $result["Message"] = "";
            $result["Details"] = "";

            // Set error data
            if(isset($response["faultcode"])) { $result["Code"] = $response["faultcode"]; }
            if(isset($response["faultstring"])) { $result["Message"] = $response["faultstring"]; }
            if(isset($response["detail"])) {
                if(is_array($response["detail"])) {
                    if(isset($response["detail"]["ExceptionDetail"])) {
                        if(is_array($response["detail"]["ExceptionDetail"])) {
                            if(isset($response["detail"]["ExceptionDetail"]["StackTrace"])) {
                                $result["Details"] = $response["detail"]["ExceptionDetail"]["StackTrace"];
                            }
                        }
                    }

                }
            }

        }
        elseif(count($response) != 1)
        {
            // Set error data
            $result = array();
            $result["Code"] = "INVALID_RESPONSE";
            $result["Message"] = "Invalid response or no response received from server!";
            $result["Details"] = "Response data contains more than one result! Only one result accepted!";
        }
        elseif(!isset($response[key($response)]["OperationResult"]) || !isset($response[key($response)]["ErrorCode"]))
        {
            // Set error data
            $result = array();
            $result["Code"] = "INVALID_RESPONSE";
            $result["Message"] = "Invalid response or no response received from server!";
            $result["Details"] = "Operation result or Error code not received from server!";
        }
        elseif(strtoupper($response[key($response)]["OperationResult"]) != "SUCCESS")
        {
            // Set error data
            $result = array();
            $result["Code"] = "";
            $result["Message"] = "";
            $result["Details"] = "";

            $result["Message"] = "Operation can not completed successfully!";

            if(isset($response[key($response)]["OperationMessage"]))
            { $result["Code"] = "API_" . $response[key($response)]["ErrorCode"]; }

            if(isset($response[key($response)]["OperationResult"]))
            { $result["Code"] .= "_" . $response[key($response)]["OperationResult"]; }

            if(isset($response[key($response)]["OperationMessage"]))
            { $result["Details"] = $response[key($response)]["OperationMessage"]; }

        }
        else
        {

        }

        return $result;
    }

    // Check if response contains error
    private function hasError($response)
    { return ($this->parseError($response) === false) ? false : true; }

    // Set error message
    private function setError($Code, $Message, $Details)
    {
        $result = array();
        $result["Code"] = $Code;
        $result["Message"] = $Message;
        $result["Details"] = $Details;
        return $result;
    }

    // Parse domain info
    private function parseDomainInfo($data)
    {
        $result = array();
        $result["ID"] = "";
        $result["Status"] = "";
        $result["DomainName"] = "";
        $result["AuthCode"] = "";
        $result["LockStatus"] = "";
        $result["PrivacyProtectionStatus"] = "";
        $result["IsChildNameServer"] = "";
        $result["Contacts"] = array();
        $result["Contacts"]["Billing"] = array();
        $result["Contacts"]["Technical"] = array();
        $result["Contacts"]["Administrative"] = array();
        $result["Contacts"]["Registrant"] = array();
        $result["Contacts"]["Billing"]["ID"] = "";
        $result["Contacts"]["Technical"]["ID"] = "";
        $result["Contacts"]["Administrative"]["ID"] = "";
        $result["Contacts"]["Registrant"]["ID"] = "";
        $result["Dates"] = array();
        $result["Dates"]["Start"] = "";
        $result["Dates"]["Expiration"] = "";
        $result["Dates"]["RemainingDays"] = "";
        $result["NameServers"] = array();
        $result["Additional"] = array();
        $result["ChildNameServers"] = array();

        foreach($data as $attrName => $attrValue)
        {
            switch($attrName)
            {
                case "Id":
                    {
                        if(is_numeric($attrValue)) { $result["ID"] = $attrValue; }
                        break;
                    }

                case "Status":
                    { $result["Status"] = $attrValue; break; }

                case "DomainName":
                    { $result["DomainName"] = $attrValue; break; }

                case "AdministrativeContactId":
                    {
                        if(is_numeric($attrValue)) { $result["Contacts"]["Administrative"]["ID"] = $attrValue;  }
                        break;
                    }

                case "BillingContactId":
                    {
                        if(is_numeric($attrValue)) { $result["Contacts"]["Billing"]["ID"] = $attrValue;  }
                        break;
                    }

                case "TechnicalContactId":
                    {
                        if(is_numeric($attrValue)) { $result["Contacts"]["Technical"]["ID"] = $attrValue;  }
                        break;
                    }

                case "RegistrantContactId":
                    {
                        if(is_numeric($attrValue)) { $result["Contacts"]["Registrant"]["ID"] = $attrValue;  }
                        break;
                    }

                case "Auth":
                    {
                        if(is_string($attrValue) && !is_null($attrValue))
                        { $result["AuthCode"] = $attrValue; }
                        break;
                    }

                case "StartDate":
                    { $result["Dates"]["Start"] = $attrValue; break; }

                case "ExpirationDate":
                    { $result["Dates"]["Expiration"] = $attrValue; break; }

                case "LockStatus":
                    {
                        if(is_bool($attrValue))
                        { $result["LockStatus"] = var_export($attrValue, true); }
                        break;
                    }

                case "PrivacyProtectionStatus":
                    {
                        if(is_bool($attrValue))
                        { $result["PrivacyProtectionStatus"] = var_export($attrValue, true); }
                        break;
                    }

                case "IsChildNameServer":
                    {
                        if(is_bool($attrValue))
                        { $result["IsChildNameServer"] = var_export($attrValue, true); }
                        break;
                    }

                case "RemainingDay":
                    {
                        if(is_numeric($attrValue))
                        { $result["Dates"]["RemainingDays"] = $attrValue; }
                        break;
                    }

                case "NameServerList":
                    {
                        if(is_array($attrValue))
                        {
                            foreach($attrValue as $nameserverValue)
                            {
                                array_push($result["NameServers"], $nameserverValue);
                            }
                        }
                        break;
                    }

                case "AdditionalAttributes":
                    {
                        if(is_array($attrValue))
                        {

                            if(isset($attrValue["KeyValueOfstringstring"]))
                            {
                                foreach($attrValue["KeyValueOfstringstring"] as $attribute)
                                {
                                    if(isset($attribute["Key"]) && isset($attribute["Value"]))
                                    {
                                        $result["Additional"][$attribute["Key"]] = $attribute["Value"];
                                    }
                                }
                            }
                        }
                        break;
                    }

                case "ChildNameServerInfo":
                    {

                        if(is_array($attrValue))
                        {

                            if(isset($attrValue["ChildNameServerInfo"]["IpAddress"]))
                            {
                                $attribute = $attrValue["ChildNameServerInfo"];

                                $ns = "";
                                $IpAddresses = array();

                                // Name of NameServer
                                if(!is_null($attribute["NameServer"]) && is_string($attribute["NameServer"]))
                                { $ns = $attribute["NameServer"]; }

                                // IP adresses of NameServer
                                if(is_array($attribute["IpAddress"]) && isset($attribute["IpAddress"]["string"]))
                                {

                                    if(is_array($attribute["IpAddress"]["string"]))
                                    {

                                        foreach($attribute["IpAddress"]["string"] as $ip)
                                        {
                                            if(isset($ip) && !is_null($ip) && is_string($ip))
                                            {
                                                array_push($IpAddresses, $ip);
                                            }
                                        }

                                    }
                                    elseif(is_string($attribute["IpAddress"]["string"]))
                                    {
                                        array_push($IpAddresses, $attribute["IpAddress"]["string"]);
                                    }

                                }

                                array_push($result["ChildNameServers"],
                                    array(
                                        "NameServer" => $ns,
                                        "IPAddresses" => $IpAddresses
                                    )
                                );


                            }
                            else
                            {
                                if(count($attrValue["ChildNameServerInfo"])>0)
                                {
                                    foreach($attrValue["ChildNameServerInfo"] as $attribute)
                                    {

                                        if(isset($attribute["NameServer"]) && isset($attribute["IpAddress"]))
                                        {
                                            $ns = "";
                                            $IpAddresses = array();

                                            // Name of NameServer
                                            if(!is_null($attribute["NameServer"]) && is_string($attribute["NameServer"]))
                                            { $ns = $attribute["NameServer"]; }

                                            // IP adresses of NameServer
                                            if(is_array($attribute["IpAddress"]) && isset($attribute["IpAddress"]["string"]))
                                            {

                                                if(is_array($attribute["IpAddress"]["string"]))
                                                {

                                                    foreach($attribute["IpAddress"]["string"] as $ip)
                                                    {
                                                        if(isset($ip) && !is_null($ip) && is_string($ip))
                                                        {
                                                            array_push($IpAddresses, $ip);
                                                        }
                                                    }

                                                }
                                                elseif(is_string($attribute["IpAddress"]["string"]))
                                                {
                                                    array_push($IpAddresses, $attribute["IpAddress"]["string"]);
                                                }

                                            }

                                            array_push($result["ChildNameServers"],
                                                array(
                                                    "NameServer" => $ns,
                                                    "IPAddresses" => $IpAddresses
                                                )
                                            );



                                        }

                                    }

                                }
                            }


                        }
                        break;
                    }
            }

        }

        return $result;
    }




    // Parse Contact info
    private function parseContactInfo($data)
    {
        $result = array();
        $result["ID"] = "";
        $result["Status"] = "";
        $result["Additional"] = array();
        $result["Address"] = array();
        $result["Address"]["Line1"] = "";
        $result["Address"]["Line2"] = "";
        $result["Address"]["Line3"] = "";
        $result["Address"]["State"] = "";
        $result["Address"]["City"] = "";
        $result["Address"]["Country"] = "";
        $result["Address"]["ZipCode"] = "";
        $result["Phone"] = array();
        $result["Phone"]["Phone"] = array();
        $result["Phone"]["Phone"]["Number"] = "";
        $result["Phone"]["Phone"]["CountryCode"] = "";
        $result["Phone"]["Fax"]["Number"] = "";
        $result["Phone"]["Fax"]["CountryCode"] = "";
        $result["AuthCode"] = "";
        $result["FirstName"] = "";
        $result["LastName"] = "";
        $result["Company"] = "";
        $result["EMail"] = "";
        $result["Type"] = "";

        foreach($data as $attrName => $attrValue)
        {
            switch($attrName)
            {
                case "Id":
                    {
                        if(is_numeric($attrValue)) { $result["ID"] = $attrValue; }
                        break;
                    }

                case "Status":
                    { $result["Status"] = $attrValue; break; }

                case "AdditionalAttributes":
                    {
                        if(is_array($attrValue))
                        {

                            if(isset($attrValue["KeyValueOfstringstring"]))
                            {
                                foreach($attrValue["KeyValueOfstringstring"] as $attribute)
                                {
                                    if(isset($attribute["Key"]) && isset($attribute["Value"]))
                                    {
                                        $result["Additional"][$attribute["Key"]] = $attribute["Value"];
                                    }
                                }
                            }
                        }
                        break;
                    }

                case "AddressLine1":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Address"]["Line1"] = $attrValue;  }
                        break;
                    }

                case "AddressLine2":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Address"]["Line2"] = $attrValue;  }
                        break;
                    }

                case "AddressLine3":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Address"]["Line3"] = $attrValue;  }
                        break;
                    }

                case "Auth":
                    {
                        if(is_string($attrValue) && !is_null($attrValue))
                        { $result["AuthCode"] = $attrValue; }
                        break;
                    }

                case "City":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Address"]["City"] = $attrValue;  }
                        break;
                    }

                case "Company":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Company"] = $attrValue;  }
                        break;
                    }

                case "Country":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Address"]["Country"] = $attrValue;  }
                        break;
                    }

                case "EMail":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["EMail"] = $attrValue;  }
                        break;
                    }

                case "Fax":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Phone"]["Fax"]["Number"] = $attrValue;  }
                        break;
                    }

                case "FaxCountryCode":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Phone"]["Fax"]["CountryCode"] = $attrValue;  }
                        break;
                    }

                case "Phone":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Phone"]["Phone"]["Number"] = $attrValue;  }
                        break;
                    }

                case "PhoneCountryCode":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Phone"]["Phone"]["CountryCode"] = $attrValue;  }
                        break;
                    }

                case "FirstName":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["FirstName"] = $attrValue;  }
                        break;
                    }

                case "LastName":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["LastName"] = $attrValue;  }
                        break;
                    }

                case "State":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Address"]["State"] = $attrValue;  }
                        break;
                    }

                case "ZipCode":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Address"]["ZipCode"] = $attrValue;  }
                        break;
                    }

                case "Type":
                    {
                        if(is_string($attrValue) && !is_null($attrValue)) { $result["Type"] = $attrValue;  }
                        break;
                    }

            }

        }

        return $result;
    }



    // API METHODs

    // Check domain is available?
    public function CheckAvailability($parameters)
    {
        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));
            $response = $this->objectToArray($response);



            $data = $response[key($response)];


            foreach ($data["DomainAvailabilityInfoList"] as $name => $value){

                $donus[]=array(

                    "TLD" => $value["Tld"],
                    "Status" => $value["Status"],
                    "Command" => $value["Command"], // Komut create,renew,transfer,restore fiyatlarının çekilmesi
                    "Period" => $value["Period"],
                    "IsFee" => $value["IsFee"],
                    "Price" => $value["Price"],


                );

            }

            return $donus;

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $donus;
    }









    // Get domain list
    public function GetList($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $data = $response[key($response)];

                // If DomainInfo a valid array
                if(isset($data["TotalCount"]) && is_numeric($data["TotalCount"]))
                {
                    $result["data"]["Domains"] = array();

                    if(isset($data["DomainInfoList"]) && is_array($data["DomainInfoList"]))
                    {
                        if(isset($data["DomainInfoList"]["DomainInfo"]["Id"]))
                        {
                            array_push($result["data"]["Domains"], $data["DomainInfoList"]["DomainInfo"]);
                        }
                        else
                        {
                            // Parse multiple domain info
                            foreach($data["DomainInfoList"]["DomainInfo"] as $domainInfo)
                            {
                                array_push($result["data"]["Domains"], $this->parseDomainInfo($domainInfo));
                            }
                        }

                    }


                    $result["result"] = "OK";

                }
                else
                {
                    // Set error
                    $result["result"] = "ERROR";
                    $result["error"] = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!");;
                }


            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }




    // Get domain details
    public function GetDetails($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $data = $response[key($response)];


                // If DomainInfo a valid array
                if(isset($data["DomainInfo"]) && is_array($data["DomainInfo"]))
                {
                    // Parse domain info
                    $result["data"] = $this->parseDomainInfo($data["DomainInfo"]);
                    $result["result"] = "OK";

                }
                else
                {
                    // Set error
                    $result["result"] = "ERROR";
                    $result["error"] = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!");;
                }


            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }





    // Modify name servers
    public function ModifyNameServer($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $result["data"] = array();
                $result["data"]["NameServers"] = array();
                $result["data"]["NameServers"] = $parameters["request"]["NameServerList"];
                $result["result"] = "OK";

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }







    // Enable Theft Protection Lock
    public function EnableTheftProtectionLock($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $result["data"] = array();
                $result["data"]["LockStatus"] = var_export(true, true);
                $result["result"] = "OK";

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }







    // Disable Theft Protection Lock
    public function DisableTheftProtectionLock($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $result["data"] = array();
                $result["data"]["LockStatus"] = var_export(false, true);
                $result["result"] = "OK";

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }





    // Modify privacy protection status
    public function ModifyPrivacyProtectionStatus($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $result["data"] = array();
                $result["data"]["PrivacyProtectionStatus"] = var_export($parameters["request"]["ProtectPrivacy"], true);
                $result["result"] = "OK";

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }




    // CHILD NAMESERVER MANAGEMENT

    // Add Child Nameserver
    public function AddChildNameServer($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $result["data"] = array();
                $result["data"]["NameServer"] = $parameters["request"]["ChildNameServer"];
                $result["data"]["IPAdresses"] = array();
                $result["data"]["IPAdresses"] = $parameters["request"]["IpAddressList"];
                $result["result"] = "OK";

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }



    // Delete Child Nameserver
    public function DeleteChildNameServer($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $result["data"] = array();
                $result["data"]["NameServer"] = $parameters["request"]["ChildNameServer"];
                $result["result"] = "OK";

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }



    // Modify Child Nameserver
    public function ModifyChildNameServer($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $result["data"] = array();
                $result["data"]["NameServer"] = $parameters["request"]["ChildNameServer"];
                $result["data"]["IPAdresses"] = array();
                $result["data"]["IPAdresses"] = $parameters["request"]["IpAddressList"];
                $result["result"] = "OK";

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }







    // CONTACT MANAGEMENT

    // Get Contact
    public function GetContacts($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $data = $response[key($response)];

                // If ContactInfo a valid array
                if(isset($data["AdministrativeContact"]) && is_array($data["AdministrativeContact"]) && isset($data["TechnicalContact"]) && is_array($data["TechnicalContact"]) && isset($data["RegistrantContact"]) && is_array($data["RegistrantContact"]) && isset($data["BillingContact"]) && is_array($data["BillingContact"]))
                {
                    // Parse domain info
                    $result["data"] = array();
                    $result["data"]["contacts"] = array();
                    $result["data"]["contacts"]["Administrative"] = $this->parseContactInfo($data["AdministrativeContact"]);
                    $result["data"]["contacts"]["Billing"] = $this->parseContactInfo($data["BillingContact"]);
                    $result["data"]["contacts"]["Registrant"] = $this->parseContactInfo($data["RegistrantContact"]);
                    $result["data"]["contacts"]["Technical"] = $this->parseContactInfo($data["TechnicalContact"]);
                    $result["result"] = "OK";

                }
                else
                {
                    // Set error
                    $result["result"] = "ERROR";
                    $result["error"] = $this->setError("INVALID_CONTACT_INTO", "Invalid response received from server!", "Contact info is not a valid array or more than one contact info has returned!");;
                }

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }




    // Save contact informations
    public function SaveContacts($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $data = $response[key($response)];

                // If ContactInfo a valid array
                if(1 == 1)
                {
                    $result["result"] = "OK";
                }
                else
                {
                    // Set error
                    $result["result"] = "ERROR";
                    $result["error"] = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!");;
                }

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }



    // Start domain transfer
    public function Transfer($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $data = $response[key($response)];

                // If DomainInfo a valid array
                if(isset($data["DomainInfo"]) && is_array($data["DomainInfo"]))
                {
                    // Parse domain info
                    $result["data"] = $this->parseDomainInfo($data["DomainInfo"]);
                    $result["result"] = "OK";

                }
                else
                {
                    // Set error
                    $result["result"] = "ERROR";
                    $result["error"] = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!");;
                }

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }








    // Cancel domain transfer
    public function CancelTransfer($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                // Parse domain info
                $result["data"] = array();
                $result["data"]["DomainName"] = $parameters["request"]["DomainName"];
                $result["result"] = "OK";


            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }





    // Register domain with contact informations
    public function RegisterWithContactInfo($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $data = $response[key($response)];

                // If DomainInfo a valid array
                if(isset($data["DomainInfo"]) && is_array($data["DomainInfo"]))
                {
                    // Parse domain info
                    $result["data"] = $this->parseDomainInfo($data["DomainInfo"]);
                    $result["result"] = "OK";

                }
                else
                {
                    // Set error
                    $result["result"] = "ERROR";
                    $result["error"] = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!");;
                }

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }




    // Renew domain
    public function Renew($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $data = $response[key($response)];

                $result["data"] = array();
                $result["data"]["ExpirationDate"] = "";

                if(isset($data["ExpirationDate"]))
                {
                    $result["data"]["ExpirationDate"] = $data["ExpirationDate"];
                }

                $result["result"] = "OK";


            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }








    // Delete domain
    public function Delete($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $result["result"] = "OK";
            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }










    // Sync domain details
    public function SyncFromRegistry($parameters)
    {
        $result = array();

        try
        {
            // SOAP method which is same as current function name called
            $response = $this->service->__soapCall(__FUNCTION__, array($parameters));

            // Serialize as array
            $response = $this->objectToArray($response);

            // Check is there any error?
            if(!$this->hasError($response))
            {
                $data = $response[key($response)];

                // If DomainInfo a valid array
                if(isset($data["DomainInfo"]) && is_array($data["DomainInfo"]))
                {
                    // Parse domain info
                    $result["data"] = $this->parseDomainInfo($data["DomainInfo"]);
                    $result["result"] = "OK";

                }
                else
                {
                    // Set error
                    $result["result"] = "ERROR";
                    $result["error"] = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!");;
                }

            }
            else
            {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"] = $this->parseError($response);
            }

        }
        catch(Exception $ex)
        {
            $result["result"] = "ERROR";
            $result["error"] = $this->parseError($this->objectToArray($ex));
        }

        return $result;
    }



}


class DomainNameAPI_PHPLibrary
{
    // VARIABLES
    public $_VERSION_ = "1.3";
    private $_USERDATA_USERNAME = "ownername";
    private $_USERDATA_PASSWORD = "ownerpass";
    private $_URL_SERVICE = "http://api.domainnameapi.com/DomainAPI.svc";
    private $_CONNECTION_METHOD = "APIConnection_SOAP";
    private $_VERSION = "1.1.0.0";
    private $con = null;
    private $_useTestMode = true;
    private $_useCaching = false;
    private $_cache = array();

    public $__REQUEST = array();
    public $__RESPONSE = array();

    // CONSTRUCTORS
    // Default constructors
    function __construct()
    {
        $this->useTestMode(true);
        $this->useCaching(false);
        $this->setConnectionMethod("Auto");
    }
    function DomainNameAPI_PHPLibrary()
    {
        $this->useTestMode(true);
        $this->useCaching(false);
        $this->setConnectionMethod("Auto");
    }

    // METHODS

    // USE TEST PLATFORM OR REAL PLATFORM
    // if value equals false, use real platform, otherwise use test platform
    public function useTestMode($value)
    {
        if($value === false)
        {
            // REAL MODE
            $this->_useTestMode = false;
            $this->_URL_SERVICE = "http://api.domainnameapi.com/domainapi.svc";
        }
        else
        {
            // TEST MODE
            $this->_useTestMode = true;
            $this->_URL_SERVICE = "http://api-ote.domainnameapi.com/DomainAPI.svc";
            $this->_USERDATA_USERNAME = "ownername";
            $this->_USERDATA_PASSWORD = "ownerpass";
        }

        $this->setConnectionMethod("Auto");
    }

    // CACHING

    // Caching will be enabled or not
    public function useCaching($value)
    {
        if($value === true)
        { $this->_useCaching = true; }
        else
        { $this->_useCaching = false; }

    }

    // Remove domain's value from cache
    public function removeDomainFromCache($DomainName)
    {
        if(isset($this->_cache[$DomainName]))
        { unset($this->_cache[$DomainName]); }
    }

    // Get used mode? TEST => true, REAL => false
    public function isTestMode()
    { return ($this->_useTestMode === false) ? false : true; }

    // SET Username and Password
    public function setUser($UserName, $Password)
    {
        $this->_USERDATA_USERNAME = $UserName;
        $this->_USERDATA_PASSWORD = $Password;
    }

    // Get connection method
    public function getConnectionMethod()
    {
        return $this->_CONNECTION_METHOD;
    }

    // Set connection method
    public function setConnectionMethod($Method)
    {
        switch(strtoupper(trim($Method)))
        {
            case "SOAP":
                $this->_CONNECTION_METHOD = "APIConnection_SOAP";
                break;

            case "CURL";
                $this->_CONNECTION_METHOD = "APIConnection_CURL";
                break;

            default:
                if(class_exists("SoapClient"))
                { $this->_CONNECTION_METHOD = "APIConnection_SOAP"; }
                elseif(function_exists("curl_init"))
                { $this->_CONNECTION_METHOD = "APIConnection_CURL"; }
                else
                {
                    // DUZELT
                    $this->_CONNECTION_METHOD = "ALL_OF_CONNECTION_METHODS_NOT_AVAILABLE";
                }
                break;
        }

        // Prepare connection
        $this->con = new $this->_CONNECTION_METHOD($this->_URL_SERVICE);

    }





    // API METHODS

    // Check domain is avaliable? Ex: ('example1', 'example2'), ('com', 'net', 'org')
    public function CheckAvailability($Domains, $TLDs,$Period,$Command)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainNameList" => $Domains,
                "TldList" => $TLDs,
                "Period" =>  $Period,
                "Commad" => $Command
            )
        );

        // Check availability via already prepared connection
        $response = $this->con->CheckAvailability($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }




    // Get domain details
    public function GetList()
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME
            )
        );

        // Get domain id via already prepared connection
        $response = $this->con->GetList($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }


    // Get domain details
    public function GetDetails($DomainName)
    {

        // If caching enabled
        if($this->_useCaching == true)
        {

            // If is there any cached value for this domain?
            if(isset($this->_cache[$DomainName]["result"]))
            {
                // Return cached value
                $result = $this->_cache[$DomainName]["result"];
                $result["fromCache"] = true;

                return $result;
            }

        }

        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );

        // Get domain id via already prepared connection
        $response = $this->con->GetDetails($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        // If caching enabled
        if($this->_useCaching == true)
        {
            $this->_cache[$DomainName]["result"] = $response;
            $this->_cache[$DomainName]["date"] = date("Y-m-d H:i:s");
        }

        return $response;
    }




    // Modify nameservers
    public function ModifyNameServer($DomainName, $NameServers)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "NameServerList" => $NameServers
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Check availability via already prepared connection
        $response = $this->con->ModifyNameServer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }



    // Enable Theft Protection Lock
    public function EnableTheftProtectionLock($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Enable theft protection lock via already prepared connection
        $response = $this->con->EnableTheftProtectionLock($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }


    // Disable Theft Protection Lock
    public function DisableTheftProtectionLock($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Disable theft protection lock via already prepared connection
        $response = $this->con->DisableTheftProtectionLock($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }




    // CHILD NAMESERVER MANAGEMENT

    // Add Child Nameserver
    public function AddChildNameServer($DomainName, $NameServer, $IPAdresses)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "ChildNameServer" => $NameServer,
                "IpAddressList" => $IPAdresses
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Add child nameserver via already prepared connection
        $response = $this->con->AddChildNameServer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }



    // Delete Child Nameserver
    public function DeleteChildNameServer($DomainName, $NameServer)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "ChildNameServer" => $NameServer
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Delete child nameserver via already prepared connection
        $response = $this->con->DeleteChildNameServer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }


    // Modify Child Nameserver
    public function ModifyChildNameServer($DomainName, $NameServer, $IPAdresses)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "ChildNameServer" => $NameServer,
                "IpAddressList" => $IPAdresses
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Add child nameserver via already prepared connection
        $response = $this->con->ModifyChildNameServer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }




    // CONTACT MANAGEMENT

    // Get Domain Contact informations
    public function GetContacts($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );

        // Get Domain Contact informations via already prepared connection
        $response = $this->con->GetContacts($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }



    // Set domain cantacts
    public function SaveContacts($DomainName, $Contacts)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "AdministrativeContact" => $Contacts["Administrative"],
                "BillingContact" => $Contacts["Billing"],
                "TechnicalContact" => $Contacts["Technical"],
                "RegistrantContact" => $Contacts["Registrant"]
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Register domain via already prepared connection
        $response = $this->con->SaveContacts($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }



    // DOMAIN TRANSFER (INCOMING DOMAIN)

    // Start domain transfer (Incoming domain)
    public function Transfer($DomainName, $AuthCode)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "AuthCode" => $AuthCode
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Start domain transfer via already prepared connection
        $response = $this->con->Transfer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }




    // Cancel domain transfer (Incoming domain)
    public function CancelTransfer($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Cancel domain transfer via already prepared connection
        $response = $this->con->CancelTransfer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }



    // Renew domain
    public function Renew($DomainName, $Period)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "Period" => $Period
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Renew domain via already prepared connection
        $response = $this->con->Renew($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }



    // Register domain with contact informations
    public function RegisterWithContactInfo($DomainName, $Period, $Contacts, $NameServers = array("dns.domainnameapi.com", "web.domainnameapi.com"), $TheftProtectionLock = true, $PrivacyProtection = false)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "Period" => $Period,
                "NameServerList" => $NameServers,
                "LockStatus" => $TheftProtectionLock,
                "PrivacyProtectionStatus" => $PrivacyProtection,
                "AdministrativeContact" => $Contacts["Administrative"],
                "BillingContact" => $Contacts["Billing"],
                "TechnicalContact" => $Contacts["Technical"],
                "RegistrantContact" => $Contacts["Registrant"]
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Register domain via already prepared connection
        $response = $this->con->RegisterWithContactInfo($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }


    // Modify privacy protection status of domain
    public function ModifyPrivacyProtectionStatus($DomainName, $Status, $Reason = "Owner request")
    {
        if(trim($Reason) == "") { $Reason = "Owner request"; }

        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "ProtectPrivacy" => $Status,
                "Reason" => $Reason
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Modify privacy protection status of domain via already prepared connection
        $response = $this->con->ModifyPrivacyProtectionStatus($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }


    // Sync domain
    public function SyncFromRegistry($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );

        // We will modify domain, so remove it from cache
        $this->removeDomainFromCache($DomainName);

        // Sync domain via already prepared connection
        $response = $this->con->SyncFromRegistry($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }


};
