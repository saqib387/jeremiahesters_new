<?php

namespace App\Http\Controllers;

use App\Providers\InstallerServiceProvider;
use App\Providers\MembersHelperServiceProvider;
use App\Models\Feed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use JavaScript;
use Session;

class HomeController extends Controller
{
    /**
     * Homepage > TikTok-style video feed
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Illuminate\View\View
     */
    public function index()
    {

        // if (!InstallerServiceProvider::checkIfInstalled()) {
        //     return Redirect::to(route('installer.install'));
        // }

        JavaScript::put(['skipDefaultScrollInits' => true]);

        // If there's a custom site index
        if (getSetting('site.homepage_redirect')) {
            return redirect()->to(getSetting('site.homepage_redirect'), 301)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
        }
        else{
            // Show TikTok-style feed on homepage - ALL data from database (no dummy/static data)
            $userId = Auth::id();

            try {
                $videos = Feed::getAllVideos($userId);
            } catch (\Exception $e) {
                \Log::error('HomeController: Feed error - ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
                $videos = collect();
            }

            // Transform to match view expectations (user object, counts, video_url)
            $videos = $videos->map(function($video) {
                if (!isset($video->user)) {
                    $video->user = (object)[
                        'id' => $video->user_id,
                        'name' => $video->user_name ?? 'User',
                        'username' => $video->username ?? $video->user_username ?? strtolower(str_replace(' ', '', $video->user_name ?? 'user'))
                    ];
                }
                if (!isset($video->video_url) && isset($video->video_path)) {
                    $video->video_url = asset('storage/' . $video->video_path);
                }
                $video->likes_count = $video->likes_count ?? 0;
                $video->comments_count = $video->comments_count ?? 0;
                $video->shares_count = $video->shares_count ?? 0;
                $video->reposts_count = $video->reposts_count ?? 0;
                $video->is_liked = (bool)($video->is_liked ?? false);
                $video->is_reposted = (bool)($video->is_reposted ?? false);
                return $video;
            });

            return view('pages.home', compact('videos'));
        }
    }
}
