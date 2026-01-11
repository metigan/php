<?php

namespace Metigan\Service;

use Metigan\Exception\ValidationException;
use Metigan\Http\HttpClient;

/**
 * Email service for sending emails
 */
class EmailService
{
    private HttpClient $httpClient;

    public function __construct(HttpClient $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * Send an email
     *
     * @param string $fromAddress Sender email address (or "Name <email>")
     * @param array $recipients List of recipient email addresses
     * @param string $subject Email subject
     * @param string $content Email content (HTML supported)
     * @param array|null $attachments Optional list of attachments
     * @param array|null $cc Optional CC recipients
     * @param array|null $bcc Optional BCC recipients
     * @param string|null $replyTo Optional reply-to address
     * @param string|null $trackingId Optional tracking ID
     * @return array Email response
     * @throws ValidationException
     */
    public function sendEmail(
        string $fromAddress,
        array $recipients,
        string $subject,
        string $content,
        ?array $attachments = null,
        ?array $cc = null,
        ?array $bcc = null,
        ?string $replyTo = null,
        ?string $trackingId = null
    ): array {
        // Validate required fields
        if (empty($fromAddress)) {
            throw new ValidationException("fromAddress is required", "fromAddress");
        }
        if (empty($recipients)) {
            throw new ValidationException("at least one recipient is required", "recipients");
        }
        if (empty($subject)) {
            throw new ValidationException("subject is required", "subject");
        }
        if (empty($content)) {
            throw new ValidationException("content is required", "content");
        }

        // Prepare request body
        $body = [
            'from' => $fromAddress,
            'recipients' => $recipients,
            'subject' => $subject,
            'content' => $content,
        ];

        // Process attachments - encode to base64 if needed
        if ($attachments !== null && !empty($attachments)) {
            $processedAttachments = [];
            foreach ($attachments as $att) {
                $attachment = [
                    'filename' => $att['filename'] ?? 'file',
                    'contentType' => $att['contentType'] ?? 'application/octet-stream',
                ];

                // If content is binary, encode to base64
                if (isset($att['content'])) {
                    if (is_string($att['content']) && !base64_decode($att['content'], true)) {
                        // Not base64, encode it
                        $attachment['content'] = base64_encode($att['content']);
                    } else {
                        // Already base64 or binary
                        $attachment['content'] = is_string($att['content']) 
                            ? $att['content'] 
                            : base64_encode($att['content']);
                    }
                }

                $processedAttachments[] = $attachment;
            }
            $body['attachments'] = $processedAttachments;
        }

        if ($cc !== null && !empty($cc)) {
            $body['cc'] = $cc;
        }
        if ($bcc !== null && !empty($bcc)) {
            $body['bcc'] = $bcc;
        }
        if ($replyTo !== null) {
            $body['replyTo'] = $replyTo;
        }
        if ($trackingId !== null) {
            $body['trackingId'] = $trackingId;
        }

        return $this->httpClient->post('/api/email/send', $body);
    }

    /**
     * Send an email using a template
     *
     * @param string $templateId Template ID
     * @param array $variables Template variables
     * @param string $fromAddress Sender email address
     * @param array $recipients List of recipient email addresses
     * @param string|null $replyTo Optional reply-to address
     * @return array Email response
     * @throws ValidationException
     */
    public function sendEmailWithTemplate(
        string $templateId,
        array $variables,
        string $fromAddress,
        array $recipients,
        ?string $replyTo = null
    ): array {
        if (empty($templateId)) {
            throw new ValidationException("templateId is required", "templateId");
        }

        $body = [
            'templateId' => $templateId,
            'variables' => $variables,
            'from' => $fromAddress,
            'recipients' => $recipients,
        ];

        if ($replyTo !== null) {
            $body['replyTo'] = $replyTo;
        }

        return $this->httpClient->post('/api/email/send', $body);
    }
}
