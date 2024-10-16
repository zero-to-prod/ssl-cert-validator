# `Zerotoprod\SslCertValidator`

[![Repo](https://img.shields.io/badge/github-gray?logo=github)](https://github.com/zero-to-prod/ssl-cert-validator)
[![GitHub Actions Workflow Status](https://img.shields.io/github/actions/workflow/status/zero-to-prod/ssl-cert-validator/phpunit.yml?label=tests)](https://github.com/zero-to-prod/ssl-cert-validator/actions)
[![Packagist Downloads](https://img.shields.io/packagist/dt/zero-to-prod/ssl-cert-validator?color=blue)](https://packagist.org/packages/zero-to-prod/ssl-cert-validator/stats)
[![Packagist Version](https://img.shields.io/packagist/v/zero-to-prod/ssl-cert-validator?color=f28d1a)](https://packagist.org/packages/zero-to-prod/ssl-cert-validator)
[![GitHub repo size](https://img.shields.io/github/repo-size/zero-to-prod/ssl-cert-validator)](https://github.com/zero-to-prod/ssl-cert-validator)
[![License](https://img.shields.io/packagist/l/zero-to-prod/ssl-cert-validator?color=red)](https://github.com/zero-to-prod/ssl-cert-validator/blob/main/LICENSE.md)

Fetch, validate, and verify SSL certificates.

## Installation

Install the package via Composer:

```bash
composer require zerotoprod/ssl-cert-validator
```

## Usage

```php
use Zerotoprod\SslCertValidator\SslCertificate;

SslCertificate::rawCertificates('https://example.com');
SslCertificate::hostIsValid('https://example.com');
SslCertificate::isExpired('example.com');
SslCertificate::isSelfSigned('example.com');
SslCertificate::isTrustedRoot('example.com', '/path/to/cafile.pem');
```