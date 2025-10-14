<?php

namespace App\Http\Requests\Auth;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class FormRegisterRequest extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|max:25',
            'surname' => 'required|max:25',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'gender' => ['required', Rule::in(['male', 'female'])],
            'date' => 'required|min:1|max:2|between:1,31',
            'month' => 'required|min:1|max:2|between:1,12',
            'year' => 'required|min:4|max:4',
        ];
    }
}
