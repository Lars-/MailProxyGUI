<?php

namespace LJPc\DoH\Servers;

class CloudFlare extends DoHServer {
	protected string $url = 'https://cloudflare-dns.com/dns-query';
}
