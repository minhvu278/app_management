<?php

namespace Theme\Demo\Http\Controllers;

use Botble\Filescode\Models\Code;
use Botble\Theme\Http\Controllers\PublicController;
use DateTime;
use DB;
use Illuminate\Http\Request;
use Theme;
use Validator;
use View;

class DemoController extends PublicController
{
    public function __construct()
    {
        $code = DB::table('codes') -> where('status', 'none') -> inRandomOrder() -> first();

        View::share ( 'code', $code );
    }
    public function getIndex()
    {
        
        $none = DB::table('codes')->where('status','none')->count();
        $done = DB::table('codes')->where('status','done')->count();
        $quantity = DB::table('codes')->count();
        $used_day = DB::table('codes')->whereRaw('datediff(now(), viewed_at) = ?', [0])->count();
        return Theme::scope('index', compact('none', 'done', 'quantity', 'used_day'))->render(); 
 
    }
    public function checkPass(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'error' => $validator->errors()
            ]);
        }else{
            if ($request->input('password') == env('FILEAPPLE_PASSWORD')) {
                return response()->json([
                    'status' => 1,
                    'message' => 'success'
                ], 200);
            }else{
                return response()->json([
                    'status' => 0,
                    'error' => ['password'=>'the password is incorrect. try again']
                ]);
            }
        }
    }
    public function updateCode(Request $request)
    {
        $code = Code::find($request->input('id'));
        if ($code) {
            $code->status = 'done';
            $code->viewed_at = date('Y-m-d H:i:s');
            $code->save();
            return response()->json([
                'status' => 1,
                'message' => 'success'
            ], 200);
        }else{
            return response()->json([
                'status' => 0,
                'error' => 'code not found'
            ]);
        }
    }
}
