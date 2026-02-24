<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:100'],
            'shortName' => ['required', 'string', 'size:2'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => is_string($this->name) ? strip_tags(trim($this->name)) : $this->name,
            'shortName' => is_string($this->shortName) ? strtoupper(strip_tags(trim($this->shortName))) : $this->shortName,
        ]);
    }

    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'type' => 'http://www.w3.org/Protocols/rfc2616/rfc2616-sec10.html',
            'title' => 'Unprocessable Entity',
            'status' => 422,
            'detail' => 'Failed Validation',
            'validation_messages' => $validator->errors()->toArray(),
        ], 422));
    }
}
