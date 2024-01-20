<?php

namespace PageSpeedApi\PageSpeed;

class PageSpeedMessage
{
    private function getMessage(string $message, int $code) : array
    {
        return [
            'code' => $code,
            'message' => $message
        ];
    }

    public function getErrorMessage(string $message, int $code = 400) : array
    {
        return $this->getMessage($message, $code);
    }
}