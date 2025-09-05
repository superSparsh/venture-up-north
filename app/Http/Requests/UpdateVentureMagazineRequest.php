<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateVentureMagazineRequest extends FormRequest
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
        $ventureMagazine = $this->route('ventureMagazine') ?? $this->route('venture_magazine');
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                Rule::unique('venture_magazines', 'title')->ignore(optional($ventureMagazine)->id),
            ],

            'contributor_id' => 'required|exists:team_members,id',
            'content' => 'required|array',

            'hero_image' => 'nullable|image|max:2048|dimensions:min_width=738,min_height=500',
            'big_hero_image' => 'nullable|image|max:5120|dimensions:min_width=1200,min_height=600',
            'seo_image' => 'nullable|image|max:200|dimensions:min_width=1200,min_height=630',

            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'is_published' => 'boolean',
        ];
    }
}
