<?php

namespace Metigan\Service;

use Metigan\Exception\ValidationException;
use Metigan\Http\HttpClient;

/**
 * Templates service for managing email templates
 */
class TemplatesService
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Get template by ID
     */
    public function get(string $templateId): array
    {
        if (empty($templateId)) {
            throw new ValidationException("templateId is required", "templateId");
        }
        return $this->httpClient->get('/api/templates/' . $templateId);
    }

    /**
     * List all templates
     */
    public function list(int $page = 1, int $limit = 10): array
    {
        $params = [
            'page' => $page,
            'limit' => $limit,
        ];

        $result = $this->httpClient->get('/api/templates', $params);
        return $result['templates'] ?? [];
    }
}

