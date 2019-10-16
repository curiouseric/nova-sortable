<?php

namespace Ofcold\NovaSortable\Http\Controllers;

use Illuminate\Routing\Controller;
use Laravel\Nova\Http\Requests\NovaRequest;
use Ofcold\NovaSortable\Http\Requests\NovaSortableRequest;

class SortableController extends Controller
{
    /**
     *
     * @param NovaRequest
     * @return
     */
    public function store(NovaSortableRequest $request)    // NovaRequest
    {
        $nova_model = $request->newResource();

        $index = ($request->page - 1) * $nova_model::$perPageViaRelationship + $request->index;
        $model = $nova_model::sort_model($request);
        $sort_column = $model::sort_column_name();

        $model::find($request->id)
            ->update([$sort_column => $index + 1]);

        $paginator = $this->paginator(
            $request,
            $resource = $request->resource()
        );

        return response()->json([
            'label' => $resource::label(),
            'resources' => $paginator->getCollection()->mapInto($resource)->map->serializeForIndex($request),
            'prev_page_url' => $paginator->previousPageUrl(),
            'next_page_url' => $paginator->nextPageUrl(),
            'softDeletes' => $resource::softDeletes(),
        ]);
    }

    /**
     * Get the paginator instance for the index request.
     *
     * @param  \Laravel\Nova\Http\Requests\ResourceIndexRequest  $request
     * @param  string  $resource
     * @return \Illuminate\Pagination\Paginator
     */
    protected function paginator(NovaSortableRequest $request, $resource)
    {
        return $request->toQuery()->simplePaginate(
            $request->viaRelationship()
                ? $resource::$perPageViaRelationship
                : ($request->perPage ?? 25)
        );
    }
}
