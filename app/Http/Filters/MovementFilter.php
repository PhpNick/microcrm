<?php

namespace App\Http\Filters;

/**
 * Class MovementFilter
 *
 * Фильтрация движения по складам
 *
 * @package App\Http\Filters
 */
class MovementFilter extends QueryFilter {

    /**
     * Фильтр по дате создания
     *
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function createdAt($value) {
        return $this->builder->whereDate('created_at', $value);
    }

    /**
     * Фильтр по дате обновления
     *
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function updatedAt($value) {
        return $this->builder->whereDate('updated_at', $value);
    }

    /**
     * Фильтр по товару
     *
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function product($value) {
        return $this->builder->whereHas('product', function($query) use ($value) {
            $query->where('id', $value);
        });
    }

    /**
     * Фильтр по складу
     *
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function warehouse($value) {
        return $this->builder->whereHas('warehouse', function($query) use ($value) {
            $query->where('id', $value);
        });
    }

}
