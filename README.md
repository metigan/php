# Metigan PHP SDK

[![Packagist](https://img.shields.io/packagist/v/metigan/metigan-php)](https://packagist.org/packages/metigan/metigan-php)
[![PHP Version](https://img.shields.io/packagist/php-v/metigan/metigan-php)](https://packagist.org/packages/metigan/metigan-php)
[![License](https://img.shields.io/packagist/l/metigan/metigan-php)](LICENSE)

Official PHP SDK for the Metigan API. Send emails, manage contacts, audiences, templates, and forms with ease.

## ✨ Features

- 📧 **Send Emails** - Send HTML emails with attachments, CC, BCC, and templates
- 👥 **Manage Contacts** - Create, update, list, and manage contact subscriptions
- 🎯 **Audiences** - Organize contacts into audiences and track statistics
- 📝 **Forms** - Submit and manage form data
- 🎨 **Templates** - Use email templates with dynamic variables
- 🔄 **Automatic Retry** - Built-in retry logic for failed requests
- ⚡ **Type Safety** - Strong typing with PHP 7.4+ features
- 🛡️ **Error Handling** - Comprehensive exception handling

## 📦 Installation

Install via Composer:

```bash
composer require metigan/metigan-php
```

Or add to your `composer.json`:

```json
{
    "require": {
        "metigan/metigan-php": "^1.0"
    }
}
```

## 🚀 Quick Start

```php
<?php

require 'vendor/autoload.php';

use Metigan\MetiganClient;
use Metigan\Exception\ApiException;
use Metigan\Exception\ValidationException;

// Initialize the client
$client = new MetiganClient(getenv('METIGAN_API_KEY'));

// Send an email
try {
    $result = $client->email()->sendEmail(
        fromAddress: "Sender <sender@example.com>",
        recipients: ["recipient@example.com"],
        subject: "Hello!",
        content: "<h1>Welcome</h1><p>Thank you for signing up.</p>"
    );

    if ($result['success'] ?? false) {
        echo "Email sent successfully!\n";
        // API returns fields in camelCase format
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
```

## ⚙️ Configuration

```php
$client = new MetiganClient(
    apiKey: "your-api-key",
    timeout: 30,        // Optional, defaults to 30 seconds
    retryCount: 3,      // Optional, defaults to 3 retries
    retryDelay: 2,      // Optional, defaults to 2 seconds between retries
    debug: false        // Optional, enables debug logging
);
```

### Getting Your API Key

Get your API key from the [Metigan Dashboard](https://app.metigan.io/api-keys).

## 📧 Sending Emails

### Basic Email

```php
$result = $client->email()->sendEmail(
    fromAddress: "Sender <sender@example.com>",
    recipients: ["recipient@example.com"],
    subject: "Email Subject",
    content: "<h1>HTML Content</h1><p>This is the email body.</p>"
);

if ($result['success']) {
    echo "Email sent! Emails remaining: " . $result['emailsRemaining'] . "\n";
}
```

### Email with CC and BCC

```php
$result = $client->email()->sendEmail(
    fromAddress: "Company <company@example.com>",
    recipients: ["main@example.com"],
    subject: "Meeting Invitation",
    content: "You're invited to our meeting.",
    cc: ["copy@example.com"],
    bcc: ["hidden@example.com"],
    replyTo: "reply@example.com"
);
```

### Email with Attachments

```php
$attachment = [
    'content' => file_get_contents('document.pdf'),
    'filename' => 'document.pdf',
    'contentType' => 'application/pdf',
];

$result = $client->email()->sendEmail(
    fromAddress: "Company <company@example.com>",
    recipients: ["customer@example.com"],
    subject: "Important Document",
    content: "Please find the document attached.",
    attachments: [$attachment]
);
```

### Email with Template

```php
$variables = [
    'name' => 'John Doe',
    'company' => 'Acme Inc',
];

$result = $client->email()->sendEmailWithTemplate(
    templateId: "template-123",
    variables: $variables,
    fromAddress: "Sender <sender@example.com>",
    recipients: ["recipient@example.com"],
    replyTo: "reply@example.com"
);
```

## 👥 Managing Contacts

### Create Contact

```php
$contact = $client->contacts()->create(
    email: "new@example.com",
    audienceId: "audience-123",
    firstName: "Jane",
    lastName: "Doe",
    phone: "+1234567890",
    tags: ["customer", "newsletter"],
    customFields: ["company" => "Acme Inc"],
    status: "subscribed"
);
```

### Get Contact

```php
// Get by ID
$contact = $client->contacts()->get("contact-456");

// Get by email
$contact = $client->contacts()->getByEmail("jane@example.com", "audience-123");
```

### List Contacts

```php
$result = $client->contacts()->list(
    audienceId: "audience-123",
    status: "subscribed",
    tag: "customer",
    search: "john",
    page: 1,
    limit: 50
);

echo "Total contacts: " . ($result['pagination']['total'] ?? 0) . "\n";
foreach ($result['contacts'] ?? [] as $contact) {
    echo $contact['email'] . ": " . ($contact['firstName'] ?? 'N/A') . "\n";
}
```

### Update Contact

```php
$updated = $client->contacts()->update(
    contactId: "contact-456",
    firstName: "Jane Marie",
    lastName: "Smith",
    tags: ["customer", "vip"],
    status: "subscribed"
);
```

### Manage Subscription

```php
// Subscribe
$client->contacts()->subscribe("contact-456");

// Unsubscribe
$client->contacts()->unsubscribe("contact-456");
```

### Manage Tags

```php
// Add tags
$client->contacts()->addTags("contact-456", ["vip", "black-friday"]);

// Remove tags
$client->contacts()->removeTags("contact-456", ["test"]);
```

## 🎯 Managing Audiences

### Create Audience

```php
$audience = $client->audiences()->create(
    name: "Main Newsletter",
    description: "Main subscriber list"
);
```

### List Audiences

```php
$result = $client->audiences()->list(page: 1, limit: 10);

foreach ($result['audiences'] ?? [] as $audience) {
    echo $audience['name'] . ": " . ($audience['count'] ?? 0) . " contacts\n";
}
```

### Get Audience Statistics

```php
$stats = $client->audiences()->getStats("audience-123");

echo "Total: " . ($stats['total'] ?? 0) . "\n";
echo "Subscribed: " . ($stats['subscribed'] ?? 0) . "\n";
echo "Unsubscribed: " . ($stats['unsubscribed'] ?? 0) . "\n";
```

### Delete Audience

```php
$client->audiences()->delete("audience-123");
```

## 🎨 Managing Templates

```php
// Get template
$template = $client->templates()->get("template-123");

// List templates
$templates = $client->templates()->list(page: 1, limit: 10);

foreach ($templates as $template) {
    echo $template['name'] . "\n";
}
```

## 📝 Managing Forms

```php
// Submit form
$result = $client->forms()->submit("form-123", [
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'message' => 'Hello, I would like more information.'
]);

echo $result['message'] ?? 'Form submitted successfully';

// Get form
$form = $client->forms()->get("form-123");

// List forms
$forms = $client->forms()->list(page: 1, limit: 10);
```

## 🛡️ Error Handling

The SDK provides specific exception types for different error scenarios:

```php
use Metigan\Exception\ApiException;
use Metigan\Exception\ValidationException;

try {
    $result = $client->email()->sendEmail(...);
} catch (ValidationException $e) {
    // Handle validation errors (422)
    echo "Validation Error: " . $e->getMessage() . "\n";
    if ($e->getField()) {
        echo "Field: " . $e->getField() . "\n";
    }
} catch (ApiException $e) {
    // Handle API errors (4xx, 5xx)
    echo "API Error: " . $e->getStatusCode() . " - " . $e->getMessage() . "\n";
    if ($e->getError()) {
        echo "Error code: " . $e->getError() . "\n";
    }
} catch (\Exception $e) {
    // Handle other exceptions
    echo "Unexpected error: " . $e->getMessage() . "\n";
}
```

### Exception Types

- **`ValidationException`** - Thrown when request validation fails (422)
- **`ApiException`** - Thrown for API errors (4xx, 5xx)
- **`\Exception`** - Thrown for network or other unexpected errors

## 📋 Response Format

**Important:** The API returns all fields in **camelCase** format, not snake_case. Always use camelCase when accessing response data:

```php
// ✅ Correct
$result['emailsRemaining']
$result['recipientCount']
$result['successfulEmails']

// ❌ Incorrect
$result['emails_remaining']  // Will be null/undefined
$result['recipient_count']   // Will be null/undefined
```

## 🔧 Requirements

- PHP 7.4 or higher
- `ext-json` PHP extension
- `ext-curl` PHP extension

## 📚 API Documentation

For detailed API documentation, visit:
- [Full Documentation](https://docs.metigan.io)
- [API Reference](https://docs.metigan.io/api)

## 🤝 Support

- **Email:** support@metigan.io
- **Issues:** [GitHub Issues](https://github.com/metigan/php/issues)
- **Documentation:** [https://docs.metigan.io](https://docs.metigan.io)

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🔗 Links

- [Metigan Homepage](https://metigan.io)
- [Documentation](https://docs.metigan.io)
- [API Reference](https://docs.metigan.io/api)
- [GitHub Repository](https://github.com/metigan/php)
