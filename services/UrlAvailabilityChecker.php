<?php

namespace app\services;

class UrlAvailabilityChecker
{
    private const TIMEOUT_SECONDS = 5;

    /**
     * @param string $url
     * @return bool
     */
    public function isAvailable(string $url): bool
    {
        $responseCode = $this->requestStatusCode($url, 'HEAD');
        if ($responseCode === null) {
            $responseCode = $this->requestStatusCode($url, 'GET');
        }

        if ($responseCode === null) {
            return false;
        }

        return $responseCode >= 200 && $responseCode < 400;
    }

    /**
     * @param string $url
     * @param string $method
     * @return int|null
     */
    private function requestStatusCode(string $url, string $method): ?int
    {
        $parts = parse_url($url);
        if (!$parts || empty($parts['host']) || empty($parts['scheme'])) {
            return null;
        }

        $host = $parts['host'];
        $scheme = strtolower((string)$parts['scheme']);
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);
        $path = ($parts['path'] ?? '/') . (isset($parts['query']) ? ('?' . $parts['query']) : '');

        $transport = $scheme === 'https' ? 'ssl://' : '';
        $socket = @fsockopen($transport . $host, $port, $errno, $errstr, self::TIMEOUT_SECONDS);
        if ($socket === false) {
            return null;
        }

        stream_set_timeout($socket, self::TIMEOUT_SECONDS);
        $request = sprintf(
            "%s %s HTTP/1.1\r\nHost: %s\r\nConnection: close\r\nUser-Agent: Yii2ShortenerBot/1.0\r\n\r\n",
            $method,
            $path,
            $host
        );
        fwrite($socket, $request);
        $statusLine = fgets($socket);
        fclose($socket);

        if (!is_string($statusLine) || !preg_match('/\s(\d{3})\s/', $statusLine, $matches)) {
            return null;
        }

        return (int)$matches[1];
    }
}
