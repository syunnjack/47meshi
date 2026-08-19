<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DishController extends Controller
{
    /** トップページ。47都道府県を地方ごとに並べる。 */
    public function index()
    {
        $counts = Dish::query()
            ->selectRaw('area, area_slug, COUNT(*) as total')
            ->groupBy('area', 'area_slug')
            ->pluck('total', 'area');

        $slugs = Dish::query()->pluck('area_slug', 'area');

        return view('dishes.index', [
            'counts' => $counts,
            'slugs' => $slugs,
            'total' => Dish::count(),
            'newest' => Dish::query()->latest('id')->take(12)->get(),
        ]);
    }

    /** 都道府県ページ。 */
    public function area(string $areaSlug)
    {
        $area = Dish::areaForSlug($areaSlug);

        if ($area === null) {
            abort(404);
        }

        $dishes = Dish::where('area_slug', $areaSlug)->orderBy('id')->get();

        if ($dishes->isEmpty()) {
            abort(404);
        }

        return view('dishes.area', [
            'area' => $area,
            'areaSlug' => $areaSlug,
            'dishes' => $dishes,
        ]);
    }

    /** 料理ページ。 */
    public function show(Dish $dish)
    {
        $others = Dish::where('area_slug', $dish->area_slug)
            ->where('id', '!=', $dish->id)
            ->orderBy('id')
            ->take(12)
            ->get();

        return view('dishes.show', compact('dish', 'others'));
    }

    /** 食材から探す。同じ食材を使う料理を都道府県をまたいで並べる。 */
    public function ingredient(string $name)
    {
        $name = trim($name);

        $dishes = Dish::where('ingredients', 'like', '%'.$name.'%')
            ->orderBy('area_slug')
            ->orderBy('id')
            ->get()
            // 「米」で「米粉」まで拾わないよう、区切りで分けた上で一致を見る
            ->filter(fn (Dish $dish) => in_array($name, $dish->ingredient_list, true))
            ->values();

        if ($dishes->isEmpty()) {
            abort(404);
        }

        return view('dishes.ingredient', compact('name', 'dishes'));
    }

    public function sitemap()
    {
        $xml = Cache::remember('sitemap-xml', now()->addHour(), function () {
            $dishes = Dish::select('slug', 'area_slug', 'updated_at')->get();

            return view('sitemap', [
                'dishes' => $dishes,
                'areaSlugs' => $dishes->pluck('area_slug')->unique()->values(),
            ])->render();
        });

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }
}
