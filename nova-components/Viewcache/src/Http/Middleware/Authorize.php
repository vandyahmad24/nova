<?php

namespace Beyoncode\Viewcache\Http\Middleware;

use Beyoncode\Viewcache\Viewcache;

class Authorize
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Illuminate\Http\Response
     */
    public function handle($request, $next)
    {
        return resolve(Viewcache::class)->authorize($request) ? $next($request) : abort(403);
    }
}
