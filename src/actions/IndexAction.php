<?php
/**
 * Файл класса IndexAction
 *
 * @copyright Copyright (c) 2019, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\rest\actions;

use Exception;
use yii\web\Response;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;
use yii\web\BadRequestHttpException;
use chulakov\rest\Controller;
use chulakov\model\formatters\ListFormatter;
use chulakov\model\exceptions\NotFoundModelException;

class IndexAction extends Action
{
    /**
     * @var ListFormatter
     */
    protected $formatter;

    /**
     * Конструктор действия вывода списка сущностей
     *
     * @param string $id
     * @param Controller $controller
     * @param ListFormatter $service
     * @param array $config
     */
    public function __construct($id, Controller $controller, ListFormatter $service, array $config = [])
    {
        $this->formatter = $service;
        parent::__construct($id, $controller, $config);
    }

    /**
     * Выполнение действия вывода списка сущностей
     *
     * @return Response
     * @throws HttpException
     */
    public function run()
    {
        try {

            return $this->asJson(
                $this->formatter->asList(\Yii::$app->request)
            );

        } catch (HttpException $e) {
            throw $e;
        } catch (NotFoundModelException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e->getCode(), $e);
        } catch (Exception $e) {
            throw new BadRequestHttpException($e->getMessage(), $e->getCode(), $e);
        }
    }
}
