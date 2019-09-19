<?php
/**
 * Файл класса ViewSlugAction
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
use chulakov\model\exceptions\NotFoundModelException;
use chulakov\model\formatters\ViewFormatter;

class ViewSlugAction extends Action
{
    /**
     * @var ViewFormatter
     */
    protected $formatter;

    /**
     * Конструктор действия вывода информации о сущности
     *
     * @param string $id
     * @param Controller $controller
     * @param ViewFormatter $service
     * @param array $config
     */
    public function __construct($id, Controller $controller, ViewFormatter $service, array $config = [])
    {
        $this->formatter = $service;
        parent::__construct($id, $controller, $config);
    }

    /**
     * Выполнение действия вывода информации о сущности
     *
     * @param string $slug
     * @return Response
     * @throws HttpException
     */
    public function run($slug)
    {
        try {

            return $this->asJson(
                $this->formatter->asView($slug)
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
