# docker-laravel-handson

## 参考リンク
### コンテナの設計〜起動まで
- https://qiita.com/ucan-lab/items/56c9dc3cf2e6762672f4

### ログイン機能の実装
- https://qiita.com/ucan-lab/items/bd0d6f6449602072cb87

### EC2へのデプロイ
- https://wingdoor.co.jp/blog/%E3%80%90amazon-linux-2%E3%80%91-laravel%E3%81%8C%E5%8B%95%E3%81%8F%E7%92%B0%E5%A2%83%E3%82%92%E6%A7%8B%E7%AF%89%E3%81%99%E3%82%8B/

### 独自ドメインとEC2の紐づけ 
#### NSレコードの登録
- https://dev.classmethod.jp/articles/mumu-domain-route53/

#### Aレコードの登録
- https://qiita.com/yuichi1992_west/items/e842d8ee50c4afd88775




## コマンド(git cloneした時 or 環境が壊れた時用)

### 1. コンテナに入る
```
docker-compose exec app bash
```

### 2. 依存関係のインストール
```
root@1d494784f8e6:/data# composer install
```

### 3. アセットのビルド

```
root@1d494784f8e6:/data# npm install
root@1d494784f8e6:/data# npm run build 
```

### 4. .envファイルの生成
```
root@1d494784f8e6:/data#　cp .env.example .env
```
  
### 5. アプリケーションキーの生成

```
root@1d494784f8e6:/data# php artisan key:generate

   INFO  Application key set successfully.
```

### 6. ログイン画面にアクセス

http://localhost:8080/login


