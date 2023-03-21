<?php

declare(strict_types=1);

namespace HyperfLjh\Captcha;

use Carbon\Carbon;
use Hyperf\Contract\Arrayable;

class Captcha implements Arrayable
{
    /**
     * @var string
     */
    private string $key;

    /**
     * @var string
     */
    private string $text;

    /**
     * @var \HyperfLjh\Captcha\Blob
     */
    private Blob $blob;

    /**
     * @var \Carbon\Carbon
     */
    private Carbon $expiresAt;

    /**
     * @param  string  $key
     * @param  string  $text
     * @param  \HyperfLjh\Captcha\Blob  $blob
     * @param  int  $expiresAt
     */
    public function __construct(string $key, string $text, Blob $blob, int $expiresAt)
    {
        $this->key       = $key;
        $this->text      = $text;
        $this->blob      = $blob;
        $this->expiresAt = Carbon::createFromTimestamp($expiresAt);
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }

    /**
     * @return Blob
     */
    public function getBlob(): Blob
    {
        return $this->blob;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getExpiresAt(): Carbon
    {
        return $this->expiresAt;
    }

    /**
     * @return int
     */
    public function getTtl(): int
    {
        return $this->expiresAt->timestamp - time();
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key'  => $this->key,
            'blob' => $this->blob->toDataUrl(),
            'ttl'  => $this->getTtl(),
        ];
    }
}
