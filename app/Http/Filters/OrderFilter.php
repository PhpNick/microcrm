<?php

namespace App\Http\Filters;

class OrderFilter extends QueryFilter {
    public function createdAt($value) {
        return $this->builder->whereDate('created_at', $value);
    }

    public function updatedAt($value) {
        return $this->builder->whereDate('updated_at', $value);
    }

    public function completedAt($value) {
        return $this->builder->whereDate('completed_at', $value);
    }

    public function customer($value) {
        return $this->builder->where('customer', 'like', '%' . $value . '%');
    }

    public function warehouse($value) {
        return $this->builder->whereHas('warehouse', function($query) use ($value) {
            $query->where('id', $value);
        });
    }

}
