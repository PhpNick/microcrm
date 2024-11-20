<?php

namespace App\Http\Filters;

class MovementFilter extends QueryFilter {
    public function createdAt($value) {
        return $this->builder->whereDate('created_at', $value);
    }

    public function updatedAt($value) {
        return $this->builder->whereDate('updated_at', $value);
    }

    public function product($value) {
        return $this->builder->whereHas('product', function($query) use ($value) {
            $query->where('id', $value);
        });
    }

    public function warehouse($value) {
        return $this->builder->whereHas('warehouse', function($query) use ($value) {
            $query->where('id', $value);
        });
    }

}
