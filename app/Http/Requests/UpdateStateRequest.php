<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class UpdateStateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'min:3', 'max:100'],
            'shortName' => ['sometimes', 'string', 'size:2'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('name') && is_string($this->name)) {
            $this->merge(['name' => strip_tags(trim($this->name))]);
        }
        if ($this->has('shortName') && is_string($this->shortName)) {
            $this->merge(['shortName' => strtoupper(strip_tags(trim($this->shortName)))]);
        }
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
