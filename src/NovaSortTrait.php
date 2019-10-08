<?php

namespace Ofcold\NovaSortable;

use Laravel\Nova\Http\Requests\NovaRequest;

trait NovaSortTrait
{
    /**
     * Add sortable property
     *
     * @param  \Laravel\Nova\Http\Requests\NovaRequest  $request
     * @param  \Illuminate\Support\Collection  $fields
     *
     * @return array
     */
    public function serializeForIndex(NovaRequest $request, $fields = null)
    {
        return array_merge(parent::serializeForIndex($request, $fields), [
            'sort_id' => $this->sort_column_value(),
            'sortable' => true,
        ]);
    }

    /**
     *
     * @return string
     */
    public function sort_column_value()
    {
        return $this->id;
    }

    /**
     *
     * @return string
     */
    public function sort_model(NovaRequest $request){
        return get_class($request->newResource()->model());
    }
}
