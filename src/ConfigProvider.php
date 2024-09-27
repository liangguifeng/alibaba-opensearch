<?php

declare(strict_types=1);

namespace AlibabaOpenSearch;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands' => [
            ],
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for Alibaba Cloud OpenSearch service.',
                    'source' => __DIR__ . '/../publish/alibaba-opensearch.php',
                    'destination' => BASE_PATH . '/config/autoload/aliyun-opensearch.php',
                ],
            ],
        ];
    }
}
