<?php
/**
 * Файл класса UpdateAction
 *
 * @copyright Copyright (c) 2019, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\rest\actions;

use Exception;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use chulakov\base\response\HTTP;
use chulakov\rest\Controller;
use chulakov\model\services\Service;
use chulakov\model\exceptions\NotFoundModelException;

class UpdateAction extends Action
{
    /**
     * @var string
     */
    public $errorMessage = 'Не удалось обновить запись.';
    /**
     * @var Service
     */
    protected $service;

    /**
     * Конструктор действия модификации сущности
     *
     * @param string $id
     * @param Controller $controller
     * @param Service $service
     * @param array $config
     */
    public function __construct($id, Controller $controller, Service $service, array $config = [])
    {
        $this->service = $service;
        parent::__construct($id, $controller, $config);
    }

    /**
     * Выполнение действия модификации сущности
     *
     * @param integer $id
     * @return array
     * @throws HttpException
     */
    public function run($id)
    {
        try {

            $model = $this->service->findOne($id);
            $form = $this->service->form($model);
            $request = \Yii::$app->request;

            if ($form->load($request->getBodyParams()) && $form->validate()) {
                if ($this->service->update($model, $form)) {
                    return $this->successResult(HTTP::SUCCESS_ACCEPTED);
                }
            }

            return $this->errorResult($form, $this->errorMessage);

        } catch (HttpException $e) {
            throw $e;
        } catch (NotFoundModelException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
