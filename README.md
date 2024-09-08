# `Zerotoprod\Url`

[![Repo](https://img.shields.io/badge/github-gray?logo=github)](https://github.com/zero-to-prod/ssl-cert-validator)
[![Latest Version on Packagist](https://img.shields.io/packagist/v/zero-to-prod/ssl-cert-validator.svg)](https://packagist.org/packages/zero-to-prod/ssl-cert-validator)
![test](https://github.com/zero-to-prod/ssl-cert-validator/actions/workflows/phpunit.yml/badge.svg)
![Downloads](https://img.shields.io/packagist/dt/zero-to-prod/ssl-cert-validator.svg?style=flat-square&#41;]&#40;https://packagist.org/packages/zero-to-prod/ssl-cert-validator&#41)

Fetch, validate, and verify SSL certificates.

## Installation

Install the package via Composer:

```bash
composer require zerotoprod/ssl-cert-validator
```

## Usage

```php
use Zerotoprod\SslCertValidator\Certificate;

Certificate::fromHostName('https://example.com');
Certificate::hostIsValid('https://example.com');
Certificate::isExpired('example.com');
Certificate::isSelfSigned('example.com');
Certificate::isTrustedRoot('example.com', '/path/to/cafile.pem');
```