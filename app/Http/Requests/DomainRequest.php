<?php

namespace App\Http\Requests;

use App\ValueObjects\Domain;
use Illuminate\Foundation\Http\FormRequest;

class DomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'domain' => 'required|string'
        ];
    }

    public function getDomain(): Domain
    {
        return new Domain((string) $this->input('domain'));
    }
}
