# Pure PHP DoH Client

This library finally makes it easy to query DNS records in PHP without any third party extensions.

<a href="https://www.buymeacoffee.com/Lars-" target="_blank"><img src="https://cdn.buymeacoffee.com/buttons/v2/default-orange.png" alt="Buy Me A Coffee" height="60" style="height: 60px !important;width: 217px !important;" ></a>

## Features

- Automatically query multiple DoH servers (load balancing and backup when one of the servers is down)
- Prefilled with CloudFlare, Google, Quad9 and AdGuard DoH servers
- Easy to expand servers
- Easy to expand resource records
- No `dig` required, everything is pure PHP
- Everything is JSON Serializable

## Resource records

The following resource records are available:

- A
- AAAA
- CAA
- CNAME
- DNAME
- DNSKEY
- DS
- KEY
- LOC
- MX
- NS
- NSEC
- PTR
- RRSIG
- SOA
- SPF
- SRV
- TXT
- URI

## Installation & loading

LJPc Pure PHP DoH Client is available on [Packagist](https://packagist.org/packages/ljpc/pure-php-doh-client) (using semantic versioning), and installation via [Composer](https://getcomposer.org) is the recommended way to install this library.

Just run:

```sh
composer require ljpc/pure-php-doh-client
```

Or add this line to your `composer.json` file:

```json
"ljpc/pure-php-doh-client": "^1.0"
```

## Structure

- `\LJPc\DoH\DNS::query` always returns a `\LJPc\DoH\DNSQueryResult` which contains the following fields:
  - used server (`->getServer()`) [string]
  - answers (`->getAnswers()`) [array of \LJPc\DoH\DNSRecord]
  - authorityRecords (`->getAuthorityRecords()`) [array of \LJPc\DoH\DNSRecord]
  - additionalRecords (`->getAdditionalRecords()`) [array of \LJPc\DoH\DNSRecord]
- `\LJPc\DoH\DNSRecord` always has the following fields:
  - domainname [string]
  - ttl [int] (according to the queried server)
  - type [string] (e.g. A or MX)
  - extras [array] (e.g. the priority in an MX record)
  - value [string]

## Example

Get the A records for cloudflare.com:

```php
<?php
use LJPc\DoH\DNS;
use LJPc\DoH\DNSType;

require __DIR__ . '/vendor/autoload.php';

$result  = DNS::query( 'cloudflare.com', DNSType::A() );
$answers = $result->getAnswers();
foreach ( $answers as $answer ) {
	echo $answer->value . "\n";
}
```

Get the PTR for 142.250.185.174:

```php
<?php
use LJPc\DoH\DNS;
use LJPc\DoH\DNSType;

require __DIR__ . '/vendor/autoload.php';

$result  = DNS::query( '142.250.185.174', DNSType::PTR() );
$answers = $result->getAnswers();
foreach ( $answers as $answer ) {
	echo $answer->value . "\n";
}
```

Get the MX records for gmail.com:

```php
<?php
use LJPc\DoH\DNS;
use LJPc\DoH\DNSType;

require __DIR__ . '/vendor/autoload.php';

$result  = DNS::query( 'gmail.com', DNSType::MX() );
$answers = $result->getAnswers();
foreach ( $answers as $answer ) {
	echo '(' . $answer->extras['priority'] . ') ' . $answer->value . "\n";
}
```

Use a specific DoH server:

```php
<?php
use LJPc\DoH\DNS;
use LJPc\DoH\DNSType;
use LJPc\DoH\Servers\Quad9;

require __DIR__ . '/vendor/autoload.php';

$result  = DNS::query( 'google.com', DNSType::AAAA(), Quad9::class );
$answers = $result->getAnswers();
echo "Used server: " . $result->getServer() . "\n";
foreach ( $answers as $answer ) {
	echo $answer->value . "\n";
}
```

## License

This software is distributed under the [GPL 3.0](http://www.gnu.org/licenses/gpl-3.0.html) license, along with the [GPL Cooperation Commitment](https://gplcc.github.io/gplcc/). Please
read [LICENSE](https://github.com/LJPc-solutions/Pure-PHP-DoH-Client/blob/master/LICENSE.md) for information on the software availability and distribution.

## Inspiration

This package is inspired by the following packages:
- https://github.com/dcid/doh-php-client
- https://github.com/mikepultz/netdns2
- http://www.purplepixie.org/phpdns/

## Custom software

Interested in a library or anything else? Please let us know via [info@ljpc.nl](mailto:info@ljpc.nl?subject=Pure%20PHP%20DoH%20Client) or [www.ljpc.solutions](https://ljpc.solutions).

## Donations

This module took us a lot of time, but we decided to make it open source anyway. If we helped you or your business, please consider donating.
[Click here](https://www.buymeacoffee.com/Lars-) to donate.
