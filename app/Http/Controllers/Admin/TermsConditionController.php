<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TermsCondition;
use App\Models\TermsConditionEvents;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Validator;

class TermsConditionController extends Controller
{
    public function edit()
    {
        $tc = TermsCondition::for('contributor_submission')
            ?? new TermsCondition(['context' => 'contributor_submission', 'boxes' => []]);


        return Inertia::render('Admin/Terms/Edit', [
            'terms' => [
                'body'      => $tc->body,
                'boxes'     => $tc->boxes ?? [],
                'is_active' => (bool) $tc->is_active,
            ],
        ]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'boxes' => ['array'],
            'boxes.*.enabled' => ['boolean'],
            'boxes.*.required' => ['boolean'],
            'boxes.*.label' => ['nullable', 'string', 'max:255'],
        ]);

        $validator->after(function ($v) use ($request) {
            $boxes = collect($request->input('boxes', []));

            // Only enabled ones are counted toward the 3 limit
            $enabled = $boxes->filter(fn($b) => filter_var($b['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN));

            if ($enabled->count() > 3) {
                $v->errors()->add('boxes', 'You can enable at most 3 checkboxes.');
            }

            // Ensure label is present when enabled
            $enabled->each(function ($b, $i) use ($v) {
                $label = trim($b['label'] ?? '');
                if ($label === '') {
                    $v->errors()->add("boxes.$i.label", 'Label is required for enabled boxes.');
                }
            });
        });

        $data = $validator->validate();

        // Normalize boxes: trim labels, required=false when not enabled
        $boxes = collect($data['boxes'] ?? [])
            ->map(function ($b) {
                $enabled = filter_var($b['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
                return [
                    'label'    => trim((string)($b['label'] ?? '')),
                    'enabled'  => $enabled,
                    'required' => $enabled ? (bool)($b['required'] ?? false) : false,
                ];
            })
            // Keep even disabled rows so admin can toggle later (but drop completely empty ones)
            ->filter(fn($b) => $b['label'] !== '' || $b['enabled'] || $b['required'])
            ->values()
            ->all();

        $tc = TermsCondition::firstOrNew(['context' => 'contributor_submission']);
        $tc->body      = $data['body'] ?? null;
        $tc->is_active = $data['is_active'] ?? true;
        $tc->boxes     = $boxes;
        $tc->updated_by = $request->user()->id;
        $tc->save();

        return back()->with('success', 'Terms & Conditions updated.');
    }

    public function editEvent()
    {
        $tc = TermsConditionEvents::for('contributor_submission')
            ?? new TermsConditionEvents(['context' => 'contributor_submission', 'boxes' => []]);


        return Inertia::render('Admin/Terms/EditEvent', [
            'terms' => [
                'body'      => $tc->body,
                'boxes'     => $tc->boxes ?? [],
                'is_active' => (bool) $tc->is_active,
            ],
        ]);
    }

    public function updateEvent(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'body' => ['nullable', 'string'],
            'is_active' => ['boolean'],
            'boxes' => ['array'],
            'boxes.*.enabled' => ['boolean'],
            'boxes.*.required' => ['boolean'],
            'boxes.*.label' => ['nullable', 'string', 'max:255'],
        ]);

        $validator->after(function ($v) use ($request) {
            $boxes = collect($request->input('boxes', []));

            // Only enabled ones are counted toward the 3 limit
            $enabled = $boxes->filter(fn($b) => filter_var($b['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN));

            if ($enabled->count() > 3) {
                $v->errors()->add('boxes', 'You can enable at most 3 checkboxes.');
            }

            // Ensure label is present when enabled
            $enabled->each(function ($b, $i) use ($v) {
                $label = trim($b['label'] ?? '');
                if ($label === '') {
                    $v->errors()->add("boxes.$i.label", 'Label is required for enabled boxes.');
                }
            });
        });

        $data = $validator->validate();

        // Normalize boxes: trim labels, required=false when not enabled
        $boxes = collect($data['boxes'] ?? [])
            ->map(function ($b) {
                $enabled = filter_var($b['enabled'] ?? false, FILTER_VALIDATE_BOOLEAN);
                return [
                    'label'    => trim((string)($b['label'] ?? '')),
                    'enabled'  => $enabled,
                    'required' => $enabled ? (bool)($b['required'] ?? false) : false,
                ];
            })
            // Keep even disabled rows so admin can toggle later (but drop completely empty ones)
            ->filter(fn($b) => $b['label'] !== '' || $b['enabled'] || $b['required'])
            ->values()
            ->all();

        $tc = TermsConditionEvents::firstOrNew(['context' => 'contributor_submission']);
        $tc->body      = $data['body'] ?? null;
        $tc->is_active = $data['is_active'] ?? true;
        $tc->boxes     = $boxes;
        $tc->updated_by = $request->user()->id;
        $tc->save();

        return back()->with('success', 'Terms & Conditions updated.');
    }
}
