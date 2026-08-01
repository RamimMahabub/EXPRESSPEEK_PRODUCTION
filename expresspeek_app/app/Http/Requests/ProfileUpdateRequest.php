<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'phone_country_code' => ['nullable', 'string', 'regex:/^\+[1-9]\d{0,3}$/'],
            'phone' => ['nullable', 'string', 'regex:/^[0-9\s().+-]{6,24}$/'],
            'avatar' => [
                'nullable',
                'image',
                'mimes:jpeg,jpg,png,webp',
                'max:8192',
                'dimensions:min_width=128,min_height=128,max_width=5000,max_height=5000',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'avatar.image' => 'Choose a valid image file.',
            'avatar.mimes' => 'The photo must be a JPG, PNG, or WebP image.',
            'avatar.max' => 'The photo must be 8 MB or smaller.',
            'avatar.dimensions' => 'Use a photo between 128×128 and 5000×5000 pixels.',
        ];
    }
}
