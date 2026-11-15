# API «Спортивна ліга» (варіант 20)

Єдина точка входу: `api.php`. Ресурс: `teams` (`id`, `name`, `city`, `points`).

Усі відповіді мають однакову структуру:

- успіх: `{"success": true, "data": ...}`
- помилка: `{"success": false, "error": "опис"}`

## Підтримувані запити

| Метод | Запит | Параметри | Успіх | Помилки |
|-------|-------|-----------|-------|---------|
| GET | `/api.php?resource=teams` | — | `200`, масив команд | — |
| GET | `/api.php?resource=teams&id=N` | `id` — додатне ціле | `200`, одна команда | `400` (некоректний id), `404` (команди немає) |
| POST | `/api.php?resource=teams` | `name`*, `city`*, `points` (≥ 0, за замовчуванням 0) | `201`, створена команда | `400` (поле відсутнє або некоректне) |
| POST | `/api.php?resource=teams&action=addPoints` | `id`*, `result`* (`win` = 3, `draw` = 1, `loss` = 0) | `200`, оновлена команда + поле `added` | `400`, `404` (команди немає) |
| інший метод | `/api.php?resource=teams` | — | — | `405` + заголовок `Allow: GET, POST` |
| будь-який | невідомий або відсутній `resource` | — | — | `404` |

Тіло POST приймається як JSON (`Content-Type: application/json`) або як форма (`application/x-www-form-urlencoded`).

## Приклади відповідей

GET список (`200`):

```json
{"success": true, "data": [
  {"id": 1, "name": "Динамо", "city": "Київ", "points": 24},
  {"id": 2, "name": "Шахтар", "city": "Донецьк", "points": 22}
]}
```

POST створення (`201`):

```json
{"success": true, "data": {"id": 5, "name": "Зоря", "city": "Луганськ", "points": 0}}
```

POST addPoints (`200`):

```json
{"success": true, "data": {"id": 3, "name": "Карпати", "city": "Львів", "points": 18, "added": 3}}
```

Помилка (`400`):

```json
{"success": false, "error": "Поле city обов'язкове"}
```

## Ручне тестування (крок 8)

Підставте шлях до своєї папки з файлами практикуму №4 (де лежать `db.php` і `TeamRepository.php`) замість `http://localhost/ВАША_ПАПКА`. Якщо запускаєте `php -S localhost:8000` у цій папці, використовуйте `http://localhost:8000`.

```bash
# GET: список та одна команда (200)
curl -i "http://localhost/ВАША_ПАПКА/api.php?resource=teams"
curl -i "http://localhost/ВАША_ПАПКА/api.php?resource=teams&id=1"

# GET: немає такої команди (404)
curl -i "http://localhost/ВАША_ПАПКА/api.php?resource=teams&id=999"

# GET: некоректний id (400)
curl -i "http://localhost/ВАША_ПАПКА/api.php?resource=teams&id=abc"

# POST: створення, JSON-тіло (201)
curl -i -X POST "http://localhost/ВАША_ПАПКА/api.php?resource=teams" \
  -H "Content-Type: application/json" \
  -d '{"name":"Зоря","city":"Луганськ","points":0}'

# POST: бракує city (400)
curl -i -X POST "http://localhost/ВАША_ПАПКА/api.php?resource=teams" \
  -H "Content-Type: application/json" \
  -d '{"name":"Без міста"}'

# POST: від'ємні очки (400)
curl -i -X POST "http://localhost/ВАША_ПАПКА/api.php?resource=teams" \
  -H "Content-Type: application/json" \
  -d '{"name":"Тест","city":"Київ","points":-5}'

# POST: форма замість JSON (201)
curl -i -X POST "http://localhost/ВАША_ПАПКА/api.php?resource=teams" \
  -d "name=Олександрія&city=Олександрія"

# POST addPoints: перемога (200)
curl -i -X POST "http://localhost/ВАША_ПАПКА/api.php?resource=teams&action=addPoints" \
  -H "Content-Type: application/json" \
  -d '{"id":3,"result":"win"}'

# POST addPoints: некоректний result (400)
curl -i -X POST "http://localhost/ВАША_ПАПКА/api.php?resource=teams&action=addPoints" \
  -H "Content-Type: application/json" \
  -d '{"id":3,"result":"victory"}'

# POST addPoints: команди немає (404)
curl -i -X POST "http://localhost/ВАША_ПАПКА/api.php?resource=teams&action=addPoints" \
  -H "Content-Type: application/json" \
  -d '{"id":999,"result":"draw"}'

# POST: невідома дія (400)
curl -i -X POST "http://localhost/ВАША_ПАПКА/api.php?resource=teams&action=foo"

# Непідтримуваний метод (405)
curl -i -X DELETE "http://localhost/ВАША_ПАПКА/api.php?resource=teams&id=1"

# Невідомий ресурс (404)
curl -i "http://localhost/ВАША_ПАПКА/api.php?resource=players"
```

У PowerShell замість `curl` використовуйте `curl.exe`, а лапки в JSON екрануйте як `\"`, або скористайтеся Postman.
