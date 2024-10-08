<?php

declare(strict_types=1);

namespace AlibabaOpenSearch;

use JetBrains\PhpStorm\ArrayShape;

class Config
{
    /**
     * @var string Alibaba OpenSearch Endpoint
     */
    protected string $endpoint = 'http://opensearch-cn-shenzhen.aliyuncs.com';

    /**
     * @var string Alibaba OpenSearch Access Key
     */
    protected string $accessKey = '';

    /**
     * @var string Alibaba OpenSearch Access Secret
     */
    protected string $accessSecret = '';

    /**
     * @var string alibaba OpenSearch App Name
     */
    protected string $appName = '';

    /**
     * @var string alibaba OpenSearch Suggest Name
     */
    protected string $suggestName = '';

    /**
     * @var array alibaba OpenSearch Guzzle Client Config
     */
    protected array $guzzleConfig = [
        'headers' => [
            'charset' => 'UTF-8',
        ],
        'http_errors' => false,
        'verify' => false,
    ];

    /**
     * Constructor.
     *
     * @param array $config
     */
    public function __construct(
        #[ArrayShape([
            'endpoint' => 'string',
            'access_key' => 'string',
            'access_secret' => 'string',
            'app_name' => 'string',
            'suggest_name' => 'string',
            'guzzle_config' => 'array',
        ])]
        array $config = []
    ) {
        isset($config['endpoint']) && $this->endpoint = (string) $config['endpoint'];
        isset($config['access_key']) && $this->accessKey = (string) $config['access_key'];
        isset($config['access_secret']) && $this->accessSecret = (string) $config['access_secret'];
        isset($config['app_name']) && $this->appName = (string) $config['app_name'];
        isset($config['suggest_name']) && $this->suggestName = (string) $config['suggest_name'];
        isset($config['guzzle_config']) && $this->guzzleConfig = (array) $config['guzzle_config'];
    }

    /**
     * Get Endpoint.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Get Access Key.
     *
     * @return string
     */
    public function getAccessKey(): string
    {
        return $this->accessKey;
    }

    /**
     * Get Access Secret.
     *
     * @return string
     */
    public function getAccessSecret(): string
    {
        return $this->accessSecret;
    }

    /**
     * Get App Name.
     *
     * @return string
     */
    public function getAppName(): string
    {
        return $this->appName;
    }

    /**
     * Get Suggest Name.
     *
     * @return string
     */
    public function getSuggestName(): string
    {
        return $this->suggestName;
    }

    /**
     * Get Guzzle Client Config.
     *
     * @return array
     */
    public function getGuzzleConfig(): array
    {
        return $this->guzzleConfig;
    }
}
