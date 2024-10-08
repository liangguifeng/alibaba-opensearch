<?php

declare(strict_types=1);

namespace AlibabaOpenSearch;

use AlibabaOpenSearch\Exception\InvalidArgumentException;
use AlibabaOpenSearch\Provider\SearchProvider;

/**
 * @property SearchProvider $search
 */
class OpenSearch
{
    protected array $alias = [
        'search' => SearchProvider::class,
    ];

    protected array $providers = [];

    public function __construct(protected Config $config)
    {
    }

    public function __get($name)
    {
        if (!isset($name) || !isset($this->alias[$name])) {
            throw new InvalidArgumentException("{$name} is invalid.");
        }

        if (isset($this->providers[$name])) {
            return $this->providers[$name];
        }

        $class = $this->alias[$name];

        return $this->providers[$name] = new $class($this, $this->config);
    }
}
