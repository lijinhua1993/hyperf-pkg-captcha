<?php

declare(strict_types=1);

namespace HyperfLjh\Captcha;


use HyperfLjh\Captcha\Listener\ValidatorFactoryResolvedListener;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
            ],
            'commands'     => [
            ],
            'annotations'  => [
                'scan' => [
                    'paths' => [
                        __DIR__,
                    ],
                ],
            ],
            'listeners' => [
                ValidatorFactoryResolvedListener::class,
            ],
            'publish'      => [
                [
                    'id'          => 'config',
                    'description' => 'The config for hyperf-ljh/captcha.',
                    'source'      => __DIR__ . '/../publish/captcha.php',
                    'destination' => BASE_PATH . '/config/autoload/captcha.php',
                ],
                [
                    'id'          => 'fonts',
                    'description' => 'The fonts for hyperf-ljh/captcha.',
                    'source'      => __DIR__ . '/../publish/fonts',
                    'destination' => BASE_PATH . '/storage/fonts',
                ],
            ],
        ];
    }
}
