<?php

declare(strict_types=1);

return [
    /**
     * 字体文件目录
     */
    'fonts_dir' => BASE_PATH . '/storage/fonts',

    'encryption_driver' => env('CAPTCHA_ENCRYPTION_DRIVER', 'aes'),

    /**
     * 验证码过期时间 单位秒
     */
    'ttl'               => env('CAPTCHA_TTL', 600),

    /**
     * 随机出现的字符列表
     */
    'characters'        => env('CAPTCHA_CHARACTERS', 'abcdefghjmnpqrtuxyzABCDEFGHJMNPQRTUXYZ'),

    /**
     * 验证码长度
     */
    'length'            => 4,

    /**
     * 生成的图片宽度高度
     */
    'width'             => 160,
    'height'            => 80,

    /**
     * 旋转角度
     */
    'swirl_min'         => 10,
    'swirl_max'         => 20,

    /**
     * 图片格式
     */
    'format'            => 'png',

    /**
     * 前景颜色列表
     */
    'foreground_colors' => ['#000000FF'],

    /**
     * 背景色
     */
    'background_color'  => '#FFFFFF00',
];
