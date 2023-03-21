<?php

declare(strict_types=1);

namespace HyperfLjh\Captcha;

use finfo;

class Blob
{
    /**
     * @var string
     */
    private string $raw;

    /**
     * @var string|false
     */
    private string|false $mimetype;

    /**
     * @param  string  $raw
     */
    public function __construct(string $raw)
    {
        $this->raw      = $raw;
        $this->mimetype = (new finfo(FILEINFO_MIME_TYPE))->buffer($raw);
    }

    /**
     * @return string
     */
    public function getRaw(): string
    {
        return $this->raw;
    }

    /**
     * @return false|string
     */
    public function getMimetype(): bool|string
    {
        return $this->mimetype;
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->raw;
    }

    /**
     * @return string
     */
    public function toDataUrl(): string
    {
        return 'data:' . $this->mimetype . ';base64,' . base64_encode($this->raw);
    }

}
