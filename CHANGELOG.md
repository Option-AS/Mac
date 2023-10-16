# Changelog

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/).

This project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
"SHOULD NOT", "RECOMMENDED",  "MAY", and "OPTIONAL" in this document are to be
interpreted as described in [RFC 2119](https://tools.ietf.org/html/rfc2119).

## 0.5.0 2023-10-16
Prepared for publication of git repo
 - Added GNU Lesser General Public License

## 0.4.0 2023-07-12
### Added
 - ::factory($candidate) Accepts various formats; a mac object itself, string, int

## 0.3.0 2023-07-12
### Added
 - Mac::fromHexOrNull(mixed) Same as fromHex, but will return null instead of throwing.
 - Some more safeguards
 ### Changed
 - Require PHP 8.1

### Bug fixes
 - Zero-padding on low integers
 - Precedence on boolean cast

## 0.2.0 - 2023
### Added
 - Added U/L and I/G bit extractors

### Changed
 - Upgraded to PHPUnit 10

## 0.1.0 - First
