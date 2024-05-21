<?php

namespace Database\Seeders;

use App\Models\Category\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allCategories = $this->getFromStorage('data/categories.json');

        foreach ($allCategories as $categoryBag) {
            $category = Category::updateOrCreate([
                'name' => $name = $categoryBag->name,
                'url' => Str::slug($name),
            ]
            );
            //  $category->attachments()->attach($this->addAttachment());
            // Inject products

            if (isset($categoryBag->children)) {
                foreach ($categoryBag->children as $subCat) {
                    $subCategory = $category->children()->updateOrCreate([
                        'name' => $name = $subCat->name,
                        'url' => Str::slug($name),
                    ]);
                    //    $subCategory->attachments()->attach($this->addAttachment());

                    if (isset($subCat->children)) {
                        foreach ($subCat->children as $child) {
                            $cats = $subCategory->children()->updateOrCreate([
                                'name' => $name = $child->name,
                                'url' => Str::slug($name),
                            ]);

                        }
                    }
                }
            }
        }
    }

    protected function getFromStorage(string $path)
    {
        return json_decode(Storage::disk('local')->get($path));
    }
}
