<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Firebase\JWT\JWT;
use DateTime;
use DateInterval;

/**
 * Description of AuthenticationController
 *
 * @author ahza0
 */
class AuthenticationController extends BaseController {

    public function __construct() {
        
    }

    public function login(Request $req) {
        $email = $req->get('email');
        $password = $req->get("password");
        $table = DB::table("users");
        $user = $table->where("email", $email)->first();
        if (isset($user)) {
            $sha1 = $table->selectRaw("sha1(?) as password", [$password])->first();
            if ($user->password == $sha1->password) {
                $token = $this->createToken($email, $user);
                $now = new DateTime();
                $interval = new DateInterval('PT1H');
                $format = "d-M-Y H:i:s";
                return response()->json([
                            'success' => true,
                            'message' => 'Login success',
                            'token' => $token,
                            'created' => date($format),
                            'expired' => $now->add($interval)->format($format)]);
            } else {
                return response()->json([
                        'success' => false,
                        'status' => 401,
                        'type' => 'Login fail',
                        'message' => 'Incorrect password',
                        'detail' => 'Provided password incorrect', 
                        $user,
                        'timestamp' => time()], 401);
            }
             
        } else {
            return response()->json([
                        'success' => false,
                        'status' => 404,
                        'type' => 'Login fail',
                        'message' => 'Email not found',
                        'detail' => 'No user with provided email',
                        'timestamp' => time()], 404);
        }
    }

    private function createToken($email, $user) {
        $app_key = env('APP_KEY');
        $iat = time();
        $payload = [
            'iss' => 'http://localhost:8000',
            'aud' => 'http://localhost:8000',
            'iat' => $iat,
            'exp' => $iat + (60 * 60),
            'name' => $user->name,
            'id' => $user->id,
            'email' => $email
        ];
        $token = JWT::encode($payload, $app_key); 
        return $token;
    }

}
