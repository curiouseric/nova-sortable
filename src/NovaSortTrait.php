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
            'sort_id' => $this->sortColumnValue(),
            'sort_on' => $this->sortOn(),                  // 'sort_order'
        ];

        $data = array_merge(parent::serializeForIndex($request, $fields), $added);

        return $data;
    }

    /**
     * needs to be overwritten for pivot table ordering
     * used in self::serializeForIndex
     * @return string
     */
    public function sortColumnValue()
    {
        return $this->pivot ? $this->pivot->id : $this->id;
        // return $this->pivot ? $this->pivot->id : 'sort-column-none';
        // return $this->id;
    }

    /**
     *
     * @param
     * @return string
     */
    public static function sortModel(NovaRequest $request)
    {
        return get_class($request->newResource()->model());
    }

    /**
     * name of the sorting column
     * @return string
     */
    public function sortOn(){
        return 'sort_order';
    }
}
