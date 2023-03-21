<?php

declare(strict_types=1);

namespace HyperfLjh\Captcha;

use Hyperf\Contract\ConfigInterface;
use HyperfLjh\Encryption\Crypt;
use Imagick;
use ImagickDraw;
use ImagickPixel;
use Psr\SimpleCache\CacheInterface;
use Throwable;

/**
 * SimpleCaptcha class.
 */
class CaptchaFactory
{
    /**
     * @var array
     */
    protected array $fonts;

    /**
     * @var array
     */
    protected array $config;

    /**
     * @var \Psr\SimpleCache\CacheInterface
     */
    protected CacheInterface $cache;

    /**
     * @param  \Hyperf\Contract\ConfigInterface  $config
     * @param  \Psr\SimpleCache\CacheInterface  $cache
     */
    public function __construct(ConfigInterface $config, CacheInterface $cache)
    {
        $this->config = (array) $config->get('captcha');
        $this->cache  = $cache;
        $this->fonts  = (array) glob(realpath($this->config['fonts_dir']) . '/*.{ttf,otf}', GLOB_BRACE);
    }

    /**
     * 创建
     *
     * @param  array|null  $config
     * @return \HyperfLjh\Captcha\Captcha
     */
    public function create(?array $config = null): Captcha
    {
        $config    = $config ? array_merge($this->config, $config) : $this->config;
        $text      = $this->getRandomText($config['characters'], $config['length']);
        $expiresAt = $config['ttl'] + time();
        $key       = $this->assembleKey($text, $expiresAt);

        return new Captcha($key, $text, $this->createImageBlob($text, $config), $expiresAt);
    }

    /**
     * 验证
     *
     * @param  string  $key
     * @param  string  $text
     * @return bool
     */
    public function validate(string $key, string $text): bool
    {
        try {
            [$original, $expiresAt] = $this->disassembleKey($key);

            if ($original === strtolower($text)
                && $expiresAt >= time()
                && $this->cache->get($cacheKey = $this->getCacheKey($key)) === null
            ) {
                $this->cache->set($cacheKey, $expiresAt, $expiresAt - time());
                return true;
            }
        } catch (Throwable $e) {
        }

        return false;
    }

    /**
     * 加密key
     *
     * @param  string  $text
     * @param  int  $expiresAt
     * @return string
     * @throws \Exception
     */
    protected function assembleKey(string $text, int $expiresAt): string
    {
        return Crypt::encrypt([strtolower($text), $expiresAt, random_bytes(16)], true,
            $this->config['encryption_driver']);
    }

    /**
     * 解密key
     *
     * @param  string  $key
     * @return array
     */
    protected function disassembleKey(string $key): array
    {
        return Crypt::decrypt($key, true, $this->config['encryption_driver']);
    }

    /**
     * 创建图像
     *
     * @param  string  $text
     * @param  array  $config
     * @return \HyperfLjh\Captcha\Blob
     * @throws \ImagickDrawException
     * @throws \ImagickException
     * @throws \ImagickPixelException
     */
    protected function createImageBlob(string $text, array $config): Blob
    {
        $image = new Imagick();

        $draws = [];
        $x     = 0;
        $y     = 0;
        foreach (str_split($text) as $char) {
            $foregroundColor = new ImagickPixel($this->getRandomForegroundColor($config['foreground_colors']));
            $draw            = new ImagickDraw();
            $draw->setFont($this->getRandomFont());
            $draw->setFontSize($config['height']);
            $draw->setFillColor($foregroundColor);
            $metrics = $image->queryFontMetrics($draw, $char);
            $draw->annotation($x, $metrics['ascender'], $char);

            $draws[] = $draw;
            $x       += $metrics['textWidth'];
            $y       = max($y, $metrics['textHeight']);
        }

        $image->newImage((int) $x, (int) $y, new ImagickPixel($config['background_color']));

        foreach ($draws as $draw) {
            $image->drawImage($draw);
        }

        $image->trimImage(0);
        $image->setImagePage(0, 0, 0, 0);

        $w = $image->getImageWidth();
        $h = $image->getImageHeight();

        $draw      = new ImagickDraw();
        $lineColor = new ImagickPixel($this->getRandomForegroundColor($config['foreground_colors']));
        $draw->setStrokeColor($lineColor);
        $draw->setFillColor($lineColor);
        $draw->setStrokeWidth(max(2, $config['height'] / 15));
        $draw->line(0, random_int($h * 2, $h * 8) / 10, $x, random_int($h * 2, $h * 8) / 10);
        $image->drawImage($draw);

        $image->swirlImage(random_int((int) $config['swirl_min'], (int) $config['swirl_max']));

        $image->scaleImage($config['width'], $config['height']);

        $image->setImageFormat($config['format']);

        $data = $image->getImageBlob();

        $image->destroy();

        return new Blob($data);
    }

    /**
     * 获取缓存key
     *
     * @param  string  $key
     * @return string
     */
    protected function getCacheKey(string $key): string
    {
        return 'captcha:' . md5($key);
    }

    /**
     * 获取随机字体
     *
     * @return string
     */
    protected function getRandomFont(): string
    {
        return $this->fonts[array_rand($this->fonts)];
    }

    /**
     * @param  array  $colors
     * @return string
     */
    protected function getRandomForegroundColor(array $colors): string
    {
        return $colors[array_rand($colors, 1)];
    }

    /**
     * 获取随机字符串
     *
     * @param  string  $characters
     * @param  int  $length
     * @return string
     * @throws \Exception
     */
    protected function getRandomText(string $characters, int $length): string
    {
        $text      = '';
        $charCount = strlen($characters);
        for ($i = 0; $i < $length; ++$i) {
            $text .= substr($characters, random_int(0, $charCount - 1), 1);
        }
        return $text;
    }
}
