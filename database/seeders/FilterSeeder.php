<?php

namespace Database\Seeders;

use App\Models\Filter\Filter;
use App\Models\Filter\FilterGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $groupBag = $this->getFromStorage('data/attribute-group.json');
        $attributeBag = $this->getFromStorage('data/attribute.json');
        $finalAttributes = new Collection();
        // Fetch Record From AttributeBag
        foreach ($attributeBag as $key => $data) {
            //Create Attribute
            $filter = Filter::updateOrCreate([
                'display_name' => $display = $data->display_name,
                'code' => Str::slug($display),
                'type' => $data->type,
                'desc' => $data->desc,
                'is_configurable' => $data->is_configurable,
                'validation' => $data->validation,
                'is_required' => $data->required,
                'is_visible_on_front' => $data->is_visible,
                'is_user_defined' => false,
            ]);
            $finalAttributes->push($filter);
            // Create Options
            foreach ($data->options as $value) {
                $filter->options()->updateOrCreate([
                    'display_name' => $display = $value->display_name,
                    'code' => Str::slug($display),
                    'swatch_value' => $value->swatch_type,
                ]);
            }
        }
        foreach ($groupBag as $key => $data) {
            //Create Group
            $filterGroup = FilterGroup::updateOrCreate([
                'admin_name' => $data->name,
                'type' => $data->type,
                'code' => $data->code,
                'position' => $key,
            ]);
            $filters = $finalAttributes->whereIn('code', $data->attributes)->pluck('id');
//            // Attach attribute
            $filterGroup->filters()->attach($filters);
        }

    }







    protected function getFromStorage(string $path)
    {
        return json_decode(Storage::disk('local')->get($path));
    }




}
