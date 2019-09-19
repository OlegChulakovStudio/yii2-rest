<?php
/**
 * Файл класса Controller
 *
 * @copyright Copyright (c) 2019, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\rest\behaviors;

use yii\base\InvalidConfigException;
use yii\filters\AccessControl;
use yii\filters\AccessRule;
use yii\filters\Cors;
use yii\filters\VerbFilter;

/**
 * Класс поведения CorsBehavior
 *
 * Поведение расширяет класс \yii\filters\Cors
 *
 * Пример подключения с минимальными параметрами:
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'cors' => [
 *              'class' => chulakov\rest\behaviors\CorsBehavior::class,
 *         ],
 *     ];
 * }
 * ```
 *
 * Пример подключения с дополнительными параметрами:
 *
 * ```php
 * public function behaviors()
 * {
 *     return [
 *         'cors' => [
 *              'class' => chulakov\rest\behaviors\CorsBehavior::class,
 *              'allowedOrigin' => ['http://www.chulakov.ru', 'https://www.api.chulakov.ru'],
 *              'allowedHeaders' => ['Authorization', 'User-Agent', 'X-Requested-With'],
 *              'isAllowedCredentials' => true,
 *              'optionsCachingMaxAge' => 3600,
 *              'exposedHeaders' => ['X-Pagination-Current-Page'],
 *         ],
 *     ];
 * }
 * ```
 *
 *
 * `isAllowedCredentials` -- булево значение, указывает будет ли с запросом дополнительно передаваться куки и авторизующие заголовки.
 *  В конфигурации поведения разрешено включать этот заголовок только если задан список имен для `Access-Control-Allow-Origin`
 *
 * `allowedHeaders` -- список разрешенных заголовков запроса, соответствует заголовку ответа Access-Control-Allow-Headers. По умолчанию будут разрешены заголовки, определенные в поведении.
 *
 * `exposedHeaders` -- список заголовков к которым будет предоставлен доступ на чтение браузером.
 *  Будет отказано в доступе js на чтение заголовока, который не был перечислен в списке `Access-Control-Expose-Headers`.
 *
 * `isAllowedCredentials` -- булево значение, указывает будет ли с запросом дополнительно передаваться куки и авторизующие заголовки.
 * В конфигурации поведения разрешено включать этот заголовок только если задан список имен для `Access-Control-Allow-Origin`
 *
 * `optionsCachingMaxAge` -- указывает время жизни предзапроса о досупности того или иного метода, после которого будет отправлен новый. Cоответствует заголовку Access-Control-Max-Age.
 * При конфигурировании параметр можно опустить, тогда будет использовано значение по умолчанию (3600)
 *
 */
class CorsBehavior extends Cors
{
    /**
     * Список разрешенных доменов
     *
     * @var array
     */
    public $allowedOrigin = ['*'];

    /**
     * Список разрешенных заголовков
     *
     * @var array
     */
    public $allowedHeaders = [
        'Authorization',
        'Accept',
        'Accept-Charset',
        'X-Requested-With',
        'User-Agent',
        'Cache-Control',
        'Content-Type',
        'Content-Length',
        'Content-Range',
        'If-Modified-Since',
    ];

    /**
     * Разрешить учетные данные для доступа к браузеру
     *
     * @var bool|null
     */
    public $isAllowedCredentials;

    /**
     * Время кеширования OPTIONS запроса
     *
     * @var int
     */
    public $optionsCachingMaxAge = 3600;

    /**
     * Список разрешенных заголовкой в браузере
     *
     * @var array
     */
    public $exposedHeaders = ['Content-Type', 'Content-Length', 'Content-Range'];

