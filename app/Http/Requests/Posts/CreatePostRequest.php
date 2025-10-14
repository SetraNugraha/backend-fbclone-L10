<?php

namespace App\Http\Requests\Posts;

use App\Http\Requests\ApiRequest;

class CreatePostRequest extends ApiRequest
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
            'body' => 'required|string|min:1|max:500',
            'post_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
        ];
    }

    public function messages(): array
    {
        return [
            'body.required' => 'The post body is required.',
            'body.min' => 'The post body must be at least :min characters.',
            'post_image.image' => 'The uploaded file must be an image.',
            'post_image.mimes' => 'Allowed image formats are: jpg, jpeg, png.',
            'post_image.max' => 'Maximum image size is 5MB.',
        ];
    }
}
