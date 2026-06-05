<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\URL;

class RedirectionEsignController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth', ['except' => ['ViewDocusign', 'emailSignShow', 'thankyou', 'ViewDocusignNew']]);
    }

    public function ViewDocusign(Request $request, $id)
    {
        return redirect('esign/docusign/view/' . $id);
    }

    public function thankyou()
    {
        return view('thankyouesign');
    }

    public function emailSignShow(Request $request, $docId)
    {
        return redirect('esign/nye/' . $docId.'?id='.$request->id);
    }

    public function ViewDocusignNew(Request $request, $id)
    {
        return redirect('esign/docusign/viewNew/' . $id);
    }
}
