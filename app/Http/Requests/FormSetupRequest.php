<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FormSetupRequest extends FormRequest
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
                'title' => ['required', Rule::unique('form_setup')->whereNull('deleted_at')],
                'is_default' => 'required',

            );
        } else {
            $rules = array(
                'title' => ['required', Rule::unique('form_setup')->ignore($id)->whereNull('deleted_at')],
                'is_default' => 'required',
            );
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'This Title Already Added.',
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