<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    /** 都道府県ページのURLに使うローマ字。 */
    public const AREA_SLUGS = [
        '北海道' => 'hokkaido', '青森県' => 'aomori', '岩手県' => 'iwate', '宮城県' => 'miyagi',
        '秋田県' => 'akita', '山形県' => 'yamagata', '福島県' => 'fukushima', '茨城県' => 'ibaraki',
        '栃木県' => 'tochigi', '群馬県' => 'gunma', '埼玉県' => 'saitama', '千葉県' => 'chiba',
        '東京都' => 'tokyo', '神奈川県' => 'kanagawa', '新潟県' => 'niigata', '富山県' => 'toyama',
        '石川県' => 'ishikawa', '福井県' => 'fukui', '山梨県' => 'yamanashi', '長野県' => 'nagano',
        '岐阜県' => 'gifu', '静岡県' => 'shizuoka', '愛知県' => 'aichi', '三重県' => 'mie',
        '滋賀県' => 'shiga', '京都府' => 'kyoto', '大阪府' => 'osaka', '兵庫県' => 'hyogo',
        '奈良県' => 'nara', '和歌山県' => 'wakayama', '鳥取県' => 'tottori', '島根県' => 'shimane',
        '岡山県' => 'okayama', '広島県' => 'hiroshima', '山口県' => 'yamaguchi', '徳島県' => 'tokushima',
        '香川県' => 'kagawa', '愛媛県' => 'ehime', '高知県' => 'kochi', '福岡県' => 'fukuoka',
        '佐賀県' => 'saga', '長崎県' => 'nagasaki', '熊本県' => 'kumamoto', '大分県' => 'oita',
        '宮崎県' => 'miyazaki', '鹿児島県' => 'kagoshima', '沖縄県' => 'okinawa',
    ];

    /** 地方ごとの並び。トップページの見出しに使う。 */
    public const REGIONS = [
        '北海道・東北' => ['北海道', '青森県', '岩手県', '宮城県', '秋田県', '山形県', '福島県'],
        '関東' => ['茨城県', '栃木県', '群馬県', '埼玉県', '千葉県', '東京都', '神奈川県'],
        '中部' => ['新潟県', '富山県', '石川県', '福井県', '山梨県', '長野県', '岐阜県', '静岡県', '愛知県'],
        '近畿' => ['三重県', '滋賀県', '京都府', '大阪府', '兵庫県', '奈良県', '和歌山県'],
        '中国・四国' => ['鳥取県', '島根県', '岡山県', '広島県', '山口県', '徳島県', '香川県', '愛媛県', '高知県'],
        '九州・沖縄' => ['福岡県', '佐賀県', '長崎県', '熊本県', '大分県', '宮崎県', '鹿児島県', '沖縄県'],
    ];

    public const SOURCE_LABEL = '農林水産省「うちの郷土料理　次世代に伝えたい大切な味」';
    public const SOURCE_URL = 'https://www.maff.go.jp/j/keikaku/syokubunka/k_ryouri/';

    protected $fillable = [
        'slug', 'name', 'area', 'area_slug', 'region', 'ingredients', 'source_url', 'confirmed_on',
    ];

    protected function casts(): array
    {
        return ['confirmed_on' => 'date'];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function areaForSlug(string $slug): ?string
    {
        return array_search($slug, self::AREA_SLUGS, true) ?: null;
    }

    /** 「豚肉、米、ねぎ」を配列にする。 */
    public function getIngredientListAttribute(): array
    {
        if (! $this->ingredients) {
            return [];
        }

        return array_values(array_filter(array_map('trim', preg_split('/[、,]/u', $this->ingredients))));
    }
}
