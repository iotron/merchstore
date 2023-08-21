<?php

namespace Database\Seeders;

use App\Models\Category\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
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
                 //   'desc' => $categoryBag->meta_desc,
                ]
            );
            //  $category->attachments()->attach($this->addAttachment());
            // Inject products

//            if (isset($categoryBag->children)) {
//                foreach ($categoryBag->children as $subCat) {
//                    $subCategory = $category->children()->updateOrCreate([
//                        'name' => $name = $subCat->name,
//                        'url' => Str::slug($name),
//                      //  'desc' => $subCat->meta_desc,
//                    ]);
//                    //    $subCategory->attachments()->attach($this->addAttachment());
//
//                    if (isset($subCat->children)) {
//                        foreach ($subCat->children as $child) {
//                            $cats = $subCategory->children()->updateOrCreate([
//                                'name' => $name = $child->name,
//                                'url' => Str::slug($name),
//                               // 'desc' => $child->meta_desc,
//                            ]);
//
//                            $cats->save();
//                        }
//                    }
//                }
//            }
        }
    }


    protected function getFromStorage(string $path)
    {
        return json_decode(Storage::disk('local')->get($path));
    }



}
