# Notifreak
![Notifreak logo](https://thumbor.jakimiak.net/0eQBHb2Vs95Qa0iL5j31fT9BIWM=/250x/https://jakimiak.net/images/projects/notifreak/logo.jpg)

Notifreak is an open-source application designed for aggregating notifications from various services and sending them to selected notification channels. The idea originated when I noticed that [Bugsnag](https://www.bugsnag.com) does not offer integration with [Telegram](https://telegram.org), though it does allow sending requests to a specified URL.

## Table of Contents
- [Prerequisites](#prerequisites)
- [Installation](#installation)
- [Configuration](#configuration)
- [Webhook URL structure](#webhook-url-structure)
- [Security](#security)
- [Request parser](#request-parser)
- [Notification channels](#notification-channels)
- [Contributing](#contributing)

## Prerequisites
- PHP 8.3 or higher
- [Composer](https://getcomposer.org)
- [PhpRedis](https://github.com/phpredis/phpredis) (optional)
- [Redis](https://redis.io) (optional)

## Installation
### Manual
1. Download the latest stable version of the application.
2. Install dependencies with the command `composer install --no-dev --optimize-autoloader --classmap-authoritative`.
3. Set environment variables. See [Configuration](#configuration).
4. Run `php bin/console messenger:consume` to start the worker that processes messages.

### Docker
Notifreak uses [dunglas/symfony-docker](https://github.com/dunglas/symfony-docker). To run Notifreak in Docker, set the environment variables and then execute the command `docker compose -f compose.yaml -f compose.prod.yaml up -d --build` as described [here](https://github.com/dunglas/symfony-docker/blob/main/docs/production.md).

## Configuration
Configuration can be done via environment variables. You can use system environment variables or the `.env.local` file as described [here](https://symfony.com/doc/current/configuration.html#configuring-environment-variables-in-production).

With reference to [https://github.com/dunglas/symfony-docker/blob/main/docs/production.md](https://github.com/dunglas/symfony-docker/blob/main/docs/production.md#passing-local-environment-variables-to-containers), if you are using Docker in production environment, variables from `.env.local` and `.env.*.local` files are ignored, so you need to use system environment variables.

### APP_ENV
**type:** `string` **default:** `'dev'` **possible values:** `'dev'`, `'test'`, or `'prod'`

The environment in which the application is running. More info [here](https://symfony.com/doc/current/configuration.html#selecting-the-active-environment).

### APP_DEBUG
**type:** `boolean` **default:** `true` for `'dev'`, `false` for `'test'` and `'prod'` environments

Determines whether the application runs in debug mode.

### APP_SECRET
**type:** `string` **default:** `'ChangeMe'`

A Symfony configuration parameter used to enhance application security. You should set this to a unique, random string. It's crucial to keep this secret safe and not expose it publicly.

### APP_TIMEZONE
**type:** `string` **default:** `'UTC'`

Your timezone.

### APP_LOCALE
**type:** `string` **default:** `'en-US'`

Defines the application locale.

### APP_URI
**type:** `string` **default:** `'http://localhost'`

The URL where the application is hosted.

### SECURITY_KEY
**type:** `string` **default:** `'ChangeMe'`

Specifies the security key Notifreak uses to [sign secure URLs](#security). You should set this to a unique, random string. It's crucial to keep this secret safe and not expose it publicly.

### MESSENGER_TRANSPORT_DSN
**type:** `string` **default:** `'redis://redis:6379/messages'`

Notifreak sends notifications using the [Symfony Messenger](https://symfony.com/doc/current/messenger.html) component. Notifications are queued in Redis by default and processed asynchronously. If you'd prefer for the notifications to be processed synchronously, set this value to `'sync://'`.

### TELEGRAM_DSN
**type:** `string` **default:** `'null://null'`

Notifreak uses the [Symfony Notifier](https://symfony.com/doc/current/notifier.html) and [Telegram Notifier](https://github.com/symfony/symfony/blob/7.1/src/Symfony/Component/Notifier/Bridge/Telegram/README.md) components to send notifications to Telegram. Set this value if you want to use this notification channel.

## Webhook URL structure

### Structure
http://example.com/notification/{signature}/{parserName}?channels[]={channel_one}&channels[]={channel_two}

#### {signature}
The URL signature. See [Security](#security).

#### {parserName}
The parser used to format the notification. If you provide a parser name that doesn't exist, the notification content will be sent as raw request body. See [Request parser](#request-parser).

#### {channel_one}, {channel_two}
Notification channels to which the notification should be sent. See [Notification channels](#notification-channels).

### Example
http://example.com/notification/714c6a17e58c08eab836128523d3390d5c1d8294e48c1210383b91bb67eaeb1e/bugsnag?channels[]=telegram

### URL Generation
Generating URLs can be cumbersome, so a wizard was created to guide you through the process. Simply use the command `php bin/console app:generate-url` and answer a few questions.

## Security
Unfortunately, not all services allow sending headers to a webhook URL. Therefore, an authorization mechanism inspired by [Thumbor](https://thumbor.readthedocs.io/en/latest/security.html#hmac-method) has been implemented. The webhook URL includes a signature generated from the parser name, serialized query params, and `SECURITY_KEY`.

### Debug mode
When debugging the application, generating the signature each time can be irritating, so while the application runs in debug mode, you can use `unsafe` as the signature (e.g., http://example.com/notification/unsafe/bugsnag?channels[]=telegram).

## Request parser
Each service sends a different request body to a webhook URL. The request parser's task is to process the request into [ContentInterface](src/Parser/ContentInterface.php), enabling a neatly formatted notification to be sent. For instance, the Bugsnag parser formats the notification as shown in the screenshot below.

![Formatted Bugsnag notification](https://thumbor.jakimiak.net/zxlX6yymasfEBqR_1o_SHbJZg4k=/https://jakimiak.net/images/projects/notifreak/bugsnag-notification.jpg)

You can easily create your own parser by implementing [ParserInterface](src/Parser/ParserInterface.php).

## Notification channels
Notifreak can support multiple notification channels. Adding new channels is simple. Just create a class implementing [ChannelInterface](src/Message/Channel/ChannelInterface.php).

## Contributing
Notifreak is an open-source project. If you'd like to contribute, feel free to propose a PR!
