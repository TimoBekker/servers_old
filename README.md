# AccService - Система учета серверов и информационных систем

Проект разделен на фронтенд (React) и бекенд (Yii2 API).

## Структура проекта

```
/
├── src/                    # React фронтенд
├── public/                 # Статические файлы фронтенда
├── backend/                # Yii2 API бекенд
├── accservice/             # Оригинальный Yii2 проект (legacy)
└── package.json            # Зависимости фронтенда
```

## Установка и запуск

### Фронтенд (React)

1. Установите зависимости:
```bash
npm install
```

2. Запустите dev сервер:
```bash
npm start
```

Фронтенд будет доступен по адресу: http://localhost:3000

### Бекенд (Yii2 API)

1. Перейдите в папку backend:
```bash
cd backend
```

2. Установите зависимости:
```bash
composer install
```

3. Настройте базу данных в `backend/config/db.php`

4. Запустите встроенный сервер PHP:
```bash
php -S localhost:8080 -t web
```

API будет доступно по адресу: http://localhost:8080

## API Endpoints

### Авторизация
- `POST /api/auth` - Вход в систему
- `POST /api/logout` - Выход
- `GET /api/profile` - Профиль пользователя

### Оборудование
- `GET /api/equipment` - Список оборудования
- `POST /api/equipment` - Создание оборудования
- `GET /api/equipment/{id}` - Просмотр оборудования
- `PUT /api/equipment/{id}` - Обновление оборудования
- `DELETE /api/equipment/{id}` - Удаление оборудования

### Дашборд
- `GET /api/dashboard/stats` - Статистика
- `GET /api/dashboard/events` - Последние события

## Технологии

### Фронтенд
- React 18
- React Router 6
- Ant Design
- Axios

### Бекенд
- Yii2 Framework
- MySQL
- JWT авторизация
- RESTful API

## Разработка

Для разработки запустите оба сервера:
- Фронтенд: `npm start` (порт 3000)
- Бекенд: `php -S localhost:8080 -t backend/web` (порт 8080)

Фронтенд настроен на проксирование API запросов на бекенд.