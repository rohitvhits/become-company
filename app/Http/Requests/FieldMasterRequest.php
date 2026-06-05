<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FieldMasterRequest extends FormRequest
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
        $id = $this->request->get('id');
        if (empty($id)) {
            $rules = array(
                'label' => ['required', Rule::unique('field_masters')->whereNull('custom')->whereNull('deleted_at')],
                'type' => 'required',
                'options.*' => 'nullable|array'

            );
        } else {
            $rules = array(
                'label' => ['required', Rule::unique('field_masters')->ignore($id)->whereNull('custom')->whereNull('deleted_at')],
                'type' => 'required',
                'options.*' => 'nullable|array'
            );
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'label.unique' => 'This Label Already Added.',
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