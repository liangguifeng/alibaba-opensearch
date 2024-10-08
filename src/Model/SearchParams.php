<?php

declare(strict_types=1);

namespace AlibabaOpenSearch\Model;

class SearchParams
{
    public array $query;

    public array $fetchFields;

    public array $sorts;

    public array $configs;

    public function __construct()
    {
        $this->query = [];
        $this->fetchFields = [];
        $this->sorts = [];
        $this->configs[] = [];
    }

    public function build()
    {
        return [
            'query' => $this->buildQuery(),
            'fetch_fields' => $this->buildFetchFields(),
            'sort' => $this->buildSorts(),
            'config' => $this->buildConfigs(),
        ];
    }

    public function buildQuery()
    {
        $query = $this->buildQueryClause();
        $orQuery = $this->buildOrQueryClause();
        $config = $this->buildConfigClause();
        $filter = $this->buildFilterClause();


        $build = sprintf('query=%s%s && config=%s && filter=%s', $query, $orQuery, $config, $filter);

        return $build;
    }

    public function buildQueryClause()
    {
        $buildQueryList = [];

        foreach ($this->query as $type => $condition) {
            if ($type == 'query') {
                foreach ($condition as $key => $value) {
                    $buildQueryList[] = sprintf("%s:'%s'", $key, $value);
                }
            }
        }

        $buildQuery = implode(' AND ', $buildQueryList);

        return $buildQuery;
    }

    public function buildOrQueryClause()
    {
        $buildOrQueryList = [];

        foreach ($this->query as $type => $condition) {
            if ($type == 'orQuery') {
                foreach ($condition as $key => $value) {
                    $buildOrQueryList[] = sprintf("%s:'%s'", $key, $value);
                }
            }
        }

        if (empty($buildOrQueryList)) {
            return '';
        }

        $buildOrQuery = count($buildOrQueryList) > 1 ? ' OR (' . implode(' AND ', $buildOrQueryList) . ')' : ' OR ' . implode(' AND ', $buildOrQueryList);

        return $buildOrQuery;
    }

    public function buildFilterClause()
    {
        $buildFilterList = [];
        $buildOrQueryList = [];
        $buildFilterInList = [];

        foreach ($this->query as $type => $condition) {
            if ($type == 'filter') {
                foreach ($condition as $key => $value) {
                    $buildFilterList[] = sprintf("%s:'%s'", $key, $value);
                }
            }

            if ($type == 'orFilter') {
                foreach ($condition as $key => $value) {
                    $buildOrQueryList[] = sprintf("%s:'%s'", $key, $value);
                }
            }

            if ($type == 'filterIn') {
                foreach ($condition as $key => $value) {
                    $buildFilterInList[] = sprintf("in(%s, '%s')", $key, implode('|', $value));
                }
            }
        }

        $filter = implode(' AND ', $buildFilterList);
        $filterIn = implode(' AND ', $buildFilterInList);
        $filterOr = '';
        if (!empty($buildOrQueryList)) {
            $filterOr = count($buildOrQueryList) > 1 ? ' OR (' . implode(' AND ', $buildOrQueryList) . ')' : ' OR ' . implode(' AND ', $buildOrQueryList);
        }

        return sprintf('');
    }

    public function buildConfigClause()
    {
        $buildConfigsList = [];

        foreach ($this->query as $type => $condition) {
            if ($type == 'configs') {
                if (!key_exists('format', $condition)) {
                    $condition['format'] = 'json';
                }

                foreach ($condition as $key => $value) {
                    $buildConfigsList[] = sprintf("%s:%s", $key, $value);
                }
            }
        }

        $buildConfig = implode(',', $buildConfigsList);

        return $buildConfig;
    }

    public function buildFetchFields()
    {
        return implode(';', $this->fetchFields);
    }

    public function buildSorts()
    {
        $buildSorts = [];

        foreach ($this->sorts as $sort) {
            foreach ($sort as $field => $value) {
                if ($value == SORT_DESC) {
                    $buildSorts[] = '+' . $field;
                } else {
                    $buildSorts[] = '-' . $field;
                }
            }
        }

        return implode(';', $buildSorts);
    }

    public function buildConfigs()
    {
        $buildConfig = [];

        foreach ($this->configs as $config) {
            foreach ($config as $key => $value) {
                $buildConfig[] = sprintf('%s:%s', $key, $value);
            }
        }

        return implode(',', $buildConfig);
    }
}
