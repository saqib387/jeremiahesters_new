<?php

namespace App\Http\Controllers;

use App\Model\PublicPage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\File;

class PublicPagesController extends Controller
{
    /**
     * Renders public ( admin-created ) pages.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function getPage(Request $request)
    {
        $slug = $request->route('slug');
        $page = PublicPage::where('slug', $slug)->first();

        if (!$page) {
            abort(404);
        }

        return view('pages.public-page', ['page'=>$page]);
    }

    /**
     * Serve Community Guidelines PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function communityGuidelines()
    {
        $fileName = '# Community Guidelines .pdf';
        $filePath = public_path('files/' . $fileName);
        
        if (!File::exists($filePath)) {
            \Log::error('Community Guidelines PDF not found at: ' . $filePath);
            abort(404, 'Community Guidelines PDF not found');
        }

        return response(file_get_contents($filePath), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Community Guidelines.pdf"');
    }

    /**
     * Serve Privacy Policy PDF
     *
     * @return \Illuminate\Http\Response
     */
    public function privacyPolicy()
    {
        $fileName = '# Privacy Policy.pdf';
        $filePath = public_path('files/' . $fileName);
        
        if (!File::exists($filePath)) {
            \Log::error('Privacy Policy PDF not found at: ' . $filePath);
            abort(404, 'Privacy Policy PDF not found');
        }

        return response(file_get_contents($filePath), 200)
            ->header('Content-Type', 'application/pdf')
            ->header('Content-Disposition', 'inline; filename="Privacy Policy.pdf"');
    }
}
