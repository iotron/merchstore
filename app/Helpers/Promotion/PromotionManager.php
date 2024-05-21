<?php

namespace App\Helpers\Promotion;

use App\Models\Category\Category;
use App\Models\Filter\Filter;
use App\Models\Filter\FilterGroup;
use Illuminate\Support\Collection;

abstract class PromotionManager
{
    protected array $category;

    protected array|\Illuminate\Database\Eloquent\Collection $attribute;

    protected bool $showOperators = false;

    protected $attributeGroup;

    public function __construct()
    {
        $this->category = Category::with('children', 'parent')->where('status', true)->pluck('name', 'id')->toArray();
        $this->attributeGroup = FilterGroup::where('type', 'static')->pluck('admin_name', 'id')->toArray();
        $this->attribute = Filter::with('options')->where('is_filterable', '!=', true)
            ->where('type', '!=', 'richtext')
            ->where('type', '!=', 'textarea')
            ->where('type', '!=', 'boolean')
            ->where('type', '!=', 'integer')
            ->get();
    }

    abstract public function getCondition(): array|Collection;

    abstract protected function getOperator(string $operator_type): array;
}
