<?php

namespace DomainNameApi;

use Exception;

trait SharedApiConfigAndUtilsTrait
{
    /**
     * Version of the library
     */
    public static $VERSION = '3.0.9'; // Must be identical in both classes; update here if changed

    public static $PERFORMANCE_SAMPLE_RATE = 40; // 2.5% (25 out of 1000)
    public static $RESULT_OK      = 'OK';
    public static $RESULT_ERROR   = 'ERROR';
    public static $RESULT_SUCCESS = 'SUCCESS';

    public static $DEFAULT_NAMESERVERS = [
        'ns1.domainnameapi.com',
        'ns2.domainnameapi.com',
    ];

    public static $DEFAULT_IGNORED_ERRORS = [
        'Domain not found',
        'ERR_DOMAIN_NOT_FOUND',
        'Reseller not found',
        'Domain is not in updateable status',
        'balance is not sufficient',
        'Price definition not found',
        'TLD is not supported',
        'Invalid API credentials',
        'could not be found', // API_404 "The domain name you requested could not be found" — high-volume expected noise
        'Insufficent reseller balance', // insufficient balance
        // 'already exists in the registry', // API_2302 object exists
        // 'Request already sent',           // API_2306 duplicate request
        // 'Parameter value policy error',   // API_2306 duplicate/policy
        // 'Premium domain is not available',// API_362 premium not registerable
        // 'Contact not found',              // API_410
        // 'Object does not exist',          // API_2303
        // 'Authorization problem',          // API_2200 reseller auth state
        // 'is not authorized to access',       // API_0  caller IP not whitelisted
        // 'clientTransferProhibited is not set',// API_2004 transfer lock absent on domain
        // 'Transfer lock exists',               // API_592 transfer lock present on domain
        // 'Domain transfer is not permitted',   // API_403 registry forbids transfer
        // 'auto-renewal period',                // API_363 op cancelled in auto-renewal window
        // 'Invalid zip code',                   // API_2306 contact zip validation
        // 'Subordinate host info not available',// API_561 partial host info on register
    ];

