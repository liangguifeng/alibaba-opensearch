<?php

declare(strict_types=1);

namespace AlibabaOpenSearch;

use Hyperf\Contract\ConfigInterface;
use Psr\Container\ContainerInterface;

class OpenSearchFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get(ConfigInterface::class)->get('alibaba-opensearch', []);

        return new OpenSearch(new Config([
            'endpoint' => $config['endpoint'],
            'access_key' => $config['access_key'],
            'access_secret' => $config['access_secret'],
            'app_name' => $config['app_name'],
            'suggest_name' => $config['suggest_name'],
            'guzzle_config' => $config['guzzle_config'],
        ]));
    }
}
