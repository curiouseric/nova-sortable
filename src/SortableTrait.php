<?php

namespace Ofcold\NovaSortable;

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

        static::saving(function($model){
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
     */
    public static function reorder($model, $event)
    {
        //dump([$model->id => $event]);

        $model->withoutEvents(function () use ($model) {
            $sort_column = $model::sort_column_name();      // 'sort_order'
            $sort_group = $model::sort_group();             // 'id'
            $sort_on = $model->sort_on();

            $res = $model::where($sort_group, $sort_on)
                ->where('id', '!=', $model->id)
                ->orderBy($sort_column, 'ASC');

            // dump($res->toSql());
            // dd($res->getBindings());

            $index = $model->$sort_column;
            //dump($index);

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
    public static function sort_column_name()
    {
        return 'sort_order';
    }

    /**
     * default
     */
    public static function sort_group()
    {
        return 'id';
    }

    /**
     *
     * @return string
     */
    public function sort_on()
    {
        $sort_group = self::sort_group();

        return $this->$sort_group;
    }
}
