<?php
/**
 * Файл класса CreateAction
 *
 * @copyright Copyright (c) 2019, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\rest\actions;

use Exception;
use yii\web\HttpException;
use yii\web\BadRequestHttpException;
use chulakov\base\response\HTTP;
use chulakov\rest\Controller;
use chulakov\model\services\Service;

class CreateAction extends Action
{
    /**
     * @var string
     */
    public $errorMessage = 'Не удалось создать запись.';
    /**
     * @var Service
     */
    protected $service;

    /**
     * Конструктор действия создания сущности
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
     * Выполнение действия создания сущности
     *
     * @return array
     * @throws HttpException
     */
    public function run()
    {
        try {

            $form = $this->service->form();
            $request = \Yii::$app->request;

            if ($form->load($request->getBodyParams()) && $form->validate()) {
                if ($model = $this->service->create($form)) {
                    return $this->successResult(HTTP::SUCCESS_CREATED);
                }
            }

            return $this->errorResult($form, $this->errorMessage);

        } catch (HttpException $e) {
            throw $e;
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
