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

        static::deleted(function ($model) {
            return self::reorder($model, 'deleted');
        });

        static::saving(function ($model) {
            // if( is_null($model->sort_order) ){
            //     $model->sort_order = 0;
            // }
        });

        static::saved(function ($model) {
            return self::reorder($model, 'saved');
        });

        // static::updated(function ($model) {
        //     return self::reorder($model, 'updated');
        // });
    }

    /**
     * callback for saved and updated events
     * @param Model which has been reordered
     * @param string for debugging @TODO remove
     * @throws Exception
     * @return
     */
    public static function reorder($model, $event)
    {
        $model->withoutEvents(function () use ($model) {
            $sort_column = $model::sortColumnName();        // 'sort_order'
            $sort_on = $model->sortOn();

            $res = $model::where($sort_on)
                ->orderBy($sort_column, 'ASC');

            if ($model->id) {
                // not present in deleting
                $res->where('id', '!=', $model->id);
            }

            // dump($res->toSql());
            // dd($res->getBindings());

            $index = $model->$sort_column;

            if ($index >= 0) {
                $index = max(0, $index);
                $index = min($index, $res->count());
            } else {
                //deleting
                $index = $res->count() + 1;
            }

            $res->get()->each(function ($item, $k) use ($index, $sort_column) {
                $sort = $k < $index ? $k : $k + 1;

                $item->$sort_column = $sort;
                $item->save();
            });

            $model->update([$sort_column => $index]);
        });
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
     * @return array
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
