<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;


class LinkPreviewController extends Controller
{
    public function fetch(Request $request)
    {
        $url = $request->input('url');
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['success' => 0, 'message' => 'Invalid URL']);
        }

        try {
            $html = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0'
            ])->get($url)->body();

            $crawler = new Crawler($html);

            $title = $crawler->filterXPath('//meta[@property="og:title"]')->attr('content') ?? '';
            $desc  = $crawler->filterXPath('//meta[@name="description"]')->attr('content') ?? '';
            $image = $crawler->filterXPath('//meta[@property="og:image"]')->attr('content') ?? '';

            return response()->json([
                'success' => 1,
                'meta' => [
                    'title' => $title,
                    'description' => $desc,
                    'image' => ['url' => $image]
                ]
            ]);
        } catch (\Throwable $e) {
            return response()->json(['success' => 0, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }
}
