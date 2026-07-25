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
    public const URL_PROD = "https://api.domainresellerapi.com/api/v1";
    public const URL_OTE  = "https://ote.domainresellerapi.com/api/v1";

    /**
     * Gateway DomainStatus enum (byte) → textual label.
     *
     * The domain listing endpoint returns the numeric enum in `status` and the
     * textual label in `statusCode`; some responses may still deliver the
     * numeric value only. This map lets us recover the SOAP-style label that
     * callers compare against (e.g. "Active"). Source: gateway DomainStatus enum.
     */
    private const DOMAIN_STATUS_MAP = [
        0  => 'Active',
        1  => 'WaitingForRegistration',
        2  => 'WaitingForDocument',
        3  => 'WaitingForIncomingTransfer',
        4  => 'TransferredOut',
        7  => 'PendingDelete',
        8  => 'Deleted',
        9  => 'ConfirmationEmailSend',
        11 => 'WaitingForOutgoingTransfer',
        12 => 'PendingHold',
        15 => 'ModificationPending',
        18 => 'InCase',
        19 => 'PendingRestore',
    ];

    /**
     * Gateway DomainRenewalMode enum (byte) → textual label.
     */
    private const DOMAIN_RENEWAL_MODE_MAP = [
        1 => 'AutoRenew',
        2 => 'AutoExpire',
        3 => 'AutoDelete',
    ];

    private string $serviceUrl          = self::URL_PROD;
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
    public function __construct($resellerId, $token,$testmode=false)
    {
        if($testmode){
            $this->serviceUrl = self::URL_OTE;
        }
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
            return 'API_' . $code ;
        }
        return (string)$code;
    }

    /**
     * Standard reason phrase for an HTTP status code. Used to build a legible
     * fallback message when the gateway returns an error status with an empty
     * body (e.g. 429 -> "Too Many Requests" instead of a generic "empty
     * response"). Falls back to a class-based label for unknown codes.
     */
    private function httpStatusReason($status): string
    {
        $reasons = [
            400 => 'Bad Request',
            401 => 'Unauthorized',
            402 => 'Payment Required',
            403 => 'Forbidden',
            404 => 'Not Found',
            405 => 'Method Not Allowed',
            408 => 'Request Timeout',
            409 => 'Conflict',
            422 => 'Unprocessable Entity',
            429 => 'Too Many Requests',
            500 => 'Internal Server Error',
            502 => 'Bad Gateway',
            503 => 'Service Unavailable',
            504 => 'Gateway Timeout',
        ];
        $status = (int) $status;
        if (isset($reasons[$status])) {
            return $reasons[$status];
        }
        if ($status >= 500) return 'Server Error';
        if ($status >= 400) return 'Client Error';
        return 'Unexpected Response';
    }

    /**
     * Format an API-supplied date to 'Y-m-d\TH:i:s', returning '' when the
     * value is missing OR unparseable. Without the strtotime() !== false
     * guard, an unexpected/MinValue date from the (.NET) gateway makes
     * strtotime() return false, which date() then coerces to epoch and emits
     * a bogus "1970-01-01T00:00:00" — silently corrupting domain expiry/next-due
     * dates in the host billing system.
     */
    private function formatApiDate($value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $ts = strtotime((string) $value);
        return $ts === false ? '' : date('Y-m-d\TH:i:s', $ts);
    }

    /**
     * Remaining days until an API-supplied date, floored at 0. Returns 0 when
     * the date is missing or unparseable (see formatApiDate() for why the
     * strtotime() guard matters).
     */
    private function remainingDaysFromDate($value): int
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $ts = strtotime((string) $value);
        if ($ts === false) {
            return 0;
        }
        return max(0, (int) ceil(($ts - time()) / 86400));
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
            'X-API-KEY: ' . $this->token,  // Swagger uses X-API-KEY
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
            // Transport failure (timeout, "Could not resolve host", reset).
            // Do NOT throw: surface it the same way a bad/empty HTTP response
            // does. Populate lastParsedResponse via setError() (mirroring the
            // HTTP-error branch) and leave the body empty, so the caller's
            // normal empty-response handling turns it into a RESULT_ERROR
            // instead of an uncaught exception. Still reported to Sentry.
            $curlErrno    = curl_errno($ch);
            $errorMessage = 'Curl error during request: ' . curl_error($ch);
            curl_close($ch);

            $this->lastParsedResponse = $this->setError('CURL_' . $curlErrno, $errorMessage, $errorMessage);
            $this->sendErrorToSentryAsync(new Exception($errorMessage, $curlErrno));
            // $parsedResponse stays [] — consumers map an empty body to the
            // standard "RESPONSE" error, identical to a real empty response.
        }
        else {
            curl_close($ch);


            if ($response_status >= 200 && $response_status <= 299) {
                $parsedResponse           = json_decode($response_body, true);
                $this->lastParsedResponse = $parsedResponse;
                $isSuccess                = true;
            } else {
                $parsedResponse           = json_decode($response_body, true);

                // 302 redirect usually indicates an auth error
                if ($response_status == 302 || $response_status == 301) {
                    $parsedResponse = ['message' => 'Invalid API credentials', 'code' => 'CREDENTIALS', 'details' => 'Authentication failed. Check your API key and reseller ID.'];
                }

                $errorMessage             = $parsedResponse['message'] ?? ($parsedResponse['error']['message'] ?? $response_body);
                $errorCode                = $parsedResponse['code'] ?? ($parsedResponse['error']['code'] ?? 'HTTP_' . $response_status);
                $errorDetails             = $parsedResponse['details'] ?? ($parsedResponse['error']['details'] ?? $response_body);

                // Empty body on an error status (429/502/503/504 from the
                // gateway, connection reset) leaves $errorMessage == '' — the
                // module then shows a blank error and Sentry records "(No error
                // message)". Synthesise a stable, human-readable message from the
                // HTTP status (e.g. "HTTP 429 - Too Many Requests") so the
                // failure is legible end to end.
                if (trim((string) $errorMessage) === '') {
                    $errorMessage = 'HTTP ' . $response_status . ' - ' . $this->httpStatusReason($response_status);
                    if (trim((string) $errorDetails) === '') {
                        $errorDetails = $errorMessage;
                    }
                }

                // The gateway sometimes surfaces raw backend internals as the
                // message: ABP boilerplate ("Exception of type
                // 'Volo.Abp.BusinessException' was thrown.") or the bare error
                // code echoed back ("Dna.DomainService:Dns:10024"). Neither
                // tells the reseller anything. Prefer the details field when it
                // carries real information (e.g. "Host is linked but not marked
                // as linked in entity"), otherwise synthesise a stable message
                // around the error code. (BUG-10124)
                $isBoilerplate = (bool) preg_match("/Exception of type '[^']*' was thrown/i", (string) $errorMessage);
                if ($isBoilerplate || (string) $errorMessage === (string) $errorCode) {
                    $detailsClean = trim(preg_replace('/^Dna\s+/', '', (string) $errorDetails));
                    if ($detailsClean !== ''
                        && $detailsClean !== (string) $errorMessage
                        && $detailsClean !== (string) $errorCode
                        && !preg_match("/Exception of type '[^']*' was thrown/i", $detailsClean)) {
                        $errorMessage = $detailsClean;
                    } else {
                        $errorMessage = 'The operation could not be completed by the registry (error: ' . $errorCode . '). Please try again or contact support.';
                    }
                }

                // The REST gateway returns per-field validation errors under
                // error.validationErrors[] as { members: ["$.path.field"], message }.
                // Surface them to the end user instead of the generic "Validation
                // failed. Please check the provided information." Otherwise the
                // reseller never learns WHICH field is wrong. REST-only — the SOAP
                // gateway does not return this shape.
                $validationErrors = $parsedResponse['error']['validationErrors']
                    ?? ($parsedResponse['validationErrors'] ?? null);
                if (is_array($validationErrors) && !empty($validationErrors)) {
                    $flattened = $this->flattenValidationErrors($validationErrors);
                    if ($flattened !== '') {
                        $errorDetails = $flattened;
                    }
                }

                $this->lastParsedResponse = $this->setError($errorCode, $errorMessage, $errorDetails);
                $error                    = new Exception($errorMessage, $response_status);
                $isSuccess                = false;
            }

            // Smart sampling for performance metrics. Only SUCCESSFUL calls
            // are sampled here: a failure already ships an error event (with
            // the same operation/duration context), so emitting a perf
            // transaction too would double the POSTs and the Sentry ingest —
            // and would leak telemetry for ignored errors that the error
            // channel deliberately suppresses. Slow successful calls (>1s)
            // are always sampled, otherwise the configured random rate.
            if ($isSuccess && method_exists($this, 'sendPerformanceMetricsToSentry')) {
                $duration = (microtime(true) - $this->startAt) * 1000;
                if ($duration > 1000 || (mt_rand(1, 1000) <= self::$PERFORMANCE_SAMPLE_RATE)) {
                    $this->sendPerformanceMetricsToSentry([
                        'operation'       => $this->lastFunction,
                        'duration'        => floatval($duration),
                        'success'         => true,
                        'timestamp'       => gmdate('Y-m-d\TH:i:s.', time()) . sprintf('%03d', round(fmod(microtime(true), 1) * 1000)) . 'Z',
                        'start_timestamp' => gmdate('Y-m-d\TH:i:s.', (int)$this->startAt) . sprintf('%03d', round(fmod($this->startAt, 1) * 1000)) . 'Z'
                    ]);
                }
            }

            if (!$isSuccess) {
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

            // Use the same pattern as SOAP
            $resp = [];

            if (isset($response['resellerId'])) {
                $resp['result'] = self::$RESULT_OK;
                $resp['id']     = $response['resellerId'];
                $resp['active'] = true; // API returns no status; default to true
                $resp['name']   = $response['resellerName'] ?? '';

                // Primary currency USD, secondary TRY
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
                $resp['result'] = self::$RESULT_ERROR;
                $resp['error']  = $this->setError('CREDENTIALS', 'Invalid response format',
                    'Response does not contain required fields');
            }

            return $resp;
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
        $currencyName = strtoupper($currencyId);

        // SOAP parity: TRY -> TL, map currency IDs
        $currencyMap = [
            'USD' => ['id' => 2, 'name' => 'USD', 'symbol' => '$'],
            'TRY' => ['id' => 1, 'name' => 'TL',  'symbol' => 'TL'],
            'EUR' => ['id' => 3, 'name' => 'EUR', 'symbol' => '€'],
            'GBP' => ['id' => 4, 'name' => 'GBP', 'symbol' => '£'],
        ];
        $mapped = $currencyMap[$currencyName] ?? ['id' => 0, 'name' => $currencyName, 'symbol' => ''];

        try {
            $response = $this->request('GET', 'deposit/accounts/me', ['currency' => $currencyName]);

            $balanceKey = strtolower($currencyId) . 'Balance';

            return [
                'ErrorCode'        => 0,
                'OperationMessage' => 'Command completed succesfully.',
                'OperationResult'  => 'SUCCESS',
                'Balance'          => number_format((float)($response[$balanceKey] ?? 0), 2, '.', ''),
                'CurrencyId'       => $mapped['id'],
                'CurrencyInfo'     => null,
                'CurrencyName'     => $mapped['name'],
                'CurrencySymbol'   => $mapped['symbol']
            ];
        } catch (Exception $e) {
            // Keep the same envelope shape as the success path so callers can
            // rely on a single schema (ErrorCode/OperationResult signal failure).
            return [
                'ErrorCode'        => $this->formatErrorCode($e->getCode()) ?: 'BALANCE',
                'OperationMessage' => $e->getMessage(),
                'OperationResult'  => 'FAILED',
                'Balance'          => '0.00',
                'CurrencyId'       => $mapped['id'],
                'CurrencyInfo'     => null,
                'CurrencyName'     => $mapped['name'],
                'CurrencySymbol'   => $mapped['symbol']
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
                    'result' => self::$RESULT_ERROR,
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
                'result' => self::$RESULT_ERROR,
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
            // Callers built for the SOAP gateway page with PageNumber (0-based)
            // + PageSize and sort with OrderColumn/OrderDirection (dna-extended
            // does exactly this). The REST endpoint only knows SkipCount /
            // MaxResultCount / Sorting — translate here, otherwise the legacy
            // keys are ignored and every "page" returns the same first batch.
            if (isset($extra_parameters['PageSize'])) {
                $extra_parameters['MaxResultCount'] = (int) $extra_parameters['PageSize'];
            }
            if (isset($extra_parameters['PageNumber'])) {
                $pageSize = (int) ($extra_parameters['PageSize'] ?? 200);
                $extra_parameters['SkipCount'] = (int) $extra_parameters['PageNumber'] * $pageSize;
            }
            if (isset($extra_parameters['OrderColumn'])) {
                $direction = strtoupper((string) ($extra_parameters['OrderDirection'] ?? 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
                $extra_parameters['Sorting'] = $extra_parameters['OrderColumn'] . ' ' . $direction;
            }
            unset($extra_parameters['PageNumber'], $extra_parameters['PageSize'],
                $extra_parameters['OrderColumn'], $extra_parameters['OrderDirection']);

            $defaults = ['MaxResultCount' => 200, 'SkipCount' => 0];
            $params   = array_merge($defaults, $extra_parameters);
            $response = $this->request('GET', 'domains', $params);

            return [
                'data'   => [
                    'Domains'    => isset($response['items']) ? array_map(function ($item) {
                        return [
                            'ID'                      => (int)($item['id'] ?? 0),
                            // The listing endpoint puts the textual label in
                            // `statusCode` and the numeric enum in `status`
                            // (the reverse of domains/info). Prefer the label,
                            // then legacy `statusText`, then resolve the enum.
                            'Status'                  => (isset($item['statusCode']) && $item['statusCode'] !== '' && !is_numeric($item['statusCode']))
                                ? (string)$item['statusCode']
                                : (isset($item['statusText']) && $item['statusText'] !== '' && !is_numeric($item['statusText'])
                                    ? (string)$item['statusText']
                                    : $this->normalizeDomainStatus($item['status'] ?? '')),
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
                                'Start'         => $this->formatApiDate($item['startDate'] ?? null),
                                'Expiration'    => $this->formatApiDate($item['expirationDate'] ?? null),
                                'RemainingDays' => (int)($item['remainingDay'] ?? 0),
                            ],
                            'NameServers'       => $item['nameServers'] ?? [],
                            'Additional'        => [],
                            'ChildNameServers'  => []
                        ];
                    }, $response['items']) : [],
                ],
                'result'     => self::$RESULT_OK,
                'TotalCount' => (int)($response['totalCount'] ?? 0),
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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

                    // Prices
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
                                    // If it's an array
                                    foreach ($apiValue as $priceInfo) {
                                        if (is_array($priceInfo)) {
                                            $period = (int)($priceInfo['period'] ?? 1);
                                            if ($period < 1) $period = 1; // SOAP parity: period 0 -> 1
                                            $price                      = isset($priceInfo['price']) ? number_format((float)$priceInfo['price'],
                                                4, '.', '') : '0.0000';
                                            $pricing[$outType][$period] = $price;
                                            $currencies[$outType]       = $priceInfo['currency'] ?? '';
                                        }
                                    }
                                } elseif (is_array($apiValue)) {
                                    // If it's an object
                                    $period = (int)($apiValue['period'] ?? 1);
                                    if ($period < 1) $period = 1; // SOAP parity: period 0 -> 1
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
                        'id'         => $idCounter++,
                        'status'     => $tld['status'] ?? 'Active',
                        'maxchar'    => $tld['constraints']['maxLenght'] ?? 63,
                        'maxperiod'  => $tld['maxRegistrationPeriod'] ?? 10,
                        'minchar'    => $tld['constraints']['minLength'] ?? 1,
                        'minperiod'  => $tld['minRegistrationPeriod'] ?? 1,
                        'tld'        => $tld['name'],
                        'pricing'    => $pricing,
                        'currencies' => $currencies,
                    ];
                }
            }

            return [
                'data'   => $tldData,
                'result' => self::$RESULT_OK
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
                'result' => self::$RESULT_ERROR,
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
            $payload  = ['domainName' => $domainName, 'nameServers' => $this->normalizeHostNameList($nameServers)];
            $response = $this->request('PUT', 'domains/dns/name-server', $payload);

            return [
                'result' => self::$RESULT_OK,
                'data'   => [
                    'NameServers' => $response['nameServers'] ?? $nameServers
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
            $data     = ['domainName' => $domainName, 'lockStatus' => true];
            $response = $this->request('POST', 'domains/lock', $data);

            return [
                'result' => self::$RESULT_OK,
                'data'   => [
                    'LockStatus' => true
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
            $data     = ['domainName' => $domainName, 'lockStatus' => false];
            $response = $this->request('POST', 'domains/lock', $data);
            return [
                'result' => self::$RESULT_OK,
                'data'   => [
                    'LockStatus' => false
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
                'hostName'    => $this->normalizeHostName($nameServer),
                'ipAddresses' => [
                    [
                        'ipAddress' => $ipAddress,
                        'ipVersion' => filter_var($ipAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) ? 'v4' : 'v6'
                    ]
                ]
            ];
            $response = $this->request('POST', 'domains/dns/host', $payload);

            return [
                'result' => self::$RESULT_OK,
                'data'   => [
                    'NameServer' => $nameServer,
                    'IPAdresses' => [$ipAddress]
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
                'hostName'   => $this->normalizeHostName($nameServer)
            ];
            $response = $this->request('DELETE', 'domains/dns/host' ,$payload);

            return [
                'result' => self::$RESULT_OK,
                'data'   => [
                    'NameServer' => $nameServer
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
            $nameServer = $this->normalizeHostName($nameServer);
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
                'result' => self::$RESULT_OK,
                'data'   => [
                    'NameServer' => $response['hostName'] ?? $nameServer,
                    'IPAdresses' => $response['ipAddresses'] ?? [$ipAddress]
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
            // Domain info already includes the contacts
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
                'result' => self::$RESULT_OK
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
                'result' => self::$RESULT_OK,
                'data'   => ['contacts' => $parsedContacts]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
                'result' => self::$RESULT_ERROR,
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
                'result' => self::$RESULT_OK,
                'data'   => [
                    'DomainName' => $domainName,
                    'Status'     => $response['status'] ?? 'Cancelled'
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
                'result' => self::$RESULT_OK,
                'data'   => [
                    'DomainName' => $domainName,
                    'Status'     => $response['status'] ?? 'Approved'
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
                'result' => self::$RESULT_OK,
                'data'   => [
                    'DomainName' => $domainName,
                    'Status'     => $response['status'] ?? 'Rejected'
                ]
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
                    'result' => self::$RESULT_OK,
                    'data'   => [
                        'ExpirationDate' => $this->formatApiDate($response['expirationDate'] ?? null)
                    ]
                ];
            } else {
                $this->sendErrorToSentryAsync(new Exception("[DOMAIN_RENEW] " . self::$DEFAULT_ERRORS['DOMAIN_RENEW']['description']));
                return [
                    'result' => self::$RESULT_ERROR,
                    'error'  => $this->setError("DOMAIN_RENEW")
                ];
            }
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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

            // Normalize + strip empty entries first, THEN fall back to
            // defaults — a list like ["", ""] is non-empty but yields zero
            // valid NS, which the gateway rejects with API_560. (Parity with
            // DNASoap::register.)
            $nameServers = $this->normalizeHostNameList($nameServers);
            if (empty($nameServers)) {
                $nameServers = self::$DEFAULT_NAMESERVERS;
            }

            // For .tr domains the REST gateway only accepts the canonical TRABIS
            // schema (TRABISDOMAINCATEGORY=Owner|Corporate + TRABISCITIZENID /
            // TRABISTAXOFFICE / TRABISTAXNUMBER). Downstream modules still build the
            // legacy SOAP-era shape (numeric or Company/Personal category, the
            // TRABISCITIZIENID typo, and TRABISCOUNTRY*/CITY*/NAMESURNAME/ORG
            // fields), which the gateway rejects with "Validation failed". Normalize
            // here — REST ONLY; the SOAP gateway still accepts the legacy names.
            if (substr(strtolower((string) $domainName), -3) === '.tr') {
                $additionalAttributes = $this->normalizeTrAttributes((array) $additionalAttributes);
            }

            // The REST gateway types tldAttributes as a string dictionary; a
            // non-string value (e.g. an integer 215, or a bool) makes its JSON
            // deserializer fail with "The JSON value could not be converted".
            // Coerce every value to string before sending.
            $additionalAttributes = $this->stringifyAttributes((array) $additionalAttributes);

            // Gateway schema uses `tldAttributes` (object/dict), not the
            // legacy `additionalAttributes` (array). Always send as an object
            // — `{}` when empty, not `[]` — so it matches the OpenAPI schema
            // and the backend's own canonical example.
            $payload = [
                'domainName'    => $domainName,
                'period'        => $period,
                'nameServers'   => $nameServers,
                'isLocked'      => $eppLock,
                'privacyEnabled'=> $privacyLock,
                'contacts'      => $payloadContacts,
                'tldAttributes' => empty($additionalAttributes)
                    ? new \stdClass()
                    : (object) $additionalAttributes,
            ];

            $response = $this->request('POST', 'domains/register-with-contacts', $payload);

            return $this->parseDomainInfo($response);
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'REGISTER_DOMAIN', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
        }
    }

    /**
     * Coerce every tldAttributes value to a string. REST-only — the gateway
     * deserializes tldAttributes as a string dictionary, so integers/bools
     * otherwise throw "The JSON value could not be converted".
     *
     * @param array $attrs
     * @return array
     */
    private function stringifyAttributes($attrs)
    {
        $out = [];
        foreach ($attrs as $key => $value) {
            if (is_bool($value)) {
                $out[$key] = $value ? 'true' : 'false';
            } elseif (is_null($value)) {
                $out[$key] = '';
            } elseif (is_scalar($value)) {
                $out[$key] = (string) $value;
            } else {
                // Arrays/objects are not valid attribute values; encode defensively.
                $out[$key] = json_encode($value);
            }
        }
        return $out;
    }

    /**
     * Normalize a contact country to the 2-character ISO code the gateway
     * requires ("Country must be a 2-character ISO code"). Already-2-char codes
     * are upper-cased; a few common full names are mapped; otherwise the value
     * is returned untouched (the surfaced validation error then tells the
     * reseller to fix it).
     *
     * @param string $country
     * @return string
     */
    private function normalizeCountryCode($country)
    {
        $country = trim((string) $country);
        if ($country === '') {
            return '';
        }
        if (strlen($country) === 2) {
            return strtoupper($country);
        }

        $map = [
            'turkey' => 'TR', 'türkiye' => 'TR', 'turkiye' => 'TR',
            'united states' => 'US', 'united states of america' => 'US', 'usa' => 'US',
            'united kingdom' => 'GB', 'great britain' => 'GB', 'england' => 'GB',
            'germany' => 'DE', 'deutschland' => 'DE', 'france' => 'FR', 'spain' => 'ES',
            'italy' => 'IT', 'netherlands' => 'NL', 'india' => 'IN', 'china' => 'CN',
            'russia' => 'RU', 'canada' => 'CA', 'australia' => 'AU', 'brazil' => 'BR',
        ];
        $key = strtolower($country);
        return $map[$key] ?? $country;
    }

    /**
     * Flatten the REST gateway's validation errors into an end-user friendly
     * string so resellers see WHICH field failed, not just "Validation failed".
     *
     * REST-ONLY. Input shape: [ { "members": ["$.path.field", ...], "message": "..." }, ... ].
     * Produces e.g. "Admin PostalCode, Tech PostalCode - 'PostalCode' is required".
     *
     * @param array $validationErrors
     * @return string
     */
    private function flattenValidationErrors($validationErrors)
    {
        $parts = [];
        foreach ($validationErrors as $ve) {
            if (is_string($ve)) {
                if (trim($ve) !== '') {
                    $parts[] = trim($ve);
                }
                continue;
            }
            if (!is_array($ve)) {
                continue;
            }

            $message = isset($ve['message']) ? trim((string) $ve['message']) : '';
            $members = [];
            if (isset($ve['members']) && is_array($ve['members'])) {
                foreach ($ve['members'] as $member) {
                    $label = $this->humanizeValidationMember((string) $member);
                    if ($label !== '') {
                        $members[] = $label;
                    }
                }
            }

            if (!empty($members) && $message !== '') {
                $parts[] = implode(', ', $members) . ' - ' . $message;
            } elseif (!empty($members)) {
                $parts[] = implode(', ', $members);
            } elseif ($message !== '') {
                $parts[] = $message;
            }
        }

        return implode(' | ', array_values(array_unique($parts)));
    }

    /**
     * Turn a gateway validation member path into a readable field label.
     * e.g. "$.administrativeContact.postalCode" => "Admin PostalCode",
     *      "$.tldAttributes.TRABISCOUNTRYID"    => "TldAttributes TRABISCOUNTRYID".
     *
     * @param string $member
     * @return string
     */
    private function humanizeValidationMember($member)
    {
        $member = trim((string) $member);
        if ($member === '') {
            return '';
        }
        $member = ltrim($member, '$.');               // drop JSONPath root "$."
        $member = preg_replace('/\[\d+\]/', '', $member); // drop array indexes
        $segments = preg_split('/[.\/]+/', $member, -1, PREG_SPLIT_NO_EMPTY);
        if (empty($segments)) {
            return '';
        }

        $roles = [
            'administrativecontact' => 'Admin', 'administrative' => 'Admin',
            'technicalcontact'      => 'Tech',  'technical'      => 'Tech',
            'registrantcontact'     => 'Registrant', 'registrant' => 'Registrant',
            'billingcontact'        => 'Billing', 'billing'      => 'Billing',
        ];

        $out = [];
        foreach ($segments as $seg) {
            $key   = strtolower($seg);
            $out[] = $roles[$key] ?? ucfirst($seg);
        }

        return trim(implode(' ', $out));
    }

    /**
     * Normalize legacy .tr TRABIS attributes to the REST gateway schema.
     *
     * REST-ONLY. The SOAP gateway still accepts the legacy/typo'd field names,
     * so this must NOT be reused for DNASoap. Supported REST keys are exactly:
     * TRABISDOMAINCATEGORY, TRABISCITIZENID, TRABISTAXOFFICE, TRABISTAXNUMBER.
     *
     * @param array $attrs
     * @return array
     */
    private function normalizeTrAttributes($attrs)
    {
        if (!is_array($attrs) || empty($attrs)) {
            return is_array($attrs) ? $attrs : [];
        }

        // Direct key→key map: legacy/SOAP-era name => REST /tlds schema name.
        // Any key NOT listed here (TRABISNAMESURNAME, TRABISCOUNTRYID, TRABISCITYID,
        // TRABISCOUNTRYNAME, TRABISCITYNAME, TRABISORGANIZATION, ...) is dropped — it
        // is legacy data the gateway now derives from the contact record, and sending
        // it as an unsupported attribute triggers "Validation failed".
        $keyMap = [
            'TRABISDOMAINCATEGORY' => 'TRABISDOMAINCATEGORY',
            'TRABISCITIZENID'      => 'TRABISCITIZENID',
            'TRABISCITIZIENID'     => 'TRABISCITIZENID', // long-standing typo → canonical
            'TRABISTAXOFFICE'      => 'TRABISTAXOFFICE',
            'TRABISTAXNUMBER'      => 'TRABISTAXNUMBER',
        ];

        $normalized = [];
        foreach ($attrs as $key => $value) {
            if (!isset($keyMap[$key])) {
                continue;
            }
            // The canonical TRABISCITIZENID always wins over the typo'd variant.
            if ($key === 'TRABISCITIZIENID' && array_key_exists('TRABISCITIZENID', $normalized)) {
                continue;
            }
            $normalized[$keyMap[$key]] = $value;
        }

        // Value map for the TRABISDOMAINCATEGORY dropdown: 1 => Owner, 0 => Corporate.
        if (isset($normalized['TRABISDOMAINCATEGORY'])) {
            $cat = strtolower(trim((string) $normalized['TRABISDOMAINCATEGORY']));
            if (in_array($cat, ['1', 'owner', 'personal', 'individual'], true)) {
                $normalized['TRABISDOMAINCATEGORY'] = 'Owner';
            } elseif (in_array($cat, ['0', 'corporate', 'company'], true)) {
                $normalized['TRABISDOMAINCATEGORY'] = 'Corporate';
            }
        }

        return $normalized;
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
            // If reason is empty, use the default value
            if (empty($reason)) {
                $reason = self::$DEFAULT_REASON;
            }

            $payload  = ['domainName' => $domainName, 'privacyStatus' => $status===true];
            $response = $this->request('POST', "domains/privacy", $payload);

            return [
                'data'   => [
                    'PrivacyProtectionStatus' => $status
                ],
                'result' => self::$RESULT_OK
            ];
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
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
     * Normalize a gateway domain status to its textual label.
     *
     * Accepts either the numeric DomainStatus enum (e.g. 0) or a label string
     * (e.g. "Active"). Numeric values are resolved via DOMAIN_STATUS_MAP;
     * unknown numerics fall through as their string form, and label strings
     * are returned untouched. This keeps SOAP/REST parity for callers that
     * compare against "Active".
     *
     * @param mixed $value
     * @return string
     */
    private function normalizeDomainStatus($value): string
    {
        if (is_numeric($value)) {
            return self::DOMAIN_STATUS_MAP[(int)$value] ?? (string)$value;
        }
        return (string)$value;
    }

    /**
     * Parse domain information from response
     * @param array $data
     * @return array
     */
    private function parseDomainInfo($data)
    {
        // Always return a 'result'-keyed array. An empty/null gateway body
        // (common during 5xx/timeout storms) otherwise returned a bare []
        // and every consumer reading $result['result'] hit
        // "Undefined array key 'result'".
        if (empty($data)) {
            return [
                'result' => self::$RESULT_ERROR,
                'error'  => $this->setError('RESPONSE'),
            ];
        }
        return [
            'data'   => [
                'ID'                      => (int)($data['id'] ?? 0),
                // domains/info delivers the label in `status` (statusCode here
                // carries the EPP code, not the lifecycle status). Normalize so
                // a future switch to the numeric enum keeps working.
                'Status'                  => $this->normalizeDomainStatus($data['status'] ?? ''),
                'DomainName'              => (string)($data['domainName'] ?? ($data['name'] ?? '')),
                'AuthCode'                => (string)($data['authCode'] ?? ($data['eppCode'] ?? '')),
                'LockStatus'              => !empty($data['lockStatus']) ? 'true' : 'false',
                'PrivacyProtectionStatus' => !empty($data['privacyProtectionStatus']) ? 'true' : 'false',
                'IsChildNameServer'       => !empty($data['hosts']) ? 'true' : 'false',
                'Contacts'                => $this->parseContactIds($data['contacts'] ?? []),
                'Dates'                   => [
                    'Start'         => $this->formatApiDate($data['startDate'] ?? null),
                    'Expiration'    => $this->formatApiDate($data['expirationDate'] ?? null),
                    // The register endpoint returns expirationDate but no
                    // remainingDay; derive it from expirationDate so callers
                    // still get a sane day count. getDetails (which does send
                    // remainingDay) keeps using the server value.
                    'RemainingDays' => isset($data['remainingDay'])
                        ? (int)$data['remainingDay']
                        : $this->remainingDaysFromDate($data['expirationDate'] ?? null)
                ],
                'NameServers'             => isset($data['nameservers']) ? array_map('strval',
                    $data['nameservers']) : [],
                'Additional'              => isset($data['additionalAttributes']) ? (array)$data['additionalAttributes'] : [],
                'ChildNameServers'        => isset($data['hosts']) ? array_map(function ($ns) {
                    // SOAP parity: ip returned as a string (the last IP)
                    $ips = array_map(function ($ip) {
                        return $ip['ipAddress'];
                    }, $ns['ipAddresses'] ?? []);
                    return [
                        'ns' => $ns['name'],
                        'ip' => !empty($ips) ? end($ips) : ''
                    ];
                }, $data['hosts']) : []
            ],
            'result' => self::$RESULT_OK
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

        // If it's an array, take it as a string (nested format)
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
            'country'          => $this->normalizeCountryCode($contact['Country'] ?? ($contact['Address']['Country'] ?? '')),
            'postalCode'       => $contact['ZipCode'] ?? ($contact['Address']['ZipCode'] ?? ''),
            'phoneCountryCode' => (string)$phoneCc,
            'phone'            => (string)$phone,
            'faxCountryCode'   => (string)$faxCc,
            'fax'              => (string)$fax,
            // Required non-nullable bool in the gateway ContactLiteDto. The
            // backend's own example payload includes it; omitting causes
            // ModelState validation to reject the whole registration.
            'isHidden'         => (bool)($contact['IsHidden'] ?? false),
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
     * Check whether a domain can be transferred with the given auth code.
     * Mirrors the SOAP contract: returns {result: OK|ERROR}; OK iff the
     * registry reports the domain is currently transferable. The richer
     * fields the REST gateway returns (authCodeIsValid, transferLock, etc.)
     * are surfaced under `data` for callers that want them.
     *
     * @param string $domainName Domain name to check transfer for
     * @param string $authcode   Authorization/EPP code for transfer
     * @return array
     */
    public function checkTransfer($domainName, $authcode)
    {
        try {
            $payload  = ['domainName' => $domainName, 'authCode' => $authcode];
            $response = $this->request('POST', 'domains/transfers/check', $payload);

            $available = !empty($response['transferAvailabilityStatus']);
            $envelope = [
                'result' => $available ? self::$RESULT_OK : self::$RESULT_ERROR,
                'data'   => [
                    'TransferAvailabilityStatus' => $available,
                    'AuthCodeIsRequired'         => !empty($response['authCodeIsRequired']),
                    'AuthCodeIsValid'            => !empty($response['authCodeIsValid']),
                    'UserTransferRequired'       => !empty($response['userTransferRequired']),
                    'TransferLock'               => !empty($response['transferLock']),
                    'Message'                    => (string)($response['message'] ?? ''),
                    'MessageKey'                 => (string)($response['messageKey'] ?? ''),
                    'Additional'                 => isset($response['additionalAttributes'])
                        ? (array)$response['additionalAttributes']
                        : [],
                ],
            ];
            if (!$available) {
                $envelope['error'] = $this->setError(
                    'TRANSFER_NOT_AVAILABLE',
                    $response['message'] ?? 'Domain is not transferable',
                    $response['messageKey'] ?? ($response['message'] ?? '')
                );
            }
            return $envelope;
        } catch (Exception $e) {
            return [
                'result' => self::$RESULT_ERROR,
                'error'  => $this->setError($this->formatErrorCode($e->getCode()) ?: 'CHECK_TRANSFER', $e->getMessage(),
                    $this->lastParsedResponse['Details'] ?? ($this->lastResponse['raw_response'] ?? $e->getMessage()))
            ];
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
        // Define default values
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

        // Fill missing keys with default values
        foreach ($defaults as $key => $value) {
            if (!isset($contact[$key])) {
                $contact[$key] = $value;
            }
        }

        // Check empty values and fill with defaults
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

        // Phone number processing
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