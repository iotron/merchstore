<?php

namespace App\Helpers\Promotion\Sales;

use App\Helpers\Promotion\PromotionManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class SaleHelper extends PromotionManager
{

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
     * @param  string  $operator_type
     * @return array
     */
    protected function getOperator(string $operator_type): array
    {
        return match ($operator_type) {
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
                'label' => trans(Str::ucfirst($attr->display_name)),
                'options' => $attr->options->pluck('display_name', 'id')->toArray(),
            ];
        }

        return $attrBag;
    }


}
