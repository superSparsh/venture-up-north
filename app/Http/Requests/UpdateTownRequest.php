<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTownRequest extends FormRequest
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
        $townId = $this->route('town')->id;

        return [
            'name' => 'required|string|max:255|unique:towns,name,' . $townId,
            'summary' => 'required|string|max:500',
            'description' => 'required|array',
            'description.blocks' => 'required|array|min:1',
            'hero_image' => 'nullable|image|max:2048|dimensions:min_width=738,min_height=500',
            'big_hero_image' => 'nullable|image|max:5120|dimensions:min_width=1200,min_height=600',
            'seo_image' => 'nullable|image|max:200|dimensions:min_width=1200,min_height=630',

            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'rezdy_url' => 'required|url|max:500',

            'is_active' => 'boolean',
        ];
    }
}
