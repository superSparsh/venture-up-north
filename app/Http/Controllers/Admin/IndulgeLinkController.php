<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\IndulgeLink;
use Illuminate\Http\Request;
use Inertia\Inertia;

class IndulgeLinkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $links = IndulgeLink::orderBy('sort_order')->paginate(10);
        return Inertia::render('Admin/IndulgeLinks/Index', compact('links'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Admin/IndulgeLinks/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        IndulgeLink::create($request->all());
        return redirect()->route('admin.indulge-links.index')->with('success', 'Link created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IndulgeLink $indulgeLink)
    {
        return Inertia::render('Admin/IndulgeLinks/Edit', compact('indulgeLink'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, IndulgeLink $indulgeLink)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'url' => 'required|url',
            'is_active' => 'boolean',
            'sort_order' => 'integer'
        ]);

        $indulgeLink->update($request->all());
        return redirect()->route('admin.indulge-links.index')->with('success', 'Link updated.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(IndulgeLink $indulgeLink)
    {
        $indulgeLink->delete();
        return back()->with('success', 'Link deleted.');
    }

    public function toggleStatus(IndulgeLink $link)
    {
        $link->update(['is_active' => !$link->is_active]);

        return back();
    }
}
