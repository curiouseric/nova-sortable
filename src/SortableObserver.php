<?php

namespace Ofcold\NovaSortable;

class SortableObserver
{
    /**
     *
     */
    public function deleted($model){
        return self::reorder($model, 'deleted');
    }

    /**
     *
     */
    public function saved($model)
    {
        return self::reorder($model, 'saved');
    }

    /**
     * callback for saved and updated events
     * @param Model which has been reordered
     * @param string for debugging @TODO remove
     * @throws Exception
     * @return
     */
    protected static function reorder($model, $event)
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
}
