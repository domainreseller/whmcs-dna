<?php
/**
 * Created by PhpStorm.
 * User: bunyaminakcay
 * Project name whmcs-dna
 * 20.11.2022 00:13
 * Bünyamin AKÇAY <bunyamin@bunyam.in>
 */

namespace DomainNameApi;

require_once __DIR__ . '/SharedApiConfigAndUtilsTrait.php';

use Exception;

class DNARest
{
    use SharedApiConfigAndUtilsTrait;

    /**
     * Error reporting enabled
     */
    private bool $errorReportingEnabled = true;
    /**
     * Error Reporting Will send this sentry endpoint, if errorReportingEnabled is true
     * This request does not include sensitive informations, sensitive informations are filtered.
     * @var string $errorReportingDsn
     */
    private string $errorReportingDsn  = '';
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
     * Api Service REST URL
     * @var string $serviceUrl
     */
    private string $serviceUrl          = "https://rest-test.domainnameapi.com/api/v1";
    private string $application         = "CORE";
    public array   $lastRequest         = [];
    public array   $lastResponse        = [];
    public ?array  $lastResponseHeaders = [];
    public ?array  $lastParsedResponse  = [];
    public string  $lastFunction        = '';
    private        $startAt;

    private $token;
    private $tokenExpire;
    private $authenticated = false;
    private $resellerId;

    /**
     * DNARest constructor.
     * Token-based authentication mode:
     * - ($resellerIdUUID, $token) -> use provided API key directly
     *
     * @param string $resellerId
     * @param string $token
     * @throws Exception
     */
    public function __construct($resellerId, $token)
    {
        $this->startAt = microtime(true);
        $this->_setApplication(__FILE__);

        $this->resellerId = $resellerId;
        $this->token      = $token;
    }


    /**
     * Get last request sent to API
     *
     * @return array Request data
     */
    public function getRequestData(): array
    {
        return $this->lastRequest;
    }

    /**
     * Get last response from API
     *
     * @return array Response data
     */
    public function getResponseData(): array
    {
        return $this->lastResponse;
    }

    /**
     * Set last request sent to API
     *
     * @param array $request Request data to set
     * @return DNARest
     */
    public function setRequestData($request)
    {
        $this->lastRequest = $request;
        return $this;
    }

    /**
     * Set last response from API
     *
     * @param array $response Response data to set
     * @return DNARest
     */
    public function setResponseData($response)
    {
        $this->lastResponse = $response;
        return $this;
    }

    /**
     * Get last response headers from API
     *
     * @return ?array Response headers
     */
    public function getResponseHeaders()
    {
        return $this->lastResponseHeaders;
    }

    /**
     * Set last response headers from API
     *
     * @param ?array $headers Response headers to set
     * @return DNARest
     */
    public function setResponseHeaders($headers)
    {
        $this->lastResponseHeaders = $headers;
        return $this;
    }

    /**
     * Get last parsed response from API
     *
     * @return ?array Parsed response data
     */
    public function getParsedResponseData()
    {
        return $this->lastParsedResponse;
    }

    /**
     * Set last parsed response from API
     *
     * @param ?array $response Parsed response data to set
     * @return DNARest
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
     * @return DNARest
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
     * Format HTTP error codes to match SOAP error format: API_{code}_ERROR
     */
    private function formatErrorCode($code): string
    {
        if (is_numeric($code)) {
            return 'API_' . $code . '_ERROR';
        }
        return (string)$code;
    }

    private function get(string $url, array $params = [])
    {
        return $this->request('GET', $url, $params);
    }

    private function post(string $url, array $params = [])
    {
        return $this->request('POST', $url, $params);
    }

    private function put(string $url, array $params = [])
    {
        return $this->request('PUT', $url, $params);
    }

    private function delete(string $url, array $params = [])
    {
        return $this->request('DELETE', $url, $params);
    }


    /**
     * Make API request
     * @param string $method
     * @param string $endpoint
     * @param array $data
     * @return array
     * @throws Exception
     */
    private function request($method, $endpoint, $data = [])
    {
        $parsedResponse     = [];
        $this->lastFunction = __FUNCTION__ . ':' . $method . ' ' . $endpoint;

        $url = $this->serviceUrl . '/' . ltrim($endpoint, '/');

        $payloadForLog     = $data;
        $this->lastRequest = ['url' => $url, 'method' => $method, 'payload' => $payloadForLog];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_TIMEOUT, self::$DEFAULT_TIMEOUT);

        //ignore ssl
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


