### Подготовка
Надо скопировать базовый .env и сгенерить ключ
```shell
cp .env.example .env
php artisan key:generate
```

Прописать редис в .env
```yaml
QUEUE_CONNECTION=redis
CACHE_STORE=redis
MEMCACHED_HOST=redis
SESSION_DRIVER=redis
REDIS_CLIENT=phpredis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
```

### Запуск
```shell
docker-compose up -d
```
### URI
```shell
http://localhost:8060/appTopCategory?date=2025-03-25
```
