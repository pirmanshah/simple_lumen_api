<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsersController extends Controller {
    public function __construct() {
        parent::__construct('users', 'id', ["id", "name", "email", 'password']); 
    } 
    public function getByEmail(Request $req, $email) { 
        $user = $this->builder->where($this->key, $email);
        if ($user->exists()) {
            return response()->json($user->first());
        } else {
            return response()->json([
                        'success' => false,
                        'status' => 404,
                        'type' => 'Not found',
                        'message' => 'Data not found',
                        'detail' => 'No row(s) found',
                        'timestamp' => time()], 404);
        }
    }
}
