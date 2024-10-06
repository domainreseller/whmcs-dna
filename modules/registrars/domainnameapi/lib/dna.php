<?php
/**
 * Created by PhpStorm.
 * User: bunyaminakcay
 * Project name whmcs-dna
 * 20.11.2022 00:13
 * Bünyamin AKÇAY <bunyamin@bunyam.in>
 */

/**
 * Class DomainNameAPI_PHPLibrary
 * @package DomainNameApi
 * @version 2.0.22
 */

/*
 * This library was written really long before the PSR-7 standards and was not structured according to most coding disciplines. It has only optimized from legacy version.
 * The code inherited from the 1st version has been revamped to create the 2nd version, and a complete overhaul is planned for the 3rd version.
 */
namespace DomainNameApi;

use Exception;
use SoapClient;
use SoapFault;

class DomainNameAPI_PHPLibrary
{
    /**
     * Version of the library
     */
    const VERSION = '2.0.22';

    /**
     * Error reporting enabled
     */
    private bool $errorReportingEnabled = true;
    /**
     * Error Reporting Will send this sentry endpoint, if errorReportingEnabled is true
     * This request does not include sensitive informations, sensitive informations are filtered.
     * @var string $errorReportingDsn
     */
    private string $errorReportingDsn = 'https://d4e2d61e4af2d4c68fb21ab93bf51ff2@o4507492369039360.ingest.de.sentry.io/4507492373954640';

    /**
     * Api Username
     *
     * @var string $serviceUsername
     */
    private string $serviceUsername = "ownername";

    /*
     * Api Password
     * @var string $servicePassword
     */
    private string $servicePassword = "ownerpass";

    /**
     * Api Service Soap URL
     * @var string $serviceUrl
     */
    private string $serviceUrl         = "https://whmcs.domainnameapi.com/DomainApi.svc";
    public  array $lastRequest        = [];
    public  array $lastResponse       = [];
    public  array $lastParsedResponse = [];
    public string $lastFunction       = '';
    private SoapClient $service;
    private $startAt;


    /**
     * DomainNameAPI_PHPLibrary constructor.
     * @param string $userName
     * @param string $password
     * @param bool $testMode
     * @throws Exception | SoapFault
     */
    public function __construct($userName = "ownername", $password = "ownerpass", $testMode = false)
    {
        $this->startAt = microtime(true);
        self::setCredentials($userName, $password);
        self::useTestMode($testMode);

        try {
        // Create unique connection
            $this->service = new SoapClient($this->serviceUrl . "?singlewsdl", [
            "encoding"   => "UTF-8",
            'features'   => SOAP_SINGLE_ELEMENT_ARRAYS,
            'exceptions' => true,
                'connection_timeout' => 20,
        ]);
        } catch (SoapFault $e) {
            $this->sendErrorToSentryAsync($e);
            throw new Exception("SOAP Connection Error: " . $e->getMessage());
        } catch (Exception $e) {
            $this->sendErrorToSentryAsync($e);
            throw new Exception("SOAP Error: " . $e->getMessage());
        }
    }


    /**
     * Deprecated
     * @param bool $value
     */
    private function useTestMode($value = true) {
        //if ($value === true || $value == 'on') {
        //    $this->serviceUsername = 'test1.dna@apiname.com';
        //    $this->servicePassword = 'FsUvpJMzQ69scpqE';
        //}
    }


    /**
     * SET Username and Password
     * @param $userName
     * @param $password
     * @return void
     */
    private function setCredentials($userName, $password)
    {
        $this->serviceUsername = $userName;
        $this->servicePassword = $password;
    }


    /**
     * This method returns the last request sent to the API
     * @return array|mixed
     */
    public function getRequestData(){
        return $this->lastRequest;
    }

    /**
     * This method sets the last request sent to the API
     * @return DomainNameAPI_PHPLibrary
     */
    public function setRequestData($request){
        $this->lastRequest = $request;
        return $this;
    }

