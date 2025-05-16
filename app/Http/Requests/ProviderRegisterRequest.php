<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProviderRegisterRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Дозволяємо всім користувачам (включно з неавторизованими) надсилати запит на реєстрацію
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email|max:255',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Поле "Ім’я" є обов’язковим.',
            'email.required' => 'Поле "Електронна пошта" є обов’язковим.',
            'email.email' => 'Введіть коректну електронну пошту.',
            'email.unique' => 'Ця електронна пошта вже зареєстрована.',
            'password.required' => 'Поле "Пароль" є обов’язковим.',
            'password.min' => 'Пароль має містити щонайменше 8 символів.',
            'password.confirmed' => 'Підтвердження пароля не збігається.',
        ];
    }
}