<?php

namespace Database\Seeders;

use App\Models\Category\Theme;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class ThemeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allThemes = json_decode(Storage::disk('local')->get('data/theme.json'));

        foreach ($allThemes as $item){

            // create parent theme
            $theme = Theme::updateOrCreate([
                'name' => $item->name,
                'url' => $item->url
            ]);

            // create children themes
            if(!empty($item->children)){
                foreach($item->children as $child){
                    $theme->children()->updateOrCreate([
                        'name' => $child->name,
                        'url' => $child->url
                    ]);
                }
            }
        }



    }

}
