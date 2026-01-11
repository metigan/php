<?php

namespace Metigan;

use Metigan\Exception\ValidationException;
use Metigan\Http\HttpClient;
use Metigan\Service\EmailService;
use Metigan\Service\ContactsService;
use Metigan\Service\AudiencesService;
use Metigan\Service\TemplatesService;
use Metigan\Service\FormsService;

/**
 * Main Metigan client that provides access to all services
 */
class MetiganClient
{
    private HttpClient $httpClient;
    private EmailService $email;
    private ContactsService $contacts;
    private AudiencesService $audiences;
    private TemplatesService $templates;
    private FormsService $forms;

    /**
     * Initialize the Metigan client
     *
     * @param string $apiKey Your Metigan API key
     * @param int $timeout Request timeout in seconds (default: 30)
     * @param int $retryCount Number of retries on failure (default: 3)
     * @param int $retryDelay Delay between retries in seconds (default: 2)
     * @param bool $debug Enable debug mode (default: false)
     * @throws ValidationException
     */
    public function __construct(
        string $apiKey,
        int $timeout = 30,
        int $retryCount = 3,
        int $retryDelay = 2,
        bool $debug = false
    ) {
        if (empty($apiKey)) {
            throw new ValidationException("API key is required", "apiKey");
        }

        $this->httpClient = new HttpClient(
            $apiKey,
            $timeout,
            $retryCount,
            $retryDelay,
            $debug
        );

        // Initialize services
        $this->email = new EmailService($this->httpClient);
        $this->contacts = new ContactsService($this->httpClient);
        $this->audiences = new AudiencesService($this->httpClient);
        $this->templates = new TemplatesService($this->httpClient);
        $this->forms = new FormsService($this->httpClient);
    }

    /**
     * Get the email service
     */
    public function email(): EmailService
    {
        return $this->email;
    }

    /**
     * Get the contacts service
     */
    public function contacts(): ContactsService
    {
        return $this->contacts;
    }

    /**
     * Get the audiences service
     */
    public function audiences(): AudiencesService
    {
        return $this->audiences;
    }

    /**
     * Get the templates service
     */
    public function templates(): TemplatesService
    {
        return $this->templates;
    }

    /**
     * Get the forms service
     */
    public function forms(): FormsService
    {
        return $this->forms;
    }
}

