<?php

namespace PageSpeedApi\PageSpeed;

class PageSpeedStore
{
    private array $page_speed_data;

    public function saveData(string $category, string $device, array $data): void
    {
        $this->page_speed_data[$category][$device] = $data;
    }

    public function getData(string $category, string $device): array | false
    {
        return isset($this->page_speed_data[$category][$device]) ? $this->page_speed_data[$category][$device] : false;
    }

    public function getAuditsShortData($category = 'performance', $device = 'mobile'): array | false
    {
        if (
            $this->getData($category, $device) == false ||
            isset($this->page_speed_data[$category][$device]['lighthouseResult']['audits']) &&
            $this->page_speed_data[$category][$device]['lighthouseResult']['audits'] == null
        ) return false;

        $audits_arr = $this->page_speed_data[$category][$device]['lighthouseResult']['audits'];

        $audits_info = [];

        foreach ($audits_arr as $key => $audit_arr) {
            $audits_info[$key] = [
                'title' => $audit_arr['title'],
                'description' => $audit_arr['description']
            ];
        }

        return $audits_info;
    }

    public function getAuditsFiltredData(array $fields, string $catefory, string $device): array | false
    {
        $audits = $this->getAudits($catefory, $device);

        $filtred_audits_data = [];

        if (!$audits) return false;

        foreach ($audits as $key => $audit) {

            foreach ($fields as $field) {

                if (array_key_exists($field, $audit)) {

                    $filtred_audits_data[$key][$field] = $audit[$field];
                }
            }
        }

        return $filtred_audits_data;
    }

    public function getAudits(string $category = 'performance', string $device = 'mobile'): array | false
    {
        if (
            $this->getData($category, $device) == false ||
            isset($this->page_speed_data[$category][$device]['lighthouseResult']['audits']) &&
            $this->page_speed_data[$category][$device]['lighthouseResult']['audits'] == null
        ) return false;

        $audits = $this->page_speed_data[$category][$device]['lighthouseResult']['audits'];

        return $audits;
    }
}
