<?php

namespace Vipertecpro\Laravel\VisitorTracker\Middleware;

use Closure;
use Illuminate\Http\Request;
use Vipertecpro\Laravel\VisitorTracker\Tracker;

class RecordVisits
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        Tracker::recordVisit();

        return $next($request);
    }
}
