<?php

namespace Database\Seeders;

use App\Models\Attribute\Attribute;
use App\Models\Attribute\AttributeGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class AttributeSeeder extends Seeder
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
            $attribute = Attribute::updateOrCreate([
                'code' => $data->code,
                'display_name' => $data->admin_name,
                'type' => $data->type,
                'desc' => $data->desc,
                'is_configurable' => $data->is_configurable,
                'validation' => $data->validation,
                'is_required' => $data->required,
                'is_visible_on_front' => $data->is_visible,
                'is_user_defined' => false,
            ]);
            $finalAttributes->push($attribute);
            // Create Options
            foreach ($data->options as $value) {
                $attribute->options()->updateOrCreate([
                    'admin_name' => $value->admin_name,
                    'swatch_value' => $value->swatch_type,
                ]);
            }
        }
        foreach ($groupBag as $key => $data) {
            //Create Group
            $attributeGroup = AttributeGroup::updateOrCreate([
                'admin_name' => $data->name,
                'type' => $data->type,
                'code' => $data->code,
                'position' => $key,
            ]);
            $attributes = $finalAttributes->whereIn('code', $data->attributes)->pluck('id');
            // Attach attribute
            $attributeGroup->attributes()->attach($attributes);
        }
    }











    protected function getFromStorage(string $path)
    {
        return json_decode(Storage::disk('local')->get($path));
    }


}
