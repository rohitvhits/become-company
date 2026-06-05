<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;

use App\Services\UserService;
use Illuminate\Http\Request;
class DirectoryController extends BaseController
{
    protected $userService="";
    public function __construct(UserService $userService){
        $this->middleware('auth');
       $this->middleware('permission:directory-list', ['only' => ['index', 'ajaxList']]);
        
        $this->userService = $userService;
    }

    public function index(){
        $data['menu'] = "Directory";
        $data['user'] = auth()->user();
       
        return view("directory.index", $data);
    }
    public function ajaxList(Request $request)
    {
        $data['page'] = $request->page;
        $data['query'] = $this->userService->getUserList($request->all());

        return view("directory.ajax_list", $data);
    }
}