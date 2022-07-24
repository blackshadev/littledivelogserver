<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Fideloper\Proxy\TrustProxies as Middleware;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Http\Request;

final class TrustProxies extends Middleware
{
    protected $proxies;

    protected $headers =
        Request::HEADER_X_FORWARDED_FOR |
        Request::HEADER_X_FORWARDED_HOST |
        Request::HEADER_X_FORWARDED_PORT |
        Request::HEADER_X_FORWARDED_PROTO |
        Request::HEADER_X_FORWARDED_AWS_ELB;

    public function __construct(Repository $config)
    {
        parent::__construct($config);

        $trustedProxies = env('TRUSTED_PROXIES');
        if (!empty($trustedProxies)) {
            $this->proxies = explode(',', $trustedProxies);
        }
    }
}
