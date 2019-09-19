<?php
/**
 * Файл класса Controller
 *
 * @copyright Copyright (c) 2017, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\rest;

use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\ContentNegotiator;
use yii\web\BadRequestHttpException;
use chulakov\base\response\HTTP;
use chulakov\base\Controller as BaseController;
use chulakov\rest\behaviors\CorsBehavior;
use sem\helpers\ModelHelper;

/**
 * Класс контроллера для работы с REST API
 */
abstract class Controller extends BaseController
{
    /**
     * @inheritdoc
     */
    public $enableCsrfValidation = false;
    /**
     * @var int Статус исключения при ошибке в запрос
     */
    public $errorStatus = 1;
    /**
     * @var array Массив действий, исключенных из проверки авторизации
     */
    public $authenticatorExcept = [];
    /**
     * @var array Массив действий, не обязательных для авторзации
     */
    public $authenticatorOptional = [];
    /**
     * @var array Массив действий, требующих проверки авторизации
     */
    public $authenticatorOnly;
    /**
     * @var array Массив действий, исключенных из преобразования формата
     */
    public $negotiatorExcept = [];
    /**
     * @var array Массив действий, требующих преобразование формата
     */
    public $negotiatorOnly;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        // Добавление поведения авторизации
        $behaviors = [];
        if ($cors = $this->corsBehavior()) {
            $behaviors['cors'] = $cors;
        }
        if ($auth = $this->authenticatorBehavior()) {
            $behaviors['authenticator'] = $auth;
        }
        $behaviors = ArrayHelper::merge(
            $behaviors, parent::behaviors()
        );
        if ($negotiator = $this->contentNegotiatorBehavior()) {
            $behaviors['negotiator'] = $negotiator;
        }
        return $behaviors;
    }

    /**
     * Создание настроек для поведения проверки авторизации
     *
     * @return array
     */
    protected function authenticatorBehavior()
    {
        return [
            'class' => HttpBearerAuth::class,
            'optional' => $this->authenticatorOptional,
            'except' => $this->authenticatorExcept,
            'only' => $this->authenticatorOnly,
        ];
    }

    /**
     * Создание настроек для CORS фильтра
     *
     * @return array
     */
    protected function corsBehavior()
    {
        return [
            'class' => CorsBehavior::class,
        ];
    }

    /**
     * Создание настроек для форматера запросов
     *
     * @return array
     */
    protected function contentNegotiatorBehavior()
    {
        return [
            'class' => ContentNegotiator::class,
            'except' => $this->negotiatorExcept,
            'only' => $this->negotiatorOnly,
            'formats' => [
                'application/json' => Response::FORMAT_JSON,
            ],
        ];
    }

    /**
     * Стандартный успешный ответ
     *
     * @param integer $code
     * @param array|string $message
     * @return array
     */
    public function successResult($code = HTTP::SUCCESS_OK, $message = null)
    {
        \Yii::$app->response->statusCode = $code;
        $result = [
            'success' => true,
        ];
        if (!is_null($message)) {
            $result['message'] = $message;
        }
        return $result;
    }

    /**
     * Генерация ответа об ошибке
     *
     * @param Model $model
     * @param string $error
     * @throws BadRequestHttpException
     */
    public function errorResult(Model $model, $error)
    {
        if ($model->hasErrors()) {
            $error = ModelHelper::firstErrorText($model);
        }
        throw new BadRequestHttpException($error, $this->errorStatus);
    }
}
