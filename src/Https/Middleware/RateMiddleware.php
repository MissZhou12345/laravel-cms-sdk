<?php

namespace QuickCms\SDK\Https\Middleware;

use Closure;
use Cache;

class RateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     *
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!auth()->check()) {
            return $next($request);
        }

        $forbidden = 403;
        $user = auth()->user();

        $key = getCacheKey('request_rate', $request->url() . '_' . $user->id);
        if (Cache::get($key)) {
            if ($request->ajax()) {
                $json['status'] = $forbidden;
                $json['msg'] = '操作失败,请不要频繁提交';
                $json['data'] = [];

                return json_encode($json);
            } else {
                return response('操作失败,请不要频繁提交', $forbidden);
            }
        }
        Cache::put($key, true, 0.1);

        return $next($request);
    }
}
