<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PublicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PublicPagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $pages = PublicPage::orderBy('page_order')->get();
        return view('admin.public-pages.index', compact('pages'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.public-pages.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:191|unique:public_pages',
            'title' => 'required|string|max:191',
            'short_title' => 'nullable|string|max:191',
            'content' => 'required|string',
            'is_privacy' => 'boolean',
            'is_tos' => 'boolean',
            'shown_in_footer' => 'boolean',
            'page_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        PublicPage::create($request->all());

        return redirect()->route('admin.public-pages.index')
            ->with('success', 'Page created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(PublicPage $publicPage)
    {
        return view('admin.public-pages.show', compact('publicPage'));
    }

    /**
     * Show the form for editing the specified resource.
     * Special method for privacy page editing
     */
    public function edit(PublicPage $publicPage)
    {
        return view('admin.public-pages.edit', compact('publicPage'));
    }

    /**
     * Show privacy page edit form specifically
     */
    public function editPrivacy()
    {
        $privacyPage = PublicPage::getPrivacyPage();
        
        if (!$privacyPage) {
            // Create privacy page if it doesn't exist
            $privacyPage = PublicPage::create([
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'short_title' => 'Privacy',
                'content' => 'Privacy policy content goes here...',
                'is_privacy' => 1,
                'is_tos' => 0,
                'shown_in_footer' => 1,
                'page_order' => 1,
            ]);
        }

        // For Voyager compatibility
        $dataType = (object) [
            'slug' => 'public-pages',
            'name' => 'public_pages'
        ];
        $isModelTranslatable = false;

        return view('admin.public-pages.edit-privacy', compact('privacyPage', 'dataType', 'isModelTranslatable'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, PublicPage $publicPage)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|max:191|unique:public_pages,slug,' . $publicPage->id,
            'title' => 'required|string|max:191',
            'short_title' => 'nullable|string|max:191',
            'content' => 'required|string',
            'is_privacy' => 'boolean',
            'is_tos' => 'boolean',
            'shown_in_footer' => 'boolean',
            'page_order' => 'integer|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $publicPage->update($request->all());

        return redirect()->route('admin.public-pages.index')
            ->with('success', 'Page updated successfully.');
    }

    /**
     * Update privacy page specifically
     */
    public function updatePrivacy(Request $request)
    {
        $privacyPage = PublicPage::getPrivacyPage();
        
        if (!$privacyPage) {
            return redirect()->back()->with('error', 'Privacy page not found.');
        }

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:191',
            'short_title' => 'nullable|string|max:191',
            'content' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $privacyPage->update([
            'title' => $request->title,
            'short_title' => $request->short_title,
            'content' => $request->content,
        ]);

        return redirect()->route('voyager.public-pages.edit-privacy')
            ->with('success', 'Privacy policy updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PublicPage $publicPage)
    {
        $publicPage->delete();

        return redirect()->route('admin.public-pages.index')
            ->with('success', 'Page deleted successfully.');
    }
}