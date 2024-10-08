<?php

declare(strict_types=1);

namespace AlibabaOpenSearch\Provider;

use AlibabaOpenSearch\Client;
use AlibabaOpenSearch\Config;
use AlibabaOpenSearch\OpenSearch;

abstract class AbstractProvider
{
    protected string $uri;

    public function __construct(protected OpenSearch $app, protected Config $config)
    {
        $this->client = new Client($config);
    }
}
