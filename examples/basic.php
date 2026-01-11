<?php

require __DIR__ . '/../vendor/autoload.php';

use Metigan\MetiganClient;
use Metigan\Exception\ApiException;
use Metigan\Exception\ValidationException;

// Initialize the client
$client = new MetiganClient(getenv('METIGAN_API_KEY'));

// Send an email
try {
    $result = $client->email()->sendEmail(
        fromAddress: "sender@example.com",
        recipients: ["recipient@example.com"],
        subject: "Hello!",
        content: "<h1>Welcome</h1><p>Thank you for signing up.</p>"
    );

    if ($result['success'] ?? false) {
        echo "Email sent successfully!\n";
        echo "Emails remaining: " . ($result['emailsRemaining'] ?? 'N/A') . "\n";
    }
} catch (ValidationException $e) {
    echo "Validation Error: " . $e->getMessage() . "\n";
    if ($e->getField()) {
        echo "Field: " . $e->getField() . "\n";
    }
} catch (ApiException $e) {
    echo "API Error: " . $e->getStatusCode() . " - " . $e->getMessage() . "\n";
}








