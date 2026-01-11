<?php

namespace Metigan\Http;

use Metigan\Exception\ApiException;
use Metigan\Exception\ValidationException;

/**
 * HTTP client for Metigan API with retry logic
 */
class HttpClient
{
    private const BASE_URL = "https://api.metigan.com";

    private string $apiKey;
    private int $timeout;
    private int $retryCount;
    private int $retryDelay;
    private bool $debug;

    public function __construct(
        string $apiKey,
        int $timeout = 30,
        int $retryCount = 3,
        int $retryDelay = 2,
        bool $debug = false
    ) {
        $this->apiKey = $apiKey;
        $this->timeout = $timeout;
        $this->retryCount = $retryCount;
        $this->retryDelay = $retryDelay;
        $this->debug = $debug;
    }

    /**
     * Make GET request
     */
    public function get(string $endpoint, ?array $params = null): array
    {
        return $this->request('GET', $endpoint, $params);
    }

    /**
     * Make POST request
     */
    public function post(string $endpoint, ?array $body = null): array
    {
        return $this->request('POST', $endpoint, null, $body);
    }

    /**
     * Make PATCH request
     */
    public function patch(string $endpoint, ?array $body = null): array
    {
        return $this->request('PATCH', $endpoint, null, $body);
    }

    /**
     * Make DELETE request
     */
    public function delete(string $endpoint, ?array $body = null): void
    {
        $this->request('DELETE', $endpoint, null, $body);
    }

    /**
     * Execute HTTP request with retry logic
     */
    private function request(string $method, string $endpoint, ?array $params = null, ?array $body = null): array
    {
        $url = self::BASE_URL . $endpoint;

        // Add query parameters for GET requests
        if ($method === 'GET' && $params !== null && !empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        $lastException = null;

        for ($attempt = 0; $attempt <= $this->retryCount; $attempt++) {
            try {
                $ch = curl_init($url);

                $headers = [
                    'Content-Type: application/json',
                    'x-api-key: ' . $this->apiKey,
                    'User-Agent: Metigan-PHP-SDK/1.0',
                ];

                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST => $method,
                    CURLOPT_HTTPHEADER => $headers,
                    CURLOPT_TIMEOUT => $this->timeout,
                    CURLOPT_CONNECTTIMEOUT => $this->timeout,
                ]);

                // Set request body for POST, PATCH, DELETE
                if (in_array($method, ['POST', 'PATCH', 'DELETE']) && $body !== null) {
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($body));
                }

                $response = curl_exec($ch);
                $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $error = curl_error($ch);
                curl_close($ch);

                if ($response === false) {
                    throw new ApiException("cURL error: " . $error, 0);
                }

                $data = json_decode($response, true);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new ApiException("Invalid JSON response: " . json_last_error_msg(), $statusCode);
                }

                // Handle errors
                if ($statusCode >= 400) {
                    $message = $data['message'] ?? 'API request failed';
                    $error = $data['error'] ?? null;

                    // Retry on 5xx errors
                    if ($statusCode >= 500 && $attempt < $this->retryCount) {
                        if ($this->debug) {
                            error_log("Retrying request after {$statusCode} error (attempt " . ($attempt + 1) . ")");
                        }
                        sleep($this->retryDelay * ($attempt + 1));
                        $lastException = new ApiException($message, $statusCode, $error);
                        continue;
                    }

                    // Handle validation errors (422)
                    if ($statusCode === 422) {
                        $field = $data['field'] ?? null;
                        throw new ValidationException($message, $field);
                    }

                    throw new ApiException($message, $statusCode, $error);
                }

                return $data ?? [];

            } catch (ApiException $e) {
                if ($e->getStatusCode() >= 500 && $attempt < $this->retryCount) {
                    $lastException = $e;
                    continue;
                }
                throw $e;
            } catch (\Exception $e) {
                if ($attempt < $this->retryCount) {
                    $lastException = $e;
                    sleep($this->retryDelay * ($attempt + 1));
                    continue;
                }
                throw new ApiException("Request failed: " . $e->getMessage(), 0, null, $e);
            }
        }

        // If we exhausted all retries, throw the last exception
        if ($lastException instanceof ApiException) {
            throw $lastException;
        }
        throw new ApiException("Request failed after " . ($this->retryCount + 1) . " attempts");
    }
}








