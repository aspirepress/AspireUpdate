<?php

class AspirePress_Debug
{
    const ERROR = 1;
    CONST WARNING = 2;
    CONST INFO = 3;
    CONST DEBUG = 4;
    const NONE = 5;

    private static $statusTranslate = [
        self::ERROR => 'ERROR',
        self::WARNING => 'WARNING',
        self::INFO => 'INFO',
        self::DEBUG => 'DEBUG',
    ];

    private const DESIRED_REQUEST_KEYS = [
        'body',
        'method',
        'headers'
    ];

    private const DESIRED_RESPONSE_KEYS = [
        'body',
        'headers',
        'status'
    ];

    private static $desiredTypes = [
        'request',
        'response',
        'string'
    ];

    private static $enabled = false;

    private static $logPath = WP_CONTENT_DIR;

    private static $debugLevel = self::NONE;

    public static function logRequest(string $url, array $arguments, array $desiredKeys = self::DESIRED_REQUEST_KEYS)
    {
        if (!self::$enabled || !in_array('request', self::$desiredTypes)) {
            return;
        }

        $loggedData = [
            'url' => $url,
            'arguments' => self::filterKeys($arguments, $desiredKeys)
        ];

        self::logData('REQUEST', $loggedData);
    }

    public static function logResponse(string $url, $response, array $desiredKeys = self::DESIRED_RESPONSE_KEYS)
    {
        if (!self::$enabled || !in_array('response', self::$desiredTypes)) {
            return;
        }

        $loggedData = [
            'url' => $url,
        ];

        if ($response instanceof WP_Error) {
            $loggedData['wp_response'] = $response->get_error_message();
        }

        if (is_array($response)) {
            $loggedData['wp_response'] = self::filterResponse($response['http_response']);
        }

        self::logData('RESPONSE', $loggedData);
    }

    public static function logString(string $message, string $type = self::DEBUG)
    {
        if (!self::$enabled || !in_array('string', self::$desiredTypes) || self::$debugLevel > $type) {
            return;
        }

        self::logData(self::$statusTranslate[$type], $message);
    }

    public static function logNonScalar($message, string $type = self::DEBUG)
    {
        if (!self::$enabled || !in_array('string', self::$desiredTypes) || self::$debugLevel > $type) {
            return;
        }

        self::logData(self::$statusTranslate[$type], $message);
    }

    private static function logData(string $type, $data)
    {
        if (!self::$enabled) {
            return;
        }

        $message = self::parseData($data);

        $logMessage = sprintf('[%s] [%s] %s', $type, date('Y-m-d H:i:s'), $message);

        $logMessage .= str_repeat('=', 20);

        file_put_contents(self::$logPath . '/aspirepress-debug.log', $logMessage . PHP_EOL, FILE_APPEND);
    }

    private static function filterKeys(array $data, array $desiredKeys)
    {
        return array_filter($data, function ($key) use ($desiredKeys) {
            return in_array($key, $desiredKeys);
        }, ARRAY_FILTER_USE_KEY);
    }

    private static function filterResponse(WP_HTTP_Response $response)
    {
        $returnResponse = [];
        if (in_array('headers', self::DESIRED_RESPONSE_KEYS)) {
            $headers = $response->get_headers();
            if (is_object($headers) && $headers instanceof Requests_Utility_CaseInsensitiveDictionary) {
                $headers = $headers->getAll();
            }

            $returnResponse['headers'] = $headers;
        }

        if (in_array('body', self::DESIRED_RESPONSE_KEYS )) {
            $returnResponse['body'] = $response->get_data();
        }

        if (in_array('status', self::DESIRED_RESPONSE_KEYS )) {
            $returnResponse['status'] = $response->get_status();
        }

        return $returnResponse;
    }

    public static function setLogPath(string $path)
    {
        if (is_writable($path)) {
            self::$logPath = $path;
            return true;
        }

        throw new \InvalidArgumentException('Unable to write debug log!');
    }

    public static function enableDebug()
    {
        return self::$enabled = true;
    }

    public static function disableDebug()

    {
        return self::$enabled = false;
    }

    public static function registerDesiredType(string $type)
    {
        if (!in_array($type, self::$desiredTypes)) {
            self::$desiredTypes[] = $type;
        }
    }

    public static function removeDesiredType(string $type)
    {
        if (($key = array_search($type, self::$desiredTypes)) !== false) {
            unset(self::$desiredTypes[$key]);
        }
    }

    private static function parseData($data, $level = 1)
    {
        if (is_scalar($data)) {
            if ($level === 1) {
                return PHP_EOL . $data . PHP_EOL;
            }
            return $data;
        }

        if (is_object($data)) {
            return print_r($data, true);
        }

        if (is_array($data)) {
            $response = PHP_EOL;
            foreach ($data as $key => $value) {
                $response .= str_repeat(' ', $level * 4) . '[' . $key . '] => ' . self::parseData($value, $level + 1) . PHP_EOL;
            }
        }

        return $response;
    }

    public static function setDebugLevel(int $level = self::DEBUG)
    {
        if ($level > 4) {
            self::$debugLevel = self::ERROR;
            return;
        }

        self::$debugLevel = $level;
    }
}