    /**
     * Список разрешенных действий
     *
     * @var array
     */
    public $allowedMethods = [];

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        $this->checkOrigin();
        $this->checkCredentials();
        $this->checkAllowHeaders();
        $this->checkExposeHeaders();
    }

    /**
     * @inheritDoc
     */
    public function beforeAction($action)
    {
        $this->checkAllowedMethods($action->id);
        $this->setCorsHeaders();

        parent::beforeAction($action);

        if (\Yii::$app->request->method === 'OPTIONS') {
            return false;
        }
        return true;
    }

    /**
     * Инициализация заголовков CORS
     */
    protected function setCorsHeaders()
    {
        $this->cors = [
            'Origin' => $this->allowedOrigin,
            'Access-Control-Request-Method' => $this->allowedMethods,
            'Access-Control-Allow-Headers' => $this->allowedHeaders,
            'Access-Control-Allow-Credentials' => $this->isAllowedCredentials,
            'Access-Control-Max-Age' => $this->optionsCachingMaxAge,
            'Access-Control-Expose-Headers' => $this->exposedHeaders,
        ];
    }

    /**
     * Проверка списка Origin
     */
    protected function checkOrigin()
    {
        if (!empty($this->allowedOrigin) && !is_array($this->allowedOrigin)) {
            throw new InvalidConfigException(
                "Origin must be an array"
            );
        }

        if (empty($this->allowedOrigin)) {
            $this->allowedOrigin = ['*'];
        }
    }

    /**
     * Проверка соответствия заголовков Origin и Credentials
     *
     * @throws InvalidConfigException
     */
    protected function checkCredentials()
    {
        if ($this->isAllowedCredentials === true && in_array('*', $this->allowedOrigin)) {
            throw new InvalidConfigException(
                "Allowed origins are required when credentials are enabled"
            );
        }
    }

    /**
     * Проверка правильности указания разрешенных заголовков
     *
     * @throws InvalidConfigException
     */
    protected function checkAllowHeaders()
    {
        if (!is_array($this->allowedHeaders) && !empty($this->allowedHeaders)) {
            throw new InvalidConfigException(
                "Allowed headers must be an array"
            );
        }
    }

    /**
     * Проверка правильности указания разрешенных заголовков
     *
     * @throws InvalidConfigException
     */
    protected function checkExposeHeaders()
    {
        if (!is_array($this->exposedHeaders) && !empty($this->exposedHeaders)) {
            throw new InvalidConfigException(
                "Exposed headers must be an array"
            );
        }
    }

    /**
     * Проверка разрешенных методов
     */
    protected function checkAllowedMethods($actionID)
    {
        if (empty($this->allowedMethods)) {
            $this->allowedMethods = $this->getUsedMethods($actionID);
        }

        if (!in_array('OPTIONS', $this->allowedMethods)) {
            $this->allowedMethods[] = 'OPTIONS';
        }
    }

    /**
     * Получение и установка доступных методов
     *
     * @param string $actionID
     * @return array
     */
    protected function getUsedMethods($actionID)
    {
        $verbMethods = $this->getVerbFilterMethods($actionID);
        $accessMethods = $this->getAccessControlRuleMethods($actionID);

        $usedMethods = array_merge($verbMethods, $accessMethods);

        if (empty($usedMethods)) {
            $usedMethods = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'];
        }

        $usedMethods = array_unique(array_map(
            'mb_strtoupper', $usedMethods
        ));

        return $usedMethods;
    }


    /**
     * Получение доступных методов VerbFilter
     *
     * @param string $actionID
     * @return array|null
     */
    protected function getVerbFilterMethods($actionID)
    {
        $behaviors = $this->owner->getBehaviors();
        foreach ($behaviors as $behavior) {
            if ($behavior instanceof VerbFilter) {
                $actions = $behavior->actions;
                if (array_key_exists($actionID, $actions)) {
                    return $actions[$actionID];
                }
            }
        }
        return null;
    }

    /**
     * Получение доступных методов из правил AccessRule
     *
     * @param string $actionId
     * @return array
     */
    protected function getAccessControlRuleMethods($actionId)
    {
        $ruleMethods = [];
        $behaviors = $this->owner->getBehaviors();

        foreach ($behaviors as $behavior) {
            if ($behavior instanceof AccessControl) {
                /** @var AccessRule $rule */
                foreach ($behavior->rules as $rule) {
                    if (!empty($rule->actions) && !in_array($actionId, $rule->actions)) {
                        continue;
                    }
                    if ($verbs = $rule->verbs) {
                        $ruleMethods = array_merge($ruleMethods, $verbs);
                    }
                }
            }
        }
        return $ruleMethods;
    }
}