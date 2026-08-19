<?php

namespace Database\Seeders;

use App\Models\Dish;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use RuntimeException;

class DishSeeder extends Seeder
{
    /**
     * 農林水産省「うちの郷土料理」から取り出した郷土料理を取り込む。
     *
     * データは scripts/build-dish-data.py が database/data/kyodo-ryori.json に書き出す。
     * 出典は各ページに表示する。元データに無いことは補わない。
     */
    private const CHUNK = 50;

    public function run(): void
    {
        $path = database_path('data/kyodo-ryori.json');

        if (! File::exists($path)) {
            throw new RuntimeException('database/data/kyodo-ryori.json が見つかりません。');
        }

        $payload = json_decode(File::get($path), true, 512, JSON_THROW_ON_ERROR);
        $dishes = $payload['dishes'] ?? [];

        if ($dishes === []) {
            throw new RuntimeException('料理データが空です。');
        }

        $now = now();
        $written = 0;

        foreach (array_chunk($dishes, self::CHUNK) as $chunk) {
            $rows = [];

            foreach ($chunk as $dish) {
                $rows[] = [
                    'slug' => $dish['slug'],
                    'name' => $dish['name'],
                    'area' => $dish['area'],
                    'area_slug' => $dish['areaSlug'],
                    'region' => $dish['region'],
                    'ingredients' => $dish['ingredients'],
                    'source_url' => $dish['sourceUrl'],
                    'confirmed_on' => $payload['confirmedOn'] ?? null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            DB::table('dishes')->upsert($rows, ['slug'], [
                'name', 'area', 'area_slug', 'region', 'ingredients', 'source_url', 'confirmed_on', 'updated_at',
            ]);

            $written += count($rows);
        }

        $this->command?->info(number_format($written).'件を取り込みました（掲載中 '
            .number_format(Dish::count()).'件）。');
    }
}