        $headers = [
            'Content-Type: application/json',
            'Accept: application/json',
            'X-API-KEY: ' . $this->token,  // Swagger'da X-API-KEY kullanılıyor
            '__reseller: ' . $this->resellerId  // Zorunlu header
        ];

        if (in_array($method, ['GET', 'DELETE'])) {
            if (!empty($data)) {
                $url .= '?' . http_build_query($data);
            }
        } else {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response_body             = curl_exec($ch);
        $response_status           = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $this->lastResponseHeaders = curl_getinfo($ch);
        $this->lastResponse        = json_decode($response_body, true) ?? ['raw_response' => $response_body];


        // Debug output removed to avoid breaking consumers

        if (curl_errno($ch)) {
            $error = new Exception('Curl error during request: ' . curl_error($ch));
            $this->sendErrorToSentryAsync($error);
        } else {
            curl_close($ch);


            if ($response_status >= 200 && $response_status <= 299) {
                $parsedResponse           = json_decode($response_body, true);
                $this->lastParsedResponse = $parsedResponse;

                if (method_exists($this, 'sendPerformanceMetricsToSentry')) {
                    $duration = (microtime(true) - $this->startAt) * 1000;
                    $this->sendPerformanceMetricsToSentry([
                        'operation'       => $this->lastFunction,
                        'duration'        => floatval($duration),
                        'success'         => true,
                        'timestamp'       => gmdate('Y-m-d\TH:i:s.', time()) . sprintf('%03d', round(fmod(microtime(true), 1) * 1000)) . 'Z',
                        'start_timestamp' => gmdate('Y-m-d\TH:i:s.', (int)$this->startAt) . sprintf('%03d',  round(fmod($this->startAt, 1) * 1000)) . 'Z'
                    ]);
                }
            } else {
                $parsedResponse           = json_decode($response_body, true);

                // 302 redirect genellikle auth hatası
                if ($response_status == 302 || $response_status == 301) {
                    $parsedResponse = ['message' => 'Invalid API credentials', 'code' => 'CREDENTIALS', 'details' => 'Authentication failed. Check your API key and reseller ID.'];
                }

                $errorMessage             = $parsedResponse['message'] ?? ($parsedResponse['error']['message'] ?? $response_body);
                $errorCode                = $parsedResponse['code'] ?? ($parsedResponse['error']['code'] ?? 'HTTP_' . $response_status);
                $errorDetails             = $parsedResponse['details'] ?? ($parsedResponse['error']['details'] ?? $response_body);
                $this->lastParsedResponse = $this->setError($errorCode, $errorMessage, $errorDetails);
                $error                    = new Exception($errorMessage, $response_status);

                $this->sendErrorToSentryAsync($error);
                throw $error;
            }
        }

        return $parsedResponse;
    }

