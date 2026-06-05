<?php

namespace App\Http\Controllers;

use App\Model\AppToken;
use App\Services\AppTokenService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use App\Services\LogsService;
use App\Helpers\Utility;
use App\Master;
class AppTokenController extends Controller
{
    protected $appTokenService;
    protected $logService;
    protected const MODULE_NAME = "App Token";
    protected const MODULE_LINK = "app-tokens";
    public function __construct(AppTokenService $appTokenService,LogsService $logService)
    {
        $this->middleware(
            'permission:app-token-generate|create-app-token-generate|edit-app-token-generate|delete-app-token-generate',
            ['only' => ['index', 'store']]
        );
        $this->middleware(
            'permission:app-token-generate',
            ['only' => ['index', 'ajaxList']]
        );
        $this->middleware(
            'permission:create-app-token-generate',
            ['only' => ['store']]
        );
        $this->middleware(
            'permission:edit-app-token-generate',
            ['only' => ['json', 'update']]
        );
        $this->middleware(
            'permission:delete-app-token-generate',
            ['only' => ['destroy']]
        );

        $this->middleware('auth');
        $this->appTokenService = $appTokenService;
        $this->logService = $logService;
    }

    /**
     * Display a listing of app tokens.
     */
    public function index()
    {
        $data['menu'] = "user";
        $data['user'] = $user = auth()->user();

        if ($user['user_type_fk'] != 184) {
            return abort(404);
        }

        if ($user['agency_fk'] != "") {
            return abort(404);
        }

        $data['master_list'] = Master::getAllDataByMasterTypeFk(array(34));
        return view('app_tokens.index', $data);
    }

    /**
     * Store a newly created app token.
     */
    public function store(Request $request): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'app_name' => 'required',
            'referral_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status'    => 0,
                'data'      => [],
            ], 422);
        }

        try {
            $appToken = $this->appTokenService->create($request->all());
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Add App Token',
                'link' => url('/'.self::MODULE_LINK),
                'module' => self::MODULE_NAME,
                'object_id' => $appToken->id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has added app token',
                'new_response' => serialize($request->all()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json([
                'success'   => true,
                'error_msg' => 'App token created successfully!',
                'data'      => $appToken,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'error_msg' => 'Failed to create app token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Update the specified app token.
     */
    public function update(Request $request, AppToken $appToken): JsonResponse
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'app_name' => 'required',
            'referral_type' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'error_msg' => $validator->errors()->all()[0],
                'status'    => 0,
                'data'      => [],
            ], 422);
        }

        try {
            $oldResponse = $this->appTokenService->getDetailsById($appToken->id);
            $updatedToken = $this->appTokenService->update($appToken, $request->all());
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Add App Token',
                'link' => url('/'.self::MODULE_LINK),
                'module' => self::MODULE_NAME,
                'object_id' => $appToken->id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has update app token',
                'old_response' => serialize($oldResponse->toArray()),
                'new_response' => serialize($request->all()),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json([
                'success' => true,
                'message' => 'App token updated successfully!',
                'data'    => $updatedToken,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update app token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified app token.
     */
    public function destroy(AppToken $appToken): JsonResponse
    {
        $user = auth()->user();
        try {
            $this->appTokenService->delete($appToken);
            $ipaddress = Utility::getIP();
            $insertLog = [
                'type' => 'Delete App Token',
                'link' => url('/'.self::MODULE_LINK),
                'module' => self::MODULE_NAME,
                'object_id' => $appToken->id,
                'message' => $user->first_name . ' ' . $user->last_name . ' has delete app token',
                'new_response' => serialize($appToken),
                'ip' => $ipaddress,
            ];
            LogsService::save($insertLog);
            return response()->json([
                'success'   => true,
                'error_msg' => 'App token deleted successfully!',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success'   => false,
                'error_msg' => 'Failed to delete app token: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get app token data for edit modal.
     */
    public function json(AppToken $appToken): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => $appToken,
        ]);
    }

    public function ajaxList(Request $request)
    {
        $page = $request->page;
        $appTokens = $this->appTokenService->getList($request->all());

        return view(
            'app_tokens.app_tokens_ajax_list',
            compact('appTokens', 'page')
        );
    }
}
