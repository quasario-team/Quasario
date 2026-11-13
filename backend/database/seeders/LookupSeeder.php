<?php

namespace Database\Seeders;

use App\Models\City;
use App\Models\JobCategory;
use Illuminate\Database\Seeder;

class LookupSeeder extends Seeder
{
    // Demo lists only. The team will give the real lists later.
    public function run(): void
    {
        foreach (['دمشق', 'حلب', 'حمص', 'اللاذقية', 'حماة'] as $name) {
            City::firstOrCreate(['name' => $name]);
        }

        $categories = [
            'صيانة منزلية' => ['كهربائي', 'سبّاك', 'نجّار'],
            'تعليم' => ['مدرّس رياضيات', 'مدرّس لغة إنجليزية', 'مدرّس فيزياء'],
            'تصميم وبرمجة' => ['مطوّر مواقع', 'مصمّم جرافيك', 'مطوّر تطبيقات'],
        ];

        foreach ($categories as $categoryName => $titles) {
            $category = JobCategory::firstOrCreate(['name' => $categoryName]);

            foreach ($titles as $title) {
                $category->jobTitles()->firstOrCreate(['name' => $title]);
            }
        }
    }
}
