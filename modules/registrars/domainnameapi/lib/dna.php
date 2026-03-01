<?php
/**
 * Created by PhpStorm.
 * User: bunyaminakcay
 * Project name php-dna
 * 20.11.2022
 * Bünyamin AKÇAY <bunyamin@bunyam.in>
 */

/**
 * Class DomainNameAPI_PHPLibrary
 * @package DomainNameApi
 * @version 2.1.21
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
    const VERSION = '2.1.21';

    const DEFAULT_NAMESERVERS = [
        'ns1.domainnameapi.com',
        'ns2.domainnameapi.com',
    ];

    const DEFAULT_IGNORED_ERRORS = [
        'Domain not found',
        'ERR_DOMAIN_NOT_FOUND',
        'Reseller not found',
        'Domain is not in updateable status',
        'balance is not sufficient',
        'Price definition not found',
        'TLD is not supported',
        'Invalid API credentials',
    ];

    const DEFAULT_ERRORS = [
        'DOMAIN_DETAILS'          => [
            'code'        => 'DOMAIN_DETAILS',
            'message'     => 'Invalid domain details! Details format is not valid',
            'description' => 'The provided domain details are not in the expected format'
        ],
        'CREDENTIALS'             => [
            'code'        => 'CREDENTIALS',
            'message'     => 'Invalid username and password',
            'description' => 'The provided API credentials are invalid'
        ],
        'DOMAIN_LIST'             => [
            'code'        => 'DOMAIN_LIST',
            'message'     => 'The domain list is invalid or contains multiple entries!',
            'description' => 'The domain list response format is incorrect or unexpected'
        ],
        'TLD_LIST'                => [
            'code'        => 'TLD_LIST',
            'message'     => 'TLD info is not a valid array or more than one TLD info has returned!',
            'description' => 'The TLD list response is not in the expected format'
        ],
        'RESPONSE'                => [
            'code'        => 'RESPONSE',
            'message'     => 'Invalid response received from server! Response is empty.',
            'description' => 'The API response is empty or null'
        ],
        'RESPONSE_FORMAT'         => [
            'code'        => 'RESPONSE_FORMAT',
            'message'     => 'Invalid response received from server! Response format is not valid.',
            'description' => 'The API response format is not in the expected structure'
        ],
        'RESPONSE_COUNT'          => [
            'code'        => 'RESPONSE_COUNT',
            'message'     => 'Invalid parameters passed to function! Response data contains more than one result!',
            'description' => 'The API response contains multiple results when only one was expected'
        ],
        'RESPONSE_CODE'           => [
            'code'        => 'RESPONSE_CODE',
            'message'     => 'Invalid parameters passed to function! Operation result or Error code not received from server',
            'description' => 'The API response is missing required operation result or error code fields'
        ],
        'RESPONSE_SOAP'           => [
            'code'        => 'RESPONSE_SOAP',
            'message'     => 'Invalid parameters passed to function! Soap return is not a valid array!',
            'description' => 'The SOAP response is not in a valid array format'
        ],
        'CONTACT_INFO'            => [
            'code'        => 'CONTACT_INFO',
            'message'     => 'Invalid response received from server! Contact info is not a valid array or more than one contact info has returned!',
            'description' => 'The contact information response is not in the expected format'
        ],
        'CONTACT_SAVE'            => [
            'code'        => 'CONTACT_SAVE',
            'message'     => 'Invalid response received from server! Contact info could not be saved!',
            'description' => 'The contact information could not be saved on the server'
        ],
        'DOMAIN_TRANSFER_REQUEST' => [
            'code'        => 'DOMAIN_TRANSFER_REQUEST',
            'message'     => 'Invalid response received from server! Domain transfer request could not be completed!',
            'description' => 'The domain transfer request failed to complete'
        ],
        'DOMAIN_RENEW'            => [
            'code'        => 'DOMAIN_RENEW',
            'message'     => 'Invalid response received from server! Domain renew request could not be completed!',
            'description' => 'The domain renewal request failed to complete'
        ],
        'DOMAIN_REGISTER'         => [
            'code'        => 'DOMAIN_REGISTER',
            'message'     => 'Invalid response received from server! Domain register request could not be completed!',
            'description' => 'The domain registration request failed to complete'
        ],
        'DOMAIN_SYNC'             => [
            'code'        => 'DOMAIN_SYNC',
            'message'     => 'Invalid response received from server! Domain sync request could not be completed!',
            'description' => 'The domain synchronization request failed to complete'
        ]
    ];

    const DEFAULT_CACHE_TTL = 512;
    const DEFAULT_TIMEOUT   = 20;
    const DEFAULT_REASON    = 'Owner request';
    const PERFORMANCE_SAMPLE_RATE = 25; // 2.5% (25 out of 1000)

    const RESULT_OK      = 'OK';
    const RESULT_ERROR   = 'ERROR';
    const RESULT_SUCCESS = 'SUCCESS';

    private const APPLICATIONS = [
        'WHMCS'          => [
            'path' => 'modules/registrars/domainnameapi',
            'dsn'  => 'https://cbaee35fa4d2836942641e10c2109cb6@sentry.atakdomain.com/9'
        ],
        'WISECP'         => [
            'path' => 'coremio/modules/Registrars/DomainNameAPI',
            'dsn'  => 'https://16578e3378f7d6c329ff95d9573bc6fa@sentry.atakdomain.com/8'
        ],
        'HOSTBILL'       => [
            'path' => 'includes/modules/Domain/domainnameapi',
            'dsn'  => 'https://be47804b215cb479dbfc44db5c662549@sentry.atakdomain.com/11'
        ],
        'BLESTA'         => [
            'path' => 'components/modules/domainnameapi',
            'dsn'  => 'https://8f8ed6f84abaa93ff49b56f15d3c1f38@sentry.atakdomain.com/10'
        ],
        'CLIENTEXEC'     => [
            'path' => 'plugins/registrars/domainnameapi',
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
            'path' => 'Pro/3rdparty/domain/domainnameapi',
            'dsn'  => 'https://58fe0a01a6704d9f1c2dbbc1a316f233@sentry.atakdomain.com/14'
        ],
        'FOSSBILLING'       => [
            'path' => 'library/Registrar/Adapter/DomainNameApi',
            'dsn'  => 'https://3a129526bcd91cc309de8358d87846b9@sentry.atakdomain.com/15'
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

    /**
     * Api Password
     * @var string $servicePassword
     */
    private string $servicePassword = "ownerpass";

    /**
     * Api Service Soap URL
     * @var string $serviceUrl
     */
    private string     $serviceUrl          = "https://whmcs.domainnameapi.com/DomainApi.svc";
    private string     $serviceTestUrl      = "https://ote-api.domainnameapi.com/DomainApi.svc";
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
     *
     * @param string $userName API username for authentication
     * @param string $password API password for authentication
     * @param bool $testmode Enable test/OTE environment
     * @throws Exception|SoapFault When SOAP connection fails
     */
    public function __construct($userName = "ownername", $password = "ownerpass",$testmode=false)
    {
        $this->startAt = microtime(true);
        self::useTestMode($testmode);
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

    /**
     * Detect and set the application context based on file path
     *
     * @return void
     */
    private function setApplication()
    {
        $dir                      = __FILE__;
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
     * Switch service URL to test environment
     *
     * @deprecated Use constructor $testmode parameter instead
     * @param bool $value Enable or disable test mode
     * @return void
     */
    private function useTestMode($value = true)
    {
        if ($value === true || $value == 'on') {
            $this->serviceUrl = $this->serviceTestUrl;
        }
    }


    /**
     * Set API authentication credentials
     *
     * @param string $userName API username
     * @param string $password API password
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
     * Magic method to support both PascalCase and camelCase method calls
     * Converts PascalCase to camelCase and calls the method
     *
     * @param string $name Method name
     * @param array $arguments Method arguments
     * @return mixed
     * @throws Exception
     */
    public function __call($name, $arguments)
    {
        // Convert PascalCase to camelCase
        $camelCaseName = lcfirst($name);

        // Check if camelCase method exists
        if (method_exists($this, $camelCaseName)) {
            return call_user_func_array([$this, $camelCaseName], $arguments);
        }

        throw new Exception("Method {$name} does not exist");
    }

    /**
     * Get Current account details with balance
     *
     * @return array Account details and balance information
     * @see examples/GetResellerDetails.php
     */
    public function getResellerDetails()
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
                $resp['result'] = self::RESULT_OK;
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
                $resp['result'] = self::RESULT_ERROR;
                $resp['error']  = $this->setError("CREDENTIALS");
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
    public function getCurrentBalance($currencyId = 'USD')
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
     * @param string $command Operation type (create, renew, transfer etc.)
     * @return array Domain availability status and pricing information
     * @see examples/CheckAvailability.php
     */
    public function checkAvailability($domains, $extensions, $period, $command)
    {
        $parameters = [
            "request" => [
                "DomainNameList" => $domains,
                "TldList"        => $extensions,
                "Period"         => $period,
                "Commad"        => $command
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
    public function getList($extra_parameters = [])
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

                $result["result"]     = self::RESULT_OK;
                $result["TotalCount"] = $data["TotalCount"];
            } else {
                // Set error
                $result["result"] = self::RESULT_ERROR;
                $result["error"]  = $this->setError("DOMAIN_LIST");

                $this->sendErrorToSentryAsync(new Exception("[DOMAIN_LIST] " . self::DEFAULT_ERRORS['DOMAIN_LIST']['description']));
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
    public function getTldList($count = 20)
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
                    'result' => self::RESULT_OK
                ];
            } else {
                // Set error
                $result = [
                    'result' => self::RESULT_ERROR,
                    'error'  => $this->setError("TLD_LIST")
                ];
                $this->sendErrorToSentryAsync(new Exception("[TLD_LIST] " . self::DEFAULT_ERRORS['TLD_LIST']['description']));
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
    public function getDetails($domainName)
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
                $result["result"] = self::RESULT_OK;
            } else {
                // Set error
                $result["result"] = self::RESULT_ERROR;
                $result["error"]  = $this->setError("DOMAIN_DETAILS");

                $this->sendErrorToSentryAsync(new Exception("[DOMAIN_DETAILS] " . self::DEFAULT_ERRORS['DOMAIN_DETAILS']['description']));
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
    public function modifyNameServer($domainName, $nameServers)
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
            $result["result"]              = self::RESULT_OK;

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
    public function enableTheftProtectionLock($domainName)
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
                'result' => self::RESULT_OK
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
    public function disableTheftProtectionLock($domainName)
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
                'result' => self::RESULT_OK
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
    public function addChildNameServer($domainName, $nameServer, $ipAddress)
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
                'result' => self::RESULT_OK
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
    public function deleteChildNameServer($domainName, $nameServer)
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
                'result' => self::RESULT_OK
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
    public function modifyChildNameServer($domainName, $nameServer, $ipAddress)
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
                'result' => self::RESULT_OK
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
    public function getContacts($domainName)
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
                    'result' => self::RESULT_OK
                ];
            } else {
                // Set error
                $result = [
                    'error'  => $this->setError("CONTACT_INFO"),
                    'result' => self::RESULT_ERROR
                ];
                $this->sendErrorToSentryAsync(new Exception("[CONTACT_INFO] " . self::DEFAULT_ERRORS['CONTACT_INFO']['description']));
            }
            return $result;
        });


        return $response;
    }


    /**
     * Saves or updates contact information for all contact types of a domain
     *
     * @param string $domainName The domain name to update contacts for
     * @param array $contacts Associative array with Administrative, Billing, Technical, Registrant keys
     * @return array Operation result
     * @see examples/SaveContacts.php
     */
    public function saveContacts($domainName, $contacts)
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

            if ($data['OperationResult'] == self::RESULT_SUCCESS) {
                $result = [
                    'result' => self::RESULT_OK
                ];
            } else {
                // Set error
                $result = [
                    'result' => self::RESULT_ERROR,
                    'error'  => $this->setError("CONTACT_SAVE")
                ];

                $this->sendErrorToSentryAsync(new Exception("[CONTACT_SAVE] " . self::DEFAULT_ERRORS['CONTACT_SAVE']['description']));
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
    public function transfer($domainName, $eppCode, $period)
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
                    'result' => self::RESULT_OK,
                    'data'   => $this->parseDomainInfo($data["DomainInfo"])
                ];
            } else {
                // Set error
                $result = [
                    'result' => self::RESULT_ERROR,
                    'data'   => $this->setError("DOMAIN_TRANSFER_REQUEST")
                ];
                $this->sendErrorToSentryAsync(new Exception("[DOMAIN_TRANSFER_REQUEST] " . self::DEFAULT_ERRORS['DOMAIN_TRANSFER_REQUEST']['description']));
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
    public function cancelTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];

            return [
                'result' => $data['OperationResult'] == self::RESULT_SUCCESS ? self::RESULT_OK : self::RESULT_ERROR,
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
    public function approveTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];

            return [
                'result' => $data['OperationResult'] == self::RESULT_SUCCESS ? self::RESULT_OK : self::RESULT_ERROR,
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
    public function rejectTransfer($domainName)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName
            ]
        ];


        $response = self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];

            return [
                'result' => $data['OperationResult'] == self::RESULT_SUCCESS ? self::RESULT_OK : self::RESULT_ERROR,
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
    public function renew($domainName, $period)
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
                    'result' => self::RESULT_OK,
                    'data'   => [
                        'ExpirationDate' => $data["ExpirationDate"]
                    ]
                ];
            } else {
                $this->sendErrorToSentryAsync(new Exception("[DOMAIN_RENEW] " . self::DEFAULT_ERRORS['DOMAIN_RENEW']['description']));
                return [
                    'result' => self::RESULT_ERROR,
                    'error'  => $this->setError("DOMAIN_RENEW")
                ];

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
    public function registerWithContactInfo(
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

        if(substr($domainName, -3) == ".tr") {

            if(!isset($addionalAttributes['TRABISDOMAINCATEGORY'])) {
                $addionalAttributes['TRABISDOMAINCATEGORY'] = '1';
            }

            if(!isset($addionalAttributes['TRABISCOUNTRYID'])) {
                $addionalAttributes['TRABISCOUNTRYID'] = 215;
                $addionalAttributes['TRABISCOUNTRYNAME'] = 'TR';
                $addionalAttributes['TRABISCITYNAME'] = 'Istanbul';
                $addionalAttributes['TRABISCITIYID'] = 34;
            }

            if ($addionalAttributes['TRABISDOMAINCATEGORY'] == '1') {
                if (!isset($addionalAttributes['TRABISNAMESURNAME'])) {
                    $addionalAttributes['TRABISNAMESURNAME'] = $parameters["request"]["RegistrantContact"]['FirstName'] . ' ' . $parameters["request"]["RegistrantContact"]['LastName'];
                }
                if (!isset($addionalAttributes['TRABISCITIZIENID'])) {
                    $addionalAttributes['TRABISCITIZIENID'] = '11111111111';
                }
                unset($addionalAttributes['TRABISORGANIZATION'],$addionalAttributes['TRABISTAXOFFICE'],$addionalAttributes['TRABISTAXNUMBER']);
            }else{
                if (!isset($addionalAttributes['TRABISORGANIZATION'])) {
                    $addionalAttributes['TRABISORGANIZATION'] = $parameters["request"]["RegistrantContact"]['Company'];
                }
                if (!isset($addionalAttributes['TRABISTAXOFFICE'])) {
                    $addionalAttributes['TRABISTAXOFFICE'] = 'Istanbul';
                }
                if (!isset($addionalAttributes['TRABISTAXNUMBER'])) {
                    $addionalAttributes['TRABISTAXNUMBER'] = '1111111111';
                }
                unset($addionalAttributes['TRABISNAMESURNAME'],$addionalAttributes['TRABISCITIZIENID']);
            }
        }

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
                    'result' => self::RESULT_OK,
                    'data'   => $this->parseDomainInfo($data["DomainInfo"])
                ];
            } else {
                // Set error
                $result = [
                    'result' => self::RESULT_ERROR,
                    'error'  => $this->setError("DOMAIN_REGISTER")
                ];
                $this->sendErrorToSentryAsync(new Exception("[DOMAIN_REGISTER] " . self::DEFAULT_ERRORS['DOMAIN_REGISTER']['description']));
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
    public function modifyPrivacyProtectionStatus($domainName, $status, $reason = self::DEFAULT_REASON)
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
                'result' => self::RESULT_OK
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
    public function syncFromRegistry($domainName)
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
                    'result' => self::RESULT_OK
                ];
            } else {
                // Set error
                $result = [
                    'error'  => $this->setError("DOMAIN_SYNC"),
                    'result' => self::RESULT_ERROR
                ];
                $this->sendErrorToSentryAsync(new Exception("[DOMAIN_SYNC] " . self::DEFAULT_ERRORS['DOMAIN_SYNC']['description']));
            }

            return $result;
        });
    }

    /**
     * Check if domain transfer is possible
     *
     * @param string $domainName Domain name to check transfer for
     * @param string $authcode Authorization/EPP code for transfer
     * @return array Operation result
     */
    public function checkTransfer($domainName, $authcode)
    {
        $parameters = [
            "request" => [
                "DomainName" => $domainName,
                "AuthCode"   => $authcode
            ]
        ];

        return self::parseCall(__FUNCTION__, $parameters, function ($response) use ($parameters) {
            $data = $response[key($response)];
            return [
                'result' => $data['OperationResult'] == self::RESULT_SUCCESS ? self::RESULT_OK : self::RESULT_ERROR
            ];
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
            $result["Code"]    = "RESPONSE";
            $result["Message"] = self::DEFAULT_ERRORS['RESPONSE']['message'];
            $result["Details"] = self::DEFAULT_ERRORS['RESPONSE']['description'];
        } elseif (!is_array($response)) {
            // Set error data
            $result            = [];
            $result["Code"]    = "RESPONSE_FORMAT";
            $result["Message"] = self::DEFAULT_ERRORS['RESPONSE_FORMAT']['message'];
            $result["Details"] = self::DEFAULT_ERRORS['RESPONSE_FORMAT']['description'];
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
        } elseif (count($response) < 1) {
            // Set error data
            $result            = [];
            $result["Code"]    = "RESPONSE_COUNT";
            $result["Message"] = self::DEFAULT_ERRORS['RESPONSE_COUNT']['message'];
            $result["Details"] = self::DEFAULT_ERRORS['RESPONSE_COUNT']['description'];
        } elseif (!isset($response[key($response)]["OperationResult"]) || !isset($response[key($response)]["ErrorCode"])) {
            // Set error data
            $result            = [];
            $result["Code"]    = "RESPONSE_CODE";
            $result["Message"] = self::DEFAULT_ERRORS['RESPONSE_CODE']['message'];
            $result["Details"] = self::DEFAULT_ERRORS['RESPONSE_CODE']['description'];
        } elseif (strtoupper($response[key($response)]["OperationResult"]) != self::RESULT_SUCCESS) {
            // Set error data
            $result = [
                "Code"    => '',
                "Message" => 'Failed',
                "Details" => '',
            ];

            if (isset($response[key($response)]["OperationMessage"])) {
                $result["Code"]     = "API_" . $response[key($response)]["ErrorCode"];
                //$result['Response'] = print_r($response, true);
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
            'result' => self::RESULT_ERROR,
            'error'  => 'Unknown Error Occurred'
        ];

        try {
            // Sample performance metrics with 2.5% rate
            $shouldSamplePerformance = (mt_rand(1, 1000) <= self::PERFORMANCE_SAMPLE_RATE);

            $parameters["request"]["UserName"] = $this->serviceUsername;
            $parameters["request"]["Password"] = $this->servicePassword;
            $_response = $this->service->__soapCall($fn, [$parameters]);

            // Get the last response
            $this->service->__getLastResponse();

            // Convert response to array
            $_response = $this->objectToArray($_response);

            // Set function, request, and response data
            $this->setLastFunction($fn);
            $this->setRequestData($parameters);
            $this->setResponseData($_response);

            // Check for errors in the response
            if (!$this->hasError($_response)) {
                $result = $_callback($_response);
            } else {
                $result["result"] = self::RESULT_ERROR;
                $result["error"]  = $this->parseError($_response);
            }

            // Send performance metrics to Sentry
            if ($shouldSamplePerformance) {
                $duration = (microtime(true) - $this->startAt) * 1000;
                $this->sendPerformanceMetricsToSentry([
                    'operation' => $fn,
                    'duration'  => floatval($duration),
                    'success'   => ($result['result'] === self::RESULT_OK),
                    'timestamp' => gmdate('Y-m-d\TH:i:s.', time()) . sprintf('%03d', round(fmod(microtime(true), 1) * 1000)) . 'Z',
                    'start_timestamp' => gmdate('Y-m-d\TH:i:s.', (int)$this->startAt) . sprintf('%03d', round(fmod($this->startAt, 1) * 1000)) . 'Z'
                ]);
            }

        } catch (SoapFault $ex) {
            $result["result"] = self::RESULT_ERROR;
            $result["error"]  = $this->setError('RESPONSE_SOAP', self::DEFAULT_ERRORS['RESPONSE_SOAP']['description'], $ex->getMessage());
            $this->sendErrorToSentryAsync($ex);
        } catch (Exception $ex) {
            $result["result"] = self::RESULT_ERROR;
            $result["error"]  = $this->parseError($this->objectToArray($ex));
            $this->sendErrorToSentryAsync($ex);
        }

        // Set parsed response data
        $this->setParsedResponseData($result);

        return $result;
    }

    /**
     * Send performance metrics to Sentry
     *
     * @param array $metrics Performance metrics data
     * @return void
     */
    private function sendPerformanceMetricsToSentry(array $metrics): void
    {
        if (!$this->errorReportingEnabled) {
            return;
        }

        try {
            $parsed_dsn = parse_url($this->errorReportingDsn);
            if (!$parsed_dsn || !isset($parsed_dsn['host']) || !isset($parsed_dsn['path']) || !isset($parsed_dsn['user'])) {
                return;
            }

            $host = $parsed_dsn['host'];
            $project_id = ltrim($parsed_dsn['path'], '/');
            $public_key = $parsed_dsn['user'];
            $secret_key = $parsed_dsn['pass'] ?? null;
            $api_url = "https://$host/api/$project_id/store/";

            // Generate trace and span IDs
            $trace_id = bin2hex(random_bytes(16));
            $span_id = bin2hex(random_bytes(8));

            // Collect system information
            $vhostUser = '';
            try {
                $vhostUser = function_exists('get_current_user') ? \get_current_user() : '';
            } catch (Exception $ex) {
                if (preg_match('/\/home\/([^\/]+)\//', __FILE__, $matches)) {
                    $vhostUser = $matches[1];
                }
            }

            // Get OpenSSL info
            $openssl_version = defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'NA';

            // Detect environment
            $environment = 'production';
            if (isset($_SERVER['SERVER_NAME']) && (
                strpos($_SERVER['SERVER_NAME'], 'dev.') === 0 ||
                strpos($_SERVER['SERVER_NAME'], 'test.') === 0 ||
                strpos($_SERVER['SERVER_NAME'], 'staging.') === 0
            )) {
                $environment = 'development';
            }

            $performanceData = [
                'event_id' => bin2hex(random_bytes(16)),
                'timestamp' => $metrics['timestamp'],
                'platform' => 'php',
                'level' => 'info',
                'type' => 'transaction',
                'transaction' => "API.{$metrics['operation']}",
                'transaction_info' => [
                    'source' => 'custom'
                ],
                'contexts' => [
                    'trace' => [
                        'trace_id' => $trace_id,
                        'span_id' => $span_id,
                        'op' => 'soap.client',
                        'status' => $metrics['success'] ? 'ok' : 'error',
                    ],
                    'performance' => [
                        'duration_ms' => floatval($metrics['duration']),
                        'samples_taken' => 1
                    ],
                    'runtime' => [
                        'name' => 'php',
                        'version' => PHP_VERSION,
                        'openssl_version' => $openssl_version,
                        'memory_limit' => ini_get('memory_limit'),
                        'max_execution_time' => ini_get('max_execution_time')
                    ],
                    'os' => [
                        'name' => PHP_OS,
                        'version' => php_uname('r'),
                        'build' => php_uname('v')
                    ],
                    'device' => [
                        'hostname' => gethostname() ?: 'unknown',
                        'processor_count' => defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Linux' ? (function_exists('shell_exec') ? (int)\shell_exec('nproc 2>/dev/null') ?: 1 : 1) : 1                    ]
                ],
                'tags' => [
                    'release' => self::VERSION,
                    'environment' => $environment,
                    'application' => $this->application,
                    'operation' => $metrics['operation'],
                    'vhost_user' => $vhostUser,
                    'ip_address' => $this->getServerIp(),
                    'php_version' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
                    'soap_version' => defined('SOAP_1_2') ? '1.2' : '1.1',
                    'openssl_enabled' => extension_loaded('openssl') ? 'true' : 'false'
                ],
                'measurements' => [
                    'duration' => [
                        'value' => floatval($metrics['duration']),
                        'unit' => 'millisecond'
                    ],
                    'memory' => [
                        'value' => memory_get_peak_usage(true),
                        'unit' => 'byte'
                    ]
                ],
                'extra' => [
                    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'NA',
                    'server_protocol' => $_SERVER['SERVER_PROTOCOL'] ?? 'NA',
                    'request_time' => $_SERVER['REQUEST_TIME'] ?? time(),
                    'timezone' => date_default_timezone_get(),
                    'soap_enabled' => extension_loaded('soap') ? 'true' : 'false',
                    'curl_enabled' => extension_loaded('curl') ? 'true' : 'false',
                    'json_enabled' => extension_loaded('json') ? 'true' : 'false'
                ],
                'start_timestamp' => $metrics['start_timestamp'],
                'timestamp' => $metrics['timestamp']
            ];

            // Sentry auth headers
            $sentry_auth = [
                'sentry_version=7',
                'sentry_client=php-api/' . self::VERSION,
                "sentry_key=$public_key"
            ];
            if ($secret_key) {
                $sentry_auth[] = "sentry_secret=$secret_key";
            }
            $sentry_auth_header = 'X-Sentry-Auth: Sentry ' . implode(', ', $sentry_auth);

            // Asynchronous sending
            if (function_exists('exec')) {
                $cmd = sprintf(
                    'curl -X POST %s -H %s -H %s -d %s > /dev/null 2>&1 &',
                    escapeshellarg($api_url),
                    escapeshellarg('Content-Type: application/json'),
                    escapeshellarg($sentry_auth_header),
                    escapeshellarg(json_encode($performanceData))
                );
                @exec($cmd);
            } else {
                // Fallback to curl
                $ch = curl_init();
                curl_setopt_array($ch, [
                    CURLOPT_URL => $api_url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => json_encode($performanceData),
                    CURLOPT_TIMEOUT => 1,
                    CURLOPT_CONNECTTIMEOUT => 1,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        $sentry_auth_header
                    ]
                ]);
                @curl_exec($ch);
                curl_close($ch);
            }
        } catch (Exception $e) {
            // Fail silently - we don't log logging failures
            return;
        }
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
     * Check if domain has a .tr TLD
     *
     * @param string $domain Domain name to check
     * @return bool True if domain ends with .tr
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
            $vhostUser = function_exists('get_current_user') ? \get_current_user() : '';
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
            curl_setopt($ch, CURLOPT_URL, "https://ipecho.net/plain");
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
