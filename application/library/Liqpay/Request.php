<?php

/**
 * @namespace
 */

namespace Liqpay;

/**
 * Liqpay
 *
 * @package  Liqpay
 * @author   Anton Shevchuk
 * @link     https://www.liqpay.ua/ru/doc/checkout
 */
class Request
{
    public const ACTION_AUTH = 'auth';
    public const ACTION_PAY = 'pay';
    public const ACTION_HOLD = 'hold';
    public const ACTION_PAYDONATE = 'paydonate';
    public const ACTION_SUBSCRIBE = 'subscribe';
    public const ACTION_3DS_VERIFY = '3ds_verify';

    public const CURRENCY_EUR = 'EUR';
    public const CURRENCY_USD = 'USD';
    public const CURRENCY_UAH = 'UAH';
    public const CURRENCY_RUB = 'RUB';

    protected $supportedActions = [
        self::ACTION_AUTH,
        self::ACTION_PAY,
        self::ACTION_HOLD,
        self::ACTION_SUBSCRIBE,
        self::ACTION_PAYDONATE,
        self::ACTION_3DS_VERIFY
    ];

    protected $supportedCurrencies = [
        self::CURRENCY_EUR,
        self::CURRENCY_USD,
        self::CURRENCY_UAH,
        self::CURRENCY_RUB,
    ];

    /**
     * Публичный ключ - идентификатор магазина. Получить ключ можно в настройках магазина
     * @var string
     */
    protected $publicKey; // Required

    /**
     * @var string
     */
    protected $privateKey;

    /**
     * Версия API.
     * Текущее значение - 3
     *
     * @required
     * @var integer
     */
    protected $version = 3;

    /**
     * Тип операции.
     * Возможные значения:
     *
     *  pay - платеж
     *  hold - блокировка средств на счету отправителя
     *  subscribe - регулярный платеж
     *  paydonate - пожертвование
     *  auth - предавторизация карты
     *
     * @required
     * @var string
     */
    protected $action;

    /**
     * Cумма платежа.
     * Например: 5, 7.34
     *
     * @required
     * @var float
     */
    protected $amount;

    /**
     * Валюта платежа.
     * Возможные значения: USD, EUR, RUB, UAH
     *
     * @required
     * @var string
     */
    protected $currency = self::CURRENCY_UAH;

    /**
     * Назначение платежа.
     *
     * @required
     * @var string
     */
    protected $description;

    /**
     * Уникальный ID покупки в Вашем магазине.
     * Максимальная длина 255 символов.
     *
     * @required
     * @var string
     */
    protected $orderId;

    /**
     * Время до которого клиент может оплатить счет по UTC.
     * Передается в формате 2016-04-24 00:00:00
     *
     * @var string
     */
    protected $expiredDate;

    /**
     * Язык клиента ru, uk, en
     *
     * @var string
     */
    protected $language;

    /**
     * Параметр в котором передаются способы оплаты, которые будут отображены на чекауте.
     * Возможные значения
     *  card - оплата картой
     *  liqpay - через кабинет liqpay
     *  privat24 - через кабинет приват24,
     *  masterpass - через кабинет masterpass,
     *  moment_part - рассрочка,
     *  cash - наличными,
     *  invoice - счет на e-mail,
     *  qr - сканирование qr-кода.
     * Если параметр не передан, то применяются настройки магазина, вкладка Checkout.
     *
     * @var string
     */
    protected $payTypes;

    /**
     * URL в Вашем магазине на который покупатель будет переадресован после завершения покупки.
     * Максимальная длина 510 символов.
     *
     * @var string
     */
    protected $resultUrl;

    /**
     * Включает тестовый режим.
     * Средства с карты плательщика не списываются.
     * Для включения тестового режима необходимо передать значение 1.
     * Все тестовые платежи будут иметь статус sandbox - успешный тестовый платеж.
     *
     * @var bool
     */
    protected $sandbox = false;

    /**
     * URL API в Вашем магазине для уведомлений об изменении статуса платежа (сервер->сервер).
     * Максимальная длина 510 символов.
     *
     * @var string
     */
    protected $serverUrl;

    /**
     * Возможное значение Y.
     * Динамический код верификации, генерируется и возвращается в Callback.
     * Так же сгенерированный код будет передан в транзакции верификации для отображения в выписке по карте клиента.
     * Работает для action= auth.
     *
     * @var string
     */
    protected $verifyCode;

    /**
     * Constructor of Liqpay
     *
     * @access  public
     */
    public function __construct($public, $private)
    {
        if (empty($public)) {
            throw new \InvalidArgumentException('Public key is required');
        }

        if (empty($private)) {
            throw new \InvalidArgumentException('Private key is required');
        }

        $this->publicKey = $public;
        $this->privateKey = $private;
    }

