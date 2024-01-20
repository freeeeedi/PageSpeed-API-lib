<?php

namespace PageSpeedApi;

use PageSpeedApi\PageSpeed\PageSpeedApi;
use PageSpeedApi\PageSpeed\PageSpeedStore;
use PageSpeedApi\PageSpeed\PageSpeedMessage;

class PageSpeed
{
    private string $checkingUrl;
    protected PageSpeedMessage $message;
    protected PageSpeedApi $PageSpeedApi;
    protected PageSpeedStore $PageSpeedStore;

    public function __construct(string $url, string $apiKey)
    {
        $this->PageSpeedApi = new PageSpeedApi($apiKey);
        $this->PageSpeedStore = new PageSpeedStore();
        $this->message = new PageSpeedMessage();

        $this->setCheckingUrl($url);
        $this->getApiData($url);
    }

    public function setCheckingUrl(string $url)
    {
        if (!str_contains($url, 'http')) {
            $url = 'https://' . $url;
        }

        if (substr($url, -1) != '/') {
            $url .= '/';
        }

        $this->checkingUrl = $url;
    }

    /**
     * Получение полной информации о url, который храниться в этом классе
     * 
     * @param string $category - категория page speed, из которой получаются данные.
     * 
     * Возможные значения:
     * "performance" - Производительность
     * "best-practices" - Рекомендации
     * "accessibility" - Доступность
     * "seo" - Поисковая оптимизация
     * 
     * @param string $device - устройство, на котором будут проверятся все параметры
     * 
     * Возможные значения:
     * "mobile" - мобильное ус-во
     * "desktop" - компьютер
     * 
     * @param string $locale - язык, на котором будет получен ответ
     * 
     */

    public function getApiData(string $url, string $category = 'performance', string $device = 'mobile', string $locale = 'ru-RU'): array
    {
        $apiData = $this->PageSpeedApi->getData($url, $category, $device, $locale);

        if (isset($apiData['code']) && $apiData['code'] >= 300) return $apiData;

        $this->saveData($category, $device, $apiData);

        return $this->getStoreData($category, $device);
    }

    public function getStoreData(string $category = 'performance', string $device = 'mobile', string $locale = 'ru-RU'): array | false
    {
        return $this->PageSpeedStore->getData($category, $device);
    }

    public function saveData(string $category, string $device, array $data)
    {
        $this->PageSpeedStore->saveData($category, $device, $data);
    }

    public function getCategories(): array
    {
        return $this->PageSpeedApi->getCategories();
    }

    public function checkCategory($category): bool
    {
        return $this->PageSpeedApi->checkCategory($category);
    }

    public function getDevices(): array
    {
        return $this->PageSpeedApi->getDevices();
    }

    public function checkDevice($device): bool
    {
        return $this->PageSpeedApi->checkDevice($device);
    }

    public function getScore(string $category = 'performance', string $device = 'mobile'): int | array
    {
        if (!$this->checkCategory($category)) {
            return $this->message->getErrorMessage('Введена несуществующая категория.');
        }
        if (!$this->checkDevice($device)) {
            return $this->message->getErrorMessage('Введено несуществующее устройство.');
        }

        $url = $this->checkingUrl;
        $urlData = $this->getStoreData($category, $device);

        if (!$urlData) {
            $urlData = $this->getApiData($url, $category, $device);
        }

        $score = '';

        if (isset($urlData['lighthouseResult']['categories']['performance']['score'])) {

            $score = $urlData['lighthouseResult']['categories']['performance']['score'] * 100;

            return $score;
        }

        return $this->message->getErrorMessage('Не удалось получить оценку производительности сайта');
    }

    /**
     * Получаем все аудиты заданной категории и устройства
     */

    public function getAudits(string $category = 'performance', string $device = 'mobile'): array
    {
        return $this->getAuditsData('getAudits', $category, $device);
    }

    /**
     * Получаем массив аудитов состоящий из названий и описаний
     */

    public function getAuditsShortData(string $category = 'performance', string $device = 'mobile'): array
    {
        return $this->getAuditsData('getAuditsShortData', $category, $device);
    }

    /**
     * Получаем массив аудитов, в ответе присутствуют только переданные поля в параметре $fields
     */

    public function getAuditsFiltredData(array $fields, string $category = 'performance', string $device = 'mobile'): array
    {
        return $this->getAuditsData('getAuditsFiltredData', $category, $device, $fields);
    }
    
    private function getAuditsData(string $method, string $category, string $device, array $fields = []): array
    {
        if (!$this->checkCategory($category)) {
            return $this->message->getErrorMessage('Введена несуществующая категория.');
        }
        if (!$this->checkDevice($device)) {
            return $this->message->getErrorMessage('Введено несуществующее устройство.');
        }

        $url = $this->checkingUrl;

        if ($fields != []) {
            $auditsData = $this->PageSpeedStore->$method($fields, $category, $device);
        } else {
            $auditsData = $this->PageSpeedStore->$method($category, $device);
        }

        if (!$auditsData) {

            $this->getApiData($url, $category, $device);

            if ($fields != []) {
                $auditsData = $this->PageSpeedStore->$method($fields, $category, $device);
            } else {
                $auditsData = $this->PageSpeedStore->$method($category, $device);
            }
        }

        if (!$auditsData) {
            return $this->message->getErrorMessage('Не удалось получить аудиты');
        }

        return $auditsData;
    }
}
