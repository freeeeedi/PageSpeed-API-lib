# choose/page_speed_api
Библиотека для удобного использования PageSpeed Insights API

## Установка
```bash
composer require choose/page_speed_api
```

## Использование

Создаем экземпляр класса PageSpeed и передаем в него 2 параметра:
- $url - ссылка на страницу сайта, которую хотим проверить
- $apiKey - API-ключ для PageSpeed Insights API
```php
$PageSpeed = new PageSpeed(string $url, $apiKey);
```

Получить всю информацию о странице на русском языке из категории performance (Производительность), проверенную на мобильных устройствах. 

```php
$data = $PageSpeed->getApiData();
```

### Аудиты (показатели категории)

По умолчанию категория - performance, устройство проверки - mobile

Получения полной информации по аудитам

```php
$audits = $PageSpeed->getAudits();
```

Получения только названия и описания аудита

```php
$audits = $PageSpeed->getAuditsShortData();
```

Получения ответа только с переданными полями 

$fields - массив полей, которые должны придти в ответе

```php
$filtred_audits = $PageSpeed->getAuditsFiltredData(array $fields);
```

### Оценка

По умолчанию категория - performance, устройство проверки - mobile

Получение общей оценки в категории

```php
$score = $PageSpeed->getScore();
```

### Общее

Получение массива названий всех категорий

```php
$categories = $PageSpeed->getDevices();
```

Получение массива названий всех устройств

```php
$categories = $PageSpeed->getDevices();
```