<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Model\Country;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $name = $data['name'] = request('name');
        $status = $data['status'] = request('status');
        $data['list'] = Country::getAllData($name, $status);
        return view('country.index', $data);
    }
    public function create(Request $request)
    {
        return vieW('country.add');
    }
    public function store(Request $request)
    {
        $user = auth()->user();
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required',


        ]);
        if ($validator->fails()) {
            return redirect("/country/add")
                ->withErrors($validator, 'addCountry')
                ->withInput();
        } else {
            $name = request('name');
            $status = request('status');

            $data = array(
                'name' => $name,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
            );

            $ins_test = new Country($data);
            $ins_test->save();
            $insert = $ins_test->id;

            if ($insert) {
                Session::flash('success', 'Country added successfully.');
                return redirect('/country');
            } else {
                Session::flash('error', 'Sorry, something went wrong. Please try again.');
                return redirect('/country');
            }
        }
    }
    public function edit($id)
    {
        $data['id'] = $id;
        $data['data'] = Country::where("id", $id)->first();
        return view('country/edit', $data);
    }
    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $data['id'] = $id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required',


        ]);
        if ($validator->fails()) {
            return redirect("/country/edit/$id")
                ->withErrors($validator, 'edit_country')
                ->withInput();
        } else {
            $name = request('name');
            $status = request('status');
            $data = array(
                'name' => $name,
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s'),
                'updated_by' => $user->id,

            );
            $update = Country::where('id', $id)->update($data);
            Session::flash('success', 'Country update successfully.');
            return redirect('/country');
        }
    }
    public function delete($id)
    {

        $user = auth()->user();

        $data['id'] = $id;
        $update = Country::where('id', $id)->update(array('delflag' => 'Y', 'deleted_at' => date('Y-m-d H:i:s'), 'deleted_by' => $user->id));
        if ($update) {
            Session::flash('success', 'Country delete successfully.');
            return redirect('/country');
        } else {
            Session::flash('error', 'Sorry, something went wrong. Please try again.');
            return redirect('/country');
        }
    }
}
