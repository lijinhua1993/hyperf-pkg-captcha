# Hyperf CAPTCHA 组件

## 安装

```shell
composer require hyperf-ljh/captcha
```

## 发布配置

```shell
php bin/hyperf.php vendor:publish hyperf-ljh/captcha
```

> 字体文件默认发布到 `<root>/storage/fonts` 目录。
组件依赖 `lijinhua/hyperf-ext-encryption` 组件加解密 `key`，依赖 `hyperf/cache` 组件暂存使用过的 `key`，您需要发布这些组件的配置：

```shell
php bin/hyperf.php vendor:publish hyperf-ljh/encryption
php bin/hyperf.php vendor:publish hyperf/cache
```

## 使用

```php
use Hyperf\Utils\ApplicationContext;
use HyperfLjh\Captcha\CaptchaFactory;

$captchaFactory = ApplicationContext::getContainer()->get(CaptchaFactory::class);
// 生成
$captcha = $captchaFactory->create();
$response = [
    'key' => $captcha->getKey(),
    'blob' => $captcha->getBlob()->toDataUrl(),
    'ttl' => $captcha->getTtl(),
];
// 验证
$captchaFactory->validate($key, $text);
```

### 验证器

您可将 `key` 与用户输入用逗号 `,` 连接后传递给 `captcha` 验证规则来自动完成验证，验证成功的 `key` 将被自动暂存到缓存中直至该 `key` 过期。