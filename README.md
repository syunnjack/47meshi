# 47めし（47meshi.net）

47都道府県の郷土料理を、都道府県と食材から探せるサイト。

## データの出所

農林水産省「うちの郷土料理　次世代に伝えたい大切な味」
https://www.maff.go.jp/j/keikaku/syokubunka/k_ryouri/

載せているのは **料理名・都道府県・主な伝承地域・主な使用食材** と、
農林水産省の該当ページへのリンクだけ。由来や作り方の解説文は転載せず、
写真も使わない。

## データの更新

```
python scripts/build-dish-data.py database/data/kyodo-ryori.json
php artisan db:seed --class=DishSeeder
```

`scripts/.kyodo-cache/` に取得済みのHTMLが残るので、再実行しても
農林水産省のサーバへは必要な分しか取りに行かない。

## 構成

| URL | 内容 |
|---|---|
| `/` | 47都道府県の一覧（地方ごと） |
| `/areas/{ローマ字}` | 都道府県ごとの郷土料理一覧 |
| `/dishes/{slug}` | 料理ページ（伝承地域・使用食材・出典リンク） |
| `/ingredients/{食材名}` | その食材を使う郷土料理（都道府県をまたぐ） |
| `/sitemap.xml` | サイトマップ |

## デプロイ

main へ push すると GitHub Actions が Xserver へ rsync し、
migrate と DishSeeder を実行する。必要なシークレットは
`SSH_HOST` `SSH_USERNAME` `SSH_PRIVATE_KEY` `APP_KEY`、
任意で `GA_MEASUREMENT_ID` `GOOGLE_SITE_VERIFICATION`。