    /**
     * This method returns the last response from the API
     * @return array|mixed
     */
    public function getResponseData(){
        return $this->lastResponse;
    }

    /**
     * This method sets the last response from the API
     * @return array|mixed
     */
    public function setResponseData($response)
    {
        $this->lastResponse = $response;
        return $this;
    }

    /**
     * This method returns the last parsed response from the API
     * @return array|mixed
     */
    public function getParsedResponseData()
    {
        return $this->lastParsedResponse;
    }

    /**
     * This method sets the last parsed response from the API
     * @return array|mixed
     */
    public function setParsedResponseData($response)
    {
        $this->lastParsedResponse = $response;
        return $this;
    }

    /**
     * This method returns the last function called
     * @return string
     */
    public function getLastFunction()
    {
        return $this->lastFunction;
    }

    /**
     * This method sets the last function called
     * @return DomainNameAPI_PHPLibrary
     */
    public function setLastFunction($function)
    {
        $this->lastFunction = $function;
        return $this;
    }

    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    public function setServiceUrl($url)
    {
        $this->serviceUrl = $url;
    }


    /**
     * This method sends anonymous error data to the Sentry server, if error reporting is enabled
     *
     * @return void
     */
    private function sendErrorToSentryAsync(Exception $e)
    {
        if (!$this->errorReportingEnabled) {
            return;
        }

        $skipped_errors = [
            'Domain not found',
            'ERR_DOMAIN_NOT_FOUND',
            'Reseller not found'
        ];

        foreach ($skipped_errors as $ek => $ev) {
            if(strpos($e->getMessage(),$ev) !== false){
                return ;
            }
        }

        $elapsed_time = microtime(true) - $this->startAt;
        $parsed_dsn = parse_url($this->errorReportingDsn);

        // API URL'si
        $host       = $parsed_dsn['host'];
        $project_id = ltrim($parsed_dsn['path'], '/');
        $public_key = $parsed_dsn['user'];
        $secret_key = $parsed_dsn['pass'] ?? null;
        $api_url    = "https://$host/api/$project_id/store/";

        $external_ip = $this->getServerIp();



        // Hata verisi
        $errorData = [
            'event_id'  => bin2hex(random_bytes(16)),
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'level'     => 'error',
            'logger'    => 'php',
            'platform'  => 'php',
            'culprit'   => __FILE__,
            'message'   => [
                'formatted' => $e->getMessage()
            ],
            'exception' => [
                'values' => [
                    [
                        'type'       =>  str_replace(['DomainNameApi\DomainNameAPI_PHPLibrary'],['DNALib Exception'],self::class),
                        'value'      => $e->getMessage(),
                        'stacktrace' => [
                            'frames' => [
                                [
                                    'filename' => $e->getFile(),
                                    'lineno'   => $e->getLine(),
                                    'function' => str_replace([dirname(__DIR__),'DomainNameApi\DomainNameAPI_PHPLibrary'],['.','Lib'],$e->getTraceAsString()),
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            'tags'      => [
                'handled'         => 'yes',
                'level'           => 'error',
                'release'         => self::VERSION,
                'environment'     => 'production',
                'url'             => $_SERVER['REQUEST_URI'] ?? 'unknown',
                'transaction'     => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
                'status_code'     => http_response_code(),
                'trace_id'        => bin2hex(random_bytes(8)), // Trace ID örneği
                'runtime_name'    => 'PHP',
                'runtime_version' => phpversion(),
                'ip_address'      => $external_ip,
                'elapsed_time'    => number_format($elapsed_time, 4),

            ],
            'extra'     => [
                'request_data'  => $this->getRequestData(),
                'response_data' => $this->getResponseData(),
            ]
        ];

        // Sentry başlığı
        $sentry_auth = [
            'sentry_version=7',
            'sentry_client=whmcslib-php/' . self::VERSION,
            "sentry_key=$public_key"
        ];
        if ($secret_key) {
            $sentry_auth[] = "sentry_secret=$secret_key";
        }
        $sentry_auth_header = 'X-Sentry-Auth: Sentry ' . implode(', ', $sentry_auth);

        if(function_exists('escapeshellarg') && function_exists('exec')){
            $cmd = 'curl -X POST ' . escapeshellarg($api_url) . ' -H ' . escapeshellarg('Content-Type: application/json') . ' -H ' . escapeshellarg($sentry_auth_header) . ' -d ' . escapeshellarg(json_encode($errorData)) . ' > /dev/null 2>&1 &';
            exec($cmd);
        }else{
             $jsonData = json_encode($errorData);
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
                curl_setopt($ch, CURLOPT_TIMEOUT, 2);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    'Content-Type: application/json',
                    $sentry_auth_header
                ]);
                curl_exec($ch);
                curl_close($ch);
        }
    }

    private function getServerIp()
    {
        $cache_ttl    = 512; // Cache süresi 512 saniye
        $cache_key    = 'external_ip';
        $cache_file   = __DIR__ . '/ip_addr.cache';
        $current_time = time();

        if (function_exists('apcu_fetch')) {
            // APCu ile cache kontrolü
            $external_ip = apcu_fetch($cache_key);
            if ($external_ip !== false) {
                return $external_ip;
            }
        } elseif (file_exists($cache_file) && ($current_time - filemtime($cache_file) < $cache_ttl)) {
            // Dosya ile cache kontrolü
            return file_get_contents($cache_file);
        }

        // IP adresini alma ve cacheleme
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "http://ipecho.net/plain");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            $external_ip = curl_exec($ch);
            curl_close($ch);

            if ($external_ip !== false) {
                // APCu ile cachele
                if (function_exists('apcu_store')) {
                    apcu_store($cache_key, $external_ip, $cache_ttl);
                }
                // Dosya ile cachele
                file_put_contents($cache_file, $external_ip);
            }

            return $external_ip;
        } catch (Exception $e) {
            return 'unknown';
        }
    }


    /**
     * Get Current account details with balance
     */
    public function GetResellerDetails() {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                'CurrencyId'=>2 // 1: TRY, 2: USD
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {

            $data = $response[key($response)];
            $resp = [];

            if (isset($data['ResellerInfo'])) {
                $resp['result'] = 'OK';
                $resp['id']       = $data['ResellerInfo']['Id'];
                $resp['active']   = $data['ResellerInfo']['Status'] == 'Active';
                $resp['name']     = $data['ResellerInfo']['Name'];

                $active_currency = $data['ResellerInfo']['BalanceInfoList']['BalanceInfo'][0];
                $balances = [];
                foreach ($data['ResellerInfo']['BalanceInfoList']['BalanceInfo'] as  $v) {
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



        return $response;
    }

    /**
     * Get Current primary Balance for your account
     */
    public function GetCurrentBalance($currencyId = 2)
    {
        if (strtoupper($currencyId) == 'USD') {
            $currencyId = 2;
        } elseif (in_array(strtoupper($currencyId), ['TRY', 'TL', '1'])) {
            $currencyId = 1;
        }else{
            $currencyId = 2;
        }


        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                'CurrencyId' => $currencyId
            ]
        ];



        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {

            return $response['GetCurrentBalanceResult'];

        });




        return $response;
    }


    /**
     * Check Availability , SLD and TLD must be in array
     * @param array $Domains
     * @param array $TLDs
     * @param int $Period
     * @param string $Command
     * @return array
     */
    public function CheckAvailability($domains, $extensions, $period, $Command)
    {
        $parameters = [
            "request" => [
                "Password"       => $this->servicePassword,
                "UserName"       => $this->serviceUsername,
                "DomainNameList" => $domains,
                "TldList"        => $extensions,
                "Period"         => $period,
                "Commad"         => $Command
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {



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
                    "TLD"        => $value["Tld"],
                    "DomainName" => $value["DomainName"],
                    "Status"     => $value["Status"],
                    "Command"    => $value["Command"],
                    "Period"     => $value["Period"],
                    "IsFee"      => $value["IsFee"],
                    "Price"      => $value["Price"],
                    "Currency"   => $value["Currency"],
                    "Reason"     => $value["Reason"],
                ];

            }

            return $available;
        });



        // Log last request and response

        return $response;
    }

    /**
     * Get Domain List 0f your account
     * @return array
     */
    public function GetList($extra_parameters=[]) {

        $parameters = [
            "request" => [
                "Password" => $this->servicePassword,
                "UserName" => $this->serviceUsername,
            ]
        ];

        foreach ($extra_parameters as $k => $v) {
            $parameters['request'][$k] = $v;
        }

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {



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
                $result["TotalCount"] = $data["TotalCount"];

            } else {
                // Set error
                $result["result"] = "ERROR";
                $result["error"]  = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!",
                    "Domain info is not a valid array or more than one domain info has returned!");

                $this->sendErrorToSentryAsync(new Exception("INVALID_DOMAIN_LIST: Invalid response received from server! Domain info is not a valid array or more than one domain info has returned!"));
            }
            return $result;
        });





        // Log last request and response

        return $response;
    }

    /**
     * Return tld list and pricing matrix , required for price and tld sync
     * @param int $count
     */
    public function GetTldList($count = 20) {
        $parameters = [
            "request" => [
                "Password"                => $this->servicePassword,
                "UserName"                => $this->serviceUsername,
                'IncludePriceDefinitions'=>1,
                'PageSize'=>$count
            ]
        ];



        $result = self::parseCall(__FUNCTION__,$parameters, function ($response) {



             $data = $response[key($response)];
             $result =[];

            // If DomainInfo a valid array
            if (isset($data["TldInfoList"]) && is_array($data["TldInfoList"])) {
                // Parse domain info

                $extensions = [];

                foreach ($data["TldInfoList"]['TldInfo'] as $k => $v) {

                    $pricing = $currencies =[];
                    foreach ($v['PriceInfoList']['TldPriceInfo'] as $kp => $vp) {
                        $pricing[strtolower($vp['TradeType'])][$vp['Period']]=$vp['Price'];
                        $currencies[strtolower($vp['TradeType'])]=$vp['CurrencyName'];
                    }

                    $extensions[] = [
                        'id'        => $v['Id'],
                        'status'    => $v['Status'],
                        'maxchar'   => $v['MaxCharacterCount'],
                        'maxperiod' => $v['MaxRegistrationPeriod'],
                        'minchar'   => $v['MinCharacterCount'],
                        'minperiod' => $v['MinRegistrationPeriod'],
                        'tld'       => $v['Name'],
                        'pricing'=>$pricing,
                        'currencies'=>$currencies,
                    ];

                }

                $result=[
                    'data'   => $extensions,
                    'result'=>'OK'
                ];

            } else {
                // Set error
                $result=[
                    'result'=>'ERROR',
                    'error'=>$this->setError("INVALID_TLD_LIST", "Invalid response received from server!", "Domain info is not a valid array or more than one domain info has returned!")
                ];
                $this->sendErrorToSentryAsync(new Exception("INVALID_TLD_LIST: Invalid response received from server! Domain info is not a valid array or more than one domain info has returned!"));
            }

            return $result;
        });


        return $result;
    }

    /**
     * Get Domain details
     * @param string $domainName
     * @return array
     */
    public function GetDetails($domainName)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName
            ]
        ];



        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {

            $data = $response[key($response)];

            // If DomainInfo a valid array
            if (isset($data["DomainInfo"]) && is_array($data["DomainInfo"])) {
                // Parse domain info
                $result["data"]   = $this->parseDomainInfo($data["DomainInfo"]);
                $result["result"] = "OK";

            } else {
                // Set error
                $result["result"] = "ERROR";
                $result["error"]  = $this->setError("INVALID_DOMAIN_LIST", "Invalid response received from server!",
                    "Domain info is not a valid array or more than one domain info has returned!");

                $this->sendErrorToSentryAsync(new Exception("INVALID_DOMAIN_LIST: Invalid response received from server! Domain info is not a valid array or more than one domain info has returned!"));
            }
            return $result;
        });





        return $response;
    }

    /**
     * Modify Name Server, Nameservers must be valid array
     * @param string $domainName
     * @param array $nameServers
     * @return array
     */
    public function ModifyNameServer($domainName, $nameServers)
    {
        $parameters = [
            "request" => [
                "Password"       => $this->servicePassword,
                "UserName"       => $this->serviceUsername,
                "DomainName"     => $domainName,
                "NameServerList" => array_values($nameServers)
            ]
        ];




        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {


            $data = $response[key($response)];

            $result["data"]                = [];
            $result["data"]["NameServers"] = $parameters["request"]["NameServerList"];
            $result["result"]              = "OK";

            return $result;
        });


        return $response;
    }


    /**
     * Enable Theft Protection Lock for domain
     * @param string $domainName
     * @return array
     */
    public function EnableTheftProtectionLock($domainName)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName
            ]
        ];

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response)  {


            return [
                'data'   => [
                    'LockStatus' => true
                ],
                'result' => 'OK'
            ];

        });




        return $response;
    }


    /**
     * Disable Theft Protection Lock for domain
     * @param string $domainName
     * @return array
     */
    public function DisableTheftProtectionLock($domainName)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response)  {


            return [
                'data'   => [
                    'LockStatus' => false
                ],
                'result' => 'OK'
            ];
        });


        return $response;
    }


    /**
     * Add Child Name Server for domain
     * @param string $domainName
     * @param string $nameServer
     * @param string $ipAddress
     * @return array
     */
    public function AddChildNameServer($domainName, $nameServer, $ipAddress)
    {
        $parameters = [
            "request" => [
                "Password"        => $this->servicePassword,
                "UserName"        => $this->serviceUsername,
                "DomainName"      => $domainName,
                "ChildNameServer" => $nameServer,
                "IpAddressList"   => [$ipAddress]
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use($parameters)  {

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


    /**
     * Delete Child Name Server for domain
     * @param string $domainName
     * @param string $nameServer
     * @return array
     */
    public function DeleteChildNameServer($domainName, $nameServer)
    {
        $parameters = [
            "request" => [
                "Password"        => $this->servicePassword,
                "UserName"        => $this->serviceUsername,
                "DomainName"      => $domainName,
                "ChildNameServer" => $nameServer
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use($parameters)  {


            return [
                'data'   => [
                    'NameServer' => $parameters["request"]["ChildNameServer"],
                ],
                'result' => 'OK'
            ];
        });

        return $response;

    }


    /**
     * Modify IP of Child Name Server for domain
     * @param string $domainName
     * @param string $nameServer
     * @param string $ipAddress
     * @return array
     */
    public function ModifyChildNameServer($domainName, $nameServer, $ipAddress)
    {
        $parameters = [
            "request" => [
                "Password"        => $this->servicePassword,
                "UserName"        => $this->serviceUsername,
                "DomainName"      => $domainName,
                "ChildNameServer" => $nameServer,
                "IpAddressList"   => [$ipAddress]
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use($parameters)  {


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

    /**
     * Get Contacts for domain, Administrative, Billing, Technical, Registrant segments will be returned
     * @param string $domainName
     * @return array
     */
    public function GetContacts($domainName)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName
            ]
        ];



        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {


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
                $this->sendErrorToSentryAsync(new Exception("INVALID_CONTACT_INTO: Invalid response received from server! Contact info is not a valid array or more than one contact info has returned!"));
            }
            return $result;
        });



        return $response;
    }


    /**
     * Save Contacts for domain, Contacts segments will be saved as Administrative, Billing, Technical, Registrant.
     * @param string $domainName
     * @param array $contacts
     * @return array
     */
    public function SaveContacts($domainName, $contacts)
    {
        $parameters = [
            "request" => [
                "Password"              => $this->servicePassword,
                "UserName"              => $this->serviceUsername,
                "DomainName"            => $domainName,
                "AdministrativeContact" => $contacts["Administrative"],
                "BillingContact"        => $contacts["Billing"],
                "TechnicalContact"      => $contacts["Technical"],
                "RegistrantContact"     => $contacts["Registrant"]
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {


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
                    'error'  => $this->setError("INVALID_CONTACT_SAVE", "Invalid response received from server!",
                        "Contact info is not a valid array or more than one contact info has returned!")
                ];

                $this->sendErrorToSentryAsync(new Exception("INVALID_CONTACT_SAVE: Invalid response received from server! Contact info is not a valid array or more than one contact info has returned!"));
            }
            return $result;
        });


        return $response;
    }

    // DOMAIN TRANSFER (INCOMING DOMAIN)

    // Start domain transfer (Incoming domain)
    /**
     * Transfer Domain
     * @param string $domainName
     * @param string $eppCode
     * @param int $period
     * @return array
     */
    public function Transfer($domainName, $eppCode, $period)
    {
        $parameters = [
            "request" => [
                "Password"             => $this->servicePassword,
                "UserName"             => $this->serviceUsername,
                "DomainName"           => $domainName,
                "AuthCode"             => $eppCode,
                'AdditionalAttributes' => [
                    'KeyValueOfstringstring' => [
                        [
                            'Key'   => 'TRANSFERPERIOD',
                            'Value' => $period
                        ]
                    ]
                ]
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {

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
                    'data'   => $this->setError("INVALID_DOMAIN_TRANSFER_REQUEST",
                        "Invalid response received from server!",
                        "Domain info is not a valid array or more than one domain info has returned!")
                    ];
                $this->sendErrorToSentryAsync(new Exception("INVALID_DOMAIN_TRANSFER_REQUEST: Invalid response received from server! Domain info is not a valid array or more than one domain info has returned!"));
                }
            return $result;
        });





        return $response;
    }


    /**
     * Stops Incoming Transfer
     * @param string $domainName
     */
    public function CancelTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {



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


    /**
     * Approve Outgoing transfer
     * @param $domainName
     * @return mixed|string[]
     */
    public function ApproveTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {

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
    /**
     * Reject Outgoing transfer
     * @param $domainName
     * @return mixed|string[]
     */
    public function RejectTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {


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


    /**
     * Renew domain
     * @param string $domainName
     * @param int $period
     * @return array
     */
    public function Renew($domainName, $period)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName,
                "Period"     => $period
            ]
        ];

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {

            $data = $response[key($response)];

            if (isset($data["ExpirationDate"])) {
            return [
                'result'=>'OK',
                'data'=>[
                        'ExpirationDate' => $data["ExpirationDate"]
                ]
            ];
            } else {
                return [
                    'result' => 'ERROR',
                    'error'  => $this->setError("INVALID_DOMAIN_RENEW", "Invalid response received from server!",
                        "Domain info is not a valid array or more than one domain info has returned!")
                ];
                $this->sendErrorToSentryAsync(new Exception("INVALID_DOMAIN_RENEW: Invalid response received from server! Domain info is not a valid array or more than one domain info has returned!"));
            }
        });

        return $response;
    }


    // Register domain with contact information
    /**
     * Register domain with contact information
     * @param string $domainName
     * @param int $period
     * @param array $contacts
     * @param array $nameServers
     * @param bool $eppLock
     * @param bool $privacyLock
     * @param array $addionalAttributes
     * @return array
     */
    public function RegisterWithContactInfo(
        $domainName,
        $period,
        $contacts,
        $nameServers = ["dns.domainnameapi.com", "web.domainnameapi.com"],
        $eppLock = true,
        $privacyLock = false,
        $addionalAttributes = []
    ) {
        $parameters = [
            "request" => [
                "Password"                => $this->servicePassword,
                "UserName"                => $this->serviceUsername,
                "DomainName"              => $domainName,
                "Period"                  => $period,
                "NameServerList"          => $nameServers,
                "LockStatus"              => $eppLock,
                "PrivacyProtectionStatus" => $privacyLock,
                "AdministrativeContact"   => $contacts["Administrative"],
                "BillingContact"          => $contacts["Billing"],
                "TechnicalContact"        => $contacts["Technical"],
                "RegistrantContact"       => $contacts["Registrant"]
            ]
        ];

        if(count($addionalAttributes)>0){
            foreach ($addionalAttributes as $k => $v) {
                $parameters['request']['AdditionalAttributes']['KeyValueOfstringstring'][]=['Key'=>$k,'Value'=>$v];
            }
        }


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {

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
                    'error'  => $this->setError("INVALID_DOMAIN_REGISTER", "Invalid response received from server!",
                        "Domain info is not a valid array or more than one domain info has returned!")
                ];
                $this->sendErrorToSentryAsync(new Exception("INVALID_DOMAIN_REGISTER: Invalid response received from server! Domain info is not a valid array or more than one domain info has returned!"));
            }
            return $result;

        });




        return $response;
    }


    /**
     * Modify privacy protection status of domain
     * @param string $domainName
     * @param bool $status
     * @param string $Reason
     * @return array
     */
    public function ModifyPrivacyProtectionStatus($domainName, $status, $Reason = "Owner request")
    {
        if (trim($Reason) == "") {
            $Reason = "Owner request";
        }

        $parameters = [
            "request" => [
                "Password"       => $this->servicePassword,
                "UserName"       => $this->serviceUsername,
                "DomainName"     => $domainName,
                "ProtectPrivacy" => $status,
                "Reason"         => $Reason
            ]
        ];

        return  self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            return [
                'data'=>[
                    'PrivacyProtectionStatus'=>$parameters["request"]["ProtectPrivacy"]
                ]  ,
                'result'=>'OK'
            ];
        });


    }


    /**
     * Sync from registry, domain information will be updated from registry
     * @param string $domainName
     * @return array
     */
    public function SyncFromRegistry($domainName)
    {
        $parameters = [
            "request" => [
                "Password"   => $this->servicePassword,
                "UserName"   => $this->serviceUsername,
                "DomainName" => $domainName
            ]
        ];

        return self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
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
                    'error'  => $this->setError("INVALID_DOMAIN_SYNC", "Invalid response received from server!",
                        "Domain info is not a valid array or more than one domain info has returned!"),
                    'result' => 'ERROR'
                ];
                $this->sendErrorToSentryAsync(new Exception("INVALID_DOMAIN_SYNC: Invalid response received from server! Domain info is not a valid array or more than one domain info has returned!"));
            }

            return $result;


        });

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
    private function parseError($response, $trace = true)
    {
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
                $result['Response'] = print_r($response, true);
            }

            if (isset($response[key($response)]["OperationResult"])) {
                $result["Code"] .= "_" . $response[key($response)]["OperationResult"];
            }

            if (isset($response[key($response)]["OperationMessage"])) {
                $result["Details"] = $response[key($response)]["OperationMessage"];
            }
        }

        if (isset($result["Code"]) && $trace === true) {
            $this->sendErrorToSentryAsync(new Exception("API_ERROR: " . $result["Code"] . " - " . $result["Message"] . " - " . $result["Details"]));
        }

        return $result;
    }

    // Check if response contains error
    private function hasError($response) {
        return !(($this->parseError($response, false) === false));
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
  private function parseDomainInfo($data)
    {
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
                                        "ns" => $ns,
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


    /* Parses contact information from the provided data array.
 *
 * @param array $data The data array containing contact information.
 *
 * @return array An associative array with the parsed contact information, including:
 *   - string ID
 *   - string Status
 *   - string AuthCode
 *   - string FirstName
 *   - string LastName
 *   - string Company
 *   - string EMail
 *   - string Type
 *   - array Address
 *     - string Line1
 *     - string Line2
 *     - string Line3
 *     - string State
 *     - string City
 *     - string Country
 *     - string ZipCode
 *   - array Phone
 *     - array Phone
 *       - string Number
 *       - string CountryCode
 *     - array Fax
 *       - string Number
 *       - string CountryCode
 *   - array Additional
 */
    private function parseContactInfo($data)
    {
        $result = [
            "ID"         => isset($data["Id"]) && is_numeric($data["Id"]) ? $data["Id"] : "",
            "Status"     => $data["Status"] ?? "",
            "AuthCode"   => $data["Auth"] ?? "",
            "FirstName"  => $data["FirstName"] ?? "",
            "LastName"   => $data["LastName"] ?? "",
            "Company"    => $data["Company"] ?? "",
            "EMail"      => $data["EMail"] ?? "",
            "Type"       => $data["Type"] ?? "",
            "Address"    => [
                "Line1"   => $data["AddressLine1"] ?? "",
                "Line2"   => $data["AddressLine2"] ?? "",
                "Line3"   => $data["AddressLine3"] ?? "",
                "State"   => $data["State"] ?? "",
                "City"    => $data["City"] ?? "",
                "Country" => $data["Country"] ?? "",
                "ZipCode" => $data["ZipCode"] ?? "",
            ],
            "Phone"      => [
                "Phone" => [
                    "Number"      => $data["Phone"] ?? "",
                    "CountryCode" => $data["PhoneCountryCode"] ?? "",
                ],
                "Fax"   => [
                    "Number"      => $data["Fax"] ?? "",
                    "CountryCode" => $data["FaxCountryCode"] ?? "",
                ],
            ],
            "Additional" => [],
        ];

        // AdditionalAttributes kontrolü
        if (isset($data["AdditionalAttributes"]["KeyValueOfstringstring"]) && is_array($data["AdditionalAttributes"]["KeyValueOfstringstring"])) {
            foreach ($data["AdditionalAttributes"]["KeyValueOfstringstring"] as $attribute) {
                if (isset($attribute["Key"]) && isset($attribute["Value"])) {
                    $result["Additional"][$attribute["Key"]] = $attribute["Value"];
                }
            }
        }

        return $result;
    }


    private function parseCall($fn, $parameters, $_callback)
    {
        $result=[
            'result'=>'ERROR',
            'error'=>'Unknown Error Occured'
        ];

        try {

            // SOAP method which is same as current function name called
            $_response = $this->service->__soapCall($fn, [$parameters]);

            $this->service->__getLastResponse();


            // Serialize as array
            $_response = $this->objectToArray($_response);

            $this->setLastFunction($fn);
            $this->setRequestData($parameters);
            $this->setResponseData($_response);


            // Check is there any error?
            if (!$this->hasError($_response)) {

                $result = $_callback($_response);

            } else {
                // Hata mesajini dondura
                $result["result"] = "ERROR";
                $result["error"]  = $this->parseError($_response);
            }

        } catch (SoapFault $ex) {
            $result["result"] = "ERROR";
            $result["error"]  = $this->setError('INVALID_RESPONSE','Invalid Response occured',$ex->getMessage());
            $this->sendErrorToSentryAsync($ex);
        } catch (Exception $ex) {
            $result["result"] = "ERROR";
            $result["error"]  = $this->parseError($this->objectToArray($ex));
            $this->sendErrorToSentryAsync($ex);
        }

        $this->setParsedResponseData($result);


        return $result;
    }



    /**
     * Domain is TR type
     * @param $domain
     * @return bool
     */
    public function isTrTLD($domain){
        //preg_match('/\.com\.tr|\.net\.tr|\.org\.tr|\.biz\.tr|\.info\.tr|\.tv\.tr|\.gen\.tr|\.web\.tr|\.tel\.tr|\.name\.tr|\.bbs\.tr|\.gov\.tr|\.bel\.tr|\.pol\.tr|\.edu\.tr|\.k12\.tr|\.av\.tr|\.dr\.tr$/', $domain, $result);

        preg_match('/\.tr$/', $domain, $result);



        return isset($result[0]);
    }



}
