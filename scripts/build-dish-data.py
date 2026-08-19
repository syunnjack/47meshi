"""農林水産省「うちの郷土料理」から、47都道府県の郷土料理を取り出す。

出典: 農林水産省「うちの郷土料理　次世代に伝えたい大切な味」
      https://www.maff.go.jp/j/keikaku/syokubunka/k_ryouri/

取るのは「料理名・都道府県・主な伝承地域・主な使用食材・農水省の該当ページURL」だけ。
由来や作り方の解説文はそのまま持ってこない（引用の範囲を超えるため）。写真も使わない。
各ページからは、料理ごとに農水省のページへリンクを張って出典を示す。

使い方: python fetch_kyodo.py <出力先.json>
"""
import json
import re
import sys
import time
import urllib.parse
import urllib.request
from datetime import date
from html import unescape
from pathlib import Path

BASE = 'https://www.maff.go.jp/j/keikaku/syokubunka/k_ryouri/'
AREA = BASE + 'search_menu/area/{slug}.html'
UA = ('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 '
      '(KHTML, like Gecko) Chrome/131.0 Safari/537.36')
DELAY = 1.5

PREFECTURES = [
    ('北海道', 'hokkaido'), ('青森県', 'aomori'), ('岩手県', 'iwate'), ('宮城県', 'miyagi'),
    ('秋田県', 'akita'), ('山形県', 'yamagata'), ('福島県', 'fukushima'), ('茨城県', 'ibaraki'),
    ('栃木県', 'tochigi'), ('群馬県', 'gunma'), ('埼玉県', 'saitama'), ('千葉県', 'chiba'),
    ('東京都', 'tokyo'), ('神奈川県', 'kanagawa'), ('新潟県', 'niigata'), ('富山県', 'toyama'),
    ('石川県', 'ishikawa'), ('福井県', 'fukui'), ('山梨県', 'yamanashi'), ('長野県', 'nagano'),
    ('岐阜県', 'gifu'), ('静岡県', 'shizuoka'), ('愛知県', 'aichi'), ('三重県', 'mie'),
    ('滋賀県', 'shiga'), ('京都府', 'kyoto'), ('大阪府', 'osaka'), ('兵庫県', 'hyogo'),
    ('奈良県', 'nara'), ('和歌山県', 'wakayama'), ('鳥取県', 'tottori'), ('島根県', 'shimane'),
    ('岡山県', 'okayama'), ('広島県', 'hiroshima'), ('山口県', 'yamaguchi'), ('徳島県', 'tokushima'),
    ('香川県', 'kagawa'), ('愛媛県', 'ehime'), ('高知県', 'kochi'), ('福岡県', 'fukuoka'),
    ('佐賀県', 'saga'), ('長崎県', 'nagasaki'), ('熊本県', 'kumamoto'), ('大分県', 'oita'),
    ('宮崎県', 'miyazaki'), ('鹿児島県', 'kagoshima'), ('沖縄県', 'okinawa'),
]

# 農水省側のファイル名がローマ字の綴り違いになっている県がある。
# （山梨県は yamanashi ではなく yamanasi）
SOURCE_SLUGS = {'yamanashi': 'yamanasi'}

CACHE = Path(__file__).resolve().parent / '.kyodo-cache'


def get(url: str) -> str:
    CACHE.mkdir(exist_ok=True)
    key = re.sub(r'[^A-Za-z0-9]+', '_', url)[-120:]
    path = CACHE / f'{key}.html'

    if path.exists():
        return path.read_text(encoding='utf-8')

    request = urllib.request.Request(url, headers={'User-Agent': UA, 'Accept-Language': 'ja'})
    with urllib.request.urlopen(request, timeout=60) as response:
        html = response.read().decode('utf-8', 'replace')

    path.write_text(html, encoding='utf-8')
    time.sleep(DELAY)
    return html


def text_of(fragment: str) -> str:
    return re.sub(r'\s+', ' ', unescape(re.sub(r'<[^>]+>', ' ', fragment))).strip()


def field(html: str, label: str) -> str | None:
    """「主な伝承地域」のような見出しの直後にある値を取り出す。"""
    index = html.find(label)

    if index == -1:
        return None

    # 見出しの直後から次の見出しまでを見る
    tail = html[index + len(label):index + len(label) + 600]
    stop = min([p for p in (tail.find('主な使用食材'), tail.find('歴史・由来'),
                            tail.find('食習の機会'), tail.find('飲食方法')) if p != -1] or [len(tail)])
    value = text_of(tail[:stop])

    return value[:120] or None


def dishes_in(html: str, area_url: str) -> list[tuple[str, str]]:
    """一覧ページから (料理名, 詳細ページURL) を取り出す。"""
    found = []

    for block in re.findall(r'<li><a class="hover" href="(\.\./menu/[^"]+\.html)">(.*?)</a></li>', html, re.S):
        href, inner = block
        title = re.search(r'<p class="tit">(.*?)</p>', inner, re.S)

        if not title:
            continue

        found.append((text_of(title.group(1)), urllib.parse.urljoin(area_url, href)))

    return found


def main() -> None:
    output = Path(sys.argv[1])
    dishes = []
    seen = set()

    for name, slug in PREFECTURES:
        area_url = AREA.format(slug=SOURCE_SLUGS.get(slug, slug))

        try:
            html = get(area_url)
        except Exception as error:
            print(f'{name} の取得に失敗しました: {error}', flush=True)
            continue

        added = 0

        for dish_name, url in dishes_in(html, area_url):
            if url in seen:
                continue
            seen.add(url)

            region = ingredients = None

            try:
                detail = get(url)
                region = field(detail, '主な伝承地域')
                ingredients = field(detail, '主な使用食材')
            except Exception as error:
                print(f'  {dish_name}: 詳細を取得できませんでした（{error}）', flush=True)

            dishes.append({
                'name': dish_name,
                'area': name,
                'areaSlug': slug,
                'region': region,
                'ingredients': ingredients,
                'sourceUrl': url,
            })
            added += 1

        print(f'{name} {added}件', flush=True)

    output.parent.mkdir(parents=True, exist_ok=True)
    output.write_text(json.dumps({
        'confirmedOn': date.today().isoformat(),
        'sourceLabel': '農林水産省「うちの郷土料理　次世代に伝えたい大切な味」',
        'sourceUrl': BASE,
        'dishes': dishes,
    }, ensure_ascii=False), encoding='utf-8')

    print(f'{len(dishes)}件を書き出しました')


main()
