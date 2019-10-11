<?php

namespace Ofcold\NovaSortable;

use Laravel\Nova\Http\Requests\NovaRequest;

trait NovaSortTrait
{
    /**
     * Add sortable property for vue/js frontend
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Support\Collection  $fields
     *
     * @return array
     */
    public function serializeForIndex(NovaRequest $request, $fields = null)
    {
        $added = [
            'sortable' => true,
            'sort_id' => $this->sort_column_value(),
            'sort_on' => $this->sort_on(),                  'sort_order'
        ];

        return array_merge(parent::serializeForIndex($request, $fields), $added);
    }

    /**
     * needs to be overwritten for pivot table ordering
     * used in self::serializeForIndex
     * @return string
     */
    public function sort_column_value()
    {
        return $this->id;
    }

    /**
     *
     * @param
     * @return string
     */
    public function sort_model(NovaRequest $request)
    {
        return get_class($request->newResource()->model());
    }
}
