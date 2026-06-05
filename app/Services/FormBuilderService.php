<?php

namespace App\Services;

use App\Model\AgencyForm;
use App\Model\AgencyMaster;
use App\Model\Doctor;
use App\Model\FieldMaster;
use App\Model\FormSetup;
use App\Model\Patient;
use App\Model\PatientCustomData;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FormBuilderService
{
    public function getFormFieldsForAgency($agencyId, $formId = "")
    {
        if ($formId != "") {
            $formSetupData = FormSetup::where('id', $formId)->get();

            if ($formSetupData[0]['is_default'] == 1) {
                $agencyMasterData = AgencyMaster::with('formGroup')->whereNotNull('form_id')->get();
            } else {
                $agencyMasterData = AgencyMaster::with('formGroup')
                    ->where('agency_id', $agencyId)
                    ->where('form_id', $formId)
                    ->get();
            }
        } else {
            $agencyMasterData = AgencyMaster::with('formGroup')
                ->where('agency_id', $agencyId)
                ->whereNull('form_id')
                ->get();
        }
        if ($formId != "") {
            $agencyMasterData = $agencyMasterData->where('form_id', $formId);
        }

        $fieldIds = $agencyMasterData->pluck('field_id');

        $formFields = FieldMaster::whereIn('id', $fieldIds);

        if (!empty($fieldIds->toArray())) {
            $formFields->orderByRaw(DB::raw("FIELD(id, " . implode(',', $fieldIds->toArray()) . ")"));
        }

        $formFields = $formFields->get()->map(function ($field) use ($agencyMasterData) {
            $agency = $agencyMasterData->firstWhere('field_id', $field->id);

            if ($agency && $agency->formGroup) {
                $field->form_group_title = $agency->formGroup->title;
                $field->form_group_id = $agency->formGroup->id;
            }

            return $field;
        });

        return $formFields;
    }

    public function getFormSetupForAgency($agencyId)
    {
        $formSetupData = FormSetup::with('agencyValue:id,agency_name')
            ->where('agency', $agencyId)
            ->orWhere('is_default', '1')
            ->orderBy('id', 'DESC')
            ->get();

        return $formSetupData;
    }

    public function deleteAgencyMasterAndField($agencyId, $fieldId, $formId)
    {
        if (!empty($formId) && $agencyId) {
            AgencyMaster::where('agency_id', $agencyId)->where('field_id', $fieldId)->where('form_id', $formId)->delete();
            FieldMaster::where('custom', 'Agency')->where('id', $fieldId)->delete();
            PatientCustomData::where('agency_id', $agencyId)->where('field_id', $fieldId)->delete();
        } elseif (empty($agencyId) && $formId) {
            AgencyMaster::where('field_id', $fieldId)->where('form_id', $formId)->delete();
            FieldMaster::where('custom', 'Custom')->where('id', $fieldId)->delete();
            PatientCustomData::where('field_id', $fieldId)->delete();
        } else {
            AgencyMaster::where('agency_id', $agencyId)->where('field_id', $fieldId)->whereNull('form_id')->delete();
            FieldMaster::where('custom', 'Agency')->where('id', $fieldId)->delete();
            PatientCustomData::where('agency_id', $agencyId)->where('field_id', $fieldId)->delete();
        }

        return true;
    }

    public function getAgencyWiseField($agencyId)
    {
        return AgencyMaster::with('agency:id,agency_name,delete_flag', 'fields:id,label,custom,type,size,options,set_character_limit,show_in_portal')->where('agency_id', $agencyId)->get();
    }

    public function getAgencyWiseFieldWithoutFormId($agencyId)
    {
        return AgencyMaster::with('agency:id,agency_name,delete_flag', 'fields:id,label,custom,type,size,options,set_character_limit,show_in_portal')->where('agency_id', $agencyId)->whereNull('form_id')->orderBy('sort_id', 'ASC')->get();
    }

    public function getAgencyAllForm($agencyId)
    {
        return FormSetup::with(['agencyValue:id,agency_name', 'agencyMaster.fields', 'getTemplateById:id,custom_form_id,template_name'])->where('agency', $agencyId)->orWhere('is_default', '1')
            ->get();
    }

    public function getTypeWiseAgencyAllForm($agencyId, $type)
    {
        $formType = ($type === 'Caregiver') ? '0' : '1';

        $query = FormSetup::with(['agencyValue:id,agency_name', 'agencyMaster.fields', 'getTemplateById:id,custom_form_id,template_name'])
            ->where('form_type', $formType)
            ->where(function ($query) use ($agencyId) {
                $query->orWhere('agency', $agencyId)
                    ->orWhere('is_default', '1');
            })->get();

        return $query;
    }

    public function getAgencyForm($agencyId, $patientId)
    {
        return AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster' => function ($query) {
            $query->orderBy('sort_id', 'ASC');
        }])
            ->has('forms')->where('agency_id', $agencyId)->where('patient_id', $patientId)
            ->get();
    }

    public function getAllDoctor()
    {
        return Doctor::whereNull('deleted_at')->get();
    }

    public function getSubmitDataByAgencyIdPatientID($agencyId = "", $agencyForm_Id = "", $patientId = "")
    {
        return PatientCustomData::where('patient_id', $patientId)->where('agency_id', $agencyId)->where('agency_form_id', $agencyForm_Id)->get();
    }
    public function getAdvanceSubmitDataByAgencyIdPatientID($agencyId, $patientId)
    {
        return PatientCustomData::where('patient_id', $patientId)->whereNull('form_id')->where('agency_id', $agencyId)->get();
    }

    public function storePatientCustomData($request)
    {
        if (isset($request->fields)) {
            if (!is_array($request->fields)) {
                return response()->json(['error' => 'Invalid fields data'], 400);
            }
        }

        $responses = [];

        if (isset($request->fields)) {
            foreach ($request->fields as $key => $value) {
                $fieldMaster = FieldMaster::find($key);

                if (!$fieldMaster) {
                    $responses[$key] = ['status' => 'error', 'message' => 'Field not found'];
                    continue;
                }

                $field_type = $fieldMaster->type;

                if ($field_type === 'file') {
                    if ($value) {
                        $file = $value;
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('uploads', $fileName, 'public');
                        $value = $filePath;
                    }
                } else if ($field_type === 'checkbox') {
                    if ($value) {
                        if (is_array($value)) {
                            $value = serialize($value);
                        }
                    } else {
                        $value = null;
                    }
                }

                $customData = [
                    'value' => $value,
                ];

                $result = PatientCustomData::updateOrCreate(
                    [
                        'field_id' => $key,
                        'patient_id' => $request->patient_id,
                        'agency_id' => $request->agency_id,
                        'form_id' => null,
                    ],
                    $customData
                );

                $responses[$key] = [
                    'status' => 'success',
                    'message' => 'Data saved successfully',
                    'type' => $field_type,
                    'data' => $result
                ];
            }
        }

        return response()->json([
            'responses' => $responses
        ]);
    }

    public function savePatientCustomData($request)
    {
        // dd($request->all());
        if (isset($request->fields)) {
            if (!is_array($request->fields)) {
                return response()->json(['error' => 'Invalid fields data'], 400);
            }
        }

        $responses = [];
        $doctorId = $request->input('doctor_id');
        $mark_as_completed = $request->input('mark_as_completed');

        if (isset($request->fields)) {
            foreach ($request->fields as $key => $value) {
                $fieldMaster = FieldMaster::find($key);

                if (!$fieldMaster) {
                    $responses[$key] = ['status' => 'error', 'message' => 'Field not found'];
                    continue;
                }

                $field_type = $fieldMaster->type;

                if ($field_type === 'file') {
                    if ($value) {
                        $file = $value;
                        $fileName = time() . '_' . $file->getClientOriginalName();
                        $filePath = $file->storeAs('uploads', $fileName, 'public');
                        $value = $filePath;
                    }
                } else if ($field_type === 'checkbox') {
                    if ($value) {
                        if (is_array($value)) {
                            $value = serialize($value);
                        }
                    } else {
                        $value = null;
                    }
                }

                $customData = [
                    'value' => $value,
                    'form_id' => $request->form_id ?? null,
                    'agency_form_id' => $request->formId,
                    'patient_id' => $request->patient_id,
                    'agency_id' => $request->agency_id,
                ];

                $result = PatientCustomData::updateOrCreate(
                    [
                        'field_id' => $key,
                        'agency_form_id' => $request->formId
                    ],
                    $customData
                );

                $responses[$key] = [
                    'status' => 'success',
                    'message' => 'Data saved successfully',
                    'type' => $field_type,
                    'data' => $result
                ];
            }
        }

        if ($doctorId) {
            $agencyForm = AgencyForm::with('doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by')->where('id', $request->formId)
                ->where('form_id', $request->form_id)
                ->where('patient_id', $request->patient_id)
                ->where('agency_id', $request->agency_id)
                ->first();

            if ($agencyForm) {
                $agencyForm->doctor_id = $doctorId;
                $agencyForm->updated_by = auth()->user()->id;
                $agencyForm->save();

                $doctor = Doctor::find($doctorId);
                $doctorName = $doctor ? $doctor->full_name : 'Unknown';

                $responses['doctor_id'] = [
                    'doctor_id' => $agencyForm->doctor_id,
                    'doctor_name' => $doctorName,
                    'status' => 'success',
                    'message' => 'Doctor ID updated successfully'
                ];
            } else {
                $responses['doctor_id'] = [
                    'status' => 'error',
                    'message' => 'AgencyForm not found'
                ];
            }
        }
        if ($mark_as_completed) {
            $agencyForm = AgencyForm::where('id', $request->formId)->first();

            if ($agencyForm) {
                $agencyForm->mark_as_completed = $mark_as_completed;
                $agencyForm->mark_as_completed_date = date('Y-m-d H:i:s');
                $agencyForm->mark_as_completed_by = auth()->user()->id;
                $agencyForm->save();

                $responses['mark_as_completed_data'] = [
                    'mark_as_completed' => $agencyForm->mark_as_completed,
                    'status' => 'success',
                    'message' => 'Form marked as completed'
                ];
            } else {
                $responses['mark_as_completed_data'] = [
                    'status' => 'error',
                    'message' => 'AgencyForm not found'
                ];
            }
        }

        return response()->json([
            'form_id' => $request->form_id,
            'responses' => $responses
        ]);
    }

    public function getAgencyAllFormTableList($agencyId, $patientId, $status)
    {
        if ($status === 'completed') {
            $data = AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name','doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster.fields', 'users:id,first_name,last_name,delete_flag','userMarkAsComplatedDetails:id,first_name,last_name,delete_flag', 'agencyMaster:id,agency_id,field_id,form_id,sort_id','patient' => function ($query) {
                $query->orderBy('id', 'ASC');
            }])
                ->has('forms')->where('agency_id', $agencyId)->where('patient_id', $patientId)
                ->orderBy('id', 'DESC')->where('mark_as_completed', "1")->get();
        }elseif ($status === 'pending') {
            $data = AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster.fields', 'users:id,first_name,last_name,delete_flag','userMarkAsComplatedDetails:id,first_name,last_name,delete_flag', 'agencyMaster:id,agency_id,field_id,form_id,sort_id','patient' => function ($query) {
                $query->orderBy('id', 'ASC');
            }])
                ->has('forms')->where('agency_id', $agencyId)->where('patient_id', $patientId)
                ->orderBy('id', 'DESC')->where('mark_as_completed', "0")->get();
        }else{
            $data = AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster.fields', 'users:id,first_name,last_name,delete_flag','userMarkAsComplatedDetails:id,first_name,last_name,delete_flag', 'agencyMaster:id,agency_id,field_id,form_id,sort_id','patient' => function ($query) {
                $query->orderBy('id', 'ASC');
            }])
                ->has('forms')->where('agency_id', $agencyId)->where('patient_id', $patientId)
                ->orderBy('id', 'DESC')->get();
        }


        $completedCount = AgencyForm::where('agency_id', $agencyId)
            ->where('patient_id', $patientId)
            ->where('mark_as_completed', "1")
            ->count();

        $pendingCount = AgencyForm::where('agency_id', $agencyId)
            ->where('patient_id', $patientId)
            ->where('mark_as_completed', "0")
            ->count();

        return [
            'data' => $data,
            'completed_count' => $completedCount,
            'pending_count' => $pendingCount
        ];
    }

    public function agencyAllFormData($agencyFormId)
    {
        return AgencyForm::with(['forms:id,title,is_default,agency,form_type,sort_id', 'agencies:id,agency_name', 'doctors:id,full_name,phone,email,gender,remarks,deleted_flag,license,address,city,state,zipcode,place_of_examination,date_of_examination,created_by,updated_by', 'getPatientData:id,agency_id,patient_id,field_id,value,form_id,agency_form_id', 'templateById:id,template_name,custom_form_id,del_flag,agency_id', 'agencyMaster.fields', 'users:id,first_name,last_name,delete_flag', 'agencyMaster:id,agency_id,field_id,form_id,sort_id,form_group_id'])->where('id', $agencyFormId)->first();
    }

    public function getByPatientDetails($patientId)
    {
        return Patient::where('id', $patientId)->first();
    }

    public function getFormGroupTitles($agencyId)
    {
        $agencyMasters = AgencyMaster::with('formGroup')->where('agency_id', $agencyId)->get();
        return $agencyMasters->pluck('formGroup.title', 'id');
    }
}
