<?php

declare(strict_types=1);

namespace AlibabaOpenSearch\Provider;

interface ProviderInterface
{
    /**
     * Get Config URI.
     *
     * @return string
     */
    public function getUri();

    /**
     * Execute OpenSearch request.
     *
     * @param array $data
     *
     * @return array|mixed
     */
    public function execute(array $data);
}
