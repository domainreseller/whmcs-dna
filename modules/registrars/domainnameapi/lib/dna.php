<?php
/**
 * Created by PhpStorm.
 * User: bunyaminakcay
 * Project name whmcs-dna
 * 20.11.2022 00:13
 * Bünyamin AKÇAY <bunyamin@bunyam.in>
 */

namespace DomainNameApi;

class DomainNameAPI_PHPLibrary {

    private $_USERDATA_USERNAME = "ownername";
    private $_USERDATA_PASSWORD = "ownerpass";
    private $_URL_SERVICE       = "https://whmcs.domainnameapi.com/DomainApi.svc";
    private $service;


    public $__REQUEST  = [];
    public $__RESPONSE = [];


    public function __construct($UserName = "ownername", $Password = "ownerpass", $TestMode = false) {

        self::setCredentials($UserName,$Password);
        self::useTestMode($TestMode);

        // Create unique connection
        $this->service = new \SoapClient($this->_URL_SERVICE . "?singlewsdl", [
            'trace'      => true,
            "encoding"   => "UTF-8",
            'features'   => SOAP_SINGLE_ELEMENT_ARRAYS,
            'debug'      => true,
            'exceptions' => true,
        ]);

    }


    // METHODS

    // USE TEST PLATFORM OR REAL PLATFORM
    // if value equals false, use real platform, otherwise use test platform
    private function useTestMode($value = true) {
        if ($value === true || $value=='on') {
            $this->_USERDATA_USERNAME = 'test1.dna@apiname.com';
            $this->_USERDATA_PASSWORD = 'FsUvpJMzQ69scpqE';
        }
    }

    // SET Username and Password
    private function setCredentials($UserName, $Password) {
        $this->_USERDATA_USERNAME = $UserName;
        $this->_USERDATA_PASSWORD = $Password;
    }

    public function getRequestData(){
        return $this->__REQUEST;
    }
    public function setRequestData($request){
        $this->__REQUEST = $request;
    }
    public function getResponseData(){
        return $this->__RESPONSE;
    }
    public function setResponseData($response){
        $this->__RESPONSE = $response;
    }


    // API METHODS
    public function GetResellerDetails() {
        $parameters = [
            "request" => [
                "Password"       => $this->_USERDATA_PASSWORD,
                "UserName"       => $this->_USERDATA_USERNAME,
                'CurrencyId'=>2
            ]
        ];


        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {
            //self::setResponseData($response);

            $data = $response[key($response)];
            $resp = [];

            if (isset($data['ResellerInfo'])) {
                $resp['result'] = 'OK';
                $resp['id']       = $data['ResellerInfo']['Id'];
                $resp['active']   = $data['ResellerInfo']['Status'] == 'Active';
                $resp['name']     = $data['ResellerInfo']['Name'];

                $active_currency = $data['ResellerInfo']['BalanceInfoList']['BalanceInfo'][0];
                $balances = [];
                foreach ($data['ResellerInfo']['BalanceInfoList']['BalanceInfo'] as $k => $v) {

                    if ($v['CurrencyName'] == $data['ResellerInfo']['CurrencyInfo']['Code']) {
                        $active_currency = $v;
                    }

                    $balances[]= [
                        'balance'  => $v['Balance'],
                        'currency' => $v['CurrencyName'],
                        'symbol'   => $v['CurrencySymbol'],
                    ];
                }

                $resp['balance']  = $active_currency['Balance'];
                $resp['currency'] = $active_currency['CurrencyName'];
                $resp['symbol']   = $active_currency['CurrencySymbol'];
                $resp['balances'] = $balances;

            } else {
                $resp['result'] = 'ERROR';
                $resp['error'] = $this->setError("INVALID_CREDINENTIALS", "Invalid response received from server!", "invalid username and password");

            }



            return $resp;

        });



        // Log last request and response
        //self::setRequestData($parameters);


