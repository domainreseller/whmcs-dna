<?php

namespace DomainNameApi;

use Exception;

trait SharedApiConfigAndUtilsTrait
{
    /**
     * Version of the library
     */
    public static $VERSION = '3.0.1'; // Bu değer her iki sınıfta da aynı olmalı, gerekirse güncellenmeli

    public static $PERFORMANCE_SAMPLE_RATE = 25; // 2.5% (25 out of 1000)
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

    private function sendErrorToSentryAsync(Exception $e): void
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

        $errorData = [
            'event_id'  => bin2hex(random_bytes(16)),
            'timestamp' => gmdate('Y-m-d\TH:i:s\Z'),
            'level'     => 'error',
            'logger'    => 'php',
            'platform'  => 'php',
            'culprit'   => $culpritClass . ' via ' . $knownPath, 
            'message'   => [
                'formatted' => $e->getMessage()
            ],
            'exception' => [
                'values' => [
                    [
                        'type'       => str_replace(['DomainNameApi\\DNARest', 'DomainNameApi\\DNASoap', 'DomainNameApi\\DomainNameAPI_PHPLibrary'], [$this->application . ' Exception', $this->application . ' Exception', $this->application . ' Exception'], $culpritClass),
                        'value'      => $e->getMessage(),
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
            'tags'      => [
                'handled'         => 'yes',
                'level'           => 'error',
                'release'         => self::$VERSION,
                'environment'     => 'production', // Consider making this configurable
                'url'             => $_SERVER['REQUEST_URI'] ?? 'NA',
                'transaction'     => $_SERVER['REQUEST_METHOD'] ?? 'NA',
                'trace_id'        => bin2hex(random_bytes(8)),
                'runtime_name'    => 'PHP',
                'runtime_version' => phpversion(),
                'ip_address'      => $external_ip,
                'elapsed_time'    => number_format($elapsed_time, 4),
                'vhost_user'      => $vhostUser,
                'application'     => $this->application ?? 'UNKNOWN',
                'extension_soap'  => extension_loaded('soap') ? 'enabled' : 'disabled',
                'openssl_v'       => defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'NA',
                'openssl_n'       => defined('OPENSSL_VERSION_NUMBER') ? OPENSSL_VERSION_NUMBER : 'NA',
            ],
            'extra'     => [
                'request_data'  => method_exists($this, 'getRequestData') ? $this->getRequestData() : [],
                'response_data' => method_exists($this, 'getResponseData') ? $this->getResponseData() : [],
            ]
        ];

        $sentry_auth = [
            'sentry_version=7',
            'sentry_client=phplib-php/' . self::$VERSION,
            "sentry_key=$public_key"
        ];
        if ($secret_key) {
            $sentry_auth[] = "sentry_secret=$secret_key";
        }
        $sentry_auth_header = 'X-Sentry-Auth: Sentry ' . implode(', ', $sentry_auth);

        if (function_exists('escapeshellarg') && function_exists('exec')) {
            $cmd = 'curl -X POST ' . escapeshellarg($api_url) . ' -H ' . escapeshellarg('Content-Type: application/json') . ' -H ' . escapeshellarg($sentry_auth_header) . ' -d ' . escapeshellarg(json_encode($errorData)) . ' > /dev/null 2>&1 &';
            @exec($cmd);
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
            @curl_exec($ch);
            @curl_close($ch);
        }
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

            $trace_id = bin2hex(random_bytes(16));
            $span_id = bin2hex(random_bytes(8));

            $vhostUser = '';
            try {
                $vhostUser = function_exists('get_current_user') ? \get_current_user() : '';
            } catch (Exception $ex) {
                 if (property_exists($this, 'errorReportingPath') && !empty($this->errorReportingPath) && preg_match('/\/home\/([^\/]+)\//', $this->errorReportingPath, $matches)) {
                     $vhostUser = $matches[1];
                 }
            }

            $openssl_version = defined('OPENSSL_VERSION_TEXT') ? OPENSSL_VERSION_TEXT : 'NA';
            $environment = 'production'; // Consider making this configurable

            $performanceData = [
                'event_id' => bin2hex(random_bytes(16)),
                'timestamp' => $metrics['timestamp'],
                'platform' => 'php',
                'level' => 'info',
                'type' => 'transaction',
                'transaction' => "API.{$metrics['operation']}",
                'transaction_info' => ['source' => 'custom'],
                'contexts' => [
                    'trace' => [
                        'trace_id' => $trace_id,
                        'span_id' => $span_id,
                        'op' => property_exists($this, 'service') ? 'soap.client' : 'http.client', // Detect if SOAP or REST
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
                        'processor_count' => defined('PHP_OS_FAMILY') && PHP_OS_FAMILY === 'Linux' ? (function_exists('shell_exec') ? (int)@shell_exec('nproc 2>/dev/null') ?: 1 : 1) : 1
                    ]
                ],
                'tags' => [
                    'release' => self::$VERSION,
                    'environment' => $environment,
                    'application' => $this->application ?? 'UNKNOWN',
                    'operation' => $metrics['operation'],
                    'vhost_user' => $vhostUser,
                    'ip_address' => $this->getServerIp(),
                    'php_version' => PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION,
                    'api_type' => property_exists($this, 'service') ? 'SOAP' : 'REST',
                    'openssl_enabled' => extension_loaded('openssl') ? 'true' : 'false'
                ],
                'measurements' => [
                    'duration' => ['value' => floatval($metrics['duration']), 'unit' => 'millisecond'],
                    'memory' => ['value' => memory_get_peak_usage(true), 'unit' => 'byte']
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
                'start_timestamp' => $metrics['start_timestamp']
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
                @curl_close($ch);
            }
        } catch (Exception $e) {
            // Fail silently
        }
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