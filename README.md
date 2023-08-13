Dockerで環境づくり
https://qiita.com/shir01earn/items/f236c8280bb745dd6fb4

最終的なファイル配置
```
.
├── .env
├── README.md
├── docker
│   ├── nginx
│   │   └── default.conf
│   └── php
│       ├── Dockerfile
│       └── php.ini
├── docker-compose.yml
└── php
    ├── src
    │   ├── proxy_gpt_api.php
    │   └── sse.html
    └── temp
```

.env に OpenAI の API KEY を書く
```
ENV_OPENAI_API_KEY=sk-xxxx・・・
```

下記コマンドで立ち上げて、必要ならコンテナの中に入る
```
docker-compose up -d --build
docker-compose exec php bash
```
