<?php
/**
 * Файл класса DeleteAction
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

class DeleteAction extends Action
{
    /**
     * @var string
     */
    public $errorMessage = 'Не удалось удалить запись.';
    /**
     * @var Service
     */
    protected $service;

    /**
     * Конструктор действия удаления сущности
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
     * Выполнение дейтсвия удаления сущности
     *
     * @param integer $id
     * @return array
     * @throws HttpException
     */
    public function run($id)
    {
        try {

            if ($this->service->delete($id)) {
                return $this->successResult(HTTP::SUCCESS_ACCEPTED);
            }
            throw new BadRequestHttpException($this->errorMessage);

        } catch (HttpException $e) {
            throw $e;
        } catch (NotFoundModelException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
