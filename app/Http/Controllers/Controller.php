<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class Controller extends BaseController {

    protected $builder;
    protected $key;
    protected $fields;

    public function __construct($table, $key, $fields = []) {
        $this->builder = DB::table($table);
        $this->key = $key;
        $this->fields = $fields;
    }

    public function get(Request $req) {
        $pageSize = $req->get('pageSize', 10);
        $sort = $req->get("sort", $this->key);
        $asc = $req->get("asc", "true");
        $search = $req->get("search", "");
        $builder = $this->builder;
        if (!empty($search)) {
            $builder = $this->builder->whereRaw("false");
            foreach ($this->fields as $c) {
                $builder->orWhere($c, "LIKE", "%$search%");
            }
        }
        $count = $builder->count();
        $builder->orderBy($sort, $asc == 'true' ? 'asc' : 'desc');
        $builder->paginate($pageSize);
        return response()->json([
                    'data' => $builder->get(),
                    'totalRow' => $count,
                    'totalPage' => ceil($count / $pageSize),
                    'sort' => $sort,  
                    'directon' => $asc == 'asc' ? 'ASC' : 'DESC']);
    }

    public function getById(Request $req, $id) {
        $item = $this->builder->where($this->key, $id);
        if ($item->exists()) {
            return response()->json($item->first());
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

    public function create(Request $req) {
        $data = $req->all();
        $id = $this->builder->insertGetId($data);
        $data[$this->key] = $id;
        return response()->json(['success' => true, 'data' => $data]);
    }

    public function update(Request $req, $id) {
        $data = $req->all();
        $item = $this->builder->where($this->key, $id);
        if ($item->exists()) {
            $success = $item->update($data);
            return response()->json(['success' => true, 'data' => $data]);
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

    public function delete(Request $req, $id) {
        $item = $this->builder->where($this->key, $id);
        if ($item->exists()) {
            $success = $item->delete();
            return response()->json(['success' => true, 'message' => 'Delete data success']);
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