        return $response;
    }

    public function GetCurrentBalance() {
        $parameters = [
            "request" => [
                "Password"       => $this->_USERDATA_PASSWORD,
                "UserName"       => $this->_USERDATA_USERNAME,
                'CurrencyId'=>2
            ]
        ];


        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {

            //self::setResponseData($response);

            return $response['GetCurrentBalanceResult'];

        });



        // Log last request and response
        //self::setRequestData($parameters);

        return $response;
    }


    // Check domain is avaliable? Ex: ('example1', 'example2'), ('com', 'net', 'org')
    public function CheckAvailability($Domains, $TLDs, $Period, $Command) {
        $parameters = [
            "request" => [
                "Password"       => $this->_USERDATA_PASSWORD,
                "UserName"       => $this->_USERDATA_USERNAME,
                "DomainNameList" => $Domains,
                "TldList"        => $TLDs,
                "Period"         => $Period,
                "Commad"         => $Command
            ]
        ];



        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {

            //self::setResponseData($response);


            //return $response;
            $data = $response[key($response)];
            $available=[];

            if(isset($data["DomainAvailabilityInfoList"]['DomainAvailabilityInfo']['Tld'])){
                $buffer = $data["DomainAvailabilityInfoList"]['DomainAvailabilityInfo'];
                $data=[
                    'DomainAvailabilityInfoList'=>['DomainAvailabilityInfo'=>[
                      $buffer
                    ]]
                ];
            }

            foreach ($data["DomainAvailabilityInfoList"]['DomainAvailabilityInfo'] as $name => $value) {

                $available[] = [
                    "TLD"     => $value["Tld"],
                    "Status"  => $value["Status"],
                    "Command" => $value["Command"], // Komut create,renew,transfer,restore fiyatlarının çekilmesi
                    "Period"  => $value["Period"],
                    "IsFee"   => $value["IsFee"],
                    "Price"   => $value["Price"],
                ];

            }

            return $available;
        });



        // Log last request and response

        return $response;
    }

    // Get domain details
    public function GetList() {
        $parameters = [
            "request" => [
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
            ]
        ];


        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {


            //self::setResponseData($response);


            $data = $response[key($response)];

            if (isset($data["TotalCount"]) && is_numeric($data["TotalCount"])) {
                $result["data"]["Domains"] = [];

                if (isset($data["DomainInfoList"]) && is_array($data["DomainInfoList"])) {
                    if (isset($data["DomainInfoList"]["DomainInfo"]["Id"])) {
                        $result["data"]["Domains"][] = $data["DomainInfoList"]["DomainInfo"];
                    } else {
                        // Parse multiple domain info
                        foreach ($data["DomainInfoList"]["DomainInfo"] as $domainInfo) {
                            $result["data"]["Domains"][] = $this->parseDomainInfo($domainInfo);
                        }
                    }

                }

                $result["result"] = "OK";

            } else {
                // Set error
                $result["result"] = "ERROR";
                $result["error"]  = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!");
            }
            return $result;
        });





        // Log last request and response

        return $response;
    }

    // Get TLD details
    public function GetTldList($count=20) {
        $parameters = [
            "request" => [
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                'IncludePriceDefinitions'=>1,
                'PageSize'=>$count
            ]
        ];


        //self::setRequestData($parameters);



        $result = self::parseCall(__FUNCTION__,$parameters, function ($response) {

            //self::setResponseData($response);


             $data = $response[key($response)];
             $result =[];

            // If DomainInfo a valid array
            if (isset($data["TldInfoList"]) && is_array($data["TldInfoList"])) {
                // Parse domain info

                $tlds = [];

                foreach ($data["TldInfoList"]['TldInfo'] as $k => $v) {

                    $pricing = [];
                    foreach ($v['PriceInfoList']['TldPriceInfo'] as $kp => $vp) {
                        $pricing[strtolower($vp['TradeType'])][$vp['Period']]=$vp['Price'];
                    }

                    $tlds[] = [
                        'id'        => $v['Id'],
                        'status'    => $v['Status'],
                        'maxchar'   => $v['MaxCharacterCount'],
                        'maxperiod' => $v['MaxRegistrationPeriod'],
                        'minchar'   => $v['MinCharacterCount'],
                        'minperiod' => $v['MinRegistrationPeriod'],
                        'tld'       => $v['Name'],
                        'pricing'=>$pricing
                    ];

                }

                $result=[
                    'data'=>$tlds,
                    'result'=>'OK'
                ];

            } else {
                // Set error
                $result=[
                    'result'=>'ERROR',
                    'error'=>$this->setError("INVALID_TLD_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!")
                ];
            }

            return $result;
        });


        return $result;
    }

    // Get domain details
    public function GetDetails($DomainName) {


        $parameters = [
            "request" => [
                "Password"   => $this->_USERDATA_PASSWORD,
                "UserName"   => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            ]
        ];


        // Log last request and response
        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {

            //self::setResponseData($response);


            $data = $response[key($response)];

            // If DomainInfo a valid array
            if (isset($data["DomainInfo"]) && is_array($data["DomainInfo"])) {
                // Parse domain info
                $result["data"]   = $this->parseDomainInfo($data["DomainInfo"]);
                $result["result"] = "OK";

            } else {
                // Set error
                $result["result"] = "ERROR";
                $result["error"]  = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!");
            }
            return $result;
        });





        return $response;
    }

    // Modify nameservers
    public function ModifyNameServer($DomainName, $NameServers) {
        $parameters = [
            "request" => [
                "Password"       => $this->_USERDATA_PASSWORD,
                "UserName"       => $this->_USERDATA_USERNAME,
                "DomainName"     => $DomainName,
                "NameServerList" => $NameServers
            ]
        ];


        // Log last request and response
        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            //self::setResponseData($parameters);


            $data = $response[key($response)];

            $result["data"]                = [];
            $result["data"]["NameServers"] = $parameters["request"]["NameServerList"];
            $result["result"]              = "OK";

            return $result;
        });


        return $response;
    }


    // Enable Theft Protection Lock
    public function EnableTheftProtectionLock($DomainName) {
        $parameters = [
            "request" => [
                "Password"   => $this->_USERDATA_PASSWORD,
                "UserName"   => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            ]
        ];


        // Log last request and response
        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response)  {

            //self::setResponseData($response);

            return [
                'data'   => [
                    'LockStatus' => true
                ],
                'result' => 'OK'
            ];

        });




        return $response;
    }


    // Disable Theft Protection Lock
    public function DisableTheftProtectionLock($DomainName) {
        $parameters = [
            "request" => [
                "Password"   => $this->_USERDATA_PASSWORD,
                "UserName"   => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            ]
        ];


        // Log last request and response
        //self::setRequestData($parameters);
        $response = self::parseCall(__FUNCTION__, $parameters, function ($response)  {

            //self::setResponseData($response);

            return [
                'data'   => [
                    'LockStatus' => false
                ],
                'result' => 'OK'
            ];
        });


        return $response;
    }

    // CHILD NAMESERVER MANAGEMENT

    // Add Child Nameserver
    public function AddChildNameServer($DomainName, $NameServer, $IPAdresses) {
        $parameters = [
            "request" => [
                "Password"        => $this->_USERDATA_PASSWORD,
                "UserName"        => $this->_USERDATA_USERNAME,
                "DomainName"      => $DomainName,
                "ChildNameServer" => $NameServer,
                "IpAddressList"   => [$IPAdresses]
            ]
        ];


        // Log last request and response
        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use($parameters)  {
            //self::setResponseData($response);
            return [
                'data'   => [
                    'NameServer' => $parameters["request"]["ChildNameServer"],
                    'IPAdresses' => $parameters["request"]["IpAddressList"]
                ],
                'result' => 'OK'
            ];
        });

        return $response;
    }


    // Delete Child Nameserver
    public function DeleteChildNameServer($DomainName, $NameServer) {
        $parameters = [
            "request" => [
                "Password"        => $this->_USERDATA_PASSWORD,
                "UserName"        => $this->_USERDATA_USERNAME,
                "DomainName"      => $DomainName,
                "ChildNameServer" => $NameServer
            ]
        ];

        // Log last request and response
        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use($parameters)  {

            //self::setResponseData($response);

            return [
                'data'   => [
                    'NameServer' => $parameters["request"]["ChildNameServer"],
                ],
                'result' => 'OK'
            ];
        });

        return $response;

    }


    // Modify Child Nameserver
    public function ModifyChildNameServer($DomainName, $NameServer, $IPAdresses) {

        $parameters = [
            "request" => [
                "Password"        => $this->_USERDATA_PASSWORD,
                "UserName"        => $this->_USERDATA_USERNAME,
                "DomainName"      => $DomainName,
                "ChildNameServer" => $NameServer,
                "IpAddressList"   => [$IPAdresses]
            ]
        ];


        // Log last request and response
        //self::setRequestData($parameters);
        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use($parameters)  {

            //self::setResponseData($response);

            return [
                'data'   => [
                    'NameServer' => $parameters["request"]["ChildNameServer"],
                    'IPAdresses' => $parameters["request"]["IpAddressList"]
                ],
                'result' => 'OK'
            ];
        });



        return $response;
    }

    // CONTACT MANAGEMENT

    // Get Domain Contact information
    public function GetContacts($DomainName) {
        $parameters = [
            "request" => [
                "Password"   => $this->_USERDATA_PASSWORD,
                "UserName"   => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            ]
        ];


        // Log last request and response
        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            //self::setResponseData($response);
            $data = $response[key($response)];

            $result = [];

            // If ContactInfo a valid array
            if (isset($data["AdministrativeContact"]) && is_array($data["AdministrativeContact"]) && isset($data["TechnicalContact"]) && is_array($data["TechnicalContact"]) && isset($data["RegistrantContact"]) && is_array($data["RegistrantContact"]) && isset($data["BillingContact"]) && is_array($data["BillingContact"])) {
                // Parse domain info

                $result = [
                    'data'   => [
                        'contacts' => [
                            'Administrative' => $this->parseContactInfo($data["AdministrativeContact"]),
                            'Billing'        => $this->parseContactInfo($data["BillingContact"]),
                            'Registrant'     => $this->parseContactInfo($data["RegistrantContact"]),
                            'Technical'      => $this->parseContactInfo($data["TechnicalContact"]),
                        ]
                    ],
                    'result' => 'OK'
                ];

            } else {
                // Set error
                $result = [
                    'error'  => $this->setError("INVALID_CONTACT_INTO", "Invalid response received from server!", "Contact info is not a valid array or more than one contact info has returned!"),
                    'result' => 'ERROR'
                ];
            }
            return $result;
        });



        return $response;
    }


    // Set domain cantacts
    public function SaveContacts($DomainName, $Contacts) {
        $parameters = [
            "request" => [
                "Password"              => $this->_USERDATA_PASSWORD,
                "UserName"              => $this->_USERDATA_USERNAME,
                "DomainName"            => $DomainName,
                "AdministrativeContact" => $Contacts["Administrative"],
                "BillingContact"        => $Contacts["Billing"],
                "TechnicalContact"      => $Contacts["Technical"],
                "RegistrantContact"     => $Contacts["Registrant"]
            ]
        ];


        // Log last request and response
        //self::setRequestData($parameters);
        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {

            //self::setResponseData($response);

            $data = $response[key($response)];

            $result = [];

            if ($data['OperationResult']=='SUCCESS') {
                $result=[
                    'result'=>'OK'
                ] ;
            } else {
                // Set error
                $result=[
                    'result'=>'ERROR',
                    'error'=>$this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!")
                ];

            }
            return $result;
        });


        return $response;
    }

    // DOMAIN TRANSFER (INCOMING DOMAIN)

    // Start domain transfer (Incoming domain)
    public function Transfer($DomainName, $AuthCode,$Period) {
        $parameters = [
            "request" => [
                "Password"             => $this->_USERDATA_PASSWORD,
                "UserName"             => $this->_USERDATA_USERNAME,
                "DomainName"           => $DomainName,
                "AuthCode"             => $AuthCode,
                'AdditionalAttributes' => [
                    'KeyValueOfstringstring' => [
                        [
                            'Key'   => 'TRANSFERPERIOD',
                            'Value' => $Period
                        ]
                    ]
                ]
            ]
        ];

        // Log last request and response
        //self::setRequestData($parameters);
        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            //self::setResponseData($response);

            $result = [];
            $data = $response[key($response)];
                // If DomainInfo a valid array
                if (isset($data["DomainInfo"]) && is_array($data["DomainInfo"])) {
                    // Parse domain info
                    $result=[
                        'result'=>'OK',
                        'data'=>$this->parseDomainInfo($data["DomainInfo"])
                    ];

                } else {
                    // Set error
                    $result=[
                        'result'=>'ERROR',
                        'data'=>$this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!")
                    ];
                }
            return $result;
        });





        return $response;
    }


    // Cancel domain transfer (Incoming domain)
    public function CancelTransfer($DomainName) {
        $parameters = [
            "request" => [
                "Password"   => $this->_USERDATA_PASSWORD,
                "UserName"   => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            ]
        ];

        // Log last request and response
        //self::setRequestData($parameters);

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {



            //self::setResponseData($response);

             $data = $response[key($response)];

            return [
                'result'=>$data['OperationResult']=='SUCCESS'?'OK':'ERROR',
                'data'=>[
                    'DomainName'=>$parameters["request"]["DomainName"]
                ]
            ];
        });

        return $response;
    }


    // Renew domain
    public function Renew($DomainName, $Period) {
        $parameters = [
            "request" => [
                "Password"   => $this->_USERDATA_PASSWORD,
                "UserName"   => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "Period"     => $Period
            ]
        ];

        // Log last request and response
        //self::setRequestData($parameters);
        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {

            //self::setResponseData($response);

            $data = $response[key($response)];

            return [
                'result'=>'OK',
                'data'=>[
                    'ExpirationDate'=> $data["ExpirationDate"] ?? null
                ]
            ];

        });

        return $response;
    }


    // Register domain with contact information
    public function RegisterWithContactInfo($DomainName, $Period, $Contacts, $NameServers = ["dns.domainnameapi.com", "web.domainnameapi.com"],  $TheftProtectionLock = true, $PrivacyProtection = false,$addionalAttributes=[]) {
        $parameters = [
            "request" => [
                "Password"                => $this->_USERDATA_PASSWORD,
                "UserName"                => $this->_USERDATA_USERNAME,
                "DomainName"              => $DomainName,
                "Period"                  => $Period,
                "NameServerList"          => $NameServers,
                "LockStatus"              => $TheftProtectionLock,
                "PrivacyProtectionStatus" => $PrivacyProtection,
                "AdministrativeContact"   => $Contacts["Administrative"],
                "BillingContact"          => $Contacts["Billing"],
                "TechnicalContact"        => $Contacts["Technical"],
                "RegistrantContact"       => $Contacts["Registrant"]
            ]
        ];

        if(count($addionalAttributes)>0){
            foreach ($addionalAttributes as $k => $v) {
                $parameters['request']['AdditionalAttributes']['KeyValueOfstringstring'][]=['Key'=>$k,'Value'=>$v];
            }
        }


        // Log last request and response
        //self::setRequestData($parameters);
        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
        //self::setResponseData($response);

            $result = [];
            $data   = $response[key($response)];

            // If DomainInfo a valid array
            if (isset($data["DomainInfo"]) && is_array($data["DomainInfo"])) {
                // Parse domain info
                $result = [
                    'result' => 'OK',
                    'data'   => $this->parseDomainInfo($data["DomainInfo"])
                ];


            } else {
                // Set error
                $result = [
                    'result' => 'ERROR',
                    'error'  => $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!")
                ];

            }
            return $result;

        });




        return $response;
    }


    // Modify privacy protection status of domain
    public function ModifyPrivacyProtectionStatus($DomainName, $Status, $Reason = "Owner request") {
        if (trim($Reason) == "") {
            $Reason = "Owner request";
        }

        $parameters = [
            "request" => [
                "Password"       => $this->_USERDATA_PASSWORD,
                "UserName"       => $this->_USERDATA_USERNAME,
                "DomainName"     => $DomainName,
                "ProtectPrivacy" => $Status,
                "Reason"         => $Reason
            ]
        ];

        // Log last request and response
        //self::setRequestData($parameters);
        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
        //self::setResponseData($response);
            return [
                'data'=>[
                    'PrivacyProtectionStatus'=>$parameters["request"]["ProtectPrivacy"]
                ]  ,
                'result'=>'OK'
            ];
        });



        return $response;
    }


    // Sync domain
    public function SyncFromRegistry($DomainName) {
        $parameters = [
            "request" => [
                "Password"   => $this->_USERDATA_PASSWORD,
                "UserName"   => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            ]
        ];

        // Log last request and response
        //self::setRequestData($parameters);
        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            //self::setResponseData($response);

            $result = [];
            $data   = $response[key($response)];

            // If DomainInfo a valid array
            if (isset($data["DomainInfo"]) && is_array($data["DomainInfo"])) {
                // Parse domain info
                $result = [
                    'data'   => $this->parseDomainInfo($data["DomainInfo"]),
                    'result' => 'OK'
                ];

            } else {
                // Set error
                $result = [
                    'error'  => $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!"),
                    'result' => 'ERROR'
                ];
            }

            return $result;


        });

        return $response;
    }


     // Convert object to array
    private function objectToArray($_obj) {
        try {
            $_obj = json_decode(json_encode($_obj), true);
        } catch (Exception $ex) {
        }
        return $_obj;
    }

    // Get error if exists
    private function parseError($response) {
        $result = false;

        if (is_null($response)) {
            // Set error data
            $result            = [];
            $result["Code"]    = "INVALID_RESPONSE";
            $result["Message"] = "Invalid response or no response received from server!";
            $result["Details"] = "SOAP Connection returned null value!";
        } elseif (!is_array($response)) {
            // Set error data
            $result            = [];
            $result["Code"]    = "INVALID_RESPONSE";
            $result["Message"] = "Invalid response or no response received from server!";
            $result["Details"] = "SOAP Connection returned non-array value!";
        } elseif (strtolower(key($response)) == "faultstring") {
            // Handle soap fault

            $result            = [];
            $result["Code"]    = "";
            $result["Message"] = "";
            $result["Details"] = "";

            // Set error data
            if (isset($response["faultcode"])) {
                $result["Code"] = $response["faultcode"];
            }
            if (isset($response["faultstring"])) {
                $result["Message"] = $response["faultstring"];
            }
            if (isset($response["detail"])) {
                if (is_array($response["detail"])) {
                    if (isset($response["detail"]["ExceptionDetail"])) {
                        if (is_array($response["detail"]["ExceptionDetail"])) {
                            if (isset($response["detail"]["ExceptionDetail"]["StackTrace"])) {
                                $result["Details"] = $response["detail"]["ExceptionDetail"]["StackTrace"];
                            }
                        }
                    }

                }
            }

        } elseif (count($response) != 1) {
            // Set error data
            $result            = [];
            $result["Code"]    = "INVALID_RESPONSE";
            $result["Message"] = "Invalid response or no response received from server!";
            $result["Details"] = "Response data contains more than one result! Only one result accepted!";
        } elseif (!isset($response[key($response)]["OperationResult"]) || !isset($response[key($response)]["ErrorCode"])) {
            // Set error data
            $result            = [];
            $result["Code"]    = "INVALID_RESPONSE";
            $result["Message"] = "Invalid response or no response received from server!";
            $result["Details"] = "Operation result or Error code not received from server!";
        } elseif (strtoupper($response[key($response)]["OperationResult"]) != "SUCCESS") {
            // Set error data
            $result            = [
                "Code"    => '',
                "Message" => 'Failed',
                "Details" => '',
            ];

            if (isset($response[key($response)]["OperationMessage"])) {
                $result["Code"] = "API_" . $response[key($response)]["ErrorCode"];
            }

            if (isset($response[key($response)]["OperationResult"])) {
                $result["Code"] .= "_" . $response[key($response)]["OperationResult"];
            }

            if (isset($response[key($response)]["OperationMessage"])) {
                $result["Details"] = $response[key($response)]["OperationMessage"];
            }

        }

        return $result;
    }

    // Check if response contains error
    private function hasError($response) {
        return ($this->parseError($response) === false) ? false : true;
    }

    // Set error message
    private function setError($Code, $Message, $Details) {
        $result            = [];
        $result["Code"]    = $Code;
        $result["Message"] = $Message;
        $result["Details"] = $Details;
        return $result;
    }

    // Parse domain info
    private function parseDomainInfo($data) {
        $result                                     = [];
        $result["ID"]                               = "";
        $result["Status"]                           = "";
        $result["DomainName"]                       = "";
        $result["AuthCode"]                         = "";
        $result["LockStatus"]                       = "";
        $result["PrivacyProtectionStatus"]          = "";
        $result["IsChildNameServer"]                = "";
        $result["Contacts"]                         = [];
        $result["Contacts"]["Billing"]              = [];
        $result["Contacts"]["Technical"]            = [];
        $result["Contacts"]["Administrative"]       = [];
        $result["Contacts"]["Registrant"]           = [];
        $result["Contacts"]["Billing"]["ID"]        = "";
        $result["Contacts"]["Technical"]["ID"]      = "";
        $result["Contacts"]["Administrative"]["ID"] = "";
        $result["Contacts"]["Registrant"]["ID"]     = "";
        $result["Dates"]                            = [];
        $result["Dates"]["Start"]                   = "";
        $result["Dates"]["Expiration"]              = "";
        $result["Dates"]["RemainingDays"]           = "";
        $result["NameServers"]                      = [];
        $result["Additional"]                       = [];
        $result["ChildNameServers"]                 = [];

        foreach ($data as $attrName => $attrValue) {
            switch ($attrName) {
                case "Id":
                    if (is_numeric($attrValue)) {
                        $result["ID"] = $attrValue;
                    }
                    break;


                case "Status":

                    $result["Status"] = $attrValue;
                    break;


                case "DomainName":

                    $result["DomainName"] = $attrValue;
                    break;


                case "AdministrativeContactId":

                    if (is_numeric($attrValue)) {
                        $result["Contacts"]["Administrative"]["ID"] = $attrValue;
                    }
                    break;


                case "BillingContactId":

                    if (is_numeric($attrValue)) {
                        $result["Contacts"]["Billing"]["ID"] = $attrValue;
                    }
                    break;


                case "TechnicalContactId":

                    if (is_numeric($attrValue)) {
                        $result["Contacts"]["Technical"]["ID"] = $attrValue;
                    }
                    break;


                case "RegistrantContactId":

                    if (is_numeric($attrValue)) {
                        $result["Contacts"]["Registrant"]["ID"] = $attrValue;
                    }
                    break;


                case "Auth":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["AuthCode"] = $attrValue;
                    }
                    break;


                case "StartDate":

                    $result["Dates"]["Start"] = $attrValue;
                    break;


                case "ExpirationDate":

                    $result["Dates"]["Expiration"] = $attrValue;
                    break;


                case "LockStatus":

                    if (is_bool($attrValue)) {
                        $result["LockStatus"] = var_export($attrValue, true);
                    }
                    break;


                case "PrivacyProtectionStatus":

                    if (is_bool($attrValue)) {
                        $result["PrivacyProtectionStatus"] = var_export($attrValue, true);
                    }
                    break;


                case "IsChildNameServer":

                    if (is_bool($attrValue)) {
                        $result["IsChildNameServer"] = var_export($attrValue, true);
                    }
                    break;


                case "RemainingDay":

                    if (is_numeric($attrValue)) {
                        $result["Dates"]["RemainingDays"] = $attrValue;
                    }
                    break;


                case "NameServerList":

                    if (is_array($attrValue)) {
                        foreach ($attrValue as $nameserverValue) {
                            $result["NameServers"] = $nameserverValue;
                        }
                    }
                    break;


                case "AdditionalAttributes":

                    if (is_array($attrValue)) {

                        if (isset($attrValue["KeyValueOfstringstring"])) {
                            foreach ($attrValue["KeyValueOfstringstring"] as $attribute) {
                                if (isset($attribute["Key"]) && isset($attribute["Value"])) {
                                    $result["Additional"][$attribute["Key"]] = $attribute["Value"];
                                }
                            }
                        }
                    }
                    break;


                case "ChildNameServerInfo":
                    if (is_array($attrValue)) {

                            if (isset($attrValue["ChildNameServerInfo"]) && is_array($attrValue["ChildNameServerInfo"]) && count($attrValue["ChildNameServerInfo"]) > 0) {

                                foreach ($attrValue["ChildNameServerInfo"] as $attribute) {

                                    if (isset($attribute["ChildNameServer"]) && isset($attribute["IpAddress"])) {
                                        $ns          = "";
                                        $IpAddresses = [];

                                        // Name of NameServer
                                        if (is_string($attribute["ChildNameServer"])) {
                                            $ns = $attribute["ChildNameServer"];
                                        }

                                        // IP adresses of NameServer
                                        if (is_array($attribute["IpAddress"]) && isset($attribute["IpAddress"]["string"])) {

                                            if (is_array($attribute["IpAddress"]["string"])) {

                                                foreach ($attribute["IpAddress"]["string"] as $ip) {
                                                    if (isset($ip) && is_string($ip)) {
                                                        $IpAddresses = $ip;
                                                    }
                                                }

                                            } elseif (is_string($attribute["IpAddress"]["string"])) {
                                                $IpAddresses = $attribute["IpAddress"]["string"];
                                            }

                                        }

                                        $result["ChildNameServers"][] = [
                                            "ns"  => $ns,
                                            "ip" => $IpAddresses
                                        ];


                                    }

                                }

                            }



                    }
                    break;

            }

        }

        return $result;
    }

    // Parse Contact info
    private function parseContactInfo($data) {
        $result                                  = [];
        $result["ID"]                            = "";
        $result["Status"]                        = "";
        $result["Additional"]                    = [];
        $result["Address"]                       = [];
        $result["Address"]["Line1"]              = "";
        $result["Address"]["Line2"]              = "";
        $result["Address"]["Line3"]              = "";
        $result["Address"]["State"]              = "";
        $result["Address"]["City"]               = "";
        $result["Address"]["Country"]            = "";
        $result["Address"]["ZipCode"]            = "";
        $result["Phone"]                         = [];
        $result["Phone"]["Phone"]                = [];
        $result["Phone"]["Phone"]["Number"]      = "";
        $result["Phone"]["Phone"]["CountryCode"] = "";
        $result["Phone"]["Fax"]["Number"]        = "";
        $result["Phone"]["Fax"]["CountryCode"]   = "";
        $result["AuthCode"]                      = "";
        $result["FirstName"]                     = "";
        $result["LastName"]                      = "";
        $result["Company"]                       = "";
        $result["EMail"]                         = "";
        $result["Type"]                          = "";

        foreach ($data as $attrName => $attrValue) {
            switch ($attrName) {
                case "Id":

                    if (is_numeric($attrValue)) {
                        $result["ID"] = $attrValue;
                    }
                    break;


                case "Status":

                    $result["Status"] = $attrValue;
                    break;


                case "AdditionalAttributes":

                    if (is_array($attrValue)) {

                        if (isset($attrValue["KeyValueOfstringstring"])) {
                            foreach ($attrValue["KeyValueOfstringstring"] as $attribute) {
                                if (isset($attribute["Key"]) && isset($attribute["Value"])) {
                                    $result["Additional"][$attribute["Key"]] = $attribute["Value"];
                                }
                            }
                        }
                    }
                    break;


                case "AddressLine1":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Address"]["Line1"] = $attrValue;
                    }
                    break;


                case "AddressLine2":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Address"]["Line2"] = $attrValue;
                    }
                    break;


                case "AddressLine3":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Address"]["Line3"] = $attrValue;
                    }
                    break;


                case "Auth":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["AuthCode"] = $attrValue;
                    }
                    break;


                case "City":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Address"]["City"] = $attrValue;
                    }
                    break;


                case "Company":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Company"] = $attrValue;
                    }
                    break;


                case "Country":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Address"]["Country"] = $attrValue;
                    }
                    break;


                case "EMail":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["EMail"] = $attrValue;
                    }
                    break;


                case "Fax":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Phone"]["Fax"]["Number"] = $attrValue;
                    }
                    break;


                case "FaxCountryCode":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Phone"]["Fax"]["CountryCode"] = $attrValue;
                    }
                    break;


                case "Phone":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Phone"]["Phone"]["Number"] = $attrValue;
                    }
                    break;


                case "PhoneCountryCode":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Phone"]["Phone"]["CountryCode"] = $attrValue;
                    }
                    break;


                case "FirstName":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["FirstName"] = $attrValue;
                    }
                    break;


                case "LastName":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["LastName"] = $attrValue;
                    }
                    break;


                case "State":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Address"]["State"] = $attrValue;
                    }
                    break;


                case "ZipCode":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Address"]["ZipCode"] = $attrValue;
                    }
                    break;


                case "Type":

                    if (is_string($attrValue) && !is_null($attrValue)) {
                        $result["Type"] = $attrValue;
                    }
                    break;


            }

        }

        return $result;
    }

    private function parseCall($fn,$parameters, $_callback){

        $result=[
            'result'=>'ERROR',
            'error'=>'Unknown Error Occured'
        ];

        try {

            // SOAP method which is same as current function name called
            $_response = $this->service->__soapCall($fn, [$parameters]);

            //die(jsson_encode($_response));

            //file_put_contents('_lastreq.txt',$this->service->__getLastRequest());
            //print_r($this->service->__getLastRequestHeaders());
            //print_r($this->service->__getLastResponseHeaders());

            // Serialize as array
            $_response = $this->objectToArray($_response);

            self::setRequestData($parameters);
            self::setResponseData($_response);


            // Check is there any error?
            if (!$this->hasError($_response)) {

                $result = $_callback($_response);

            } else {
                // Hata mesajini dondur
                $result["result"] = "ERROR";
                $result["error"]  = $this->parseError($_response);
            }

        } catch (\SoapFault $ex) {
            $result["result"] = "ERROR";
            $result["error"]  = $this->setError('INVALID_RESPONSE','Invalid Response occured',$ex->getMessage());
        } catch (Exception $ex) {
            $result["result"] = "ERROR";
            $result["error"]  = $this->parseError($this->objectToArray($ex));
        }


        return $result;

    }


    /**
     * Domain is TR type
     * @param $domain
     * @return bool
     */
    public function isTrTLD($domain){
        preg_match('/\.com\.tr|\.net\.tr|\.org\.tr|\.biz\.tr|\.info\.tr|\.tv\.tr|\.gen\.tr|\.web\.tr|\.tel\.tr|\.name\.tr|\.bbs\.tr|\.gov\.tr|\.bel\.tr|\.pol\.tr|\.edu\.tr|\.k12\.tr|\.av\.tr|\.dr\.tr$/', $domain, $result);

        return isset($result[0]);
    }



}
