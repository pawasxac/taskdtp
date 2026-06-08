<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait SearchableFilterable
{
    /**
     * Apply search filters to query
     * 
     * @param Builder $query
     * @param string $search
     * @param array $searchFields
     * @return Builder
     */
    public function applySearch(Builder $query, ?string $search, array $searchFields = []): Builder
    {
        if (!$search || empty($searchFields)) {
            return $query;
        }

        return $query->where(function ($q) use ($search, $searchFields) {
            foreach ($searchFields as $field) {
                $q->orWhere($field, 'like', '%' . $search . '%');
            }
        });
    }

    /**
     * Apply filters to query
     * 
     * @param Builder $query
     * @param array $filters
     * @return Builder
     */
    public function applyFilters(Builder $query, array $filters = []): Builder
    {
        foreach ($filters as $field => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            // Handle range filters (e.g., price_min, price_max)
            if (str_contains($field, '_min')) {
                $baseField = str_replace('_min', '', $field);
                $query->where($baseField, '>=', $value);
            } elseif (str_contains($field, '_max')) {
                $baseField = str_replace('_max', '', $field);
                $query->where($baseField, '<=', $value);
            } 
            // Handle array filters (e.g., status in ['active', 'pending'])
            elseif (is_array($value)) {
                $query->whereIn($field, $value);
            }
            // Handle boolean/enum
            elseif ($field === 'is_active' || $field === 'is_verified' || $field === 'status') {
                $query->where($field, $value);
            }
            // Standard equality
            else {
                $query->where($field, $value);
            }
        }

        return $query;
    }

    /**
     * Apply sorting to query
     * 
     * @param Builder $query
     * @param string|null $sortBy
     * @param string $sortOrder
     * @return Builder
     */
    public function applySorting(Builder $query, ?string $sortBy = null, string $sortOrder = 'asc'): Builder
    {
        if (!$sortBy) {
            return $query->orderBy('id', 'desc');
        }

        // Security: only allow specific fields
        $allowedFields = $this->getSortableFields();
        
        if (in_array($sortBy, $allowedFields)) {
            $sortOrder = strtolower($sortOrder) === 'desc' ? 'desc' : 'asc';
            return $query->orderBy($sortBy, $sortOrder);
        }

        return $query->orderBy('id', 'desc');
    }

    /**
     * Get sortable fields for this model
     * Override this in your controller or trait
     */
    protected function getSortableFields(): array
    {
        return ['id', 'created_at', 'updated_at', 'name', 'rating'];
    }
}
