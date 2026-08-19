<?php

namespace Tests\Feature;

use App\Models\Dish;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DishPagesTest extends TestCase
{
    use RefreshDatabase;

    private function makeDish(array $attributes = []): Dish
    {
        return Dish::create(array_merge([
            'slug' => 'butadon_hokkaido',
            'name' => '豚丼',
            'area' => '北海道',
            'area_slug' => 'hokkaido',
            'region' => '十勝地方',
            'ingredients' => '豚肉、米、ねぎ',
            'source_url' => 'https://www.maff.go.jp/j/keikaku/syokubunka/k_ryouri/search_menu/menu/butadon_hokkaido.html',
            'confirmed_on' => '2026-08-19',
        ], $attributes));
    }

    public function test_トップページに都道府県と件数が出る(): void
    {
        $this->makeDish();

        $this->get('/')
            ->assertOk()
            ->assertSee('北海道')
            ->assertSee('47都道府県の郷土料理');
    }

    public function test_都道府県ページに料理が並ぶ(): void
    {
        $this->makeDish();

        $this->get('/areas/hokkaido')
            ->assertOk()
            ->assertSee('豚丼')
            ->assertSee('十勝地方');
    }

    public function test_掲載の無い都道府県と知らないURLは404になる(): void
    {
        $this->makeDish();

        $this->get('/areas/okinawa')->assertNotFound();
        $this->get('/areas/nowhere')->assertNotFound();
        $this->get('/dishes/nowhere')->assertNotFound();
    }

    public function test_料理ページに出典リンクが出る(): void
    {
        $dish = $this->makeDish();

        $this->get('/dishes/butadon_hokkaido')
            ->assertOk()
            ->assertSee('豚丼')
            ->assertSee($dish->source_url, false)
            ->assertSee('農林水産省');
    }

    public function test_食材ページは同じ食材の料理だけを集める(): void
    {
        $this->makeDish();
        $this->makeDish([
            'slug' => 'imomochi_hokkaido', 'name' => 'いももち',
            'ingredients' => 'じゃがいも、片栗粉',
        ]);

        $this->get('/ingredients/'.urlencode('豚肉'))
            ->assertOk()
            ->assertSee('豚丼')
            ->assertDontSee('いももち');
    }

    public function test_食材ページは部分一致で拾わない(): void
    {
        $this->makeDish(['slug' => 'a', 'name' => 'テスト料理', 'ingredients' => '米粉、砂糖']);

        // 「米」で「米粉」の料理を拾ってしまわないこと
        $this->get('/ingredients/'.urlencode('米'))->assertNotFound();
    }

    public function test_サイトマップに都道府県と料理が載る(): void
    {
        $this->makeDish();

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertSee(route('dishes.area', 'hokkaido'), false)
            ->assertSee(route('dishes.show', 'butadon_hokkaido'), false);
    }

    public function test_robots_txtがサイトマップを案内する(): void
    {
        $this->get('/robots.txt')
            ->assertOk()
            ->assertSee('Sitemap: '.url('/sitemap.xml'), false);
    }
}
