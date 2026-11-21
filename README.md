# Язык программирования PHP — практические задания

| Папка | Задание |
|---|---|
| `01-deaf-grandma/` | «Глухая бабушка»: `grandma.php` — базовая версия, `grandma_v2.php` — выход после трёх «ПОКА!» подряд |
| `02-captcha/` | CAPTCHA: картинка генерируется GD, строка хранится в сессии |
| `03-eshop/` | Книжный интернет-магазин: MySQL, хранимые процедуры, корзина в cookie, админка |

## Запуск

Требуется Docker.

```bash
docker compose up -d --build
```

- CAPTCHA: http://localhost:8080
- Магазин: http://localhost:8081 (админка `/admin`, демо-вход `admin` / `admin`)

«Глухая бабушка»:

```bash
docker compose run --rm cli php 01-deaf-grandma/grandma.php
docker compose run --rm cli php 01-deaf-grandma/grandma_v2.php
```

Остановка: `docker compose down` (с удалением БД: `docker compose down -v`).

## 02-captcha

- `index.php` — форма и картинка `captcha.php`.
- `captcha.php` — 5–6 случайных символов, размер 18–30 пт, шаг 40 пт, фон `noise.jpg`. Строка сохраняется в `$_SESSION['captcha']`.
- `check.php` — сравнение без учёта регистра, ответ «Корректно» / «Не корректно». Строка действует на одну попытку.
- Шрифт DejaVu Sans Bold лежит в `fonts/` (вместо `c:\windows\fonts`), лицензия — `fonts/DejaVu-LICENSE.txt`.

Требования к хостингу: PHP 8.1+, расширение GD с FreeType и JPEG.

Онлайн-версия: https://php-captcha.onrender.com/

Размещение на Render (бесплатно): `render.yaml` описывает сервис, образ собирается из `docker/captcha/Dockerfile`. В панели Render: New → Blueprint → выбрать этот репозиторий. На бесплатном тарифе сервис засыпает после 15 минут без запросов, первое открытие занимает около минуты.

## 03-eshop

Структура — по заготовке `base.zip`.

- `core/eshop.sql` — таблицы `catalog`, `orders`, `ordered_items`, `admins`, 9 хранимых процедур, демо-данные.
- `core/Eshop.class.php` — работа с БД через PDO и процедуры, корзина, заказы, пользователи.
- `core/Book`, `Basket`, `Order`, `User` — классы предметной области.
- `core/init.php` — автозагрузка, подключение к БД (`DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`), сессия, корзина, закрытие `/admin` без входа.
- `.htaccess` — все запросы идут в `index.php`; папки `core/` и `app/` закрыты от прямого доступа.

Корзина хранится в cookie `eshop` в JSON: `{"order-id": "567fhgfhr6", "2": 1, "15": 2}`.

Развёртывание без Docker: импортировать `core/eshop.sql` в MySQL 8, задать переменные окружения БД или значения в `core/init.php`, корень сайта — папка `03-eshop`, нужен `mod_rewrite`. После развёртывания сменить пароль демо-администратора.
