# EngineMailer Laravel Mail Transport

## Installation

#### Composer

To install through composer by using the following command:

    composer require hakimrazalan/enginemailer-laravel

## Get Started

### Set API Key

On `.env` file set API KEY as follow:

```
ENGINEMAILER_API_KEY=<api key>
```

### Add mail service

On `config/mail.php` file add enginemailer transport:

```
'enginemailer' => [
  'transport' => 'enginemailer'
]
```

### Set Mailer

On `.env`, you can change `MAIL_MAILER` setting to `enginemailer` to solely use EngineMailer as your mailer

```
MAIL_MAILER=enginemailer
```
