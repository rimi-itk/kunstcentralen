# Kunstcentralen

```sh
docker-compose up --detach
docker-compose exec phpfpm composer install
docker-compose exec phpfpm bin/console doctrine:migrations:migrate --no-interaction
```

```sh
docker-compose exec phpfpm bin/console doctrine:fixtures:load --no-interaction
```

```sh
docker-compose run yarn install
docker-compose run yarn watch
```


```sh
docker-compose exec --env DEFAULT_LOCALE=en phpfpm bin/console translation:update da --force
```
