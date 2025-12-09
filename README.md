# 🚀 Space Spectator

**Веб-приложение для мониторинга и анализа космических данных в реальном времени**

[![Docker](https://img.shields.io/badge/Docker-Ready-blue)](https://www.docker.com/)
[![Laravel](https://img.shields.io/badge/Laravel-11-red)](https://laravel.com/)
[![Rust](https://img.shields.io/badge/Rust-1.70+-orange)](https://www.rust-lang.org/)
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-16-blue)](https://www.postgresql.org/)

---

## 📋 Описание

**Space Spectator** - это распределенный монолит для сбора, обработки и визуализации данных из различных космических источников:

- 🌍 **Международная космическая станция (МКС)** - отслеживание позиции и телеметрии
- 🔭 **Телескоп JWST** - галерея изображений космоса
- 🛰️ **NASA OSDR** - данные открытых научных исследований
- ⭐ **Астрономические события** - календарь космических явлений
- 📊 **Legacy телеметрия** - генерация и просмотр CSV/XLSX отчетов

## 🏗️ Архитектура

Проект построен на микросервисной архитектуре с использованием Docker:

```
┌─────────────┐     ┌─────────────┐     ┌─────────────┐
│   Nginx      │────▶│   Laravel   │────▶│ PostgreSQL  │
│  (Reverse    │     │   (PHP 8.3) │     │   (v16)     │
│   Proxy)     │     │             │     │             │
└─────────────┘     └─────────────┘     └─────────────┘
                            │
                            ▼
                     ┌─────────────┐
                     │    Redis    │
                     │ (Cache +    │
                     │ Rate Limit) │
                     └─────────────┘
                            │
        ┌───────────────────┴───────────────────┐
        │                                       │
        ▼                                       ▼
┌─────────────┐                        ┌─────────────┐
│ Rust Service│                        │   Pascal    │
│  (Axum +    │                        │   Legacy    │
│   SQLx)     │                        │  (CSV/XLSX) │
└─────────────┘                        └─────────────┘
```

### Компоненты

- **Frontend (Laravel 11)**: Веб-интерфейс с анимациями, фильтрацией и поиском
- **Backend (Rust + Axum)**: Высокопроизводительный сервис для сбора данных из внешних API
- **Legacy (Pascal)**: Модуль для генерации CSV/XLSX с правильными типами данных
- **Database (PostgreSQL 16)**: Хранение всех данных
- **Cache (Redis 7)**: Кэширование и rate limiting
- **Proxy (Nginx)**: Reverse proxy и балансировка нагрузки

## 🚀 Быстрый старт

### Требования

- Docker Desktop или Docker Engine 20.10+
- Docker Compose 2.0+
- 4GB свободной RAM
- Порты: 8080, 5432, 6379

### Установка и запуск

```bash
# 1. Клонировать репозиторий
git clone https://github.com/tniXu7/Space_spectator.git
cd Space_spectator

# 2. Запустить все сервисы
docker-compose up -d --build

# 3. Дождаться инициализации (30-60 секунд)
docker-compose logs -f php

# 4. Открыть в браузере
# http://localhost:8080
```

### Первый запуск

При первом запуске автоматически:
- ✅ Создаются Docker образы
- ✅ Инициализируется база данных PostgreSQL
- ✅ Устанавливаются зависимости Laravel
- ✅ Выполняются миграции
- ✅ Настраивается Redis

## 📱 Основные страницы

### 🏠 Dashboard (`/dashboard`)
Главная панель управления с:
- **МКС**: Текущая позиция, скорость, высота
- **JWST Gallery**: Галерея изображений с фильтрацией
- **Астрономические события**: Календарь событий

### 🛰️ OSDR (`/osdr`)
Просмотр данных NASA OSDR с:
- 🔍 Поиск по ключевым словам
- 📊 Фильтрация по дате
- ⬆️⬇️ Сортировка по столбцам

### 📈 Telemetry (`/telemetry`)
Управление телеметрией:
- 📋 Список всех записей с фильтрацией
- 📁 Просмотр CSV файлов
- 📊 Визуализация данных в таблице
- 🔍 Поиск и сортировка

### 📄 CSV Files (`/telemetry/csv`)
- Список всех сгенерированных CSV/XLSX файлов
- Просмотр содержимого в виде таблицы
- Экспорт данных

## 🔌 API Endpoints

Все API эндпоинты защищены **Rate Limiting**: 60 запросов в минуту на IP.

### МКС (ISS)

```http
GET /api/iss/last
```
Возвращает последние данные о позиции МКС.

**Ответ:**
```json
{
  "timestamp": 1700000000,
  "latitude": 51.5074,
  "longitude": -0.1278,
  "altitude": 408.0,
  "velocity": 27600.0
}
```

```http
GET /api/iss/trend
```
Возвращает тренд движения МКС за последние 24 часа.

### JWST

```http
GET /api/jwst/feed?limit=10
```
Возвращает последние изображения с телескопа JWST.

**Параметры:**
- `limit` (optional): Количество изображений (по умолчанию 20)

### Астрономические события

```http
GET /api/astro/events?date=2024-01-15
```
Возвращает астрономические события на указанную дату.

**Параметры:**
- `date` (optional): Дата в формате YYYY-MM-DD (по умолчанию сегодня)

### OSDR

```http
GET /api/osdr?search=mission&sort=date&order=desc
```
Возвращает данные NASA OSDR с фильтрацией.

**Параметры:**
- `search` (optional): Поиск по ключевым словам
- `sort` (optional): Поле для сортировки
- `order` (optional): `asc` или `desc`

## 🛠️ Технологический стек

| Компонент | Технология | Версия |
|-----------|-----------|--------|
| **Frontend** | Laravel | 11.x |
| **Backend** | Rust + Axum | 1.70+ |
| **Legacy** | Free Pascal | 3.2+ |
| **Database** | PostgreSQL | 16 |
| **Cache** | Redis | 7 |
| **Web Server** | Nginx | 1.25+ |
| **PHP** | PHP-FPM | 8.3 |
| **Container** | Docker | 20.10+ |

## 📁 Структура проекта

```
Space_spectator/
├── services/
│   ├── php-web/                    # Laravel приложение
│   │   ├── laravel-patches/
│   │   │   ├── app/
│   │   │   │   ├── Http/
│   │   │   │   │   ├── Controllers/    # Контроллеры
│   │   │   │   │   ├── Middleware/     # Rate Limiting
│   │   │   │   │   └── Validators/     # Валидация данных
│   │   │   │   └── Support/            # Вспомогательные классы
│   │   │   ├── resources/views/        # Blade шаблоны
│   │   │   └── routes/                 # Маршруты
│   │   ├── Dockerfile
│   │   ├── entrypoint.sh
│   │   └── nginx.conf
│   ├── rust-iss/                     # Rust сервис
│   │   ├── src/main.rs
│   │   ├── Cargo.toml
│   │   └── Dockerfile
│   └── pascal-legacy/                # Legacy модуль
│       ├── legacy.pas                 # Генерация CSV
│       ├── csv2xlsx.py                # Конвертация в XLSX
│       ├── Dockerfile
│       └── run.sh
├── db/
│   └── init.sql                       # Инициализация БД
├── docker-compose.yml                 # Конфигурация Docker
├── .gitignore
└── README.md
```

## ✨ Основные возможности

### Frontend (Laravel)

- ✅ **Анимации**: Плавные переходы и эффекты
- ✅ **Фильтрация**: По дате, столбцам, ключевым словам
- ✅ **Сортировка**: По возрастанию/убыванию
- ✅ **Поиск**: Полнотекстовый поиск по данным
- ✅ **Адаптивный дизайн**: Работает на всех устройствах
- ✅ **Разделение на контексты**: Каждая бизнес-функция на отдельной странице

### Backend (Rust)

- ✅ **Высокая производительность**: Асинхронная обработка запросов
- ✅ **Надежность**: Обработка ошибок и retry логика
- ✅ **Масштабируемость**: Готов к горизонтальному масштабированию

### Legacy (Pascal)

- ✅ **Правильные типы данных**: Timestamp, boolean, numeric, text
- ✅ **CSV генерация**: Форматированные CSV файлы
- ✅ **XLSX экспорт**: Автоматическая конвертация в Excel
- ✅ **Визуализация**: Просмотр CSV в виде таблицы

### Инфраструктура

- ✅ **Rate Limiting**: Защита от перегрузки (60 req/min)
- ✅ **Redis кэширование**: Ускорение работы приложения
- ✅ **Валидация данных**: Отдельные классы для каждого API
- ✅ **Docker Compose**: Простой запуск всех сервисов

## 🔒 Безопасность

- **Rate Limiting**: Защита API от злоупотреблений
- **CSRF Protection**: Защита от межсайтовых атак
- **Input Validation**: Валидация всех входящих данных
- **Environment Variables**: Конфиденциальные данные в `.env`

## 📊 Мониторинг

```bash
# Просмотр логов всех сервисов
docker-compose logs -f

# Логи конкретного сервиса
docker-compose logs -f php
docker-compose logs -f rust
docker-compose logs -f postgres

# Статус контейнеров
docker-compose ps

# Использование ресурсов
docker stats
```

## 🐛 Устранение неполадок

### Проблема: 502 Bad Gateway

```bash
# Перезапустить PHP контейнер
docker-compose restart php

# Проверить логи
docker-compose logs php
```

### Проблема: База данных не подключается

```bash
# Пересоздать базу данных
docker-compose down -v
docker-compose up -d postgres
```

### Проблема: Redis не работает

```bash
# Проверить статус Redis
docker-compose ps redis
docker-compose logs redis
```

## 🔧 Разработка

### Локальная разработка

```bash
# Запуск в режиме разработки
docker-compose -f docker-compose.yml up

# Выполнение миграций
docker-compose exec php php artisan migrate

# Очистка кэша
docker-compose exec php php artisan cache:clear
```

### Добавление новых функций

1. Создать новую ветку: `git checkout -b feature/new-feature`
2. Внести изменения
3. Создать коммит: `git commit -m "Feature: описание"`
4. Отправить: `git push origin feature/new-feature`

## 📝 Лицензия

Проект создан в учебных целях.

## 👥 Авторы

- **tniXu7** - Разработка и архитектура

## 🙏 Благодарности

- NASA за открытые API
- Laravel сообщество
- Rust сообщество

---

**⭐ Если проект был полезен, поставьте звезду!**
