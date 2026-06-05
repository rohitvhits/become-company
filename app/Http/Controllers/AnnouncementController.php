<?php

namespace App\Http\Controllers;

use App\Services\AnnouncementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AnnouncementController extends Controller
{
    protected $announcementService = "";

    public function __construct(AnnouncementService $announcementService)
    {
        $this->announcementService = $announcementService;

        $this->middleware('auth');
        $this->middleware('permission:announcement-list|announcement-create|announcement-edit|announcement-show|announcement-delete', ['only' => ['index', 'create', 'store', 'edit', 'show', 'update', 'destroy']]);
        $this->middleware('permission:announcement-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:announcement-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:announcement-show', ['only' => ['show']]);
        $this->middleware('permission:announcement-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $data['menu'] = "Insurance Master";
        $data['user'] = auth()->user();
       
        return view("announcement/index", $data);
    }

    public function ajaxList()
    {
        $data['query'] = $this->announcementService->getData();

        return view("announcement.ajax_list", $data);
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()->toArray()]);
        } else {
         
            $data = array(
                'title' => $request->title,
                'description' => $request->description,
            );

            $insert = $this->announcementService->save($data);

            if ($insert) {
                return response()->json(['status' => true, 'msg' => 'Announcement added successfully', 'data' => $insert]);
            } else {
                return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.']);
            }
        }
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data['id'] = $id;

        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'description'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'error' => $validator->errors()->toArray()]);
        } else {
           
            $data = array(
                'title' => $request->title,
                'description' => $request->description,
            );

            $this->announcementService->update($data, array('id' => $id));

            return response()->json(['status' => true, 'msg' => 'Announcement updated successfully', 'data' => $data]);
        }
    }

    public function show($id)
    {
        //
    }

    public function destroy($id)
    {
        $result = $this->announcementService->delete($id);

        if ($result) {
            return response()->json(['status' => true, 'msg' => 'Announcement delete successfully', 'data' => $result]);
        } else {
            return response()->json(['status' => false, 'msg' => 'Sorry, something went wrong. Please try again.', 'data' => $result]);
        }
    }
}