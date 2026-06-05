<?php

namespace App\Http\Controllers;

use App\Services\RequestService;
use App\Services\TaskService;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        $auth = auth()->user();
        if($auth->agency_fk !=""){
            abort(404);
        }
        $query = RequestService::RequestList();
        $data['auth'] = $auth;
        $data['task_name'] = $task_name = $request->input('task_name');
        $data['user_id'] = $user_id = $request->input('user_id');
        $data['status'] = $status = $request->input('status');
        $data['pendingTask'] = $pendingTask = $request->input('pending-task');
        $data['query'] = $query;
        $data['user'] = $auth;
        return view('request.request_list',    $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
