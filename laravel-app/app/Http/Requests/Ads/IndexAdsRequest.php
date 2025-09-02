<?php
namespace App\Http\Requests\Ads;

use Illuminate\Foundation\Http\FormRequest;
use App\Domain\Ads\Condition;

class IndexAdsRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    protected function prepareForValidation(): void
    {
        if ($this->has('estado')) {
            $this->merge(['estado' => Condition::normalize((string)$this->input('estado'))]);
        }
        $this->merge(['mostrar_todos' => $this->boolean('mostrar_todos')]);
    }
    public function rules(): array
    {
        return [
            'price_min' => ['nullable','numeric','min:0'],
            'price_max' => ['nullable','numeric','gte:price_min'],
            'category_id' => ['nullable','string'],
            'estado' => ['nullable','string', 'in:nuevo,usado,restaurado,como_nuevo'],
            'q' => ['nullable','string'],
            'mostrar_todos' => ['nullable','boolean'],
            'per_page' => ['nullable','integer','min:1','max:100'],
        ];
    }
}