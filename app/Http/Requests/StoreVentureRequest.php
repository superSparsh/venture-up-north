<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentureRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:160', 'unique:ventures'],
            'owner_guest.name' => ['required_without:owner_user_id', 'string', 'max:120'],
            'owner_guest.email' => ['nullable', 'email'],
            'owner_user_id' => ['nullable', 'integer', 'exists:users,id'],
            'visibility' => ['nullable', 'in:public,unlisted,private'],
            'heroImage' => ['nullable', 'image', 'max:5120'],
            'socialImage' => ['nullable', 'image', 'max:5120'],
            'summary' => ['nullable', 'string', 'max:500'],

            'days' => ['array'],
            'days.*.title' => ['required', 'string', 'max:80'],
            'days.*.index' => ['required', 'integer', 'min:1'],

            'items' => ['array'],
            'items.*.day_index' => ['required', 'integer', 'min:1'],
            'items.*.position' => ['required', 'integer', 'min:1'],
            'items.*.item_type' => ['required', 'in:event,experience,tour,listing,town'],
            'items.*.item_id' => ['required', 'integer'],
            'items.*.source_url' => ['required', 'string', 'max:2048'],
            'items.*.cat_source_url' => ['nullable', 'string', 'max:2048'],
            'items.*.title' => ['required', 'string', 'max:200'],
            'items.*.image_url' => ['nullable', 'string', 'max:2048'],
            'items.*.tags' => ['nullable', 'array'],
            'items.*.lat' => ['nullable', 'numeric'],
            'items.*.lng' => ['nullable', 'numeric'],
            'items.*.payload' => ['nullable', 'array'],
        ];
    }
}
