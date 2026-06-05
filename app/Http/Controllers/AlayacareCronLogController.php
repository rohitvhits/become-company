<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AlayacareCronLogService;

class AlayacareCronLogController extends Controller
{
    protected $alayacareCronLogService;

    public function __construct(AlayacareCronLogService $alayacareCronLogService)
    {
        $this->middleware('auth');
        $this->alayacareCronLogService = $alayacareCronLogService;
    }

    public function index()
    {
        return view('alayacare_cron_log.index');
    }

    public function getList(Request $request)
    {
        $data['query'] = $this->alayacareCronLogService->getList($request->all());
        return view('alayacare_cron_log._table', $data);
    }

    public function view($id)
    {
        $record = $this->alayacareCronLogService->getById($id);

        if (!$record) {
            return response()->json([
                'success' => false,
                'message' => 'Record not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $record
        ]);
    }
}
