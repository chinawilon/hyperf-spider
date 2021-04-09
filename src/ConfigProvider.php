<?php

namespace Spider;


class ConfigProvider
{
    public function __invoke()
    {
        return [
            'annotations' => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
        ];
    }
}