    /**
     * Get Current account details with balance
     * @return array
     */
    public function getResellerDetails()
    {
        try {
            $response = $this->request('GET', 'deposit/accounts/me');

            // SOAP ile aynı pattern'i kullan
            $resp = [];

            if (isset($response['resellerId'])) {
                $resp['result'] = self::RESULT_OK;
                $resp['id']     = $response['resellerId'];
                $resp['active'] = true; // API'den status gelmiyor, varsayılan true
                $resp['name']   = $response['resellerName'] ?? '';

                // Ana para birimi USD, ikincil TRY
                $resp['balance']  = (string)($response['usdBalance'] ?? 0);
                $resp['currency'] = 'USD';
                $resp['symbol']   = '$';

                // Balances array'i
                $balances         = [
                    [
                        'balance'  => (string)($response['usdBalance'] ?? 0),
                        'currency' => 'USD',
                        'symbol'   => '$'
                    ],
                    [
                        'balance'  => (string)($response['tryBalance'] ?? 0),
                        'currency' => 'TL',
                        'symbol'   => 'TL'
                    ]
                ];
                $resp['balances'] = $balances;
            } else {
                $resp['result'] = self::RESULT_ERROR;
                $resp['error']  = $this->setError('CREDENTIALS', 'Invalid response format',
                    'Response does not contain required fields');
            }

            return $resp;
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'RESELLER_DETAILS', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Get Current primary Balance for your account
     * @param string $currencyId
     * @return array
     */
    public function getCurrentBalance($currencyId = 'USD')
    {
        try {
            $response = $this->request('GET', 'deposit/accounts/me', ['currency' => strtoupper($currencyId)]);

            $balanceKey   = strtolower($currencyId) . 'Balance';
            $currencyName = strtoupper($currencyId);

            // SOAP uyumu: TRY → TL, currency ID'leri eşle
            $currencyMap = [
                'USD' => ['id' => 2, 'name' => 'USD', 'symbol' => '$'],
                'TRY' => ['id' => 1, 'name' => 'TL',  'symbol' => 'TL'],
                'EUR' => ['id' => 3, 'name' => 'EUR', 'symbol' => '€'],
                'GBP' => ['id' => 4, 'name' => 'GBP', 'symbol' => '£'],
            ];
            $mapped = $currencyMap[$currencyName] ?? ['id' => 0, 'name' => $currencyName, 'symbol' => ''];

            return [
                'ErrorCode'        => 0,
                'OperationMessage' => 'Command completed succesfully.',
                'OperationResult'  => 'SUCCESS',
                'Balance'          => number_format($response[$balanceKey] ?? 0, 2, '.', ''),
                'CurrencyId'       => $mapped['id'],
                'CurrencyInfo'     => null,
                'CurrencyName'     => $mapped['name'],
                'CurrencySymbol'   => $mapped['symbol']
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'BALANCE', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Checks availability of domain names with given extensions
     * @param array $domains
     * @param array $extensions
     * @param int $period
     * @param string $Command
     * @return array
     */
    public function checkAvailability($domains, $extensions, $period, $command = 'create')
    {
        try {
            $queries = [];
            foreach ($domains as $domain) {
                foreach ($extensions as $tld) {
                    $queries[] = ['domainName' => $domain . '.' . ltrim($tld, '.')];
                }
            }

            if (empty($queries)) {
                return [
                    'result' => self::RESULT_ERROR,
                    'error'  => $this->setError('AVAILABILITY', 'Domain names not found.', 'No domain names provided for availability check')
                ];
            }

            $response = $this->request('POST', 'domains/bulk-search', $queries);

            $availabilityData = [];
            $items = $response['infos'] ?? $response;
            if (is_array($items)) {
                foreach ($items as $item) {
                    if (!is_array($item)) continue;
                    $tld       = strtolower($item['tld'] ?? substr(strrchr($item['domainName'] ?? '', "."), 1));
                    $domainRaw = $item['domainName'] ?? '';
                    $domainName = str_replace("." . $tld, '', strtolower($domainRaw));
                    $status     = strtolower($item['status'] ?? '');
                    $isAvailable = in_array($status, ['available', '1', 'true']);

                    $availabilityData[] = [
                        "TLD"        => $tld,
                        "DomainName" => $domainName,
                        "Status"     => $isAvailable ? 'available' : 'notavailable',
                        "Command"    => $command,
                        "Period"     => $item['period'] ?? $period,
                        "IsFee"      => $item['isPremium'] ?? false,
                        "Price"      => isset($item['price']) ? number_format((float)$item['price'], 4, '.', '') : null,
                        "Currency"   => $item['currency'] ?? null,
                        "Reason"     => $item['reason'] ?? null,
                    ];
                }
            }

            return $availabilityData;
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'AVAILABILITY', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Get list of domains in your account
     * @param array $extra_parameters
     * @return array
     */
    public function getList($extra_parameters = [])
    {
        try {
            $defaults = ['MaxResultCount' => 200, 'SkipCount' => 0];
            $params   = array_merge($defaults, $extra_parameters);
            $response = $this->request('GET', 'domains', $params);

            return [
                'data'   => [
                    'Domains'    => isset($response['items']) ? array_map(function ($item) {
                        return [
                            'ID'                      => (int)($item['id'] ?? 0),
                            'Status'                  => (string)($item['statusText'] ?? ($item['status'] ?? '')),
                            'DomainName'              => $item['domainName'] ?? '',
                            'AuthCode'                => $item['authCode'] ?? '',
                            'LockStatus'              => !empty($item['lockStatus']) ? 'true' : 'false',
                            'PrivacyProtectionStatus' => !empty($item['privacyProtectionStatus']) ? 'true' : 'false',
                            'IsChildNameServer'       => !empty($item['hosts']) ? 'true' : 'false',
                            'Contacts'          => [
                                'Billing'        => ['ID' => ''],
                                'Technical'      => ['ID' => ''],
                                'Administrative' => ['ID' => ''],
                                'Registrant'     => ['ID' => '']
                            ],
                            'Dates'             => [
                                'Start'         => isset($item['startDate']) ? date('Y-m-d\TH:i:s',
                                    strtotime($item['startDate'])) : '',
                                'Expiration'    => isset($item['expirationDate']) ? date('Y-m-d\TH:i:s',
                                    strtotime($item['expirationDate'])) : '',
                                'RemainingDays' => (int)($item['remainingDay'] ?? 0),
                            ],
                            'NameServers'       => $item['nameServers'] ?? [],
                            'Additional'        => [],
                            'ChildNameServers'  => []
                        ];
                    }, $response['items']) : [],
                ],
                'result'     => self::RESULT_OK,
                'TotalCount' => (int)($response['totalCount'] ?? 0),
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'DOMAIN_LIST', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Get TLD list and pricing matrix
     * @param int $count
     * @return array
     */
    public function getTldList($count = 20)
    {
        try {
            $response = $this->get('products/tlds', ['MaxResultCount' => $count, 'SkipCount' => 0]);

            $tldData   = [];
            $idCounter = 1;
            if (isset($response['items']) && is_array($response['items'])) {
                foreach ($response['items'] as $tld) {
                    $pricing    = [];
                    $currencies = [];

                    // Fiyatlar
                    if (isset($tld['prices'][0]) && is_array($tld['prices'][0])) {
                        $priceTypes = [
                            'register'  => 'registration',
                            'renew'     => 'renew',
                            'transfer'  => 'transfer',
                            'restore'   => 'restore',
                            'refund'    => 'refund',
                            'backorder' => 'backorder'
                        ];
                        foreach ($priceTypes as $apiType => $outType) {
                            if (isset($tld['prices'][0][$apiType])) {
                                $apiValue = $tld['prices'][0][$apiType];
                                if (is_array($apiValue) && isset($apiValue[0])) {
                                    // Dizi ise
                                    foreach ($apiValue as $priceInfo) {
                                        if (is_array($priceInfo)) {
                                            $period = (int)($priceInfo['period'] ?? 1);
                                            if ($period < 1) $period = 1; // SOAP uyumu: period 0 → 1
                                            $price                      = isset($priceInfo['price']) ? number_format((float)$priceInfo['price'],
                                                4, '.', '') : '0.0000';
                                            $pricing[$outType][$period] = $price;
                                            $currencies[$outType]       = $priceInfo['currency'] ?? '';
                                        }
                                    }
                                } elseif (is_array($apiValue)) {
                                    // Obje ise
                                    $period = (int)($apiValue['period'] ?? 1);
                                    if ($period < 1) $period = 1; // SOAP uyumu: period 0 → 1
                                    $price                      = isset($apiValue['price']) ? number_format((float)$apiValue['price'],
                                        4, '.', '') : '0.0000';
                                    $pricing[$outType][$period] = $price;
                                    $currencies[$outType]       = $apiValue['currency'] ?? '';
                                }
                            }
                        }
                    }

                    // Sort pricing arrays by key (period) - SOAP uses 1-based ascending order
                    foreach ($pricing as $type => $periods) {
                        ksort($pricing[$type], SORT_NUMERIC);
                    }

                    $tldData[] = [
                        'id'               => $idCounter++,
                        'status'           => $tld['status'] ?? 'Active',
                        'maxchar'          => $tld['constraints']['maxLenght'] ?? 63,
                        'maxperiod'        => $tld['maxRegistrationPeriod'] ?? 10,
                        'minchar'          => $tld['constraints']['minLength'] ?? 1,
                        'minperiod'        => $tld['minRegistrationPeriod'] ?? 1,
                        'tld'              => $tld['name'],
                        'gracePeriod'      => $tld['addGracePeriod'] == 1,
                        'redemptionPeriod' => $tld['failurePeriod'] == 1,
                        'pricing'          => $pricing,
                        'currencies'       => $currencies
                    ];
                }
            }

            return [
                'data'   => $tldData,
                'result' => self::RESULT_OK
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'TLD_LIST', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Get detailed information for a domain
     * @param string $domainName
     * @return array
     */
    public function getDetails($domainName)
    {
        try {
            $response = $this->request('GET', 'domains/info', ['DomainName' => $domainName]);

            return $this->parseDomainInfo($response);
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'DOMAIN_DETAILS', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Modify nameservers for a domain
     * @param string $domainName
     * @param array $nameServers
     * @return array
     */
    public function modifyNameServer($domainName, $nameServers)
    {
        try {
            $payload  = ['domainName' => $domainName, 'nameServers' => array_values($nameServers)];
            $response = $this->request('PUT', 'domains/dns/name-server', $payload);

            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'NameServers' => $response['nameServers'] ?? $nameServers
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'MODIFY_NS', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Enable Theft Protection Lock for a domain
     * @param string $domainName
     * @return array
     */
    public function enableTheftProtectionLock($domainName)
    {
        try {
            $data     = ['domainName' => $domainName];
            $response = $this->request('POST', 'domains/lock', $data);

            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'LockStatus' => true
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'ENABLE_LOCK', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Disable Theft Protection Lock for a domain
     * @param string $domainName
     * @return array
     */
    public function disableTheftProtectionLock($domainName)
    {
        try {
            $data     = ['domainName' => $domainName];
            $response = $this->request('POST', 'domains/unlock', $data);
            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'LockStatus' => false
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'DISABLE_LOCK', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Add child nameserver for a domain
     * @param string $domainName
     * @param string $nameServer (hostname of child nameserver, e.g., ns1child.example.com)
     * @param string $ipAddress (IP of child nameserver)
     * @return array
     */
    public function addChildNameServer($domainName, $nameServer, $ipAddress)
    {
        try {
            $payload = [
                'domainName'  => $domainName,
                'hostName'    => $nameServer,
                'ipAddresses' => [
                    [
                        'ipAddress' => $ipAddress,
                        'ipVersion' => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? 'v4' : 'v6'
                    ]
                ]
            ];
            $response = $this->request('POST', 'domains/dns/host', $payload);

            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'NameServer' => $nameServer,
                    'IPAdresses' => [$ipAddress]
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'ADD_CHILD_NS', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Delete child nameserver from a domain
     * @param string $domainName
     * @param string $nameServer (hostname of child nameserver to delete)
     * @return array
     */
    public function deleteChildNameServer($domainName, $nameServer)
    {
        try {
            $payload = [
                'domainName' => $domainName,
                'hostName'   => $nameServer
            ];
            $response = $this->request('DELETE', 'domains/dns/host' ,$payload);

            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'NameServer' => $nameServer
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'DELETE_CHILD_NS', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Modify IP address of child nameserver
     * @param string $domainName
     * @param string $nameServer (hostname of child nameserver)
     * @param string $ipAddress (new IP address)
     * @return array
     */
    public function modifyChildNameServer($domainName, $nameServer, $ipAddress)
    {
        try {
            $payload = [
                'domainName'  => $domainName,
                'hostName'    => $nameServer,
                'newHostName' => $nameServer,
                'ipAddresses' => [
                    [
                        'ipAddress' => $ipAddress,
                        'ipVersion' => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? 'v4' : 'v6'
                    ]
                ]
            ];
            $response = $this->request('PUT', 'domains/dns/host', $payload);

            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'NameServer' => $response['hostName'] ?? $nameServer,
                    'IPAdresses' => $response['ipAddresses'] ?? [$ipAddress]
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'MODIFY_CHILD_NS', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Get contact information for a domain
     * @param string $domainName
     * @return array
     */
    public function getContacts($domainName)
    {
        try {
            // Domain info contacts bilgisini zaten içeriyor
            $domainInfo = $this->request('GET', 'domains/info', ['DomainName' => $domainName]);

            // REST API contactType → SOAP key mapping
            $typeMap = [
                'Admin'      => 'Administrative',
                'Billing'    => 'Billing',
                'Tech'       => 'Technical',
                'Registrant' => 'Registrant'
            ];

            $contacts = [];
            if (isset($domainInfo['contacts']) && is_array($domainInfo['contacts'])) {
                foreach ($domainInfo['contacts'] as $contactItem) {
                    $apiType = $contactItem['contactType'] ?? '';
                    $soapKey = $typeMap[$apiType] ?? ucfirst(strtolower($apiType));
                    $contacts[$soapKey] = $this->parseContactInfo(array_merge($contactItem, ['type' => $soapKey]));
                }
            }

            // SOAP key order: Administrative, Billing, Registrant, Technical
            $orderedContacts = [];
            foreach (['Administrative', 'Billing', 'Registrant', 'Technical'] as $key) {
                if (isset($contacts[$key])) {
                    $orderedContacts[$key] = $contacts[$key];
                }
            }

            return [
                'data'   => ['contacts' => $orderedContacts],
                'result' => self::RESULT_OK
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'GET_CONTACTS', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Saves or updates contact information for all contact types of a domain
     * @param string $domainName
     * @param array $contacts (['Registrant' => [...], 'Admin' => [...], ...])
     * @return array
     */
    public function saveContacts($domainName, $contacts)
    {
        try {
            $payloadContacts = [];
            foreach ($contacts as $type => $details) {
                $payloadContacts[] = $this->parseContact($details, ucfirst(strtolower($type)));
            }
            $response = $this->request('PUT', 'domains/contacts/update', ['domainName' => $domainName, 'contacts' => $payloadContacts]);

            $parsedContacts = [];
            if (isset($response['contacts']) && is_array($response['contacts'])) {
                foreach ($response['contacts'] as $contact) {
                    $parsedContacts[ucfirst(strtolower($contact['type']))] = $this->parseContactInfo($contact);
                }
            }

            return [
                'result' => self::RESULT_OK,
                'data'   => ['contacts' => $parsedContacts]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'SAVE_CONTACTS', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Start domain transfer to your account
     * @param string $domainName
     * @param string $eppCode
     * @param int $period
     * @param array $contacts
     * @return array
     */
    public function transfer($domainName, $eppCode, $period, $contacts = [])
    {
        try {
            $payloadContacts = [];
            if (!empty($contacts)) {
                foreach ($contacts as $type => $details) {
                    $payloadContacts[] = $this->parseContact($details, ucfirst(strtolower($type)));
                }
            }

            $payload = [
                'domainName' => $domainName,
                'authCode'   => $eppCode,
                'period'     => $period,
                'contacts'   => $payloadContacts
            ];

            $response = $this->request('POST', 'domains/transfer', $payload);

            return $this->parseDomainInfo($response);
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'TRANSFER_DOMAIN', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Cancel pending incoming transfer
     * @param string $domainName
     * @return array
     */
    public function cancelTransfer($domainName)
    {
        try {
            $response = $this->request('POST', "domains/transfers/cancel", ['domainName' => $domainName]);

            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'DomainName' => $domainName,
                    'Status'     => $response['status'] ?? 'Cancelled'
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'CANCEL_TRANSFER', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Approve pending outgoing transfer
     * @param string $domainName
     * @return array
     */
    public function approveTransfer($domainName)
    {
        try {
            $response = $this->request('POST', "domains/transfers/approve", ['domainName' => $domainName]);

            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'DomainName' => $domainName,
                    'Status'     => $response['status'] ?? 'Approved'
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'APPROVE_TRANSFER', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Reject pending outgoing transfer
     * @param string $domainName
     * @return array
     */
    public function rejectTransfer($domainName)
    {
        try {
            $response = $this->request('POST', "domains/transfers/reject", ['domainName' => $domainName]);

            return [
                'result' => self::RESULT_OK,
                'data'   => [
                    'DomainName' => $domainName,
                    'Status'     => $response['status'] ?? 'Rejected'
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'REJECT_TRANSFER', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Renew domain registration
     * @param string $domainName
     * @param int $period
     * @return array
     */
    public function renew($domainName, $period)
    {
        try {
            $payload  = ['domainName' => $domainName, 'period' => $period];
            $response = $this->request('POST', 'domains/renew', $payload);

            if ($response["expirationDate"] ?? false) {
                return [
                    'result' => self::RESULT_OK,
                    'data'   => [
                        'ExpirationDate' => isset($response['expirationDate']) ? date('Y-m-d\TH:i:s', strtotime($response['expirationDate'])) : ''
                    ]
                ];
            } else {
                $this->sendErrorToSentryAsync(new Exception("[DOMAIN_RENEW] " . self::$DEFAULT_ERRORS['DOMAIN_RENEW']['description']));
                return [
                    'result' => self::RESULT_ERROR,
                    'error'  => $this->setError("DOMAIN_RENEW")
                ];
            }
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'RENEW_DOMAIN', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Register new domain with contact information
     * @param string $domainName
     * @param int $period
     * @param array $contacts
     * @param array $nameServers
     * @param bool $eppLock
     * @param bool $privacyLock
     * @param array $additionalAttributes
     * @return array
     */
    public function registerWithContactInfo(
        $domainName,
        $period,
        $contacts,
        $nameServers = [],
        $eppLock = true,
        $privacyLock = false,
        $additionalAttributes = []
    ) {
        try {
            $payloadContacts = [];
            foreach ($contacts as $type => $details) {
                $payloadContacts[] = $this->parseContact($details, ucfirst(strtolower($type)));
            }

            $payload = [
                'domainName'           => $domainName,
                'period'               => $period,
                'nameServers'          => empty($nameServers) ? self::$DEFAULT_NAMESERVERS : $nameServers,
                'isLocked'             => $eppLock,
                'privacyEnabled'       => $privacyLock,
                'contacts'             => $payloadContacts,
                'additionalAttributes' => $additionalAttributes
            ];

            $response = $this->request('POST', 'domains/register', $payload);

            return $this->parseDomainInfo($response);
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'REGISTER_DOMAIN', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Modify privacy protection status
     * @param string $domainName
     * @param bool $status
     * @param string $reason
     * @return array
     */
    public function modifyPrivacyProtectionStatus($domainName, $status, $reason = 'Owner request')
    {
        try {
            // Eğer reason boş ise, varsayılan değeri kullan
            if (empty($reason)) {
                $reason = self::$DEFAULT_REASON;
            }

            $payload  = ['domainName' => $domainName, 'privacyStatus' => $status===true];
            $response = $this->request('POST', "domains/privacy", $payload);

            return [
                'data'   => [
                    'PrivacyProtectionStatus' => $status
                ],
                'result' => self::RESULT_OK
            ];
        } catch (Exception $e) {
            return [
                'result' => self::RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'MODIFY_PRIVACY', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Synchronize domain information with registry
     * @param string $domainName
     * @return array
     */
    public function syncFromRegistry($domainName)
    {
        return $this->getDetails($domainName);
    }

    /**
     * Extract contact IDs from contacts array
     * @param array $contacts
     * @return array
     */
    private function parseContactIds($contacts)
    {
        $result = [
            'Billing'        => ['ID' => 0],
            'Technical'      => ['ID' => 0],
            'Administrative' => ['ID' => 0],
            'Registrant'     => ['ID' => 0]
        ];
        if (!is_array($contacts)) return $result;

        $typeMap = ['Billing' => 'Billing', 'Tech' => 'Technical', 'Admin' => 'Administrative', 'Registrant' => 'Registrant'];
        foreach ($contacts as $c) {
            $apiType = $c['ContactType'] ?? ($c['contactType'] ?? '');
            $soapKey = $typeMap[$apiType] ?? null;
            if ($soapKey) {
                $result[$soapKey]['ID'] = $c['handle'] ?? ($c['id'] ?? 0);
            }
        }
        return $result;
    }

    /**
     * Parse domain information from response
     * @param array $data
     * @return array
     */
    private function parseDomainInfo($data)
    {
        if (empty($data)) {
            return [];
        }
        return [
            'data'   => [
                'ID'                      => (int)($data['id'] ?? 0),
                'Status'                  => (string)($data['status'] ?? ''),
                'DomainName'              => (string)($data['domainName'] ?? ($data['name'] ?? '')),
                'AuthCode'                => (string)($data['authCode'] ?? ($data['eppCode'] ?? '')),
                'LockStatus'              => !empty($data['lockStatus']) ? 'true' : 'false',
                'PrivacyProtectionStatus' => !empty($data['privacyProtectionStatus']) ? 'true' : 'false',
                'IsChildNameServer'       => !empty($data['hosts']) ? 'true' : 'false',
                'Contacts'                => $this->parseContactIds($data['contacts'] ?? []),
                'Dates'                   => [
                    'Start'         => isset($data['startDate']) ? date('Y-m-d\TH:i:s', strtotime($data['startDate'])) : '',
                    'Expiration'    => isset($data['expirationDate']) ? date('Y-m-d\TH:i:s', strtotime($data['expirationDate'])) : '',
                    'RemainingDays' => (int)($data['remainingDay'] ?? 0)
                ],
                'NameServers'             => isset($data['nameservers']) ? array_map('strval',
                    $data['nameservers']) : [],
                'Additional'              => isset($data['additionalAttributes']) ? (array)$data['additionalAttributes'] : [],
                'ChildNameServers'        => isset($data['hosts']) ? array_map(function ($ns) {
                    // SOAP uyumu: ip string olarak dönüyor (son IP)
                    $ips = array_map(function ($ip) {
                        return $ip['ipAddress'];
                    }, $ns['ipAddresses'] ?? []);
                    return [
                        'ns' => $ns['name'],
                        'ip' => !empty($ips) ? end($ips) : ''
                    ];
                }, $data['hosts']) : []
            ],
            'result' => self::RESULT_OK
        ];
    }

    /**
     * Parse contact information from response
     * @param array $data
     * @return array
     */
    private function parseContactInfo($data)
    {
        if (empty($data)) {
            return [];
        }
        return [
            'ID'         => $data['handle'] ?? ($data['id'] ?? ''),
            'Status'     => $data['status'] ?? 'Active',
            'AuthCode'   => $data['authCode'] ?? '',
            'FirstName'  => $data['firstName'] ?? '',
            'LastName'   => $data['lastName'] ?? '',
            'Company'    => $data['companyName'] ?? ($data['organizationName'] ?? ($data['company'] ?? '')),
            'EMail'      => $data['eMail'] ?? ($data['emailAddress'] ?? ($data['email'] ?? '')),
            'Type'       => $data['type'] ?? '',
            'Address'    => [
                'Line1'   => $data['address'] ?? ($data['addressLine1'] ?? ''),
                'Line2'   => $data['addressLine2'] ?? '',
                'Line3'   => $data['addressLine3'] ?? '',
                'State'   => $data['state'] ?? ($data['stateOrProvince'] ?? ''),
                'City'    => $data['city'] ?? '',
                'Country' => $data['country'] ?? ($data['countryCode'] ?? ''),
                'ZipCode' => $data['postalCode'] ?? ($data['zipCode'] ?? '')
            ],
            'Phone'      => [
                'Phone' => [
                    'Number'      => $data['phone'] ?? ($data['phoneNumber'] ?? ''),
                    'CountryCode' => $data['phoneCountryCode'] ?? ''
                ],
                'Fax'   => [
                    'Number'      => $data['fax'] ?? ($data['faxNumber'] ?? ''),
                    'CountryCode' => $data['faxCountryCode'] ?? ''
                ]
            ],
            'Additional' => $data['additionalAttributes'] ?? []
        ];
    }

    /**
     * Parse contact for request
     * @param array $contact
     * @param string $type
     * @return array
     */
    private function parseContact($contact, $type)
    {
        // SOAP type → REST API type mapping
        $typeMap = [
            'Administrative' => 'Admin',
            'Technical'      => 'Tech',
            'Billing'        => 'Billing',
            'Registrant'     => 'Registrant'
        ];
        $apiType = $typeMap[$type] ?? $type;

        // Input hem flat (AddressLine1) hem nested (Address.Line1) olabilir
        $address = $contact['AddressLine1'] ?? ($contact['Address']['Line1'] ?? '');
        if (!empty($contact['AddressLine2'] ?? ($contact['Address']['Line2'] ?? ''))) {
            $address .= ' ' . ($contact['AddressLine2'] ?? ($contact['Address']['Line2'] ?? ''));
        }

        $phone     = $contact['Phone'] ?? ($contact['Phone']['Phone']['Number'] ?? '');
        $phoneCc   = $contact['PhoneCountryCode'] ?? ($contact['Phone']['Phone']['CountryCode'] ?? '');
        $fax       = $contact['Fax'] ?? ($contact['Phone']['Fax']['Number'] ?? '');
        $faxCc     = $contact['FaxCountryCode'] ?? ($contact['Phone']['Fax']['CountryCode'] ?? '');

        // Array ise string olarak al (nested format)
        if (is_array($phone)) { $phoneCc = $phone['Phone']['CountryCode'] ?? ''; $phone = $phone['Phone']['Number'] ?? ''; }
        if (is_array($fax)) { $faxCc = $fax['Fax']['CountryCode'] ?? ''; $fax = $fax['Fax']['Number'] ?? ''; }

        return [
            'contactType'      => $apiType,
            'firstName'        => $contact['FirstName'] ?? '',
            'lastName'         => $contact['LastName'] ?? '',
            'companyName'      => $contact['Company'] ?? '',
            'eMail'            => $contact['EMail'] ?? '',
            'address'          => $address,
            'city'             => $contact['City'] ?? ($contact['Address']['City'] ?? ''),
            'state'            => $contact['State'] ?? ($contact['Address']['State'] ?? ''),
            'country'          => $contact['Country'] ?? ($contact['Address']['Country'] ?? ''),
            'postalCode'       => $contact['ZipCode'] ?? ($contact['Address']['ZipCode'] ?? ''),
            'phoneCountryCode' => (string)$phoneCc,
            'phone'            => (string)$phone,
            'faxCountryCode'   => (string)$faxCc,
            'fax'              => (string)$fax,
        ];
    }

    /**
     * Domain is TR type
     * @param string $domain
     * @return bool
     */
    public function isTrTLD($domain)
    {
        return strtolower(substr($domain, -3)) === '.tr';
    }

    /**
     * Check if domain transfer is possible
     * Not supported in REST API mode.
     *
     * @param string $domainName Domain name to check transfer for
     * @param string $authcode Authorization/EPP code for transfer
     * @return array
     * @throws Exception
     */
    public function checkTransfer($domainName, $authcode)
    {
        throw new Exception("checkTransfer is not supported in REST API");
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
} 