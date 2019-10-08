<?php

namespace Ofcold\NovaSortable\Http\Requests;

use Laravel\Nova\Http\Requests\CountsResources;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Http\Requests\QueriesResources;

class NovaSortableRequest extends NovaRequest{
    use CountsResources, QueriesResources;
}
