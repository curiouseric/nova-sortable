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

        static::saved(function ($model) {
            return self::reorder($model, 'saved');
        });

        static::updated(function ($model) {
            return self::reorder($model, 'updated');
        });
    }

    /**
     *
     */
    public static function reorder($model, $event)
    {
        //dump([$event => $model->id]);

        $model->withoutEvents(function () use ($model) {
            $sort_column = $model::sort_column_name();
            $sort_group = $model::sort_group();
            $sort_on = $model->sort_on();

            $index = $model->$sort_column;

            $res = $model::where($sort_group, $sort_on)
                ->where('id', '!=', $model->id)
                ->orderBy($sort_column, 'ASC');

            $res->get()->each(function ($item, $k) use ($index, $sort_column) {
                $sort = $k < $index - 1 ? $k + 1 : $k + 2;

                $item->$sort_column = $sort;
                $item->save();
            });

            $index = min($index, $res->count() + 1);
            $index = max(1, $index);

            $model->update([$sort_column => $index]);
        });
    }

    /**
     *
     * @return string
     */
    public static function sort_column_name()
    {
        return 'sort_order';
    }

    /**
     *
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
