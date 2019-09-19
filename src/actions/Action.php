<?php
/**
 * Файл класса Action
 *
 * @copyright Copyright (c) 2019, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\rest\actions;

use yii\base\Model;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use chulakov\base\response\HTTP;
use chulakov\rest\Controller;

abstract class Action extends \yii\base\Action
{
    /**
     * @var Controller
     */
    public $controller;

    /**
     * Форматирование ответа как json
     *
     * @param mixed $data
     * @return Response
     */
    public function asJson($data)
    {
        return $this->controller->asJson($data);
    }

    /**
     * Стандартный успешный ответ
     *
     * @param integer $code
     * @param string $message
     * @return array
     */
    protected function successResult($code = HTTP::SUCCESS_OK, $message = null)
    {
        return $this->controller->successResult($code, $message);
    }

    /**
     * Генерация ответа об ошибке
     *
     * @param Model $model
     * @param string $error
     * @throws BadRequestHttpException
     */
    protected function errorResult(Model $model, $error)
    {
        return $this->controller->errorResult($model, $error);
    }
}
