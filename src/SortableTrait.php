<?php

namespace Ofcold\NovaSortable;

use Exception;

trait SortableTrait
{
    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::observe(SortableObserver::class);
    }

    /**
     * default name of column in db for sorting index
     * @return string
     */
    public static function sortColumnName()
    {
        return 'sort_order';
    }

    /**
     * default
     * @return array
     */
    public static function sortGroup()
    {
        return ['id'];
    }

    /**
     *
     * @return array [['content_id', '=', '123'], ['number', '=', '3']]
     */
    public function sortOn()
    {
        $sort_group = self::sortGroup();
        $cols = [];

        array_walk($sort_group, function ($col) use (&$cols) {
            $cols[] = [$col, '=', $this->$col];
        });

        return $cols;
    }
}
