<?php

declare(strict_types=1);

namespace AlibabaOpenSearch\Model;

class SearchBuilder
{
    private $searchParams;

    public function __construct(protected Model $model)
    {
        $this->searchParams = new SearchParams();
    }

    public function query($column, $value = null)
    {
        $this->searchParams->query['query'][$column] = $value;

        return $this;
    }

    public function orQuery($column, $value = null)
    {
        $this->searchParams->query['orQuery'][$column] = $value;

        return $this;
    }

    public function filter($column, $value = null)
    {
        $this->searchParams->query['filter'][$column] = $value;

        return $this;
    }

    public function orFilter($column, $value = null)
    {
        $this->searchParams->query['orFilter'][$column] = $value;

        return $this;
    }

    public function filterIn($column, $value = null)
    {
        $this->searchParams->query['filterIn'][$column] = $value;

        return $this;
    }

    public function select($columns = [])
    {
        if (is_array($columns)) {
            $this->searchParams->fetchFields = $columns;
        }

        if (is_string($columns)) {
            $this->searchParams->fetchFields[] = $columns;
        }

        return $this;
    }

    public function orderBy($column, $direction = SORT_DESC)
    {
        $this->searchParams->sorts[] = [$column => $direction];

        return $this;
    }

    public function format($format = 'json')
    {
        $this->searchParams->query['configs']['format'] = $format;

        return $this;
    }

    public function limit($limit)
    {
        $this->searchParams->query['configs']['hit'] = $limit;

        return $this;
    }

    public function offset($offset)
    {
        $this->searchParams->query['configs']['start'] = $offset;

        return $this;
    }

    public function get()
    {
        return $this->model->openSearch->search->execute($this->searchParams->build());
    }
}
