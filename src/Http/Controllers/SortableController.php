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
        $model = $request->newResource()->sort_model();
        $res = $model::where('playlist_id', $request->sort_on)
            ->where('id', '!=', $request->id);

        dump($res->toSql());
        dd($res->getBindings());

        //
        // $items = $request->items;

        // //dump($request->newResource());
        // // dump($model);
        // // dd($items);

        // foreach ($items as $item) {
        //     tap($model::find($item['id']), function ($entry) use ($model, $item) {
        //         $entry->{$model::orderColumnName()} = $item['sort_order'];
        //     })->save();
        // }

        // $paginator = $this->paginator(
        //     $request,
        //     $resource = $request->resource()
        // );

        // return response()->json([
        //     'label' => $resource::label(),
        //     'resources' => $paginator->getCollection()->mapInto($resource)->map->serializeForIndex($request),
        //     'prev_page_url' => $paginator->previousPageUrl(),
        //     'next_page_url' => $paginator->nextPageUrl(),
        //     'softDeletes' => $resource::softDeletes(),
        // ]);
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
