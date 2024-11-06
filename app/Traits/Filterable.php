<?php

namespace App\Traits;

trait Filterable
{
    public function scopeFilter($builder, $filters = [])
    {
        if (empty($filters)) {
            return $builder;
        }

        $table = $this->getTable();
        $fillable = $this->fillable;

        foreach ($filters as $field => $value) {
            if ($this->shouldFilterFieldAsBool($field, $value)) {
                $builder->where($field, (bool) $value);
            } elseif ($this->shouldFilterField($field, $value, $fillable)) {
                if (in_array($field, $this->likeFilterFields)) {
                    $builder->where($table.'.'.$field, 'LIKE', "%$value%");
                } elseif (is_array($value)) {
                    $builder->whereIn($field, $value);
                } else {
                    $builder->where($field, $value);
                }
            }
        }

        return $builder;
    }

    private function shouldFilterFieldAsBool($field, $value)
    {
        return in_array($field, $this->boolFilterFields) && $value !== null;
    }

    private function shouldFilterField($field, $value, $fillable)
    {
        return in_array($field, $fillable) && $value !== null;
    }
}
