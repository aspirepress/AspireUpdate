<?php

class AspirePress_Debug
{
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
            'response' => (is_array($response)) ? self::filterKeys($response, $desiredKeys): $response->get_error_message(),
        ];

        self::logData('RESPONSE', $loggedData);
    }

    public static function logString(string $message, string $type = 'INFO')
    {
        if (!self::$enabled || !in_array('string', self::$desiredTypes)) {
            return;
        }

        self::logData(strtoupper($type), $message);
    }

    public static function logNonScalar($message, string $type = 'INFO')
    {
        if (!self::$enabled || !in_array('string', self::$desiredTypes)) {
            return;
        }

        self::logData(strtoupper($type), $message);
    }

    private static function logData(string $type, $data)
    {
        if (!self::$enabled) {
            return;
        }
        if (!is_string($data)) {
            $logMessage = sprintf('[%s] %s', $type, print_r($data, true));
        } else {
            $logMessage = sprintf('[%s] %s', $type, $data);
        }

        file_put_contents(self::$logPath . '/aspirepress-debug.log', $logMessage . PHP_EOL, FILE_APPEND);
    }

    private static function filterKeys(array $data, array $desiredKeys)
    {
        return array_filter($data, function ($key) use ($desiredKeys) {
            return in_array($key, $desiredKeys);
        }, ARRAY_FILTER_USE_KEY);
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
}
