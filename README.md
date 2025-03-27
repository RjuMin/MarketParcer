# Отслеживание изменения цен на маркетплейсах через Telegram-бота

**Министерство образования и науки Российской Федерации**  
Федеральное государственное автономное образовательное учреждение высшего образования  

**Национальный исследовательский университет "МИЭТ"**  

**Институт системной и программной инженерии и информационных технологий**  

**Дисциплина:** Программная инженерия управляющих систем  
**Лабораторная работа 1**  

**Тема проекта:**  
Проект "Отслеживание изменения цен на маркетплейсах через Telegram-бота": пользователь будет отправлять ссылку на товар, а бот будет уведомлять об изменении цены.  

---

## Схема взаимодействия

1. Пользователь отправляет боту ссылку на товар.  
2. Telegram Bot парсит ссылку, извлекает идентификатор товара.  
3. Telegram Bot отправляет запрос в Marketplace Parser для получения текущей цены.  
4. Marketplace Parser возвращает текущую цену товара.  
5. Telegram Bot сохраняет товар в список отслеживания и возвращает пользователю текущую цену.  
6. Telegram Bot ежедневно проверяет цены через Marketplace Parser и отправляет уведомления, если цена изменилась.  

---

## Микросервисы

### 1. Telegram Bot (Frontend + Бизнес-логика)  
**Ответственный:** Бозюкова  

**Задачи:**  
- Настройка Telegram-бота (webhook, обработка событий).  
- Прием сообщений от пользователей (ссылки на товары с маркетплейсов).  
- Парсинг ссылок для извлечения идентификатора товара (например, из URL Wildberries или Ozon).  
- Отправка запросов в Marketplace Parser для получения текущей цены.  
- Хранение данных о пользователях и отслеживаемых товарах.  
- Генерация уведомлений об изменении цен:  
  - Сравнение текущей цены с предыдущей.  
  - Отправка уведомлений пользователям.  
- Работа с задачами для отправки уведомлений:  
  - Ежедневные проверки цен.  
  - Напоминания о значительных изменениях цен.  
- Сохранение истории изменений цен в БД.  

**Технологии:**  
- PHP + Laravel Framework.  
- Telegram Bot API (через `telegram-bot/api`).  
- MySQL/PostgreSQL для хранения данных.  
- Redis для очередей задач.  

### 2. Marketplace Parser (Backend)  
**Ответственный:** Силантьев  

**Задачи:**  
- Интеграция с API маркетплейсов (например, Wildberries, Ozon, Яндекс.Маркет).  
- Парсинг данных о товарах (цена, наличие, скидки) по идентификатору товара.  
- Нормализация данных для хранения в БД.  
- Кэширование данных (Redis/MySQL) для снижения нагрузки на API маркетплейсов.  
- Предоставление API для Telegram Bot Handler:  
  - `GET /product/{product_id}` — текущая информация о товаре.  
  - `GET /price-history/{product_id}` — история изменения цен.  
- Обработка изменений в данных (обновление кэша).  

**Технологии:**  
- PHP + Laravel.  
- MySQL/PostgreSQL + Redis для кэша.  
- Guzzle для запросов к API маркетплейсов.  

---

## График работ

| ЛР | Бозюкова | Силантьев |
|----|----------|-----------|
| 1  | - Создать бота в Telegram.<br>- Реализовать обработку сообщений с ссылками на товары.<br>- Разработка базы данных для хранения пользовательских данных и списка отслеживаемых товаров. | - Изучить API маркетплейсов (например, Wildberries, Ozon).<br>- Реализовать эндпоинт `GET /product/{product_id}` (возвращает тестовые данные о товаре в формате JSON).<br>- Парсинг ссылок для извлечения идентификатора товара.<br>- Настроить отправку/прием сообщений через PHP. |
| 2  | - Добавить авторизацию (привязка user_id к данным пользователя).<br>- Реализовать сохранение товаров в список отслеживания.<br>- Интеграция с Marketplace Parser для получения текущей цены.<br>- Реализовать хранение данных о товарах в БД. | - Реализовать парсинг реальных данных с маркетплейсов.<br>- Добавить кэширование данных для снижения нагрузки на API. |
| 3  | - Добавить клавиатуры и кнопки для удобства (например, "Мои товары", "История цен").<br>- Реализовать отправку ежедневных уведомлений (интеграция с cron).<br>- Реализовать сравнение цен и отправку уведомлений пользователям.<br>- Добавить хранение истории изменений цен. | - Реализовать автообновление данных о товарах.<br>- Добавить обработку исключений (например, если товар не найден).<br>- Обработка ошибок (например, если API маркетплейса недоступно). |

--- 

**Выполнили студенты ПИН-36:**  
Силантьев М. В., Бозюкова Л. С.  

**Зеленоград, 2024 г.**  
