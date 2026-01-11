<?php

namespace Metigan\Service;

use Metigan\Exception\ValidationException;
use Metigan\Http\HttpClient;

/**
 * Audiences service for managing audiences
 */
class AudiencesService
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Create a new audience
     */
    public function create(string $name, ?string $description = null): array
    {
        if (empty($name)) {
            throw new ValidationException("name is required", "name");
        }

        $body = ['name' => $name];
        if ($description !== null) {
            $body['description'] = $description;
        }

        return $this->httpClient->post('/api/audiences', $body);
    }

    /**
     * Get audience by ID
     */
    public function get(string $audienceId): array
    {
        if (empty($audienceId)) {
            throw new ValidationException("audienceId is required", "audienceId");
        }
        return $this->httpClient->get('/api/audiences/' . $audienceId);
    }

    /**
     * List audiences with optional pagination
     */
    public function list(int $page = 1, int $limit = 10): array
    {
        $params = [
            'page' => $page,
            'limit' => $limit,
        ];

        return $this->httpClient->get('/api/audiences', $params);
    }

    /**
     * Get statistics for an audience
     */
    public function getStats(string $audienceId): array
    {
        if (empty($audienceId)) {
            throw new ValidationException("audienceId is required", "audienceId");
        }
        return $this->httpClient->get('/api/audiences/' . $audienceId . '/stats');
    }

    /**
     * Delete an audience
     */
    public function delete(string $audienceId): void
    {
        if (empty($audienceId)) {
            throw new ValidationException("audienceId is required", "audienceId");
        }
        $this->httpClient->delete('/api/audiences/' . $audienceId);
    }
}

