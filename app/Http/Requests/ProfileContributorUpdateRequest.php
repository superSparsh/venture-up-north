<?php

namespace App\Http\Requests;

use App\Models\Author;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileContributorUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name'         => trim((string) $this->input('name')),
            'email'        => trim(strtolower((string) $this->input('email'))),
            'display_name' => trim((string) $this->input('display_name')),
        ]);
    }

    public function rules(): array
    {
        return [
            'name'  => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:255',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
            'photo' => ['nullable', 'image', 'max:2048', 'dimensions:min_width=738,min_height=500'],
            'display_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('authors', 'display_name')
                    ->ignore(optional($this->user()->contributor?->author)->id),
            ],
        ];
    }
}
