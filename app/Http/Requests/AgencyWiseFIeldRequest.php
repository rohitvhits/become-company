<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AgencyWiseFIeldRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $agency_id = $this->input('agency_id');
        $field_id = $this->input('field_id');
        $form_id = $this->input('form_id');
        
        return [
            'field_id' => [
                'required',
                'array',
                Rule::unique('agency_masters')
                    ->where(function ($query) use ($agency_id, $form_id, $field_id) {
                        if (!empty($form_id) && !empty($agency_id)) {
                            return $query
                                ->where('agency_id', $agency_id)
                                ->where('form_id', $form_id)
                                ->where('field_id', $field_id)
                                ->whereNull('deleted_at');
                        }else if (!empty($form_id) && empty($agency_id)){
                            return $query
                            ->where('form_id', $form_id)
                            ->where('field_id', $field_id)
                            ->whereNull('deleted_at');
                        } else {
                            return $query
                                ->where('agency_id', $agency_id)
                                ->where('field_id', $field_id)
                                ->whereNull('form_id')
                                ->whereNull('deleted_at');
                        }
                    })
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'field_id.unique' => 'This Field Already Added.',
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'errors' => $validator->errors()->all()[0],
                'status' => false,
            ], 422)
        );
    }
}