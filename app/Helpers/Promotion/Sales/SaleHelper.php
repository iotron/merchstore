<?php

namespace App\Helpers\Promotion\Sales;

use App\Models\Attribute\Attribute;
use App\Models\Attribute\AttributeGroup;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SaleHelper
{


    private array $category;

    private array|\Illuminate\Database\Eloquent\Collection $attribute;

    private bool $showOperators = false;

    private $attributeGroup;

    public function __construct()
    {
        $this->category = Category::with('children', 'parent')->where('status', true)->pluck('name', 'id')->toArray();
        $this->attributeGroup = AttributeGroup::where('type', 'static')->pluck('admin_name', 'id')->toArray();
        $this->attribute = Attribute::with('options')->where('is_filterable', '!=', true)
            ->where('type', '!=', 'richtext')
            ->where('type', '!=', 'textarea')
            ->where('type', '!=', 'boolean')
            ->where('type', '!=', 'integer')
            ->get();
    }

    /**
     * @param  string  $type
     * @return array
     */
    private function getOperator(string $type): array
    {
        return match ($type) {
            'text' => [
                '=' => 'Contain',
                '!=' => 'Not Contain',
            ],
            'number' => [
                '=' => 'Equal With',
                '>' => 'Greater than',
                '<' => 'Less than',
                '!=' => 'Not Equal',
            ],
            'select', 'multiselect' => [
                '=' => 'Equal With',
                '!=' => 'Not Equal',
            ],
            default => [],
        };
    }

    /**
     * @return Collection
     */
    public function getCondition(): Collection
    {
        $collection = collect([[
            'key' => 'product',
            'label' => trans('catalog-rules.product-attribute'),
            'children' => $this->getChildren(),
        ]]);
        $conditions = collect();
        $conditions = $collection->map(function ($item, $key) use ($conditions) {
            return $conditions->merge($item['children']);
        });

        return $conditions[0];
    }

    /**
     * @return Collection
     */
    private function getChildren(): Collection
    {
        $result = collect([
            [
                'key' => 'product|category_id',
                'type' => 'multiselect',
                'operator' => $this->getOperator('multiselect'),
                'label' => trans('Categories'),
                'options' => $this->category,
            ],
        ]);

        return $result->merge($this->getStaticAttributes());
    }

    /**
     * @return array
     */
    private function getStaticAttributes(): array
    {
        $attrBag = [];
        $allAttribute = $this->attribute;
        foreach ($allAttribute as $attr) {
            $key = 'attribute|'.$attr->code;
            $attrBag[] = [
                'key' => Str::lower($key),
                'type' => $attr->type,
                'operator' => $this->getOperator(Str::lower($attr->type)),
                'label' => trans(Str::ucfirst($attr->admin_name)),
                'options' => $attr->options->pluck('admin_name', 'id')->toArray(),
            ];
        }

        return $attrBag;
    }


}