    /**
     * data
     *
     * @param array $params
     * @return string
     */
    public function data(array $params = []): ?string
    {
        $this->setParams($params);

        // try to validate
        $this->validate();

        // build data array
        // required
        $data = [
            'action' => $this->getAction(),
            'amount' => $this->getAmount(),
            'currency' => $this->getCurrency(),
            'description' => $this->getDescription(),
            'order_id' => $this->getOrderId(),
            'version' => $this->version,
            'public_key' => $this->publicKey,
        ];

        if ($expireDate = $this->getExpiredDate()) {
            $data['expired_date'] = $expireDate;
        }

        if ($language = $this->getLanguage()) {
            $data['language'] = $language;
        }

        if ($payTypes = $this->getPayTypes()) {
            $data['paytypes'] = $payTypes;
        }

        if ($resultUrl = $this->getResultUrl()) {
            $data['result_url'] = $resultUrl;
        }

        if ($serverUrl = $this->getServerUrl()) {
            $data['server_url'] = $serverUrl;
        }

        if ($sandbox = $this->getSandbox()) {
            $data['sandbox'] = 1;
        }

        if ($verifyCode = $this->getVerifyCode()) {
            $data['verifycode'] = $verifyCode;
        }

        $data = array_map('strval', $data);

        return base64_encode(json_encode($data));
    }

    /**
     * signature
     *
     * @return string
     */
    public function signature()
    {
        $string = $this->privateKey . $this->data() . $this->privateKey;
        return base64_encode(sha1($string, 1));
    }

    /**
     * @return string
     */
    public function getAction(): ?string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return float
     */
    public function getAmount(): ?float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     */
    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    /**
     * @param string $currency
     */
    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return string
     */
    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    /**
     * @param string $orderId
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @return string
     */
    public function getExpiredDate(): ?string
    {
        return $this->expiredDate;
    }

    /**
     * @param string $expiredDate
     */
    public function setExpiredDate(string $expiredDate): void
    {
        $this->expiredDate = $expiredDate;
    }

    /**
     * @return string
     */
    public function getLanguage(): ?string
    {
        return $this->language;
    }

    /**
     * @param string $language
     */
    public function setLanguage(string $language): void
    {
        $this->language = $language;
    }

    /**
     * @return string
     */
    public function getPayTypes(): ?string
    {
        return $this->payTypes;
    }

    /**
     * @param string $payTypes
     */
    public function setPayTypes(string $payTypes): void
    {
        $this->payTypes = $payTypes;
    }

    /**
     * @return string
     */
    public function getResultUrl(): ?string
    {
        return $this->resultUrl;
    }

    /**
     * @param string $resultUrl
     */
    public function setResultUrl(string $resultUrl): void
    {
        $this->resultUrl = $resultUrl;
    }

    /**
     * @return bool
     */
    public function getSandbox(): bool
    {
        return $this->sandbox;
    }

    /**
     * @return void
     */
    public function setSandbox(): void
    {
        $this->sandbox = true;
    }

    /**
     * @return string
     */
    public function getServerUrl(): ?string
    {
        return $this->serverUrl;
    }

    /**
     * @param string $serverUrl
     */
    public function setServerUrl(string $serverUrl): void
    {
        $this->serverUrl = $serverUrl;
    }

    /**
     * @return string
     */
    public function getVerifyCode(): ?string
    {
        return $this->verifyCode;
    }

    /**
     * @param string $verifyCode
     */
    public function setVerifyCode(string $verifyCode): void
    {
        $this->verifyCode = $verifyCode;
    }

    /**
     * Set param by key over setter
     *
     * @param  string $key
     * @param  string $value
     *
     * @return void
     */
    public function setParam($key, $value): void
    {
        $method = 'set' . $this->toCamelCase($key);
        if (method_exists($this, $method)) {
            $this->$method($value);
        }
    }

    /**
     * Set params
     *
     * Requirements
     * - options must be a array
     * - options can be empty
     *
     * @param  array $params
     *
     * @return void
     */
    public function setParams(array $params = []): void
    {
        // apply params
        foreach ($params as $key => $value) {
            $this->setParam($key, $value);
        }
    }

    /**
     * Validate request
     *
     * @return void
     */
    private function validate()
    {
        // check required
        if (empty($this->version)) {
            throw new \InvalidArgumentException('Version is required');
        }
        if (empty($this->amount)) {
            throw new \InvalidArgumentException('Amount is required');
        }
        if (empty($this->action)) {
            throw new \InvalidArgumentException('Actions is required');
        }
        if (!in_array($this->action, $this->supportedActions)) {
            throw new \InvalidArgumentException('Actions is not supported');
        }
        if (empty($this->currency)) {
            throw new \InvalidArgumentException('Currency is required');
        }
        if (!in_array($this->currency, $this->supportedCurrencies)) {
            throw new \InvalidArgumentException('Currency is not supported');
        }
        if (empty($this->description)) {
            throw new \InvalidArgumentException('Description is required');
        }
    }

    /**
     * Encode params
     *
     * @param array $params
     * @return string
     */
    private function encode($params)
    {
        return base64_encode(json_encode($params));
    }

    /**
     * @param $subject
     *
     * @return string
     */
    private function toCamelCase($subject): string
    {
        $subject = str_replace(['_', '-'], ' ', strtolower($subject));
        return str_replace(' ', '', ucwords($subject));
    }
}
