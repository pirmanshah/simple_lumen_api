<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Closure;

/**
 * Description of CorsMiddleware
 *
 * @author ahza0
 */
class CorsMiddleware {
    
    public function handle (Request $request, Closure $next) {
        $headers = [
            'Access-Control-Allow-Origin'=>'*',
            'Access-Control-Allow-Methods'=>'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Credentials'=>'true',
            'Access-Control-Allow-Headers'=>'Authorization, Content-Type, X-Requested-With',
            'Access-Control-Max-Age'=>'86400',
        ];
        if ($request->isMethod("OPTIONS")) {
            return response()->json('{"method":"OPTIONS"}', 200, $headers);
        }
        $response = $next($request);
        foreach ($headers as $key => $value) {
            $response->header($key, $value);
        }
        return $response;
    }
}
