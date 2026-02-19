# Сервис коротких ссылок + QR (Yii2 Basic)

Документ описывает запуск и проверку тестового задания на стеке `Yii2 + MySQL/MariaDB + jQuery + Bootstrap` без использования API сторонних серверов.

## 1. Требования

- Docker и Docker Compose (рекомендуемый путь).
- Либо локально: PHP 8.1+ (или 7.4+), Composer, MySQL/MariaDB.
- Доступ к порту приложения (по умолчанию `8080`) и БД (`3306`).

## 1.1. Docker-упаковка (готово)

Проект уже упакован в Docker:

- `Dockerfile`
- `docker-compose.yml`
- `docker/entrypoint.sh`
- `docker/apache-vhost.conf`

## 2. Что реализовано

- Главная страница с формой (`input + OK`).
- Ajax-обработка без перезагрузки страницы.
- Валидация URL (только `http/https` + формат URL).
- Проверка доступности URL (серверный HTTP-запрос, без сторонних API).
- Генерация короткой ссылки и QR-кода.
- Отдельный контроллер редиректа по короткому коду.
- Логирование внешнего IP и user-agent при каждом переходе.
- Счетчик переходов (`visits_count`).

## 3. Структура БД

Применяются миграции:

- `m260218_000001_create_short_url_table`
  - `id`
  - `original_url`
  - `short_code` (unique)
  - `visits_count`
  - `created_at`
  - `updated_at`
- `m260218_000002_create_short_url_visit_log_table`
  - `id`
  - `short_url_id` (FK -> short_url.id)
  - `visitor_ip`
  - `user_agent`
  - `visited_at`

## 4. Запуск (рекомендуемый, через Docker Compose)

В корне проекта выполнить:

```bash
docker compose up --build -d
```

Что произойдет:

- поднимется `mysql:8` (с БД `yii2_shortener`);
- соберется контейнер приложения из `Dockerfile`;
- при старте приложения автоматически:
  - установятся зависимости (если `vendor` отсутствует);
  - выполнятся миграции (`RUN_MIGRATIONS=1`);
  - запустится Apache с `DocumentRoot=/var/www/html/web`.

Открыть в браузере: `http://localhost:8080`

### Полезные команды

```bash
# Логи приложения
docker compose logs -f app

# Логи БД
docker compose logs -f db

# Остановить
docker compose down

# Остановить и удалить volume БД
docker compose down -v
```

### Если MySQL уходит в Restarting

Причина обычно в несовместимости параметров между версиями `mysql:8.x` и старым volume.

Исправление:

```bash
docker compose down -v
docker image rm mysql:8 || true
docker compose up --build -d
```

### Если `app` завис на `Waiting for MySQL at db:3306...`

Обычно это означает, что пользователь БД не может подключиться (даже если MySQL уже поднялся).

Сделайте чистый перезапуск после обновления `docker-compose.yml`:

```bash
docker compose down -v
docker compose up --build -d
docker compose logs -f app
```

## 5. Запуск без Docker (альтернатива)

1. Установить зависимости: `composer install`
2. Создать БД `yii2_shortener`.
3. Задать переменные окружения:
   - `DB_HOST`
   - `DB_PORT`
   - `DB_NAME`
   - `DB_USER`
   - `DB_PASSWORD`
4. Выполнить миграции: `php yii migrate`
5. Запустить сервер: `php yii serve --docroot=@app/web --port=8080`

## 6. Проверка сценариев задания

1. Открыть главную страницу.
2. Вставить валидный URL и нажать `OK`.
3. Убедиться, что без перезагрузки появились:
   - короткая ссылка;
   - QR-код.
4. Проверить невалидный или недоступный URL:
   - выводится ошибка `Данный URL не доступен` (или сообщение валидации формата URL).
5. Перейти по короткой ссылке:
   - происходит редирект на исходный URL.
6. Проверить БД:
   - в `short_url` увеличивается `visits_count`;
   - в `short_url_visit_log` добавляется запись с IP/UA.

## 7. Основные маршруты

- Главная страница: `/`
- Ajax создание короткой ссылки: `/api/shorten`
- Редирект по коду: `/r/<code>`

## 8. Публикация внешней ссылки на документ

Этот файл (`docs/DEPLOY_AND_RUN.md`) можно опубликовать в Google Docs/Notion/Gist и приложить публичную ссылку как ссылку на документ.
