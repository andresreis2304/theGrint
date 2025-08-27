<?php

namespace App\Http\Requests;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAnuncioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // handled by middleware
    }

    public function rules(): array
    {
        $allowed = [
            'new', 'used', 'refurbished', 'like_new',
            'nuevo', 'usado', 'restaurado', 'como_nuevo',
        ];

        return [
            // Title: only A–Z, a–z and spaces
            'title' => ['required','regex:/^[A-Za-z ]+$/','max:500'],
            'price' => ['required','numeric','min:0'],
            'condition' => ['required','string', Rule::in([
                'new','used','refurbished','like_new',
                'nuevo','usado','reacondicionado','restaurado','como_nuevo','como nuevo',
            ])],
            'description' => ['nullable','string'],
            'end_date' => ['required','date','after:now'],
            'category_id' => ['required','integer','exists:categoria,categoria_id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.regex' => 'Title may only contain letters A-Z and spaces.',
            'condition.in' => 'Condition must be one of: new, used, refurbished, like_new.',
            'end_date.after' => 'End date must be in the future.',
        ];
    }
}
