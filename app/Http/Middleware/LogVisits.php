<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Cache;
use App\Models\SiteVisitor;
class LogVisits
{
    public function handle($request, Closure $next)
    {
        $ip = $request->ip();
        $site_visitor = SiteVisitor::firstOrCreate(
            ['ip_address' => $ip]
        );
        return $next($request);
    }
}

