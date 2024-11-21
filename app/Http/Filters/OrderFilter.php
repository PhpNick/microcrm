<?php

namespace App\Http\Filters;

/**
 * Class OrderFilter
 *
 * Фильтрация заказов
 *
 * @package App\Http\Filters
 */
class OrderFilter extends QueryFilter {

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
     * Фильтр по дате завершения
     *
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function completedAt($value) {
        return $this->builder->whereDate('completed_at', $value);
    }

    /**
     * Фильтр по покупателю
     *
     * @param string $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function customer($value) {
        return $this->builder->where('customer', 'like', '%' . $value . '%');
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
