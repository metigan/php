<?php

namespace Metigan\Service;

use Metigan\Exception\ValidationException;
use Metigan\Http\HttpClient;

/**
 * Contacts service for managing contacts
 */
class ContactsService
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Create a new contact
     */
    public function create(
        string $email,
        string $audienceId,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $phone = null,
        ?array $tags = null,
        ?array $customFields = null,
        string $status = 'subscribed'
    ): array {
        if (empty($email)) {
            throw new ValidationException("email is required", "email");
        }
        if (empty($audienceId)) {
            throw new ValidationException("audienceId is required", "audienceId");
        }

        $body = [
            'email' => $email,
            'audienceId' => $audienceId,
            'status' => $status,
        ];

        if ($firstName !== null) {
            $body['firstName'] = $firstName;
        }
        if ($lastName !== null) {
            $body['lastName'] = $lastName;
        }
        if ($phone !== null) {
            $body['phone'] = $phone;
        }
        if ($tags !== null) {
            $body['tags'] = $tags;
        }
        if ($customFields !== null) {
            $body['customFields'] = $customFields;
        }

        return $this->httpClient->post('/api/contacts', $body);
    }

    /**
     * Get contact by ID
     */
    public function get(string $contactId): array
    {
        if (empty($contactId)) {
            throw new ValidationException("contactId is required", "contactId");
        }
        return $this->httpClient->get('/api/contacts/' . $contactId);
    }

    /**
     * Get contact by email address
     */
    public function getByEmail(string $email, string $audienceId): array
    {
        if (empty($email)) {
            throw new ValidationException("email is required", "email");
        }
        if (empty($audienceId)) {
            throw new ValidationException("audienceId is required", "audienceId");
        }
        return $this->httpClient->get('/api/contacts/email/' . $email, ['audienceId' => $audienceId]);
    }

    /**
     * Update an existing contact
     */
    public function update(
        string $contactId,
        ?string $firstName = null,
        ?string $lastName = null,
        ?string $phone = null,
        ?array $tags = null,
        ?array $customFields = null,
        ?string $status = null
    ): array {
        if (empty($contactId)) {
            throw new ValidationException("contactId is required", "contactId");
        }

        $body = [];
        if ($firstName !== null) {
            $body['firstName'] = $firstName;
        }
        if ($lastName !== null) {
            $body['lastName'] = $lastName;
        }
        if ($phone !== null) {
            $body['phone'] = $phone;
        }
        if ($tags !== null) {
            $body['tags'] = $tags;
        }
        if ($customFields !== null) {
            $body['customFields'] = $customFields;
        }
        if ($status !== null) {
            $body['status'] = $status;
        }

        return $this->httpClient->patch('/api/contacts/' . $contactId, $body);
    }

    /**
     * List contacts with optional filters
     */
    public function list(
        ?string $audienceId = null,
        ?string $status = null,
        ?string $tag = null,
        ?string $search = null,
        int $page = 1,
        int $limit = 50
    ): array {
        $params = [];
        if ($audienceId !== null) {
            $params['audienceId'] = $audienceId;
        }
        if ($status !== null) {
            $params['status'] = $status;
        }
        if ($tag !== null) {
            $params['tag'] = $tag;
        }
        if ($search !== null) {
            $params['search'] = $search;
        }
        $params['page'] = $page;
        $params['limit'] = $limit;

        return $this->httpClient->get('/api/contacts', $params);
    }

    /**
     * Delete a contact
     */
    public function delete(string $contactId): void
    {
        if (empty($contactId)) {
            throw new ValidationException("contactId is required", "contactId");
        }
        $this->httpClient->delete('/api/contacts/' . $contactId);
    }

    /**
     * Subscribe a contact
     */
    public function subscribe(string $contactId): void
    {
        if (empty($contactId)) {
            throw new ValidationException("contactId is required", "contactId");
        }
        $this->httpClient->post('/api/contacts/' . $contactId . '/subscribe', []);
    }

    /**
     * Unsubscribe a contact
     */
    public function unsubscribe(string $contactId): void
    {
        if (empty($contactId)) {
            throw new ValidationException("contactId is required", "contactId");
        }
        $this->httpClient->post('/api/contacts/' . $contactId . '/unsubscribe', []);
    }

    /**
     * Add tags to a contact
     */
    public function addTags(string $contactId, array $tags): void
    {
        if (empty($contactId)) {
            throw new ValidationException("contactId is required", "contactId");
        }
        if (empty($tags)) {
            throw new ValidationException("at least one tag is required", "tags");
        }
        $this->httpClient->post('/api/contacts/' . $contactId . '/tags', ['tags' => $tags]);
    }

    /**
     * Remove tags from a contact
     */
    public function removeTags(string $contactId, array $tags): void
    {
        if (empty($contactId)) {
            throw new ValidationException("contactId is required", "contactId");
        }
        if (empty($tags)) {
            throw new ValidationException("at least one tag is required", "tags");
        }
        $this->httpClient->delete('/api/contacts/' . $contactId . '/tags', ['tags' => $tags]);
    }
}

