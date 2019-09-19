<?php
/**
 * Файл класса SearchModel
 *
 * @copyright Copyright (c) 2017, Oleg Chulakov Studio
 * @link http://chulakov.com/
 */

namespace chulakov\rest\models;

use chulakov\rest\data\Pagination;
use chulakov\model\models\search\SearchForm;

/**
 * Поисковая модель для REST приложения с дополнительным указанием REST пагинатора.
 */
abstract class SearchModel extends SearchForm
{
    /**
     * @var int
     */
    public $offset = 0;
    /**
     * @var int
     */
    public $total = 10;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['offset', 'total'], 'integer'],
        ];
    }

    /**
     * Сборка массива пагинации
     *
     * @return array|boolean
     */
    protected function buildPagination()
    {
        return [
            'class' => Pagination::class,
            'offset' => $this->offset,
            'pageSize' => $this->total,
        ];
    }
}
