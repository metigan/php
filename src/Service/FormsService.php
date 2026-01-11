<?php

namespace Metigan\Service;

use Metigan\Exception\ValidationException;
use Metigan\Http\HttpClient;

/**
 * Forms service for managing forms
 */
class FormsService
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Submit a form
     */
    public function submit(string $formId, array $data): array
    {
        if (empty($formId)) {
            throw new ValidationException("formId is required", "formId");
        }
        if (empty($data)) {
            throw new ValidationException("form data is required", "data");
        }

        return $this->httpClient->post('/api/submissions', [
            'formId' => $formId,
            'data' => $data,
        ]);
    }

    /**
     * Get form by ID or slug
     */
    public function get(string $formIdOrSlug): array
    {
        if (empty($formIdOrSlug)) {
            throw new ValidationException("formIdOrSlug is required", "formIdOrSlug");
        }
        return $this->httpClient->get('/api/forms/' . $formIdOrSlug);
    }

    /**
     * List all forms
     */
    public function list(int $page = 1, int $limit = 10): array
    {
        $params = [
            'page' => $page,
            'limit' => $limit,
        ];

        return $this->httpClient->get('/api/forms', $params);
    }
}

