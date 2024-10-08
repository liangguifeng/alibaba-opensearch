<?php

declare(strict_types=1);

namespace AlibabaOpenSearch\Provider;

/**
 * @see https://help.aliyun.com/zh/open-search/industry-algorithm-edition/query-drop-down-suggestions?spm=a2c4g.11186623.0.0.564f224afcrMUR
 */
class SuggestProvider extends AbstractProvider implements ProviderInterface
{
    /**
     * @var string Search URI.
     */
    protected string $uri = '/v3/openapi/apps/%s/suggest/%s/search';

    /**
     * Get Config URI.
     *
     * @return string
     */
    public function getUri()
    {
        return sprintf($this->uri, $this->config->getAppName(), $this->config->getSuggestName());
    }

    /**
     * Execute OpenSearch request.
     *
     * @param array $data
     *
     * @return array|mixed
     */
    public function execute($data)
    {
        return $this->client->get($this->getUri(), $data);
    }
}
