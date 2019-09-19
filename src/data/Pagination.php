<?php
/**
 * Файл класса Pagination
 *
 * @copyright Copyright (c) 2017, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\rest\data;

/**
 * Пагинатор с возможностью указывать смещение выборки
 */
class Pagination extends \yii\data\Pagination
{
    /**
     * @var integer
     */
    public $offset;

    /**
     * @inheritdoc
     */
    public function getOffset()
    {
        if (empty($this->offset)) {
            return parent::getOffset();
        }
        return $this->offset;
    }
}
