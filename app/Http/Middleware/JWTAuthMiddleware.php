<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Firebase\JWT\JWT;
use Closure;

/**
 * Description of JWTAuthMiddleware
 *
 * @author ahza0
 */
class JWTAuthMiddleware {

    public function handle(Request $request, Closure $next) {
        if ($request->is('api/v1/account/login') || $request->is('documentation')) {
            return $next($request);
        }
        $app_key = env('APP_KEY');
        $token = $request->bearerToken();
        if (isset($token)) {
            try {
                $decoded = JWT::decode($token, $app_key, ['HS256']);

                return $next($request);
            } catch (\Exception $ex) {
                return response()->json([
                            'status' => 401,
                            'success' => false,
                            'type' => 'authorization',
                            'message' => 'Invalid token!',
                            'detail' => $ex->getMessage(),
                            'timestamp' => time()], 401);
            }
        } else {
            return response()->json([
                        'status' => 401,
                        'success' => false,
                        'type' => 'authorization',
                        'message' => 'Invalid token!',
                        'detail' =>
                        'Token not found',
                        'timestamp' => time()], 401);
        }
    }

}
