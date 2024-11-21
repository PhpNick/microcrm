<?php

namespace App\Http\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

/**
 * Class QueryFilter
 *
 * Родительский класс для фильтров
 *
 * @package App\Http\Filters
 */
class QueryFilter {

    /**
     * @var Builder
     */
    protected $builder;

    /**
     * @var Request
     */
    protected $request;

    /**
     * Конструктор QueryFilter
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Подключение нужного фильтра, в зависимости от параметров в запросе
     *
     * @param Builder $builder
     * @return Builder
     */
    public function apply(Builder $builder) {
        $this->builder = $builder;

        foreach($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }

        return $this->builder;
    }
}
