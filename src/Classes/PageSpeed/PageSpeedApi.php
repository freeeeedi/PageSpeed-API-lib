<?php

namespace PageSpeedApi\PageSpeed;

use GuzzleHttp\Client;
use PageSpeedApi\PageSpeed\PageSpeedMessage;

class PageSpeedApi
{
    private Client $http_client;
    private PageSpeedMessage $message;
    protected string $api_key = '';
    protected const API_ROUTE = 'https://www.googleapis.com/pagespeedonline/v5/runPagespeed';

    public function __construct($api_key)
    {
        $this->http_client = new Client();
        $this->message = new PageSpeedMessage();
        
        $this->saveApiKey($api_key);
    }

    public function getData(string $url, string $category = 'performance', string $device = 'mobile', string $locale = 'ru-RU'): array
    {
        if (!$this->checkCategory($category)) {
            return $this->message->getErrorMessage('Введена несуществующая категория.');
        }
        if (!$this->checkDevice($device)) {
            return $this->message->getErrorMessage('Введено несуществующее устройство.');
        }

        $GET_params = [
            'url' => $url,
            'locale' => $locale,
            'key' => $this->getApiKey(),
            'category' => $category,
            'strategy' => $device
        ];

        try {
            $response = $this->http_client->request(
                'GET',
                self::API_ROUTE,
                [
                    'query' => $GET_params
                ]
            );
            
        } catch (\Exception $exception) {

            $code = $exception->getCode();            

            return $this->message->getErrorMessage('Не удалось получить ответ подходящий запросу', $code);
        }
        
        return json_decode($response->getBody()->getContents(), true);
    }

    public function getCategories(): array
    {
        $categories = [
            [
                'en' => 'performance',
                'ru' => 'Производительность'
            ],
            [
                'en' => 'accessibility',
                'ru' => 'Специальные возможности'
            ],
            [
                'en' => 'best-practices',
                'ru' => 'Рекомендации'
            ],
            [
                'en' => 'seo',
                'ru' => 'Поисковая оптимизация'
            ],
            [
                'en' => 'pwa',
                'ru' => 'Прогрессивное веб-приложение'
            ]
        ];

        return $categories;
    }

    public function checkCategory($category) : bool
    {
        $categories = $this->getCategories();

        foreach ($categories as $categoryArr) {
            if (in_array($category, $categoryArr)) return true;
        }

        return false;
    }


    public function getDevices(): array
    {
        $devices = [
            [
                'en' => 'mobile',
                'ru' => 'Мобильные  устройства'
            ],
            [
                'en' => 'desktop',
                'ru' => 'Компьютер'
            ]
        ];

        return $devices;
    }

    public function checkDevice(string $device) : bool
    {
        $devices = $this->getDevices();

        foreach ($devices as $deviceArr) {
            if (in_array($device, $deviceArr)) return true;
        }

        return false;
    }

    public function saveApiKey(string $key) : void
    {
        $this->api_key = $key;
    }

    protected function getApiKey(): string
    {
        return $this->api_key;
    }
}