    public static $DEFAULT_ERRORS = [
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
        'RESPONSE_REST'           => [
            'code'        => 'RESPONSE_REST',
            'message'     => 'Invalid parameters passed to function! REST return is not a valid array!',
            'description' => 'The REST response is not in a valid array format'
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

    public static $DEFAULT_CACHE_TTL = 512;
    public static $DEFAULT_TIMEOUT   = 30;
    public static $DEFAULT_REASON    = 'Owner request';

    private static $APPLICATIONS = [
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
        'FOSSBILLING'    => [
            'path' => 'library/Registrar/Adapter/DomainNameApi',
            'dsn'  => 'https://3a129526bcd91cc309de8358d87846b9@sentry.atakdomain.com/15'
        ],
        'NONE'           => [
            'path' => '',
            'dsn'  => ''
        ]
    ];

    private static $CURRENCIES = [
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
     * Sets the application context for error reporting based on the file path.
     * This method should be called from the constructor of the class using this trait.
     * Example: $this->_setApplication(__FILE__);
     *
     * @param string $filePathFromClass The result of __FILE__ from the calling class.
     */
    protected function _setApplication(string $filePathFromClass): void
    {
        $this->application        = 'CORE'; // Default application
        $this->errorReportingPath = self::$APPLICATIONS['CORE']['path'];
        $this->errorReportingDsn  = self::$APPLICATIONS['CORE']['dsn'];

        foreach (self::$APPLICATIONS as $app => $config) {
            if (!empty($config['path']) && strpos($filePathFromClass, $config['path']) !== false) {
                $this->application        = $app;
                $this->errorReportingPath = $config['path'];
                $this->errorReportingDsn  = $config['dsn'];
                break;
            }
        }
    }

    /**
     * Enable or disable Sentry telemetry at runtime. Useful for tests that
     * deliberately probe the library with pathological inputs and would
     * otherwise pollute the shared Sentry project with synthetic errors.
     *
     * @param bool $enabled
     * @return void
     */
    public function setErrorReportingEnabled(bool $enabled): void
    {
        $this->errorReportingEnabled = $enabled;
    }

    /**
     * Normalize a nameserver/host name coming from module UIs before it is
     * sent to the gateway: trim whitespace, drop the FQDN trailing dot,
     * lowercase. Registries treat "NS1.EXAMPLE.COM." and "ns1.example.com"
     * as the same host, but raw user input with a trailing dot or stray
     * spaces reaches the gateway as-is and fails there (BUG-10124).
     */
    private function normalizeHostName($hostName): string
    {
        return strtolower(rtrim(trim((string) $hostName), '.'));
    }

    /**
     * Normalize a nameserver list: normalize each entry, drop empties and
     * duplicates, reindex. Order is preserved. Returns [] for non-arrays so
     * callers can apply their own default-NS fallback.
     */
    private function normalizeHostNameList($hostNames): array
    {
        if (!is_array($hostNames)) {
            return [];
        }
        $normalized = [];
        foreach ($hostNames as $hostName) {
            if (!is_string($hostName)) {
                continue;
            }
            $hostName = $this->normalizeHostName($hostName);
            if ($hostName !== '' && !in_array($hostName, $normalized, true)) {
                $normalized[] = $hostName;
            }
        }
        return $normalized;
    }

    private function setError($code, $message = '', $details = ''): array
    {
        $result = [];
        if (isset(self::$DEFAULT_ERRORS[$code])) {
            $error = self::$DEFAULT_ERRORS[$code];
            $result["Code"] = $error['code'];
            $result["Message"] = $error['message'];
            $result["Details"] = $error['description'];
        } else {
            $result["Code"] = $code;
            $result["Message"] = $message;
            $result["Details"] = $details;
        }
        return $result;
    }

    /**
     * Public entry point. Telemetry must NEVER throw into the host billing
     * application (and it runs while the host is already handling an error),
     * so the whole body is guarded against any Throwable, not just Exception.
     */
    private function sendErrorToSentryAsync(Exception $e): void
    {
        try {
            $this->doSendErrorToSentryAsync($e);
        } catch (\Throwable $t) {
            // Swallow — a telemetry failure must not surface in the host app.
        }
    }

    private function doSendErrorToSentryAsync(Exception $e): void
    {
        if (!$this->errorReportingEnabled) {
            return;
        }

        foreach (self::$DEFAULT_IGNORED_ERRORS as $ev) {
            if (strpos($e->getMessage(), $ev) !== false) {
                return;
            }
        }

        $elapsed_time = property_exists($this, 'startAt') ? (microtime(true) - $this->startAt) : 0;
        $parsed_dsn   = parse_url($this->errorReportingDsn);

        if (!$parsed_dsn || !isset($parsed_dsn['host'], $parsed_dsn['path'], $parsed_dsn['user'])) {
            return; // Invalid DSN
        }

        $host       = $parsed_dsn['host'];
        $project_id = ltrim($parsed_dsn['path'], '/');
        $public_key = $parsed_dsn['user'];
        $secret_key = $parsed_dsn['pass'] ?? null;
        $api_url    = "https://$host/api/$project_id/store/";

        $external_ip = $this->getServerIp();

        $knownPath = __FILE__; // This will be the trait's file path
        $errFile   = $e->getFile();
        $vhostUser = '';

        try {
            $vhostUser = function_exists('get_current_user') ? \get_current_user() : '';
        } catch (Exception $ex) {
            $vhostUser = '';
        }
        if ($vhostUser == '') {
            // Try to determine from a known path if possible, though less reliable from trait
            if (property_exists($this, 'errorReportingPath') && !empty($this->errorReportingPath) && preg_match('/\/home\/([^\/]+)\//', $this->errorReportingPath, $matches)) {
                 $vhostUser = $matches[1];
            } elseif (preg_match('/\/home\/([^\/]+)\//', $errFile, $matches)) {
                 $vhostUser = $matches[1];
            }
        }

        // Adjust paths based on application context if errorReportingPath is set
        if (property_exists($this, 'errorReportingPath') && !empty($this->errorReportingPath)) {
            if (strpos($knownPath, $this->errorReportingPath) !== false) {
                $knownPath = substr($knownPath, strpos($knownPath, $this->errorReportingPath) + strlen($this->errorReportingPath));
            }
            if (strpos($errFile, $this->errorReportingPath) !== false) {
                $errFile = substr($errFile, strpos($errFile, $this->errorReportingPath) + strlen($this->errorReportingPath));
            }
        }
        $culpritClass = get_class($this);

        // Stable classification: normalised message, volatile data as tags,
        // explicit fingerprint. Without this Sentry groups on the variable
        // backend message (IP, reseller id, domain) and shatters one logical
        // error into hundreds of issues.
        $classification = $this->buildSentryClassification($e);

        $errorData = [
            'event_id'  => bin2hex(random_bytes(16)),
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'level'     => 'error',
            'logger'    => 'php',
            'platform'  => 'php',
            'culprit'   => $culpritClass . ' via ' . $knownPath,
            'message'   => [
                'formatted' => $classification['value']
            ],
            'exception' => [
                'values' => [
                    [
                        'type'       => str_replace(['DomainNameApi\\DNARest', 'DomainNameApi\\DNASoap', 'DomainNameApi\\DomainNameAPI_PHPLibrary'], [$this->application . ' Exception', $this->application . ' Exception', $this->application . ' Exception'], $culpritClass),
                        'value'      => $classification['value'],
                        'stacktrace' => [
                            'frames' => [
                                [
                                    'filename' => $errFile,
                                    'lineno'   => $e->getLine(),
                                    'function' => str_replace([
                                        dirname(__DIR__), // May need adjustment depending on final structure
                                        'DomainNameApi\\DNARest',
                                        'DomainNameApi\\DNASoap',
                                        'DomainNameApi\\DomainNameAPI_PHPLibrary'
                                    ], ['.', 'Lib', 'Lib', 'Lib'], $e->getTraceAsString()),
                                ]
                            ]
                        ]
                    ]
                ]
            ],
            // `release` and `environment` are TOP-LEVEL event fields below,
            // not tags — Sentry's Release Health / Performance-per-Release
            // and environment selectors only honour the top-level form.
            'tags'      => [
                'handled'         => 'yes',
                'level'           => 'error',
                'url'             => $_SERVER['REQUEST_URI'] ?? 'NA',
                'transaction'     => $_SERVER['REQUEST_METHOD'] ?? 'NA',
                'trace_id'        => bin2hex(random_bytes(8)),
                'runtime_name'    => 'PHP',
                'runtime_version' => phpversion(),
                'elapsed_time'    => number_format($elapsed_time, 4),
                'application'     => $this->application ?? 'UNKNOWN',
                'extension_soap'  => extension_loaded('soap') ? 'enabled' : 'disabled',
                'ip_address'      => $external_ip,
                'vhost_user'      => $vhostUser,
            ],
            'extra'     => [
                'request_data'  => method_exists($this, 'getRequestData') ? $this->getRequestData() : [],
                'response_data' => method_exists($this, 'getResponseData') ? $this->getResponseData() : [],
                'openssl_v'     => defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'NA',
                'openssl_n'     => defined('OPENSSL_VERSION_NUMBER') ? OPENSSL_VERSION_NUMBER : 'NA',
            ]
        ];

        // Top-level release/environment — required for Sentry to associate
        // the event with a real release and an environment selector.
        $errorData['release']     = self::$VERSION;
        $errorData['environment'] = $this->getEnvironment();

        // Volatile data (reseller, domain, IP, api_code, operation) lives as
        // tags so it's filterable; the raw backend message is preserved in
        // extra.original_message so no detail is lost.
        $errorData['tags']                      = array_merge($errorData['tags'], $classification['tags']);
        $errorData['extra']['original_message'] = $e->getMessage();
        $errorData['fingerprint']               = $classification['fingerprint'];

        $sentry_auth = [
            'sentry_version=7',
            'sentry_client=phplib-php/' . self::$VERSION,
            "sentry_key=$public_key"
        ];
        if ($secret_key) {
            $sentry_auth[] = "sentry_secret=$secret_key";
        }
        $sentry_auth_header = 'X-Sentry-Auth: Sentry ' . implode(', ', $sentry_auth);

        $payload = $this->encodeSentryPayload($errorData);
        if ($payload === '') {
            return; // unencodable even after sanitising — drop rather than POST empty
        }
        $this->fireAndForgetPost($api_url, $payload, $sentry_auth_header);
    }

    /**
     * JSON-encode a Sentry event payload defensively. request_data /
     * response_data / original_message can carry invalid UTF-8 (a real
     * failure mode — e.g. a binary SOAP body), which makes a plain
     * json_encode() return false and silently drop the whole event exactly
     * when an error occurred. Substitute bad bytes and tolerate partial
     * output so the event still ships.
     *
     * @param array $data
     * @return string JSON, or '' if it could not be encoded at all.
     */
    private function encodeSentryPayload(array $data): string
    {
        $flags = 0;
        if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
            $flags |= JSON_INVALID_UTF8_SUBSTITUTE; // PHP 7.2+
        }
        if (defined('JSON_PARTIAL_OUTPUT_ON_ERROR')) {
            $flags |= JSON_PARTIAL_OUTPUT_ON_ERROR; // PHP 5.5+
        }
        $json = json_encode($data, $flags);
        return is_string($json) ? $json : '';
    }

    /**
     * Trace id shared by all telemetry this *client instance* emits, so the
     * calls made through one client (e.g. check → register → sync) stitch
     * into one Sentry trace. Deliberately instance-scoped, NOT static: a
     * process-global would merge unrelated tenants/requests into a single
     * trace under long-running workers (swoole/roadrunner/queue) or a CLI
     * cron looping over many domains with separate client instances.
     *
     * @var string|null
     */
    private ?string $sessionTraceId = null;

    /**
     * Volatile data shared by both error events and transaction events:
     * reseller (REST UUID / SOAP username), domain (from lastRequest),
     * operation (lastFunction), api_code (from exception message regex or
     * the exception's own numeric code). Lives as tags so Sentry filters
     * like "reseller=X" or "api_code=API_500" cut across both event types.
     *
     * @param Exception|null $e
     * @return array<string,string>
     */
    private function buildCommonTags(?Exception $e = null): array
    {
        $tags = [];

        if (property_exists($this, 'resellerId') && !empty($this->resellerId)) {
            $tags['reseller'] = (string) $this->resellerId;
        } elseif (property_exists($this, 'serviceUsername') && !empty($this->serviceUsername)) {
            $tags['reseller'] = (string) $this->serviceUsername;
        }

        $lr = (property_exists($this, 'lastRequest') && is_array($this->lastRequest))
            ? $this->lastRequest : [];
        $domain = $lr['payload']['domainName']
               ?? $lr['request']['DomainName']
               ?? null;
        if (!empty($domain) && is_string($domain)) {
            $tags['domain'] = $domain;
        }

        if (property_exists($this, 'lastFunction') && !empty($this->lastFunction)) {
            $tags['operation'] = (string) $this->lastFunction;
        }

        // Transport: DNASoap has a $service (SoapClient), DNARest does not.
        $tags['api_type'] = property_exists($this, 'service') ? 'SOAP' : 'REST';

        if ($e !== null) {
            $msg = (string) $e->getMessage();
            if (preg_match('/API_(-?\d+)_ERROR/', $msg, $m)) {
                $tags['api_code'] = 'API_' . $m[1];
            } else {
                $code = $e->getCode();
                if (is_numeric($code) && (int) $code !== 0) {
                    $tags['api_code'] = 'API_' . (int) $code;
                }
            }
            // Raw HTTP/response status code as its own filterable tag.
            $rawCode = $e->getCode();
            if (is_numeric($rawCode) && (int) $rawCode !== 0) {
                $tags['status_code'] = (string) (int) $rawCode;
            }
        }

        return $tags;
    }

    /**
     * Map success + api_code to an OTel-compatible trace status so Sentry's
     * Performance "Failure Rate", alerts and queries work correctly. The
     * legacy literal "error" was non-standard and would silently disable
     * those widgets.
     *
     * @param bool $success
     * @param string|null $apiCode like "API_500", "API_2302", "API_-1"
     * @return string OTel status: ok / internal_error / unavailable / ...
     */
    private function mapTraceStatus(bool $success, ?string $apiCode): string
    {
        if ($success) {
            return 'ok';
        }
        if ($apiCode === null) {
            return 'unknown';
        }
        if (!preg_match('/-?\d+/', $apiCode, $m)) {
            return 'unknown';
        }
        $code = (int) $m[0];
        if ($code === 401 || $code === 2202) return 'unauthenticated';
        if ($code === 403)                   return 'permission_denied';
        if ($code === 404 || $code === 510)  return 'not_found';
        if ($code === 408 || $code === 504)  return 'deadline_exceeded';
        if ($code === -1)                    return 'unavailable';
        if ($code >= 500 && $code < 600)     return 'internal_error';
        if ($code >= 400 && $code < 500)     return 'invalid_argument';
        return 'unknown';
    }

    /**
     * Resolve the Sentry "environment" string from the configured service
     * URL — OTE/test traffic should not be lumped into production metrics.
     */
    private function getEnvironment(): string
    {
        if (property_exists($this, 'serviceUrl') && is_string($this->serviceUrl)
            && stripos($this->serviceUrl, 'ote.') !== false) {
            return 'ote';
        }
        return 'production';
    }

    /**
     * Lazily generate (once per client instance) the trace id used by every
     * transaction this client emits. Lets Sentry stitch this client's
     * `check → register → sync` into one waterfall, without merging other
     * clients/tenants/requests that live in the same long-running process.
     */
    private function getSessionTraceId(): string
    {
        if ($this->sessionTraceId === null) {
            $this->sessionTraceId = bin2hex(random_bytes(16));
        }
        return $this->sessionTraceId;
    }

    /**
     * Build a stable Sentry classification from an exception.
     *
     * Returns:
     *  - 'value'       : a normalised message — IP/domain/numbers/quoted
     *                    strings replaced with placeholders so the same
     *                    underlying error always renders the same title.
     *  - 'tags'        : volatile data extracted into transport-agnostic
     *                    tags (reseller, domain, operation, api_code, ip_value)
     *                    so they remain filterable in Sentry.
     *  - 'fingerprint' : ['dna', application, operation, normalised value] —
     *                    explicit grouping key. Reseller is intentionally
     *                    NOT in the fingerprint (would re-shatter groups).
     *
     * The raw backend message is preserved unchanged in
     * extra.original_message at the call-site so no detail is lost.
     *
     * @param Exception $e
     * @return array{value:string,tags:array<string,string>,fingerprint:array<int,string>}
     */
    private function buildSentryClassification(Exception $e): array
    {
        $msg  = (string) $e->getMessage();

        // Reseller / domain / operation / api_code now live in the shared
        // helper so error and transaction events agree on tag keys.
        $tags = $this->buildCommonTags($e);

        // ip_value is meaningful only when the error message itself names
        // an IP (e.g. "Your current IP address (X) is not authorized") —
        // not relevant for transactions, so kept here.
        if (preg_match('/\b(\d{1,3}(?:\.\d{1,3}){3})\b/', $msg, $m)) {
            $tags['ip_value'] = $m[1];
        }

        // Normalise the message: strip the variable bits so identical
        // underlying errors render an identical title.
        $apiCode = $tags['api_code'] ?? null;
        $n = $msg;
        $n = preg_replace('/^\[API_ERROR\]:\s*/i', '', $n);
        $n = preg_replace('/API_-?\d+_ERROR\s*-\s*Failed\s*-\s*/i', '', $n);
        $n = preg_replace('/\s*\(Reseller Id[^)]*\)\s*/i', '', $n);
        $n = preg_replace('/\b\d{1,3}(?:\.\d{1,3}){3}\b/', '{ip}', $n);
        $n = preg_replace("/'[^']{0,80}'|\"[^\"]{0,80}\"/", '{v}', $n);
        // Replace runs of 2+ digits (single digits often carry meaning,
        // e.g. "v4", "All 4 contact types required").
        $n = preg_replace('/\b\d{2,}\b/', '{n}', $n);
        $n = trim((string) preg_replace('/\s+/', ' ', (string) $n));

        if ($n === '') {
            // Empty backend message (e.g. HTTP 4xx/5xx with empty body):
            // prefer the api_code alone as a clean, stable title; otherwise
            // fall back to the exception class so the title is still useful.
            if ($apiCode) {
                $n = $apiCode;
            } else {
                $parts = explode('\\', get_class($e));
                $n     = end($parts) ?: 'Exception';
            }
        } elseif ($apiCode && strpos($n, $apiCode) !== 0) {
            $n = $apiCode . ': ' . $n;
        }
        if (strlen($n) > 160) {
            $n = substr($n, 0, 157) . '...';
        }

        return [
            'value'       => $n,
            'tags'        => $tags,
            'fingerprint' => [
                'dna',
                $this->application ?? 'CORE',
                $tags['operation'] ?? 'unknown',
                $n,
            ],
        ];
    }

    private function sendPerformanceMetricsToSentry(array $metrics): void
    {
        if (!property_exists($this, 'errorReportingEnabled') || !$this->errorReportingEnabled) {
            return;
        }
        if (!property_exists($this, 'errorReportingDsn') || empty($this->errorReportingDsn)) {
            return;
        }

        try {
            $parsed_dsn = parse_url($this->errorReportingDsn);
            if (!$parsed_dsn || !isset($parsed_dsn['host'], $parsed_dsn['path'], $parsed_dsn['user'])) {
                return;
            }

            $host = $parsed_dsn['host'];
            $project_id = ltrim($parsed_dsn['path'], '/');
            $public_key = $parsed_dsn['user'];
            $secret_key = $parsed_dsn['pass'] ?? null;
            $api_url = "https://$host/api/$project_id/store/";

            // Shared per-request trace id so multiple library calls in the
            // same PHP request show up as one Sentry trace waterfall.
            $trace_id        = $this->getSessionTraceId();
            $root_span_id    = bin2hex(random_bytes(8));
            $child_span_id   = bin2hex(random_bytes(8));

            $isSoap = property_exists($this, 'service');
            $op     = $isSoap ? 'soap.client' : 'http.client';

            // OTel-compatible trace status (replaces the legacy "error"
            // literal which Sentry's Performance widgets do not understand).
            $apiCode = null;
            if (property_exists($this, 'lastParsedResponse') && is_array($this->lastParsedResponse)
                && isset($this->lastParsedResponse['Code']) && is_string($this->lastParsedResponse['Code'])) {
                $apiCode = $this->lastParsedResponse['Code'];
            } elseif (property_exists($this, 'lastResponse') && is_array($this->lastResponse)
                && isset($this->lastResponse['error']['code']) && is_string($this->lastResponse['error']['code'])) {
                $apiCode = $this->lastResponse['error']['code'];
            }
            $traceStatus = $this->mapTraceStatus((bool) $metrics['success'], $apiCode);

            // Common cross-event tags (reseller, domain, operation, api_type).
            $commonTags = $this->buildCommonTags();
            if ($apiCode !== null && !isset($commonTags['api_code'])) {
                $commonTags['api_code'] = (string) $apiCode;
            }
            // HTTP status code for REST (curl_getinfo http_code) so the perf
            // transaction is filterable by status alongside the error events.
            if (property_exists($this, 'lastResponseHeaders') && is_array($this->lastResponseHeaders)
                && isset($this->lastResponseHeaders['http_code']) && (int) $this->lastResponseHeaders['http_code'] !== 0) {
                $commonTags['status_code'] = (string) (int) $this->lastResponseHeaders['http_code'];
            }

            // Outbound-call span attributes. Sentry's "Outbound API Requests"
            // module attributes a call to a domain by parsing the span
            // description ("METHOD https://host") and reads the status from
            // span.data — without these the dashboard shows "Unknown Domain"
            // and "(no value)" response codes.
            $httpMethod = $isSoap ? 'POST' : 'GET';
            if (property_exists($this, 'lastRequest') && is_array($this->lastRequest)
                && !empty($this->lastRequest['method'])) {
                $httpMethod = (string) $this->lastRequest['method'];
            }
            $callHost = '';
            $callUrl  = '';
            if (property_exists($this, 'lastRequest') && is_array($this->lastRequest)
                && !empty($this->lastRequest['url'])) {
                $callUrl  = (string) $this->lastRequest['url']; // REST: full URL recorded
                $callHost = (string) (parse_url($callUrl, PHP_URL_HOST) ?: '');
            } elseif (property_exists($this, 'serviceUrl') && is_string($this->serviceUrl)) {
                $callHost = (string) (parse_url($this->serviceUrl, PHP_URL_HOST) ?: ''); // SOAP: gateway host
            }

            $spanData = ['http.request.method' => $httpMethod];
            if ($callHost !== '') {
                $spanData['server.address'] = $callHost;
            }
            if ($callUrl !== '') {
                $spanData['url.full'] = $callUrl;
            }
            if (isset($commonTags['status_code'])) {
                $spanData['http.response.status_code'] = (int) $commonTags['status_code'];
            }
            // Sentry parses the outbound domain from this description shape.
            $spanDescription = $callHost !== ''
                ? $httpMethod . ' https://' . $callHost
                : (string) $metrics['operation'];

            $vhostUser = '';
            try {
                $vhostUser = function_exists('get_current_user') ? \get_current_user() : '';
            } catch (Exception $ex) {
                if (property_exists($this, 'errorReportingPath') && !empty($this->errorReportingPath)
                    && preg_match('/\/home\/([^\/]+)\//', $this->errorReportingPath, $matches)) {
                    $vhostUser = $matches[1];
                }
            }

            $performanceData = [
                'event_id'         => bin2hex(random_bytes(16)),
                'platform'         => 'php',
                'level'            => 'info',
                'type'             => 'transaction',
                'transaction'      => "API.{$metrics['operation']}",
                'transaction_info' => ['source' => 'custom'],
                // Top-level release + environment — required for Sentry's
                // Release Health and environment selector to work.
                'release'          => self::$VERSION,
                'environment'      => $this->getEnvironment(),
                'timestamp'        => $metrics['timestamp'],
                'start_timestamp'  => $metrics['start_timestamp'],
                'contexts' => [
                    'trace' => [
                        'trace_id' => $trace_id,
                        'span_id'  => $root_span_id,
                        'op'       => $op,
                        'status'   => $traceStatus,
                    ],
                    'runtime' => [
                        'name'    => 'php',
                        'version' => PHP_VERSION,
                    ],
                ],
                // Static/per-server bits (server_software, processor count,
                // os build, extension flags…) were dropped — they don't
                // change per request and were bloating every event.
                // api_type comes from $commonTags now; ip_address/vhost_user
                // are surfaced as tags so they're filterable in Performance.
                'tags' => array_merge([
                    'application' => $this->application ?? 'UNKNOWN',
                    'ip_address'  => $this->getServerIp(),
                    'vhost_user'  => $vhostUser,
                ], $commonTags),
                'measurements' => [
                    'duration' => ['value' => floatval($metrics['duration']), 'unit' => 'millisecond'],
                    'memory'   => ['value' => memory_get_peak_usage(true),    'unit' => 'byte'],
                ],
                // Stub child span: a single span covering the outgoing call
                // so the Sentry waterfall shows the op type (http/soap.client)
                // even before we instrument sub-call timing. When parseCall/
                // request gain real internal timing this span will narrow
                // to just the network portion.
                'spans' => [
                    [
                        'span_id'         => $child_span_id,
                        'parent_span_id'  => $root_span_id,
                        'trace_id'        => $trace_id,
                        'op'              => $op,
                        'description'     => $spanDescription,
                        'status'          => $traceStatus,
                        'data'            => $spanData,
                        'start_timestamp' => $metrics['start_timestamp'],
                        'timestamp'       => $metrics['timestamp'],
                    ],
                ],
            ];

            $sentry_auth = [
                'sentry_version=7',
                'sentry_client=php-api/' . self::$VERSION,
                "sentry_key=$public_key"
            ];
            if ($secret_key) {
                $sentry_auth[] = "sentry_secret=$secret_key";
            }
            $sentry_auth_header = 'X-Sentry-Auth: Sentry ' . implode(', ', $sentry_auth);

            $this->fireAndForgetPost($api_url, $this->encodeSentryPayload($performanceData), $sentry_auth_header);
        } catch (\Throwable $e) {
            // Fail silently — telemetry must never throw into the host app.
        }
    }

    /**
     * Best-effort CPU count without invoking a shell. Falls back to 1.
     */
    private function detectProcessorCount(): int
    {
        if (defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Linux' && is_readable('/proc/cpuinfo')) {
            $cpuinfo = @file_get_contents('/proc/cpuinfo');
            if ($cpuinfo !== false) {
                $count = preg_match_all('/^processor\s*:/m', $cpuinfo);
                if ($count > 0) {
                    return $count;
                }
            }
        }
        return 1;
    }

    /**
     * Fire-and-forget HTTP POST used for telemetry. Uses curl_multi to
     * dispatch the request without blocking the caller for more than a
     * short bounded window. No shell execution involved.
     */
    private function fireAndForgetPost(string $url, string $jsonBody, string $authHeader): void
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonBody,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_NOSIGNAL       => true,
            CURLOPT_CONNECTTIMEOUT_MS => 200,
            CURLOPT_TIMEOUT_MS     => 500,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                $authHeader,
            ],
        ]);

        $mh = curl_multi_init();
        curl_multi_add_handle($mh, $ch);

        $running  = null;
        $deadline = microtime(true) + 0.2;
        do {
            curl_multi_exec($mh, $running);
            if ($running) {
                curl_multi_select($mh, 0.02);
            }
        } while ($running && microtime(true) < $deadline);

        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
        curl_multi_close($mh);
    }

    private function getServerIp(): string
    {
        $cache_ttl    = self::$DEFAULT_CACHE_TTL;
        $cache_key    = 'dna_external_ip'; // Changed key to be more specific
        // Define cache_file path more robustly, assuming trait is in DomainNameApi directory
        $cache_file   = __DIR__ . '/.ip_addr.cache'; 

        $current_time = time();

        $external_ip = false;
        if (function_exists('apcu_fetch')) {
            $external_ip = apcu_fetch($cache_key);
        }
        
        if ($external_ip === false && file_exists($cache_file) && ($current_time - filemtime($cache_file) < $cache_ttl)) {
            $external_ip = file_get_contents($cache_file);
        }

        if ($external_ip !== false && filter_var($external_ip, FILTER_VALIDATE_IP)) {
            return (string)$external_ip;
        }
        
        try {
            $ch = curl_init();
            // Using a more reliable service for IP, and ensuring https
            curl_setopt($ch, CURLOPT_URL, "https://api.ipify.org"); 
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 2);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
            $fetched_ip = curl_exec($ch);
            curl_close($ch);

            if ($fetched_ip !== false && filter_var($fetched_ip, FILTER_VALIDATE_IP)) {
                $external_ip = (string)$fetched_ip;
                if (function_exists('apcu_store')) {
                    apcu_store($cache_key, $external_ip, $cache_ttl);
                }
                // Ensure directory is writable, though __DIR__ should be.
                @file_put_contents($cache_file, $external_ip);
                return $external_ip;
            }
        } catch (Exception $e) {
            // Silently ignore error, return unknown
        }
        return 'unknown';
    }
} 