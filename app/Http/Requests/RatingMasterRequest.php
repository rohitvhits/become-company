<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RatingMasterRequest extends FormRequest
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
                'title' => ['required', Rule::unique('rating_master')->whereNull('deleted_at')],
                'type' => 'required',

            );
        } else {
            $rules = array(
                'title' => ['required', Rule::unique('rating_master')->ignore($id)->whereNull('deleted_at')],
                'type' => 'required',
            );
        }
        return $rules;
    }

    public function messages(): array
    {
        return [
            'title.unique' => 'This Label Already Added.',
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