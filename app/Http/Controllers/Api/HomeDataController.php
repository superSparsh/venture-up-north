<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HomeSection;

class HomeDataController extends Controller
{
    public function index()
    {
        return HomeSection::where('is_active', true)
            ->orderBy('order')
            ->get()
            ->keyBy('slug')
            ->map(fn($section) => [
                'title' => $section->title,
                'content' => json_decode($section->content, true),
            ]);
    }
}
