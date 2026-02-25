<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMemberRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:members,email',
            'contact' => 'required|string|max:20',
            'location' => 'required|string|max:255',
            'occupation' => 'required|string|max:255',
            'roles' => 'required|array|min:1',
            'roles.*' => 'required|in:client,shareholder,cashier,td,ceo',
            'password' => 'required|min:6',
            'savings' => 'nullable|numeric|min:0',
            'balance' => 'nullable|numeric|min:0',
            'profile_picture' => 'nullable',
        ];
    }

    public function messages()
    {
        return [
            'profile_picture.image' => 'The profile picture must be an image file.',
            'profile_picture.mimes' => 'The profile picture must be a file of type: jpeg, jpg, png, gif, webp.',
            'profile_picture.max' => 'The profile picture may not be greater than 5MB.',
            'profile_picture.dimensions' => 'The profile picture must be between 100x100 and 2000x2000 pixels.',
        ];
    }
}
