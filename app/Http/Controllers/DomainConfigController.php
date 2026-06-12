<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\DomainConfigService;
use App\Services\LogsService;
use App\Helpers\Utility;

class DomainConfigController extends Controller
{
    protected $domainConfigService;

    public function __construct(DomainConfigService $domainConfigService)
    {
        $this->middleware('auth');
        $this->domainConfigService = $domainConfigService;
    }

    public function index()
    {
        $data['menu'] = 'Company Master';
        $data['user'] = auth()->user();
        $data['configs'] = $this->domainConfigService->getAll();
        return view('domain_config.list', $data);
    }

    public function create()
    {
        $data['menu'] = 'Company Master';
        $data['config'] = null;
        return view('domain_config.form', $data);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'domain'       => 'required|string|max:255|unique:domain_configs,domain',
            'company_name' => 'nullable|string|max:255',
            'title'        => 'required|string|max:255',
            'login_bg'     => 'required|string|max:20',
            'theme_color'  => 'required|string|max:20',
            'logo_style'   => 'nullable|string',
            'login_image'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $logo    = $request->logo;
        $favicon = $request->favicon;

        if ($request->hasFile('logo_file')) {
            $logo = $this->domainConfigService->uploadFile($request->file('logo_file'));
        }
        if ($request->hasFile('favicon_file')) {
            $favicon = $this->domainConfigService->uploadFile($request->file('favicon_file'));
        }

        $saveData = [
            'domain'       => $request->domain,
            'company_name' => $request->company_name,
            'logo'         => $logo,
            'favicon'      => $favicon,
            'title'        => $request->title,
            'login_bg'     => $request->login_bg,
            'theme_color'  => $request->theme_color,
            'logo_style'   => $request->logo_style ?? 'width:100%;',
            'login_image'  => $request->login_image ?? 'img/pana.png',
            'created_by'   => auth()->id(),
        ];

        $record = $this->domainConfigService->save($saveData);

        $user = auth()->user();
        LogsService::save([
            'type'         => 'Add',
            'link'         => url('/domain-config'),
            'module'       => 'Company Master',
            'object_id'    => $record->id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' has added Company Master',
            'new_response' => serialize($saveData),
            'ip'           => Utility::getIP(),
        ]);

        return redirect()->route('domain-config.index')->with('success', 'Company Master created successfully.');
    }

    public function edit($id)
    {
        $data['menu'] = 'Company Master';
        $data['config'] = $this->domainConfigService->getDetailById($id);

        if (!$data['config']) {
            return redirect()->route('domain-config.index')->with('error', 'Company Master not found.');
        }

        return view('domain_config.form', $data);
    }

    public function update(Request $request, $id)
    {
        $config = $this->domainConfigService->getDetailById($id);

        if (!$config) {
            return redirect()->route('domain-config.index')->with('error', 'Company Master not found.');
        }

        $validator = Validator::make($request->all(), [
            'domain'       => 'required|string|max:255|unique:domain_configs,domain,' . $id,
            'company_name' => 'nullable|string|max:255',
            'title'        => 'required|string|max:255',
            'login_bg'     => 'required|string|max:20',
            'theme_color'  => 'required|string|max:20',
            'logo_style'   => 'nullable|string',
            'login_image'  => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $logo    = $request->logo    ?? $config->logo;
        $favicon = $request->favicon ?? $config->favicon;

        if ($request->hasFile('logo_file')) {
            $logo = $this->domainConfigService->uploadFile($request->file('logo_file'));
        }
        if ($request->hasFile('favicon_file')) {
            $favicon = $this->domainConfigService->uploadFile($request->file('favicon_file'));
        }

        $updateData = [
            'domain'       => $request->domain,
            'company_name' => $request->company_name,
            'logo'         => $logo,
            'favicon'      => $favicon,
            'title'        => $request->title,
            'login_bg'     => $request->login_bg,
            'theme_color'  => $request->theme_color,
            'logo_style'   => $request->logo_style ?? 'width:100%;',
            'login_image'  => $request->login_image ?? 'img/pana.png',
            'updated_by'   => auth()->id(),
        ];

        $this->domainConfigService->update($updateData, ['id' => $id]);

        $user = auth()->user();
        LogsService::save([
            'type'         => 'Update',
            'link'         => url('/domain-config/' . $id . '/edit'),
            'module'       => 'Company Master',
            'object_id'    => $id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' has updated Company Master',
            'old_response' => serialize($config->toArray()),
            'new_response' => serialize($updateData),
            'ip'           => Utility::getIP(),
        ]);

        return redirect()->route('domain-config.index')->with('success', 'Company Master updated successfully.');
    }

    public function destroy($id)
    {
        $config = $this->domainConfigService->getDetailById($id);

        $this->domainConfigService->delete($id);

        $user = auth()->user();
        LogsService::save([
            'type'         => 'Delete',
            'link'         => url('/domain-config/' . $id),
            'module'       => 'Company Master',
            'object_id'    => $id,
            'message'      => $user->first_name . ' ' . $user->last_name . ' has deleted Company Master',
            'new_response' => serialize($config ? $config->toArray() : []),
            'ip'           => Utility::getIP(),
        ]);

        return redirect()->route('domain-config.index')->with('success', 'Company Master deleted successfully.');
    }
}
