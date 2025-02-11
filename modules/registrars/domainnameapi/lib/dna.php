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
 * @version 2.1.8
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
    const VERSION = '2.1.8';

    const DEFAULT_NAMESERVERS = [
        'ns1.domainnameapi.com',
        'ns2.domainnameapi.com',
    ];

    const DEFAULT_IGNORED_ERRORS = [
        '*Domain not found*',
        '*ERR_DOMAIN_NOT_FOUND*',
        '*Reseller not found*',
        '*Domain is not in updateable status. It must be active*',
        '*balance is not sufficient*',
        '*Price definition not found*',
    ];

    const DEFAULT_ERRORS = [
        'INVALID_DOMAIN_DETAILS'          => [
            'code'        => 'INVALID_DOMAIN_DETAILS',
            'message'     => 'Invalid domain details! Details format is not valid',
            'description' => 'The provided domain details are not in the expected format'
        ],
        'INVALID_CREDENTIALS'             => [
            'code'        => 'INVALID_CREDENTIALS',
            'message'     => 'Invalid username and password',
            'description' => 'The provided API credentials are invalid'
        ],
        'INVALID_DOMAIN_LIST'             => [
            'code'        => 'INVALID_DOMAIN_LIST',
            'message'     => 'Domain info is not a valid array or more than one domain info has returned!',
            'description' => 'The domain list response is not in the expected format'
        ],
        'INVALID_TLD_LIST'                => [
            'code'        => 'INVALID_TLD_LIST',
            'message'     => 'TLD info is not a valid array or more than one TLD info has returned!',
            'description' => 'The TLD list response is not in the expected format'
        ],
        'INVALID_RESPONSE'                => [
            'code'        => 'INVALID_RESPONSE',
            'message'     => 'Invalid response received from server! Response is empty.',
            'description' => 'The API response is empty or null'
        ],
        'INVALID_RESPONSE_FORMAT'         => [
            'code'        => 'INVALID_RESPONSE_FORMAT',
            'message'     => 'Invalid response received from server! Response format is not valid.',
            'description' => 'The API response format is not in the expected structure'
        ],
        'INVALID_RESPONSE_COUNT'          => [
            'code'        => 'INVALID_RESPONSE_COUNT',
            'message'     => 'Invalid parameters passed to function! Response data contains more than one result!',
            'description' => 'The API response contains multiple results when only one was expected'
        ],
        'INVALID_RESPONSE_CODE'           => [
            'code'        => 'INVALID_RESPONSE_CODE',
            'message'     => 'Invalid parameters passed to function! Operation result or Error code not received from server',
            'description' => 'The API response is missing required operation result or error code fields'
        ],
        'INVALID_RESPONSE_SOAP'           => [
            'code'        => 'INVALID_RESPONSE_SOAP',
            'message'     => 'Invalid parameters passed to function! Soap return is not a valid array!',
            'description' => 'The SOAP response is not in a valid array format'
        ],
        'INVALID_CONTACT_INFO'            => [
            'code'        => 'INVALID_CONTACT_INFO',
            'message'     => 'Invalid response received from server! Contact info is not a valid array or more than one contact info has returned!',
            'description' => 'The contact information response is not in the expected format'
        ],
        'INVALID_CONTACT_SAVE'            => [
            'code'        => 'INVALID_CONTACT_SAVE',
            'message'     => 'Invalid response received from server! Contact info could not be saved!',
            'description' => 'The contact information could not be saved on the server'
        ],
        'INVALID_DOMAIN_TRANSFER_REQUEST' => [
            'code'        => 'INVALID_DOMAIN_TRANSFER_REQUEST',
            'message'     => 'Invalid response received from server! Domain transfer request could not be completed!',
            'description' => 'The domain transfer request failed to complete'
        ],
        'INVALID_DOMAIN_RENEW'            => [
            'code'        => 'INVALID_DOMAIN_RENEW',
            'message'     => 'Invalid response received from server! Domain renew request could not be completed!',
            'description' => 'The domain renewal request failed to complete'
        ],
        'INVALID_DOMAIN_REGISTER'         => [
            'code'        => 'INVALID_DOMAIN_REGISTER',
            'message'     => 'Invalid response received from server! Domain register request could not be completed!',
            'description' => 'The domain registration request failed to complete'
        ],
        'INVALID_DOMAIN_SYNC'             => [
            'code'        => 'INVALID_DOMAIN_SYNC',
            'message'     => 'Invalid response received from server! Domain sync request could not be completed!',
            'description' => 'The domain synchronization request failed to complete'
        ]
    ];

    const DEFAULT_CACHE_TTL = 512;
    const DEFAULT_TIMEOUT   = 20;
    const DEFAULT_REASON    = 'Owner request';

    private const APPLICATIONS = [
        'WHMCS'          => [
            'path' => '/modules/registrars/domainnameapi/',
            'dsn'  => 'https://cbaee35fa4d2836942641e10c2109cb6@sentry.atakdomain.com/9'
        ],
        'WISECP'         => [
            'path' => '/coremio/modules/Registrars/DomainNameAPI/',
            'dsn'  => 'https://16578e3378f7d6c329ff95d9573bc6fa@sentry.atakdomain.com/8'
        ],
        'HOSTBILL'       => [
            'path' => '/includes/modules/Domain/domainnameapi/',
            'dsn'  => 'https://be47804b215cb479dbfc44db5c662549@sentry.atakdomain.com/11'
        ],
        'BLESTA'         => [
            'path' => '/components/modules/domainnameapi/',
            'dsn'  => 'https://8f8ed6f84abaa93ff49b56f15d3c1f38@sentry.atakdomain.com/10'
        ],
        'CLIENTEXEC'     => [
            'path' => '/plugins/registrars/domainnameapi/',
            'dsn'  => 'https://033791219211d863fdb9c08b328ba058@sentry.atakdomain.com/13'
        ],
        'CORE'           => [
            'path' => '',
            'dsn'  => 'https://0ea94fed70c09f95c17dfa211d43ac66@sentry.atakdomain.com/2'
        ],
        'ISPBILLMANAGER' => [
            'path' => '',
            'dsn'  => ''
        ],
        'HOSTFACT'       => [
            'path' => '',
            'dsn'  => ''
        ],
        'NONE'           => [
            'path' => '',
            'dsn'  => ''
        ]
    ];

    private const CURRENCIES = [
        'TRY' => [
            'id'   => 1,
            'code' => 'TRY',
            'name' => 'Turkish Lira'
        ],
        'USD' => [
            'id'   => 2,
            'code' => 'USD',
            'name' => 'US Dollar'
        ]
    ];

    /**
     * Error reporting enabled
     */
    private bool $errorReportingEnabled = true;
    /**
     * Error Reporting Will send this sentry endpoint, if errorReportingEnabled is true
     * This request does not include sensitive informations, sensitive informations are filtered.
     * @var string $errorReportingDsn
     */
    private string $errorReportingDsn  = 'https://0ea94fed70c09f95c17dfa211d43ac66@sentry.atakdomain.com/2';
    private string $errorReportingPath = '';

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
    private string     $serviceUrl          = "https://whmcs.domainnameapi.com/DomainApi.svc";
    private string     $application         = "CORE";
    public array       $lastRequest         = [];
    public array       $lastResponse        = [];
    public ?string     $lastResponseHeaders = '';
    public array       $lastParsedResponse  = [];
    public string      $lastFunction        = '';
    private SoapClient $service;
    private            $startAt;


    /**
     * DomainNameAPI_PHPLibrary constructor.
     * @param string $userName
     * @param string $password
     * @param bool $testMode
     * @throws Exception | SoapFault
     */
    public function __construct($userName = "ownername", $password = "ownerpass")
    {
        $this->startAt = microtime(true);
        self::setCredentials($userName, $password);
        self::setApplication();

        $context = stream_context_create([
            'ssl' => [
                'verify_peer'      => false,
                'verify_peer_name' => false
            ]
        ]);

        try {
            // Create unique connection
            $this->service = new SoapClient($this->serviceUrl . "?singlewsdl", [
                "encoding"           => "UTF-8",
                'features'           => SOAP_SINGLE_ELEMENT_ARRAYS,
                'exceptions'         => true,
                'connection_timeout' => self::DEFAULT_TIMEOUT,
                'stream_context'     => $context
            ]);
        } catch (SoapFault $e) {
            $this->sendErrorToSentryAsync($e);
            throw new Exception("SOAP Connection Error: " . $e->getMessage());
        } catch (Exception $e) {
            $this->sendErrorToSentryAsync($e);
            throw new Exception("SOAP Error: " . $e->getMessage());
        }
    }

    private function setApplication()
    {
        $dir                      = __DIR__;
        $this->application        = 'CORE';
        $this->errorReportingPath = self::APPLICATIONS['CORE']['path'];
        $this->errorReportingDsn  = self::APPLICATIONS['CORE']['dsn'];

        foreach (self::APPLICATIONS as $app => $config) {
            if ($config['path'] && strpos($dir, $config['path']) !== false) {
                $this->application        = $app;
                $this->errorReportingPath = $config['path'];
                $this->errorReportingDsn  = $config['dsn'];
                break;
            }
        }
    }

    /**
     * Deprecated
     * @param bool $value
     */
    private function useTestMode($value = true)
    {
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
     * Get last request sent to API
     *
     * @return array Request data
     */
    public function getRequestData()
    {
        return $this->lastRequest;
    }

    /**
     * Set last request sent to API
     *
     * @param array $request Request data to set
     * @return DomainNameAPI_PHPLibrary
     */
    public function setRequestData($request)
    {
        $this->lastRequest = $request;
        return $this;
    }

    /**
     * Get last response from API
     *
     * @return array Response data
     */
    public function getResponseData()
    {
        return $this->lastResponse;
    }

    /**
     * Set last response from API
     *
     * @param array $response Response data to set
     * @return DomainNameAPI_PHPLibrary
     */
    public function setResponseData($response)
    {
        $this->lastResponse = $response;
        return $this;
    }

    /**
     * Get last response headers from API
     *
     * @return ?string Response headers
     */
    public function getResponseHeaders()
    {
        return $this->lastResponseHeaders;
    }

    /**
     * Set last response headers from API
     *
     * @param ?string $headers Response headers to set
     * @return DomainNameAPI_PHPLibrary
     */
    public function setResponseHeaders($headers)
    {
        $this->lastResponseHeaders = $headers;
        return $this;
    }

    /**
     * Get last parsed response from API
     *
     * @return array Parsed response data
     */
    public function getParsedResponseData()
    {
        return $this->lastParsedResponse;
    }

    /**
     * Set last parsed response from API
     *
     * @param array $response Parsed response data to set
     * @return DomainNameAPI_PHPLibrary
     */
    public function setParsedResponseData($response)
    {
        $this->lastParsedResponse = $response;
        return $this;
    }

    /**
     * Get last function called
     *
     * @return string Function name
     */
    public function getLastFunction()
    {
        return $this->lastFunction;
    }

    /**
     * Set last function called
     *
     * @param string $function Function name to set
     * @return DomainNameAPI_PHPLibrary
     */
    public function setLastFunction($function)
    {
        $this->lastFunction = $function;
        return $this;
    }

    /**
     * Get API service URL
     *
     * @return string Service URL
     */
    public function getServiceUrl()
    {
        return $this->serviceUrl;
    }

    /**
     * Set API service URL
     *
     * @param string $url New service URL
     */
    public function setServiceUrl($url)
    {
        $this->serviceUrl = $url;
    }


    /**
     * Get Current account details with balance
     *
     * @return array Account details and balance information
     * @see examples/GetResellerDetails.php
     */
    public function GetResellerDetails()
    {
        $parameters = [
            "request" => [
                'CurrencyId' => self::CURRENCIES['USD']['id'] // Varsayılan USD
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {
            $data = $response[key($response)];
            $resp = [];

            if (isset($data['ResellerInfo'])) {
                $resp['result'] = 'OK';
                $resp['id']     = $data['ResellerInfo']['Id'];
                $resp['active'] = $data['ResellerInfo']['Status'] == 'Active';
                $resp['name']   = $data['ResellerInfo']['Name'];

                $active_currency = $data['ResellerInfo']['BalanceInfoList']['BalanceInfo'][0];
                $balances        = [];
                foreach ($data['ResellerInfo']['BalanceInfoList']['BalanceInfo'] as $v) {
                    if ($v['CurrencyName'] == $data['ResellerInfo']['CurrencyInfo']['Code']) {
                        $active_currency = $v;
                    }

                    $balances[] = [
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
                $resp['error']  = $this->setError("INVALID_CREDINENTIALS");
            }


            return $resp;
        });


        return $response;
    }

    /**
     * Get Current primary Balance for your account
     *
     * @param string $currencyId Currency code (USD, TRY etc.)
     * @return array Balance information for specified currency
     * @see examples/GetCurrentBalance.php
     */
    public function GetCurrentBalance($currencyId = 'USD')
    {
        $currencyId = strtoupper($currencyId);

        // Para birimi ID'sini bul
        $currency = self::CURRENCIES['USD']; // Varsayılan USD
        if (isset(self::CURRENCIES[$currencyId])) {
            $currency = self::CURRENCIES[$currencyId];
        } elseif ($currencyId === '1' || $currencyId === '2') {
            // Eski ID tabanlı kullanım için geriye dönük uyumluluk
            foreach (self::CURRENCIES as $curr) {
                if ($curr['id'] == $currencyId) {
                    $currency = $curr;
                    break;
                }
            }
        }

        $parameters = [
            "request" => [
                'CurrencyId' => $currency['id']
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {
            return $response['GetCurrentBalanceResult'];
        });


        return $response;
    }


    /**
     * Checks availability of domain names with given extensions
     *
     * @param array $domains Domain names to check
     * @param array $extensions Extensions to check
     * @param int $period Registration period in years
     * @param string $Command Operation type (create, renew, transfer etc.)
     * @return array Domain availability status and pricing information
     * @see examples/CheckAvailability.php
     */
    public function CheckAvailability($domains, $extensions, $period, $Command)
    {
        $parameters = [
            "request" => [
                "DomainNameList" => $domains,
                "TldList"        => $extensions,
                "Period"         => $period,
                "Commad"         => $Command
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {
            //return $response;
            $data      = $response[key($response)];
            $available = [];

            if (isset($data["DomainAvailabilityInfoList"]['DomainAvailabilityInfo']['Tld'])) {
                $buffer = $data["DomainAvailabilityInfoList"]['DomainAvailabilityInfo'];
                $data   = [
                    'DomainAvailabilityInfoList' => [
                        'DomainAvailabilityInfo' => [
                            $buffer
                        ]
                    ]
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
     * Get list of domains in your account
     *
     * @param array $extra_parameters Optional parameters for filtering and pagination
     * @return array List of domains with their details
     * @see examples/GetList.php
     */
    public function GetList($extra_parameters = [])
    {
        $parameters = [
            "request" => []
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

                $result["result"]     = "OK";
                $result["TotalCount"] = $data["TotalCount"];
            } else {
                // Set error
                $result["result"] = "ERROR";
                $result["error"]  = $this->setError("INVALID_DOMAIN_LIST");

                $this->sendErrorToSentryAsync(new Exception("[INVALID_DOMAIN_LIST] " . self::DEFAULT_ERRORS['INVALID_DOMAIN_LIST']['description']));
            }
            return $result;
        });


        // Log last request and response

        return $response;
    }

    /**
     * Get TLD list and pricing matrix
     *
     * @param int $count Number of results per page
     * @return array List of TLDs with pricing information
     * @see examples/GetTldList.php
     */
    public function GetTldList($count = 20)
    {
        $parameters = [
            "request" => [
                'IncludePriceDefinitions' => 1,
                'PageSize'                => $count
            ]
        ];


        $result = self::parseCall(__FUNCTION__, $parameters, function ($response) {
            $data   = $response[key($response)];
            $result = [];

            // If DomainInfo a valid array
            if (isset($data["TldInfoList"]) && is_array($data["TldInfoList"])) {
                // Parse domain info

                $extensions = [];

                foreach ($data["TldInfoList"]['TldInfo'] as $k => $v) {
                    $pricing = $currencies = [];
                    foreach ($v['PriceInfoList']['TldPriceInfo'] as $kp => $vp) {
                        $pricing[strtolower($vp['TradeType'])][$vp['Period']] = $vp['Price'];
                        $currencies[strtolower($vp['TradeType'])]             = $vp['CurrencyName'];
                    }

                    $extensions[] = [
                        'id'         => $v['Id'],
                        'status'     => $v['Status'],
                        'maxchar'    => $v['MaxCharacterCount'],
                        'maxperiod'  => $v['MaxRegistrationPeriod'],
                        'minchar'    => $v['MinCharacterCount'],
                        'minperiod'  => $v['MinRegistrationPeriod'],
                        'tld'        => $v['Name'],
                        'pricing'    => $pricing,
                        'currencies' => $currencies,
                    ];
                }

                $result = [
                    'data'   => $extensions,
                    'result' => 'OK'
                ];
            } else {
                // Set error
                $result = [
                    'result' => 'ERROR',
                    'error'  => $this->setError("INVALID_TLD_LIST")
                ];
                $this->sendErrorToSentryAsync(new Exception("[INVALID_TLD_LIST] " . self::DEFAULT_ERRORS['INVALID_TLD_LIST']['description']));
            }

            return $result;
        });


        return $result;
    }

    /**
     * Get detailed information for a domain
     *
     * @param string $domainName Domain name to query
     * @return array Detailed domain information
     * @see examples/GetDetails.php
     */
    public function GetDetails($domainName)
    {
        $parameters = [
            "request" => [
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
                $result["error"]  = $this->setError("INVALID_DOMAIN_DETAILS");

                $this->sendErrorToSentryAsync(new Exception("[INVALID_DOMAIN_DETAILS] " . self::DEFAULT_ERRORS['INVALID_DOMAIN_DETAILS']['description']));
            }
            return $result;
        });


        return $response;
    }

    /**
     * Modify nameservers for a domain
     *
     * @param string $domainName Domain name to modify
     * @param array $nameServers New nameserver addresses
     * @return array Operation result
     * @see examples/ModifyNameServer.php
     */
    public function ModifyNameServer($domainName, $nameServers)
    {
        $parameters = [
            "request" => [
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
     * Enable Theft Protection Lock for a domain
     *
     * @param string $domainName Domain name to enable protection for
     * @return array Operation result
     * @see examples/EnableTheftProtectionLock.php
     */
    public function EnableTheftProtectionLock($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {
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
     * Disable Theft Protection Lock for a domain
     *
     * @param string $domainName Domain name to disable protection for
     * @return array Operation result
     * @see examples/DisableTheftProtectionLock.php
     */
    public function DisableTheftProtectionLock($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) {
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
     * Add child nameserver for a domain
     *
     * @param string $domainName Domain name to add child nameserver for
     * @param string $nameServer Hostname of child nameserver
     * @param string $ipAddress IP address for child nameserver
     * @return array Operation result
     * @see examples/AddChildNameServer.php
     */
    public function AddChildNameServer($domainName, $nameServer, $ipAddress)
    {
        $parameters = [
            "request" => [
                "DomainName"      => $domainName,
                "ChildNameServer" => $nameServer,
                "IpAddressList"   => [$ipAddress]
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
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
     * Delete child nameserver from a domain
     *
     * @param string $domainName Domain name to remove child nameserver from
     * @param string $nameServer Hostname of child nameserver to delete
     * @return array Operation result
     * @see examples/DeleteChildNameServer.php
     */
    public function DeleteChildNameServer($domainName, $nameServer)
    {
        $parameters = [
            "request" => [
                "DomainName"      => $domainName,
                "ChildNameServer" => $nameServer
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
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
     * Modify IP address of child nameserver
     *
     * @param string $domainName Domain name that owns the child nameserver
     * @param string $nameServer Hostname of child nameserver to modify
     * @param string $ipAddress New IP address for child nameserver
     * @return array Operation result
     * @see examples/ModifyChildNameServer.php
     */
    public function ModifyChildNameServer($domainName, $nameServer, $ipAddress)
    {
        $parameters = [
            "request" => [
                "DomainName"      => $domainName,
                "ChildNameServer" => $nameServer,
                "IpAddressList"   => [$ipAddress]
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
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
     * Get contact information for a domain
     *
     * @param string $domainName Domain name to get contacts for
     * @return array Contact information for all contact types
     * @see examples/GetContacts.php
     */
    public function GetContacts($domainName)
    {
        $parameters = [
            "request" => [
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
                    'error'  => $this->setError("INVALID_CONTACT_INFO"),
                    'result' => 'ERROR'
                ];
                $this->sendErrorToSentryAsync(new Exception("[INVALID_CONTACT_INFO] " . self::DEFAULT_ERRORS['INVALID_CONTACT_INFO']['description']));
            }
            return $result;
        });


        return $response;
    }


    /**
     * Saves or updates contact information for all contact types of a domain
     *
     * @param string $domainName The domain name to update contacts for
     * @param array $contacts
     * @return array
     */
    public function SaveContacts($domainName, $contacts)
    {
        $parameters = [
            "request" => [
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

            if ($data['OperationResult'] == 'SUCCESS') {
                $result = [
                    'result' => 'OK'
                ];
            } else {
                // Set error
                $result = [
                    'result' => 'ERROR',
                    'error'  => $this->setError("INVALID_CONTACT_SAVE")
                ];

                $this->sendErrorToSentryAsync(new Exception("[INVALID_CONTACT_SAVE] " . self::DEFAULT_ERRORS['INVALID_CONTACT_SAVE']['description']));
            }
            return $result;
        });


        return $response;
    }

    /**
     * Start domain transfer to your account
     *
     * @param string $domainName Domain name to transfer
     * @param string $eppCode Authorization code from current registrar
     * @param int $period Transfer period in years
     * @return array Transfer status and domain information
     * @see examples/Transfer.php
     */
    public function Transfer($domainName, $eppCode, $period)
    {
        $parameters = [
            "request" => [
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
                    'data'   => $this->setError("INVALID_DOMAIN_TRANSFER_REQUEST")
                ];
                $this->sendErrorToSentryAsync(new Exception("[INVALID_DOMAIN_TRANSFER_REQUEST] " . self::DEFAULT_ERRORS['INVALID_DOMAIN_TRANSFER_REQUEST']['description']));
            }
            return $result;
        });


        return $response;
    }


    /**
     * Cancel pending incoming transfer
     *
     * @param string $domainName Domain name to cancel transfer for
     * @return array Operation result
     * @see examples/CancelTransfer.php
     */
    public function CancelTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];

            return [
                'result' => $data['OperationResult'] == 'SUCCESS' ? 'OK' : 'ERROR',
                'data'   => [
                    'DomainName' => $parameters["request"]["DomainName"]
                ]
            ];
        });

        return $response;
    }


    /**
     * Approve pending outgoing transfer
     *
     * @param string $domainName Domain name to approve transfer for
     * @return array Operation result
     * @see examples/ApproveTransfer.php
     */
    public function ApproveTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];

            return [
                'result' => $data['OperationResult'] == 'SUCCESS' ? 'OK' : 'ERROR',
                'data'   => [
                    'DomainName' => $parameters["request"]["DomainName"]
                ]
            ];
        });

        return $response;
    }

    /**
     * Reject pending outgoing transfer
     *
     * @param string $domainName Domain name to reject transfer for
     * @return array Operation result
     * @see examples/RejectTransfer.php
     */
    public function RejectTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];

            return [
                'result' => $data['OperationResult'] == 'SUCCESS' ? 'OK' : 'ERROR',
                'data'   => [
                    'DomainName' => $parameters["request"]["DomainName"]
                ]
            ];
        });

        return $response;
    }


    /**
     * Renew domain registration
     *
     * @param string $domainName Domain name to renew
     * @param int $period Renewal period in years
     * @return array Renewal status and expiration date
     * @see examples/Renew.php
     */
    public function Renew($domainName, $period)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName,
                "Period"     => $period
            ]
        ];

        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];

            if (isset($data["ExpirationDate"])) {
                return [
                    'result' => 'OK',
                    'data'   => [
                        'ExpirationDate' => $data["ExpirationDate"]
                    ]
                ];
            } else {
                return [
                    'result' => 'ERROR',
                    'error'  => $this->setError("INVALID_DOMAIN_RENEW")
                ];
                $this->sendErrorToSentryAsync(new Exception("[INVALID_DOMAIN_RENEW] " . self::DEFAULT_ERRORS['INVALID_DOMAIN_RENEW']['description']));
            }
        });

        return $response;
    }


    /**
     * Register new domain with contact information
     *
     * @param string $domainName Domain name to register
     * @param int $period Registration period in years
     * @param array $contacts Contact information for all types
     * @param array $nameServers Nameserver addresses
     * @param bool $eppLock Enable EPP lock
     * @param bool $privacyLock Enable privacy protection
     * @param array $addionalAttributes Additional TLD-specific attributes
     * @return array Registration status and domain information
     * @see examples/RegisterWithContactInfo.php
     */
    public function RegisterWithContactInfo(
        $domainName,
        $period,
        $contacts,
        $nameServers = self::DEFAULT_NAMESERVERS,
        $eppLock = true,
        $privacyLock = false,
        $addionalAttributes = []
    ) {
        // BUG-5337: Remove empty nameservers
        foreach ($nameServers as $k => $v) {
            if (strlen($v) < 1) {
                unset($nameServers[$k]);
            }
        }
        $nameServers = array_values($nameServers);


        $parameters = [
            "request" => [
                "DomainName"              => $domainName,
                "Period"                  => $period,
                "NameServerList"          => $nameServers,
                "LockStatus"              => $eppLock,
                "PrivacyProtectionStatus" => $privacyLock,
                "AdministrativeContact"   => $this->validateContact($contacts["Administrative"]),
                "BillingContact"          => $this->validateContact($contacts["Billing"]),
                "TechnicalContact"        => $this->validateContact($contacts["Technical"]),
                "RegistrantContact"       => $this->validateContact($contacts["Registrant"])
            ]
        ];

        if (count($addionalAttributes) > 0) {
            foreach ($addionalAttributes as $k => $v) {
                $parameters['request']['AdditionalAttributes']['KeyValueOfstringstring'][] = [
                    'Key'   => $k,
                    'Value' => $v
                ];
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
                    'error'  => $this->setError("INVALID_DOMAIN_REGISTER")
                ];
                $this->sendErrorToSentryAsync(new Exception("[INVALID_DOMAIN_REGISTER] " . self::DEFAULT_ERRORS['INVALID_DOMAIN_REGISTER']['description']));
            }
            return $result;
        });


        return $response;
    }


    /**
     * Modify privacy protection status
     *
     * @param string $domainName Domain name to modify
     * @param bool $status New privacy protection status
     * @param string $reason Reason for modification
     * @return array Operation result
     * @see examples/ModifyPrivacyProtectionStatus.php
     */
    public function ModifyPrivacyProtectionStatus($domainName, $status, $reason = self::DEFAULT_REASON)
    {
        $parameters = [
            "request" => [
                "DomainName"     => $domainName,
                "ProtectPrivacy" => $status,
                "Reason"         => trim($reason) ?: self::DEFAULT_REASON
            ]
        ];

        return self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            return [
                'data'   => [
                    'PrivacyProtectionStatus' => $parameters["request"]["ProtectPrivacy"]
                ],
                'result' => 'OK'
            ];
        });
    }


    /**
     * Synchronize domain information with registry
     *
     * @param string $domainName Domain name to synchronize
     * @return array Updated domain information
     * @see examples/SyncFromRegistry.php
     */
    public function SyncFromRegistry($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];

        return self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];

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
                    'error'  => $this->setError("INVALID_DOMAIN_SYNC"),
                    'result' => 'ERROR'
                ];
                $this->sendErrorToSentryAsync(new Exception("[INVALID_DOMAIN_SYNC] " . self::DEFAULT_ERRORS['INVALID_DOMAIN_SYNC']['description']));
            }

            return $result;
        });
    }

    /**
     * Convert object to array
     *
     * @param mixed $_obj Object to convert
     * @return array Converted array
     */
    private function objectToArray($_obj)
    {
        try {
            $_obj = json_decode(json_encode($_obj), true);
        } catch (Exception $ex) {
        }
        return $_obj;
    }

    // Get error if exists

    /**
     * Parse error from response
     *
     * @param array $response API response
     * @param bool $trace Whether to send error to Sentry
     * @return array|false Error details or false if no error
     */
    private function parseError($response, $trace = true)
    {
        $result = false;

        if (is_null($response)) {
            // Set error data
            $result            = [];
            $result["Code"]    = "INVALID_RESPONSE";
            $result["Message"] = self::DEFAULT_ERRORS['INVALID_RESPONSE']['message'];
            $result["Details"] = self::DEFAULT_ERRORS['INVALID_RESPONSE']['description'];
        } elseif (!is_array($response)) {
            // Set error data
            $result            = [];
            $result["Code"]    = "INVALID_RESPONSE_FORMAT";
            $result["Message"] = self::DEFAULT_ERRORS['INVALID_RESPONSE_FORMAT']['message'];
            $result["Details"] = self::DEFAULT_ERRORS['INVALID_RESPONSE_FORMAT']['description'];
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
            $result["Code"]    = "INVALID_RESPONSE_COUNT";
            $result["Message"] = self::DEFAULT_ERRORS['INVALID_RESPONSE_COUNT']['message'];
            $result["Details"] = self::DEFAULT_ERRORS['INVALID_RESPONSE_COUNT']['description'];
        } elseif (!isset($response[key($response)]["OperationResult"]) || !isset($response[key($response)]["ErrorCode"])) {
            // Set error data
            $result            = [];
            $result["Code"]    = "INVALID_RESPONSE_CODE";
            $result["Message"] = self::DEFAULT_ERRORS['INVALID_RESPONSE_CODE']['message'];
            $result["Details"] = self::DEFAULT_ERRORS['INVALID_RESPONSE_CODE']['description'];
        } elseif (strtoupper($response[key($response)]["OperationResult"]) != "SUCCESS") {
            // Set error data
            $result = [
                "Code"    => '',
                "Message" => 'Failed',
                "Details" => '',
            ];

            if (isset($response[key($response)]["OperationMessage"])) {
                $result["Code"]     = "API_" . $response[key($response)]["ErrorCode"];
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
            $this->sendErrorToSentryAsync(new Exception("[API_ERROR]: " . $result["Code"] . " - " . $result["Message"] . " - " . $result["Details"]));
        }

        return $result;
    }

    /**
     * Check if response contains error
     *
     * @param array $response API response
     * @return bool True if response has error
     */
    private function hasError($response)
    {
        return !(($this->parseError($response, false) === false));
    }

    /**
     * Set error message
     *
     * @param string $code Error code
     * @param string $message Error message
     * @param string $details Error details
     * @return array Error information
     */
    private function setError($code, $message = '', $details = '')
    {
        $result = [];
        if (isset(self::DEFAULT_ERRORS[$code])) {
            $error             = self::DEFAULT_ERRORS[$code];
            $result["Code"]    = $error['code'];
            $result["Message"] = $error['message'];
            $result["Details"] = $error['description'];
        } else {
            $result["Code"]    = $code;
            $result["Message"] = $message;
            $result["Details"] = $details;
        }
        return $result;
    }

    /**
     * Parse domain information from response
     *
     * @param array $data Domain data from API
     * @return array Parsed domain information
     */
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

    /**
     * Parse contact information from response
     *
     * @param array $data Contact data from API
     * @return array Parsed contact information
     */
    private function parseContactInfo($data): array
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

    /**
     * Parse API call and handle response
     *
     * @param string $fn Function name
     * @param array $parameters Request parameters
     * @param callable $_callback Response handler function
     * @return array Parsed response
     */
    private function parseCall($fn, $parameters, $_callback): array
    {
        $result = [
            'result' => 'ERROR',
            'error'  => 'Unknown Error Occurred'
        ];

        try {
            $parameters["request"]["UserName"] = $this->serviceUsername;
            $parameters["request"]["Password"] = $this->servicePassword;
            // Call the SOAP method with the same name as the current function
            $_response = $this->service->__soapCall($fn, [$parameters]);

            // Get the last response
            $this->service->__getLastResponse();

            // Convert response to array
            $_response = $this->objectToArray($_response);

            // Set function, request, and response data
            $this->setLastFunction($fn);
            $this->setRequestData($parameters);
            $this->setResponseData($_response);
            //$this->setResponseHeaders($this->service->__getLastResponseHeaders());

            // Check for errors in the response
            if (!$this->hasError($_response)) {
                $result = $_callback($_response);
            } else {
                $result["result"] = "ERROR";
                $result["error"]  = $this->parseError($_response);
            }
        } catch (SoapFault $ex) {
            $result["result"] = "ERROR";
            $result["error"]  = $this->setError('INVALID_RESPONSE_SOAP',
                self::DEFAULT_ERRORS['INVALID_RESPONSE_SOAP']['description'], $ex->getMessage());
            $this->sendErrorToSentryAsync($ex);
        } catch (Exception $ex) {
            $result["result"] = "ERROR";
            $result["error"]  = $this->parseError($this->objectToArray($ex));
            $this->sendErrorToSentryAsync($ex);
        }

        // Set parsed response data
        $this->setParsedResponseData($result);


        return $result;
    }

    /**
     * Validate and normalize contact information
     *
     * @param array $contact Contact data to validate
     * @return array Validated contact information
     */
    public function validateContact($contact)
    {
        // Varsayılan değerleri tanımla
        $defaults = [
            "FirstName"        => "Isimyok",
            "LastName"         => "Isimyok",
            "AddressLine1"     => "Addres yok",
            "City"             => "ISTANBUL",
            "Country"          => "TR",
            "ZipCode"          => "34000",
            "Phone"            => "5555555555",
            "PhoneCountryCode" => "90"
        ];

        // Eksik anahtarları varsayılan değerlerle doldur
        foreach ($defaults as $key => $value) {
            if (!isset($contact[$key])) {
                $contact[$key] = $value;
            }
        }

        // Boş değerleri kontrol et ve varsayılan değerlerle doldur
        if (strlen(trim($contact["FirstName"])) == 0) {
            $contact["FirstName"] = $defaults["FirstName"];
        }
        if (strlen(trim($contact["LastName"])) == 0) {
            $contact["LastName"] = $contact["FirstName"];
        }
        if (strlen(trim($contact["AddressLine1"])) == 0) {
            $contact["AddressLine1"] = $defaults["AddressLine1"];
        }
        if (strlen(trim($contact["City"])) == 0) {
            $contact["City"] = $defaults["City"];
        }
        if (strlen(trim($contact["Country"])) == 0) {
            $contact["Country"] = $defaults["Country"];
        }
        if (strlen(trim($contact["ZipCode"])) == 0) {
            $contact["ZipCode"] = $defaults["ZipCode"];
        }

        // Telefon numarası işleme
        $tmpPhone = isset($contact["Phone"]) ? preg_replace('/[^0-9]/', '', $contact["Phone"]) : '';
        if (strlen($tmpPhone) == 10) {
            $contact["PhoneCountryCode"] = '';
            $contact["Phone"]            = $tmpPhone;
        } elseif (strlen($tmpPhone) == 11 && substr($tmpPhone, 0, 1) == '9') {
            $contact["PhoneCountryCode"] = substr($tmpPhone, 0, 2);
            $contact["Phone"]            = substr($tmpPhone, 2);
        } elseif (strlen($tmpPhone) == 12 && substr($tmpPhone, 0, 2) == '90') {
            $contact["PhoneCountryCode"] = substr($tmpPhone, 0, 2);
            $contact["Phone"]            = substr($tmpPhone, 2);
        } else {
            $contact["PhoneCountryCode"] = $defaults["PhoneCountryCode"];
            $contact["Phone"]            = $tmpPhone ?: $defaults["Phone"];
        }

        if (strlen(trim($contact["PhoneCountryCode"])) == 0) {
            $contact["PhoneCountryCode"] = $defaults["PhoneCountryCode"];
        }
        if (strlen(trim($contact["Phone"])) == 0) {
            $contact["Phone"] = $defaults["Phone"];
        }

        return $contact;
    }


    /**
     * Domain is TR type
     * @param $domain
     * @return bool
     */
    public function isTrTLD($domain)
    {
        preg_match('/\.tr$/', $domain, $result);

        return isset($result[0]);
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

        $skipped_errors = self::DEFAULT_IGNORED_ERRORS;

        foreach ($skipped_errors as $ek => $ev) {
            if (strpos($e->getMessage(), $ev) !== false) {
                return;
            }
        }

        $elapsed_time = microtime(true) - $this->startAt;
        $parsed_dsn   = parse_url($this->errorReportingDsn);

        // API URL'si
        $host       = $parsed_dsn['host'];
        $project_id = ltrim($parsed_dsn['path'], '/');
        $public_key = $parsed_dsn['user'];
        $secret_key = $parsed_dsn['pass'] ?? null;
        $api_url    = "https://$host/api/$project_id/store/";

        $external_ip = $this->getServerIp();


        $knownPath = __FILE__;
        $errFile   = $e->getFile();
        $vhostUser = '';


        try {
            $vhostUser = \get_current_user();
        } catch (Exception $ex) {
            $vhostUser = '';
        }
        if ($vhostUser == '') {
            if (preg_match('/\/home\/([^\/]+)\//', $knownPath, $matches)) {
                $vhostUser = $matches[1];
            }
        }


        if (strlen($this->errorReportingPath) > 0) {
            if (strpos($knownPath, $this->errorReportingPath) !== false) {
                $knownPath = substr($knownPath,
                    strpos($knownPath, $this->errorReportingPath) + strlen($this->errorReportingPath));
                $errFile   = substr($errFile,
                    strpos($errFile, $this->errorReportingPath) + strlen($this->errorReportingPath));
            }
        }


        // Hata verisi
        $errorData = [
            'event_id'  => bin2hex(random_bytes(16)),
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'level'     => 'error',
            'logger'    => 'php',
            'platform'  => 'php',
            'culprit'   => $knownPath,
            'message'   => [
                'formatted' => $e->getMessage()
            ],
            'exception' => [
                'values' => [
                    [
                        'type'       => str_replace(['DomainNameApi\DomainNameAPI_PHPLibrary'],
                            [$this->application . ' Exception'], self::class),
                        'value'      => $e->getMessage(),
                        'stacktrace' => [
                            'frames' => [
                                [
                                    'filename' => $errFile,
                                    'lineno'   => $e->getLine(),
                                    'function' => str_replace([
                                        dirname(__DIR__),
                                        'DomainNameApi\DomainNameAPI_PHPLibrary'
                                    ], ['.', 'Lib'], $e->getTraceAsString()),
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
                'url'             => $_SERVER['REQUEST_URI'] ?? 'NA',
                'transaction'     => $_SERVER['REQUEST_METHOD'] ?? 'NA',
                'trace_id'        => bin2hex(random_bytes(8)), // Trace ID örneği
                'runtime_name'    => 'PHP',
                'runtime_version' => phpversion(),
                'ip_address'      => $external_ip,
                'elapsed_time'    => number_format($elapsed_time, 4),
                'vhost_user'      => $vhostUser,
                'application'     => $this->application,
                'extension_soap'  => extension_loaded('soap') ? 'enabled' : 'disabled',
                'openssl_v'       => defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'NA',
                'openssl_n'       => defined('OPENSSL_VERSION_NUMBER') ? OPENSSL_VERSION_NUMBER : 'NA',
            ],
            'extra'     => [
                'request_data'  => $this->getRequestData(),
                'response_data' => $this->getResponseData(),
            ]
        ];

        // Sentry başlığı
        $sentry_auth = [
            'sentry_version=7',
            'sentry_client=phplib-php/' . self::VERSION,
            "sentry_key=$public_key"
        ];
        if ($secret_key) {
            $sentry_auth[] = "sentry_secret=$secret_key";
        }
        $sentry_auth_header = 'X-Sentry-Auth: Sentry ' . implode(', ', $sentry_auth);

        if (function_exists('escapeshellarg') && function_exists('exec')) {
            $cmd = 'curl -X POST ' . escapeshellarg($api_url) . ' -H ' . escapeshellarg('Content-Type: application/json') . ' -H ' . escapeshellarg($sentry_auth_header) . ' -d ' . escapeshellarg(json_encode($errorData)) . ' > /dev/null 2>&1 &';
            exec($cmd);
        } else {
            $jsonData = json_encode($errorData);
            $ch       = curl_init();
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

    /**
     * Get server's external IP address
     *
     * @return string Server IP address
     */
    private function getServerIp()
    {
        $cache_ttl    = self::DEFAULT_CACHE_TTL;
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

}
