# Zerotoprod\SslCertValidator

![](./art/logo.png)

[![Repo](https://img.shields.io/badge/github-gray?logo=github)](https://github.com/zero-to-prod/ssl-cert-validator)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/zero-to-prod/ssl-cert-validator/test.yml?label=tests)](https://github.com/zero-to-prod/ssl-cert-validator/actions)
[![Packagist Downloads](https://img.shields.io/packagist/dt/zero-to-prod/ssl-cert-validator?color=blue)](https://packagist.org/packages/zero-to-prod/ssl-cert-validator/stats)
[![Packagist Version](https://img.shields.io/packagist/v/zero-to-prod/ssl-cert-validator?color=f28d1a)](https://packagist.org/packages/zero-to-prod/ssl-cert-validator)
[![GitHub repo size](https://img.shields.io/github/repo-size/zero-to-prod/ssl-cert-validator)](https://github.com/zero-to-prod/ssl-cert-validator)
[![License](https://img.shields.io/packagist/l/zero-to-prod/ssl-cert-validator?color=red)](https://github.com/zero-to-prod/ssl-cert-validator/blob/main/LICENSE.md)
[![Hits-of-Code](https://hitsofcode.com/github/zero-to-prod/ssl-cert-validator?branch=main)](https://hitsofcode.com/github/zero-to-prod/ssl-cert-validator/view?branch=main)

## Contents

- [Introduction](#introduction)
- [Requirements](#requirements)
- [Installation](#installation)
- [Usage](#usage)
    - [Nested Objects](#nested-objects)
- [Local Development](#local-development)
    - [Prerequisites](#prerequisites)
    - [Initializing](#initializing)
    - [Testing](#testing)
    - [Configuration](#configuration)
- [Contributing](#contributing)

## Introduction

Fetch, validate, and verify SSL certificates.

## Requirements

- PHP 7.1 or higher.

## Installation

Install `Zerotoprod\SslCertValidator` via [Composer](https://getcomposer.org/):

```bash
composer require zero-to-prod/ssl-cert-validator
```

This will add the package to your project’s dependencies and create an autoloader entry for it.

## Usage

```php
use Zerotoprod\SslCertValidator\SslCertificate;

SslCertificate::rawCertificates('https://example.com');
SslCertificate::hostIsValid('https://example.com');
SslCertificate::isExpired('example.com');
SslCertificate::isSelfSigned('example.com');
SslCertificate::isTrustedRoot('example.com', '/path/to/cafile.pem');
```

## Local Development

This project provides a convenient [dock](https://github.com/zero-to-prod/dock) script to simplify local development workflows within Docker containers.

You can use this script to initialize the project, manage Composer dependencies, and run tests in a consistent PHP environment.

### Prerequisites

- Docker installed and running
- A `.env` file (created automatically via the `dock init` command, if it doesn’t already exist)

### Initializing

Use the following commands to set up the project:

```shell
sh dock init
sh dock composer update
```

### Testing

This command runs PHPUnit inside the Docker container, using the PHP version specified in your `.env` file.
You can modify or extend this script to include additional tests or commands as needed.

```shell
sh dock test
```

Run the test suite with all versions of php:

```shell
sh test.sh
```

### Configuration

Before starting development, verify that your `.env` file contains the correct settings.

You can specify which PHP version to use for local development, debugging, and Composer operations by updating these variables in your `.env` file:

```dotenv
PHP_VERSION=8.1
PHP_DEBUG=8.1
PHP_COMPOSER=8.1
```

Make sure these values reflect the PHP versions you intend to use.
If the `.env` file does not exist, run the `sh dock init` command to create one from the `.env.example` template.

## Contributing

Contributions, issues, and feature requests are welcome!
Feel free to check the [issues](https://github.com/zero-to-prod/ssl-cert-validator/issues) page if you want to contribute.

1. Fork the repository.
2. Create a new branch (`git checkout -b feature-branch`).
3. Commit changes (`git commit -m 'Add some feature'`).
4. Push to the branch (`git push origin feature-branch`).
5. Create a new Pull Request.