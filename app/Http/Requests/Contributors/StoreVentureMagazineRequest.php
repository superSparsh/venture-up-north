<?php

namespace App\Http\Requests\Contributors;

use App\Models\TermsCondition;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreVentureMagazineRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255|unique:venture_magazines,title',
            'real_contributor_id' => 'required|exists:contributors,id',
            'hero_image' => 'required|image|max:2048|dimensions:min_width=738,min_height=500',
            'big_hero_image' => 'required|image|max:5120|dimensions:min_width=1200,min_height=600',
            'content' => 'required|array',
            'status' => 'required|in:draft,pending,approved,rejected',
            'is_published' => 'boolean',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:500',
            'seo_image' => 'nullable|image|max:200|dimensions:min_width=1200,min_height=630',
            'agreements' => ['array'],
        ];

        $terms = TermsCondition::for('contributor_submission');
        if ($terms && $terms->is_active && is_array($terms->boxes)) {
            foreach ($terms->boxes as $i => $box) {
                if (!($box['enabled'] ?? false)) continue;
                // Require accepted if required=true
                if ($box['required'] ?? false) {
                    $rules["agreements.$i"] = ['accepted']; // accepted means true/"on"/"1"
                } else {
                    $rules["agreements.$i"] = ['nullable', 'boolean'];
                }
            }
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        // ensure agreements indices are booleans
        $agreements = (array) $this->input('agreements', []);
        $this->merge(['agreements' => array_map(fn($v) => filter_var($v, FILTER_VALIDATE_BOOLEAN), $agreements)]);
    }
}
