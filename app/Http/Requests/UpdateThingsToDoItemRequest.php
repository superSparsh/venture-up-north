<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateThingsToDoItemRequest extends FormRequest
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
        $itemId = $this->route('category_listing')->id;

        return [
            'title' => 'required|string|max:255|unique:things_to_do_items,title,' . $itemId,
            'category_id' => [
                'required',
                function ($attribute, $value, $fail) {
                    $categoryId = is_array($value) ? ($value['id'] ?? null) : $value;
                    if (!$categoryId || !\App\Models\ThingsToDoCategory::where('id', $categoryId)->exists()) {
                        $fail('The selected category is invalid.');
                    }
                },
            ],
            'summary' => 'required|string|max:500',
            'address' => 'nullable|string|max:500',
            'phone_number' => 'nullable|string|max:500',
            'email' => 'nullable|string|max:500',
            'location' => 'nullable|string',
            'video' => 'nullable|string',
            'content' => 'required|array',
            'content.blocks' => 'required|array|min:1',
            'opening_times' => 'nullable|array',
            'opening_times.blocks' => 'nullable|array|min:1',
            'image' => 'nullable|image|max:2048|dimensions:min_width=738,min_height=500',
            'big_image' => 'nullable|image|max:5120|dimensions:min_width=1200,min_height=600',
            'seo_image' => 'nullable|image|max:200|dimensions:min_width=1200,min_height=630',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
            'custom_fields' => 'nullable|array',
            'custom_fields.*.label' => 'required_with:custom_fields.*.value|string|max:255',
            'custom_fields.*.value' => 'nullable|string|max:1000',
            'custom_fields.*.show' => 'nullable|boolean',
            'custom_buttons' => 'nullable|array',
            'custom_buttons.*.label' => 'required_with:custom_buttons.*.value|string|max:255',
            'custom_buttons.*.value' => 'nullable|string|max:1000',
            'social_links' => 'nullable|array',
            'social_links.*.label' => 'required_with:social_links.*.value|string|max:255',
            'social_links.*.value' => 'nullable|string|max:1000',
        ];
    }
}
