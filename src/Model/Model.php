<?php

namespace AlibabaOpenSearch\Model;

use AlibabaOpenSearch\Config;
use AlibabaOpenSearch\OpenSearch;

abstract class Model
{
    public OpenSearch $openSearch;

    public function __construct(array $config)
    {
        $this->openSearch = new OpenSearch(new Config($config));
    }
    /**
     * Begin querying the model.
     */
    public static function search()
    {
        return (new static())->newQuery();
    }

    /**
     * Get a new query builder for the model's table.
     */
    public function newQuery()
    {

        return new SearchBuilder($this);
    }

}