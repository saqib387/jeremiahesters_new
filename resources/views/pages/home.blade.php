@extends('layouts.generic')

@section('page_description', getSetting('site.description'))
@section('share_url', route('home'))
@section('share_title', getSetting('site.name') . ' - ' . getSetting('site.slogan'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@section('styles')
    {!!
        Minify::stylesheet([
            '/libs/swiper/swiper-bundle.min.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/css/pages/checkout.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
            '/css/pages/feed.css',
            '/css/posts/post.css',
            '/css/pages/search.css',
         ])->withFullUrl()
    !!}
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        /* Hide header on home page - TikTok style */
        header, .header, .navbar, nav.navbar, .site-header, footer, .footer {
            display: none !important;
        }
        
        body {
            overflow: hidden !important;
            background-color: #000 !important;
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }
        
        /* Ensure full height for reels */
        html, body {
            height: 100vh !important;
            height: 100dvh !important;
            overflow: hidden !important;
        }
        
        .reels-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            background: #000;
            z-index: 1000;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .video-feed {
            width: 100%;
            max-width: 100%;
            height: 100vh;
            height: 100dvh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
            margin: 0 auto;
        }
        
        .video-feed::-webkit-scrollbar {
            display: none;
        }
        
        .video-item {
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            position: relative;
            scroll-snap-align: start;
            scroll-snap-stop: always;
            display: flex;
            justify-content: center;
            align-items: center;
            max-width: 100%;
            margin: 0;
        }
        
        .video-wrapper {
            width: 100%;
            height: 100%;
            max-width: 100%;
            position: relative;
            overflow: hidden;
            margin: 0;
            pointer-events: none;
        }
        
        .video-player {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            cursor: pointer;
            pointer-events: auto;
            background: #000;
            max-width: 100%;
        }
        
        .video-overlay {
            position: absolute;
            bottom: 60px;
            left: 0;
            right: 0;
            padding: 20px 20px 20px 10px;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: #fff;
            z-index: 10;
            pointer-events: auto;
        }
        
        .video-info {
            max-width: 70%;
            text-align: left;
        }
        
        /* Right Action Buttons (TikTok style) */
        .video-actions-left {
            position: absolute;
            right: 20px;
            bottom: 160px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            z-index: 15;
            pointer-events: auto;
        }
        
        /* Right Navigation Controls */
        .video-navigation-right {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            display: none;
            flex-direction: column;
            align-items: center;
            gap: 20px;
            z-index: 15;
            pointer-events: auto;
        }
        
        .nav-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            pointer-events: auto;
        }
        
        .nav-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.1);
        }
        
        .nav-btn:active {
            transform: scale(0.95);
        }
        
        .user-info {
            display: inline-flex;
            align-items: center;
            margin-bottom: 18px;
            gap: 14px;
        }
        
        .user-info.clickable {
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 10px 14px 10px 10px;
            border-radius: 14px;
            margin: -10px -14px 8px -10px;
            background: transparent !important;
            backdrop-filter: none !important;
            pointer-events: auto;
        }
        
        .user-info.clickable:hover {
            background: transparent !important;
            transform: scale(1.03);
        }
        
        .user-avatar-initials {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            border: 3px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 18px;
            color: white;
            flex-shrink: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.4);
            transition: transform 0.2s ease;
        }
        
        .user-info.clickable:hover .user-avatar-initials {
            transform: scale(1.08);
        }
        
        .user-details h4 {
            margin: 0 0 4px 0;
            font-size: 17px;
            font-weight: 700;
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.6);
            letter-spacing: 0.2px;
        }
        
        .user-details p {
            margin: 0;
            font-size: 14px;
            opacity: 0.85;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .video-caption {
            margin-top: 8px;
            text-align: left;
        }
        
        .video-title {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.3;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            text-align: left;
        }
        
        .video-description {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.4;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .video-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding-bottom: 0;
        }
        
        .action-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            pointer-events: auto;
        }
        
        .action-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            pointer-events: auto;
        }
        
        .action-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }
        
        .action-btn:active {
            transform: scale(0.95);
        }
        
        .like-btn.liked {
            background: rgba(255, 23, 68, 0.3);
            color: #ff1744;
            border-color: #ff1744;
        }
        
        .repost-btn.reposted {
            background: rgba(23, 191, 99, 0.3);
            color: #17bf63;
            border-color: #17bf63;
        }
        
        .upload-btn {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
            border-color: #007bff;
        }
        
        .action-count {
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.7);
            min-height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 4px;
        }
        
        .progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 15;
            pointer-events: none;
        }
        
        .progress {
            height: 100%;
            background: linear-gradient(90deg, #ff6b6b, #ff8a80);
            width: 0%;
            transition: width 0.1s linear;
        }
        
        /* Comments Sidebar */
        .comments-sidebar {
            position: fixed;
            top: 0;
            right: -25%;
            width: 25%;
            height: 100vh;
            background: #fff;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1002;
            display: flex;
            flex-direction: column;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
            min-width: 320px;
            opacity: 0;
            visibility: hidden;
            transform: translateX(100%);
            overflow: hidden;
        }
        
        .comments-sidebar.active {
            right: 0;
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }
        
        .comments-sidebar.active .comment-form {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
        }
        
        /* Comments Overlay Background */
        .comments-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1001;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .comments-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .comments-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            position: relative;
        }
        
        .comments-header::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            background: #ddd;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .comments-header h3 {
            margin: 0;
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }
        
        .close-comments {
            background: rgba(0, 0, 0, 0.05);
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #666;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .close-comments:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #333;
            transform: scale(1.05);
        }
        
        .comments-list {
            flex: 1 1 auto;
            overflow-y: auto;
            padding: 20px;
            min-height: 0;
            max-height: 100%;
        }
        
        .no-comments {
            text-align: center;
            color: #888;
            padding: 40px 20px;
        }
        
        .loading {
            text-align: center;
            color: #888;
            padding: 40px 20px;
        }
        
        .comment-item {
            display: flex;
            margin-bottom: 16px;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.02);
            transition: background-color 0.3s ease;
        }
        
        .comment-item:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .comment-avatar-initials {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 13px;
            color: white;
            flex-shrink: 0;
            border: 2px solid #e9ecef;
        }
        
        .comment-content {
            flex: 1;
        }
        
        .comment-content strong {
            color: #333;
            font-size: 14px;
            font-weight: 600;
        }
        
        .comment-content p {
            margin: 4px 0;
            color: #333;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .comment-time {
            color: #666;
            font-size: 12px;
        }
        
        .comment-form {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 12px;
            background: #fff;
            position: relative;
            bottom: 0;
            z-index: 10;
            flex-shrink: 0;
            margin-top: auto;
        }
        
        .comment-form input {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            background: #fff;
            display: block;
            visibility: visible;
        }
        
        .comment-form input:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }
        
        .comment-form button {
            background: linear-gradient(135deg, #ff6b6b, #ff8a80);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 12px 24px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            display: block;
            visibility: visible;
        }
        
        .comment-form button:hover:not(:disabled) {
            background: linear-gradient(135deg, #ff5252, #ff6b6b);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(255, 82, 82, 0.4);
        }
        
        .comment-form button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .no-videos {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #fff;
            text-align: center;
            padding: 40px;
        }
        
        .no-videos i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .no-videos h3 {
            margin-bottom: 10px;
            font-size: 1.5rem;
        }
        
        .no-videos p {
            opacity: 0.7;
            margin-bottom: 30px;
        }
        
        .upload-first-video-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #ff6b6b, #ff8a80);
            color: #fff;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255, 107, 107, 0.3);
        }
        
        .upload-first-video-btn:hover {
            background: linear-gradient(135deg, #ff5252, #ff6b6b);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255, 82, 82, 0.4);
            color: #fff;
        }
        
        .upload-first-video-btn i {
            font-size: 18px;
        }
        
        /* Desktop - Centered like TikTok */
        @media (min-width: 769px) {
            .video-feed {
                max-width: 692px;
            }
            
            .video-item {
                max-width: 692px;
            }
            
            .video-wrapper {
                max-width: 692px;
            }
            
            .video-player {
                max-width: 692px;
            }
        }
        
        /* Mobile Optimizations - Full Screen */
        @media (max-width: 768px) {
            .reels-container {
                justify-content: flex-start;
            }
            
            .video-feed {
                max-width: 100%;
                width: 100%;
            }
            
            .video-item {
                max-width: 100%;
            }
            
            .video-wrapper {
                max-width: 100%;
            }
            
            .video-player {
                max-width: 100%;
            }
            
            /* Comments as bottom sheet on mobile */
            .comments-sidebar {
                top: auto;
                bottom: 0;
                left: 0;
                right: 0;
                width: 100%;
                height: 85vh;
                max-height: 85vh;
                min-width: unset;
                border-radius: 20px 20px 0 0;
                transform: translateY(100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.3s ease;
            }
            
            .comments-sidebar.active {
                transform: translateY(0);
                right: auto;
            }
            
            .video-overlay {
                padding: 15px 15px 15px 8px;
                bottom: 55px;
            }
            
            .video-info {
                max-width: calc(100% - 70px);
                text-align: left;
            }
            
            .video-actions-left {
                bottom: 140px;
                right: 15px;
            }
            
            .user-avatar-initials {
                width: 48px;
                height: 48px;
                font-size: 16px;
            }
            
            .user-details h4 {
                font-size: 16px;
            }
            
            .user-details p {
                font-size: 13px;
            }
            
            .video-title {
                font-size: 15px;
            }
            
            .video-description {
                font-size: 13px;
            }
            
            .action-btn {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }
            
            .video-actions {
                gap: 12px;
                padding-bottom: 0;
            }
            
            .video-actions-left {
                bottom: 80px;
                gap: 15px;
            }
            
            .video-navigation-right {
                right: 10px;
                gap: 15px;
            }
            
            .nav-btn {
                width: 44px;
                height: 44px;
                font-size: 20px;
            }
            
            .action-count {
                font-size: 11px;
            }
            
            /* Mobile comments header with drag handle */
            .comments-header::before {
                width: 40px;
                height: 4px;
            }
            
            .comments-list {
                max-height: calc(85vh - 200px);
                overflow-y: auto;
            }
            
            /* Mobile comment form - ensure it's always visible */
            .comment-form {
                padding: 16px 20px;
                padding-bottom: calc(24px + env(safe-area-inset-bottom, 16px));
                position: sticky !important;
                bottom: 0;
                background: #fff;
                box-shadow: 0 -2px 10px rgba(0, 0, 0, 0.1);
                margin-bottom: 0;
                margin-top: 0;
            }
            
            .comment-form input {
                padding: 12px 16px;
                font-size: 15px;
            }
            
            .comment-form button {
                padding: 12px 24px;
                font-size: 14px;
                white-space: nowrap;
            }
        }
        
        /* Tablet and Desktop - hide overlay, use sidebar */
        @media (min-width: 769px) {
            .comments-overlay {
                display: none;
            }
            
            /* Ensure sidebar and form are visible on desktop */
            .comments-sidebar {
                display: flex !important;
                flex-direction: column;
                overflow: hidden;
            }
            
            .comments-header {
                flex-shrink: 0;
                flex-grow: 0;
            }
            
            .comments-list {
                flex: 1;
                overflow-y: auto;
                min-height: 0;
                max-height: calc(100vh - 200px);
            }
            
            /* Ensure comment form is visible on desktop */
            .comment-form {
                display: flex !important;
                position: relative !important;
                bottom: auto !important;
                background: #fff;
                flex-shrink: 0 !important;
                flex-grow: 0 !important;
                padding: 20px;
                border-top: 1px solid #eee;
                visibility: visible !important;
                opacity: 1 !important;
                height: auto;
                min-height: 80px;
                margin-top: auto;
                gap: 12px;
            }
            
            .comment-form input {
                flex: 1;
                padding: 12px 18px;
                border: 2px solid #e9ecef;
                border-radius: 25px;
                font-size: 14px;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                background: #fff;
            }
            
            .comment-form button {
                padding: 12px 24px;
                background: linear-gradient(135deg, #ff6b6b, #ff8a80) !important;
                color: #fff !important;
                border: none;
                border-radius: 25px;
                cursor: pointer;
                font-size: 14px;
                font-weight: 600;
                display: block !important;
                visibility: visible !important;
                opacity: 1 !important;
                box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
            }
            
            .comment-form button:hover {
                background: linear-gradient(135deg, #ff5252, #ff6b6b) !important;
                transform: translateY(-1px);
                box-shadow: 0 6px 16px rgba(255, 82, 82, 0.4);
            }
        }
        
        /* Tablet responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .comments-sidebar {
                width: 35%;
                right: -35%;
                min-width: 350px;
            }
        }
        
        /* Top Header Styles */
        .top-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1002;
            padding: 15px 20px;
            background: linear-gradient(rgba(0, 0, 0, 0.6), transparent);
            display: flex;
            justify-content: space-between;
            align-items: center;
            pointer-events: auto;
        }
        
        .header-menu-btn,
        .search-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            width: 45px;
            height: 45px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            pointer-events: auto;
        }
        
        .header-menu-btn:hover,
        .search-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }
        
        /* Bottom Modal Styles */
        .header-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 10005 !important; /* Higher than bottom nav (usually z-index: 3) */
            background: rgba(0, 0, 0, 0.7);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            overflow: hidden;
        }
        
        .header-modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Prevent body scroll when modal is open */
        body.modal-open {
            overflow: hidden;
            position: fixed;
            width: 100%;
        }
        
        .header-modal-content {
            background: #fff;
            border-radius: 20px 20px 0 0;
            padding: 0;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            overflow-x: hidden;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: absolute;
            bottom: 0;
            display: flex;
            flex-direction: column;
            z-index: 10006 !important;
        }
        
        .header-modal.active .header-modal-content {
            transform: translateY(0);
        }
        
        .header-modal-header {
            padding: 20px 20px 16px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            position: sticky;
            top: 0;
            z-index: 10;
            flex-shrink: 0;
        }
        
        .header-modal-header::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 4px;
            background: #ddd;
            border-radius: 2px;
        }
        
        .header-modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }
        
        .header-modal-close {
            background: rgba(0, 0, 0, 0.05);
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #666;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .header-modal-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #333;
            transform: scale(1.05);
        }
        
        /* Header Menu Items */
        .header-menu-items {
            padding: 20px;
            background: #fff;
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden;
            -webkit-overflow-scrolling: touch;
            padding-bottom: 120px; /* Extra padding to ensure all items are visible above bottom nav */
            min-height: 0;
        }
        
        .header-menu-item {
            display: flex !important;
            align-items: center !important;
            gap: 15px;
            padding: 16px 20px;
            text-decoration: none;
            color: #1a1a1a !important;
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 600;
            background: #ffffff !important;
            border: 2px solid #e9ecef !important;
            width: 100%;
            box-sizing: border-box;
            position: relative;
            min-height: 56px;
        }
        
        .header-menu-item:last-child {
            margin-bottom: 0;
        }
        
        .header-menu-item:hover,
        .header-menu-item:active,
        .header-menu-item:focus {
            background: linear-gradient(135deg, #667eea, #764ba2) !important;
            color: #ffffff !important;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
            border-color: transparent !important;
            text-decoration: none;
        }
        
        .header-menu-item i {
            font-size: 20px;
            width: 24px;
            text-align: center;
            flex-shrink: 0;
            color: inherit;
        }
        
        .header-menu-item span {
            flex: 1;
            font-weight: 500;
            color: inherit;
        }
        
        /* TikTok-Style Full Screen Search */
        .search-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            z-index: 1004;
            background: #000;
            display: flex;
            flex-direction: column;
            transform: translateX(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            opacity: 0;
            visibility: hidden;
        }
        
        .search-modal.active {
            transform: translateX(0);
            opacity: 1;
            visibility: visible;
        }
        
        .search-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            background: #000;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 10;
        }
        
        .search-back-btn {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 24px;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: background 0.2s ease;
        }
        
        .search-back-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .search-input-wrapper {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            gap: 8px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px 16px;
        }
        
        .search-input-icon {
            color: rgba(255, 255, 255, 0.6);
            font-size: 18px;
        }
        
        .search-input-wrapper input {
            flex: 1;
            background: transparent;
            border: none;
            color: #fff;
            font-size: 16px;
            outline: none;
            padding: 0;
        }
        
        .search-input-wrapper input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .search-clear-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
            cursor: pointer;
            padding: 4px;
            display: none;
            align-items: center;
            justify-content: center;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .search-clear-btn.visible {
            display: flex;
        }
        
        .search-clear-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .search-send-btn {
            background: transparent;
            border: none;
            color: rgba(255, 255, 255, 0.6);
            font-size: 20px;
            cursor: pointer;
            padding: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            transition: all 0.2s ease;
        }
        
        .search-send-btn:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
        }
        
        .search-content {
            flex: 1;
            overflow-y: auto;
            padding: 16px;
            background: #000;
        }
        
        .search-suggestions {
            margin-bottom: 24px;
        }
        
        /* Grid layout for video results */
        .search-suggestions.video-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 24px;
        }
        
        @media (max-width: 1400px) {
            .search-suggestions.video-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }
        
        @media (max-width: 900px) {
            .search-suggestions.video-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }
        
        @media (max-width: 600px) {
            .search-suggestions.video-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 12px;
            }
        }
        
        .search-suggestion-item {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 12px 0;
            cursor: pointer;
            transition: opacity 0.2s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .search-suggestion-item:last-child {
            border-bottom: none;
        }
        
        .search-suggestion-item:hover {
            opacity: 0.8;
        }
        
        .suggestion-icon {
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.6);
            font-size: 18px;
        }
        
        .suggestion-text {
            flex: 1;
            color: #fff;
            font-size: 16px;
            font-weight: 400;
        }
        
        .user-profile-suggestion {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            cursor: pointer;
            transition: opacity 0.2s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }
        
        .user-profile-suggestion:last-child {
            border-bottom: none;
        }
        
        .user-profile-suggestion:hover {
            opacity: 0.8;
        }
        
        .user-avatar-search {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            background: linear-gradient(135deg, #FF6B6B, #4ECDC4);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }
        
        .user-avatar-search img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }
        
        .user-info-search {
            flex: 1;
            min-width: 0;
        }
        
        .user-info-search .username {
            display: flex;
            align-items: center;
            gap: 6px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            margin: 0 0 4px 0;
        }
        
        .user-info-search .verified-badge {
            color: #1DA1F2;
            font-size: 14px;
        }
        
        .user-info-search .user-desc {
            color: rgba(255, 255, 255, 0.6);
            font-size: 14px;
            margin: 0;
        }
        
        .search-loading,
        .no-search-results,
        .search-placeholder {
            text-align: center;
            padding: 60px 20px;
            color: rgba(255, 255, 255, 0.6);
            font-size: 16px;
        }
        
        .search-result-video {
            display: flex;
            flex-direction: column;
            cursor: pointer;
            transition: all 0.3s ease;
            border-radius: 8px;
            overflow: hidden;
        }
        
        .search-result-video:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(255, 107, 107, 0.3);
        }
        
        .video-thumbnail {
            width: 100%;
            height: 200px;
            border-radius: 8px 8px 0 0;
            background: rgba(255, 255, 255, 0.1);
            overflow: hidden;
            position: relative;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .video-thumbnail img,
        .video-thumbnail video {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .video-overlay-stats {
            position: absolute;
            bottom: 8px;
            left: 8px;
            right: 8px;
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(10px);
            padding: 6px 10px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 12px;
            color: #fff;
            font-weight: 600;
        }
        
        .video-overlay-stats i {
            color: #ff6b6b;
            font-size: 14px;
        }
        
        .video-info-search {
            padding: 12px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 0 0 8px 8px;
        }
        
        .video-info-search h4 {
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            margin: 0 0 6px 0;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.3;
            min-height: 36px;
        }
        
        .video-info-search p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
            margin: 0 0 4px 0;
            display: flex;
            align-items: center;
            gap: 4px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .video-info-search p i {
            font-size: 11px;
            opacity: 0.8;
            flex-shrink: 0;
        }
        
        .video-views {
            font-size: 11px !important;
            color: rgba(255, 255, 255, 0.5) !important;
        }
        
        @media (max-width: 768px) {
            .search-header {
                padding: 10px 12px;
            }
            
            .search-input-wrapper {
                padding: 8px 12px;
            }
            
            .search-content {
                padding: 12px;
            }
            
            .user-avatar-search {
                width: 44px;
                height: 44px;
                font-size: 16px;
            }
            
            .video-thumbnail {
                width: 100px;
                height: 134px;
            }
        }
        
        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.95);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 8px 5px 8px 5px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1001;
            padding-bottom: calc(8px + env(safe-area-inset-bottom));
            pointer-events: auto;
        }
        
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: rgba(255, 255, 255, 0.6);
            transition: all 0.3s ease;
            padding: 6px 8px;
            border-radius: 12px;
            flex: 1;
            max-width: 80px;
            background: transparent !important;
        }
        
        .bottom-nav-item.active {
            color: #fff;
            background: transparent !important;
        }
        
        .bottom-nav-item:hover {
            color: #fff;
            transform: translateY(-2px);
            background: transparent !important;
        }
        
        .bottom-nav-item:active {
            background: transparent !important;
        }
        
        .bottom-nav-icon {
            font-size: 20px;
            margin-bottom: 4px;
        }
        
        .bottom-nav-text {
            font-size: 10px;
            font-weight: 500;
        }
        
        /* Mobile adjustments */
        @media (max-width: 768px) {
            .user-avatar-initials {
                width: 44px;
                height: 44px;
                font-size: 15px;
                border-width: 2px;
            }
            
            .user-details h4 {
                font-size: 15px;
            }
            
            .user-details p {
                font-size: 12px;
            }
            
            .user-info {
                gap: 12px;
            }
            
            .user-info.clickable {
                padding: 8px 12px 8px 8px;
                margin: -8px -12px 6px -8px;
                background: transparent !important;
                backdrop-filter: none !important;
            }
            
            .user-info.clickable:hover {
                background: transparent !important;
            }
            
            .top-header {
                padding: 12px 15px;
            }
            
            .header-menu-btn,
            .search-btn {
                width: 40px;
                height: 40px;
                font-size: 16px;
            }
            
            .bottom-nav {
                padding: 6px 5px 6px 5px;
            }
            
            .bottom-nav-icon {
                font-size: 18px;
                margin-bottom: 3px;
            }
            
            .bottom-nav-text {
                font-size: 9px;
            }
            
            /* Mobile Menu Modal Fixes */
            .header-modal {
                z-index: 1005 !important;
            }
            
            .header-modal {
                z-index: 10005 !important;
            }
            
            .header-modal-content {
                max-height: 90vh !important;
                bottom: 0 !important;
                z-index: 10006 !important;
            }
            
            .header-menu-items {
                padding-bottom: 150px !important; /* Extra padding for bottom nav */
                max-height: calc(90vh - 80px);
            }
            
            .header-menu-item {
                background: #ffffff !important;
                border: 2px solid #e9ecef !important;
                color: #1a1a1a !important;
                font-weight: 600;
                min-height: 56px;
                display: flex !important;
                align-items: center !important;
            }
            
            .header-menu-item:hover,
            .header-menu-item:active,
            .header-menu-item:focus {
                background: linear-gradient(135deg, #667eea, #764ba2) !important;
                color: #ffffff !important;
                border-color: transparent !important;
            }
            
            .header-menu-item i {
                color: inherit;
            }
            
            .header-menu-item span {
                display: block !important;
            }
        }
    </style>
@stop

@section('content')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Top Left Menu Button -->
    <div class="top-header">
        <button id="header-menu-btn" class="header-menu-btn">
            <i class="fas fa-bars"></i>
        </button>
        <button id="search-btn" class="search-btn">
            <i class="fas fa-search"></i>
        </button>
    </div>

    <!-- Header Menu Modal -->
    <div class="header-modal" id="header-modal">
        <div class="header-modal-content">
            <div class="header-modal-header">
                <h3>Menu</h3>
                <button class="header-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="header-menu-items">
                @auth
                <div style="padding:14px 16px;margin-bottom:10px;border-radius:12px;background:linear-gradient(135deg,#830866,#a10a7f);color:#fff;">
                    <div style="display:flex;align-items:center;justify-content:space-between;font-weight:700;">
                        <span>Level {{ Auth::user()->level ?? 1 }}</span>
                        <span>🔥 {{ Auth::user()->streak_count ?? 0 }} day streak</span>
                    </div>
                    <div style="height:7px;background:rgba(255,255,255,.3);border-radius:6px;overflow:hidden;margin-top:8px;">
                        <div style="height:100%;width:{{ (int)(Auth::user()->xp ?? 0) % 100 }}%;background:#fff;border-radius:6px;"></div>
                    </div>
                    <div style="font-size:.72rem;opacity:.9;margin-top:4px;">{{ (int)(Auth::user()->xp ?? 0) % 100 }}/100 XP to next level</div>
                </div>
                @endauth
                <a href="/" class="header-menu-item">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                @if(getSetting('streams.allow_streams'))
                <a href="{{ route('streams.index') }}" class="header-menu-item">
                    <i class="fas fa-video"></i>
                    <span>Live Streams</span>
                </a>
                @endif
                <a href="{{ route('custom-requests.marketplace') }}" class="header-menu-item">
                    <i class="fas fa-gift"></i>
                    <span>Custom Requests</span>
                </a>
                @auth
                    <a href="/{{ Auth::user()->username ?? 'profile' }}" class="header-menu-item">
                        <i class="fas fa-user"></i>
                        <span>Profile</span>
                    </a>
                    <a href="{{ route('creator.dashboard') }}" class="header-menu-item">
                        <i class="fas fa-chart-line"></i>
                        <span>Creator Dashboard</span>
                    </a>
                    <a href="{{ route('gamification.achievements') }}" class="header-menu-item">
                        <i class="fas fa-trophy"></i>
                        <span>Achievements</span>
                    </a>
                    <a href="{{ route('gamification.leaderboard') }}" class="header-menu-item">
                        <i class="fas fa-ranking-star"></i>
                        <span>Leaderboard</span>
                    </a>
                    <a href="{{ route('videos.create') }}" class="header-menu-item">
                        <i class="fas fa-video"></i>
                        <span>Create Video</span>
                    </a>
                    <a href="{{ route('cryptocurrency.wallet') }}" class="header-menu-item">
                        <i class="fas fa-wallet"></i>
                        <span>Wallet</span>
                    </a>
                    <a href="{{ route('cryptocurrency.marketplace') }}" class="header-menu-item">
                        <i class="fas fa-store"></i>
                        <span>Marketplace</span>
                    </a>
                    <a href="{{ route('my.settings') }}" class="header-menu-item">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                    <a href="{{ route('logout') }}" class="header-menu-item" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                @else
                    <a href="{{ route('login') }}" class="header-menu-item">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                    <a href="{{ route('register') }}" class="header-menu-item">
                        <i class="fas fa-user-plus"></i>
                        <span>Register</span>
                    </a>
                @endauth
            </div>
        </div>
    </div>

    <!-- TikTok-Style Full Screen Search -->
    <div class="search-modal" id="search-modal">
        <div class="search-header">
            <button class="search-back-btn" id="search-back-btn">
                <i class="fas fa-arrow-left"></i>
            </button>
            <div class="search-input-wrapper">
                <i class="fas fa-search search-input-icon"></i>
                <input type="text" id="search-input" placeholder="Search" autocomplete="off">
                <button class="search-clear-btn" id="search-clear-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <button class="search-send-btn" id="search-send-btn">
                <i class="fas fa-paper-plane"></i>
            </button>
        </div>
        <div class="search-content" id="search-results">
            <div class="search-placeholder">Start typing to search...</div>
        </div>
    </div>

    <div class="reels-container">
        <div class="video-feed">
            @if($videos && $videos->count() > 0)
                @foreach($videos as $video)
                    <div class="video-item" data-video-id="{{ $video->id }}">
                        <div class="video-wrapper">
                            <video 
                                src="{{ $video->video_url }}"
                                autoplay
                                loop
                                muted
                                playsinline
                                preload="auto"
                                class="video-player"
                                webkit-playsinline
                            ></video>
                            
                            <!-- Left Action Buttons -->
                            <div class="video-actions-left">
                                <div class="action-item">
                                    <button class="action-btn like-btn @if($video->is_liked) liked @endif" data-video-id="{{ $video->id }}">
                                        <i class="@if($video->is_liked) fas @else far @endif fa-heart"></i>
                                    </button>
                                    <span class="action-count">{{ $video->likes_count }}</span>
                                </div>
                                
                                <div class="action-item">
                                    <button class="action-btn comment-btn" data-video-id="{{ $video->id }}">
                                        <i class="fas fa-comment"></i>
                                    </button>
                                    <span class="action-count">{{ $video->comments_count }}</span>
                                </div>
                                
                                <div class="action-item">
                                    <button class="action-btn share-btn" data-video-id="{{ $video->id }}">
                                        <i class="fas fa-share"></i>
                                    </button>
                                    <span class="action-count">{{ $video->shares_count }}</span>
                                </div>
                                
                                <div class="action-item">
                                    <button class="action-btn repost-btn @if($video->is_reposted) reposted @endif" data-video-id="{{ $video->id }}">
                                        <i class="fas fa-retweet"></i>
                                    </button>
                                    <span class="action-count">{{ $video->reposts_count }}</span>
                                </div>
                                
                                <div class="action-item">
                                    <button class="action-btn upload-btn" onclick="handleUploadClick()">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Right Navigation Controls -->
                            <div class="video-navigation-right">
                                <button class="nav-btn nav-up-btn" onclick="scrollToPreviousVideo()">
                                    <i class="fas fa-chevron-up"></i>
                                </button>
                                <button class="nav-btn nav-down-btn" onclick="scrollToNextVideo()">
                                    <i class="fas fa-chevron-down"></i>
                                </button>
                            </div>
                            
                            <!-- Bottom Video Info -->
                            <div class="video-overlay">
                                <div class="video-info">
                                    <div class="user-info clickable" data-user-id="{{ $video->user->id }}" data-user-username="{{ $video->user->username ?? strtolower(str_replace(' ', '', $video->user->name)) }}">
                                        <div class="user-avatar-initials" style="background: {{ ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7'][rand(0,4)] }};">
                                            {{ strtoupper(substr($video->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $video->user->name)[1] ?? '', 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <h4>{{ $video->user->name }}</h4>
                                            <p>{{ '@' . ($video->user->username ?? strtolower(str_replace(' ', '', $video->user->name))) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="video-caption">
                                        <p class="video-title">{{ $video->title }}</p>
                                        @if($video->description)
                                            <p class="video-description">{{ $video->description }}</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            
                            <div class="progress-bar">
                                <div class="progress"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-videos">
                    <i class="fas fa-video"></i>
                    <h3>No videos available</h3>
                    <p>Be the first to share a video!</p>
                    <a href="/videos/create" class="upload-first-video-btn">
                        <i class="fas fa-plus"></i>
                        Upload Your First Video
                    </a>
                </div>
            @endif
        </div>

        <!-- Comments Overlay -->
        <div class="comments-overlay" id="comments-overlay"></div>
        
        <!-- Comments Sidebar -->
        <div class="comments-sidebar" id="comments-sidebar">
            <div class="comments-header">
                <h3>Comments</h3>
                <button class="close-comments">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="comments-list" id="comments-list">
                <div class="no-comments">
                    <p>No comments yet. Be the first to comment!</p>
                </div>
            </div>
            
            <div class="comment-form">
                <input type="text" id="comment-input" placeholder="Add a comment..." maxlength="500">
                <button id="post-comment">Post</button>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav">
        <a href="/" class="bottom-nav-item active">
            <i class="fas fa-home bottom-nav-icon"></i>
            <span class="bottom-nav-text">Home</span>
        </a>

        @auth
            <a href="{{ route('videos.create') }}" class="bottom-nav-item">
                <i class="fas fa-video bottom-nav-icon"></i>
                <span class="bottom-nav-text">Create</span>
            </a>
            <a href="{{ route('cryptocurrency.wallet') }}" class="bottom-nav-item">
                <i class="fas fa-wallet bottom-nav-icon"></i>
                <span class="bottom-nav-text">Wallet</span>
            </a>
            <a href="{{ route('cryptocurrency.marketplace') }}" class="bottom-nav-item">
                <i class="fas fa-store bottom-nav-icon"></i>
                <span class="bottom-nav-text">Market</span>
            </a>
            <a href="{{ route('profile', ['username' => Auth::user()->username ?? Auth::user()->id]) }}" class="bottom-nav-item">
                <i class="fas fa-user bottom-nav-icon"></i>
                <span class="bottom-nav-text">Profile</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="bottom-nav-item">
                <i class="fas fa-sign-in-alt bottom-nav-icon"></i>
                <span class="bottom-nav-text">Login</span>
            </a>
            <a href="{{ route('register') }}" class="bottom-nav-item">
                <i class="fas fa-user-plus bottom-nav-icon"></i>
                <span class="bottom-nav-text">Register</span>
            </a>
            <a href="{{ route('cryptocurrency.marketplace') }}" class="bottom-nav-item">
                <i class="fas fa-store bottom-nav-icon"></i>
                <span class="bottom-nav-text">Market</span>
            </a>
        @endauth
    </nav>
@stop

@section('scripts')
    <!-- Removed feed.js and Post.js as they conflict with home page video functionality -->
    
    <!-- Reels JavaScript -->
    <script>
        console.log('Home page video JavaScript loaded successfully');
        
        // Global variables
        let currentVideoIndex = 0;
        
        // Global functions that need to be accessible immediately for onclick handlers
        window.handleUploadClick = function() {
            @auth
                window.location.href = '{{ route("videos.create") }}';
            @else
                window.location.href = '{{ route("register") }}';
            @endauth
        };
        
        window.scrollToNextVideo = function() {
            const videoItems = document.querySelectorAll('.video-item');
            if (currentVideoIndex < videoItems.length - 1) {
                currentVideoIndex++;
                videoItems[currentVideoIndex].scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        };
        
        window.scrollToPreviousVideo = function() {
            const videoItems = document.querySelectorAll('.video-item');
            if (currentVideoIndex > 0) {
                currentVideoIndex--;
                videoItems[currentVideoIndex].scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        };
        
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM Content Loaded - Initializing video page');
            const videoItems = document.querySelectorAll('.video-item');
            const videoPlayers = document.querySelectorAll('.video-player');
            const videoFeed = document.querySelector('.video-feed');
            let isScrolling = false;
            
            console.log('Found', videoItems.length, 'video items');
            console.log('Found', videoPlayers.length, 'video players');
            
            // Initialize all videos
            videoPlayers.forEach((video, index) => {
                video.setAttribute('playsinline', '');
                video.setAttribute('webkit-playsinline', '');
                video.muted = true; // Start muted for autoplay
                video.load();
                
                // Click to play/pause
                video.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (this.paused) {
                        this.play();
                    } else {
                        this.pause();
                    }
                });
            });
            
            let viewedVideos = new Set();
            // Intersection Observer for autoplay
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    const video = entry.target;
                    if (entry.isIntersecting && entry.intersectionRatio > 0.7) {
                        pauseAllVideosExcept(video);
                        video.currentTime = 0;
                        video.play().catch(e => console.log('Autoplay failed:', e));
                        
                        // Increment views when video starts playing
                        const videoId = video.closest('.video-item').getAttribute('data-video-id');
                        if (videoId) {
                            incrementVideoViews(videoId);
                        }

                        // Update progress bar
                        const progressBar = video.closest('.video-item').querySelector('.progress');
                        if (progressBar) {
                            startProgressBar(video, progressBar);
                        }
                    } else {
                        video.pause();
                    }
                });
            }, {
                threshold: [0.7],
                rootMargin: '-10% 0px',
                root: videoFeed || null
            });
            
            // Observe all videos
            videoPlayers.forEach(video => observer.observe(video));

            // Force first visible video to play on load
            const playFirstVisible = () => {
                const firstVideo = videoPlayers[0];
                if (!firstVideo) return;
                pauseAllVideosExcept(firstVideo);
                firstVideo.currentTime = 0;
                firstVideo.play().catch(() => {
                    // Autoplay can be blocked until a user gesture
                });
            };
            setTimeout(playFirstVisible, 300);

            // Resume autoplay after any user interaction
            const resumeOnGesture = () => {
                const currentVideo = videoPlayers[currentVideoIndex] || videoPlayers[0];
                if (currentVideo) {
                    currentVideo.play().catch(() => {});
                }
            };
            document.addEventListener('click', resumeOnGesture, { once: true });
            document.addEventListener('touchstart', resumeOnGesture, { once: true });
            
            function pauseAllVideosExcept(currentVideo) {
                videoPlayers.forEach(video => {
                    if (video !== currentVideo) {
                        video.pause();
                        video.currentTime = 0;
                    }
                });
            }
            
            function startProgressBar(video, progressBar) {
                const updateProgress = () => {
                    if (!video.paused && video.duration > 0) {
                        const progress = (video.currentTime / video.duration) * 100;
                        progressBar.style.width = progress + '%';
                        
                        if (progress < 100 && !video.paused) {
                            requestAnimationFrame(updateProgress);
                        }
                    }
                };
                requestAnimationFrame(updateProgress);
            }
            
            // Like button functionality - using event delegation for dynamic content
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.like-btn');
                if (!btn) return;
                
                console.log('Like button clicked:', btn);
                e.preventDefault();
                e.stopPropagation();
                
                @guest
                    // If user is not logged in, redirect to login
                    if (confirm('Please login to like videos')) {
                        window.location.href = '{{ route("login") }}';
                    }
                    return;
                @endguest
                
                const videoId = btn.getAttribute('data-video-id');
                const countSpan = btn.closest('.action-item').querySelector('.action-count');
                const icon = btn.querySelector('i');
                let count = parseInt(countSpan.textContent) || 0;
                
                const wasLiked = btn.classList.contains('liked');
                const button = btn;
                    
                // Optimistic UI update
                if (!wasLiked) {
                    button.classList.add('liked');
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    countSpan.textContent = count + 1;
                } else {
                    button.classList.remove('liked');
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    countSpan.textContent = Math.max(0, count - 1);
                }
                
                // Make API call
                const csrfToken = document.querySelector('meta[name="csrf-token"]');
                if (!csrfToken) {
                    console.error('CSRF token not found');
                    return;
                }
                
                fetch(`/videos/${videoId}/like`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken.content,
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    credentials: 'same-origin'
                })
                .then(response => {
                    console.log('Like response status:', response.status);
                    if (response.status === 401) {
                        // Redirect to login if not authenticated
                        if (confirm('Please login to like videos')) {
                            window.location.href = '{{ route("login") }}';
                        }
                        throw new Error('Authentication required');
                    }
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Like response data:', data);
                    if (data.success) {
                        // Update with real data from server
                        countSpan.textContent = data.likes_count || countSpan.textContent;
                        if (data.is_liked) {
                            button.classList.add('liked');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                        } else {
                            button.classList.remove('liked');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                        }
                        
                        // Show notification if using fallback
                        if (data.note) {
                            console.warn('Using fallback data:', data.note);
                        }
                    } else {
                        // Revert UI on failure
                        if (wasLiked) {
                            button.classList.add('liked');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            countSpan.textContent = count;
                        } else {
                            button.classList.remove('liked');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            countSpan.textContent = count;
                        }
                        console.error('Like failed:', data.message || data.error || 'Unknown error');
                    }
                })
                .catch(error => {
                    console.error('Error liking video:', error);
                    // Don't revert UI for authentication errors
                    if (error.message === 'Authentication required') {
                        return;
                    }
                    // Revert UI on other errors
                    if (wasLiked) {
                        button.classList.add('liked');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        countSpan.textContent = count;
                    } else {
                        button.classList.remove('liked');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        countSpan.textContent = count;
                    }
                });
            });
            
            // Comments sidebar functionality
            const commentsSidebar = document.getElementById('comments-sidebar');
            const commentsOverlay = document.getElementById('comments-overlay');
            const closeCommentsBtn = document.querySelector('.close-comments');
            
            function openComments() {
                commentsSidebar.classList.add('active');
                if (commentsOverlay) {
                    commentsOverlay.classList.add('active');
                }
            }
            
            function closeComments() {
                commentsSidebar.classList.remove('active');
                if (commentsOverlay) {
                    commentsOverlay.classList.remove('active');
                }
            }
            
            // Comment button functionality - using event delegation for dynamic content
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.comment-btn');
                if (!btn) return;
                
                console.log('Comment button clicked:', btn);
                e.preventDefault();
                e.stopPropagation();
                openComments();
                const videoId = btn.getAttribute('data-video-id');
                loadComments(videoId);
                
                // Store current video ID for posting comments
                currentVideoIdForComments = videoId;
            });
            
            closeCommentsBtn.addEventListener('click', function() {
                closeComments();
            });
            
            // Close comments when clicking on overlay
            if (commentsOverlay) {
                commentsOverlay.addEventListener('click', function() {
                    closeComments();
                });
            }
            
            // Share button functionality - using event delegation for dynamic content
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.share-btn');
                if (!btn) return;
                
                console.log('Share button clicked:', btn);
                e.preventDefault();
                e.stopPropagation();
                
                const videoId = btn.getAttribute('data-video-id');
                const countSpan = btn.closest('.action-item').querySelector('.action-count');
                let count = parseInt(countSpan.textContent);
                countSpan.textContent = count + 1;
                
                fetch(`/videos/${videoId}/share`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        countSpan.textContent = data.shares_count;
                    }
                })
                .catch(error => console.error('Error:', error));
                
                const videoUrl = `${window.location.origin}/videos/${videoId}`;
                if (navigator.share) {
                    navigator.share({
                        title: 'Check out this video!',
                        url: videoUrl
                    });
                } else {
                    navigator.clipboard.writeText(videoUrl);
                }
            });
            
            // Repost button functionality - using event delegation for dynamic content
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.repost-btn');
                if (!btn) return;
                
                console.log('Repost button clicked:', btn);
                e.preventDefault();
                e.stopPropagation();
                
                const videoId = btn.getAttribute('data-video-id');
                const countSpan = btn.closest('.action-item').querySelector('.action-count');
                const wasReposted = btn.classList.contains('reposted');
                let count = parseInt(countSpan.textContent);
                
                // Toggle UI immediately
                if (!wasReposted) {
                    btn.classList.add('reposted');
                    btn.style.color = '#17bf63';
                    countSpan.textContent = count + 1;
                } else {
                    btn.classList.remove('reposted');
                    btn.style.color = '#fff';
                    countSpan.textContent = Math.max(0, count - 1);
                }
                
                // Make API call
                fetch(`/videos/${videoId}/repost`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        countSpan.textContent = data.reposts_count;
                        if (data.is_reposted) {
                            btn.classList.add('reposted');
                            btn.style.color = '#17bf63';
                        } else {
                            btn.classList.remove('reposted');
                            btn.style.color = '#fff';
                        }
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Revert UI on error
                    if (wasReposted) {
                        btn.classList.add('reposted');
                        btn.style.color = '#17bf63';
                        countSpan.textContent = count;
                    } else {
                        btn.classList.remove('reposted');
                        btn.style.color = '#fff';
                        countSpan.textContent = count;
                    }
                });
            });
            
            // Comments functionality
            function loadComments(videoId) {
                const commentsList = document.getElementById('comments-list');
                if (!commentsList) return;
                
                commentsList.innerHTML = '<div class="loading">Loading comments...</div>';
                
                fetch(`/videos/${videoId}/comments`, {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                        'Accept': 'application/json'
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Comments API Response:', data);
                    
                    // Check if data.success exists and is true
                    if (data.success !== false) {
                        // Get comments array - could be data.comments or data.data.comments
                        let comments = data.comments || data.data?.comments || [];
                        
                        // If comments is not an array, try to convert it
                        if (!Array.isArray(comments)) {
                            if (typeof comments === 'object' && comments !== null) {
                                comments = Object.values(comments);
                            } else {
                                comments = [];
                            }
                        }
                        
                        console.log('Parsed comments array:', comments);
                        console.log('Comments count:', comments.length);
                        
                        if (comments && comments.length > 0) {
                            const commentsHtml = comments.map(comment => {
                                // Handle different comment structures
                                const user = comment.user || {};
                                const userName = user.name || comment.user_name || 'User';
                                const userUsername = user.username || comment.user_username || 'user';
                                const commentContent = comment.content || '';
                                // Backend returns created_at as "2 hours ago" format, so use it directly
                                const createdAt = comment.created_at || 'Just now';
                                
                                return `
                                    <div class="comment-item">
                                        <div class="comment-avatar-initials" style="background: ${getRandomColor()};">${getUserInitials(userName)}</div>
                                        <div class="comment-content">
                                            <strong>${userName}</strong>
                                            <p>${commentContent}</p>
                                            <span class="comment-time">${createdAt}</span>
                                        </div>
                                    </div>
                                `;
                            }).join('');
                            commentsList.innerHTML = commentsHtml;
                        } else {
                            commentsList.innerHTML = '<div class="no-comments"><p>No comments yet. Be the first to comment!</p></div>';
                        }
                    } else {
                        // If success is false, show no comments
                        commentsList.innerHTML = '<div class="no-comments"><p>No comments yet. Be the first to comment!</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading comments:', error);
                    commentsList.innerHTML = '<div class="no-comments"><p>No comments yet. Be the first to comment!</p></div>';
                });
            }
            
            // Post comment functionality
            const commentInput = document.getElementById('comment-input');
            const postCommentBtn = document.getElementById('post-comment');
            let currentVideoIdForComments = null;
            
            if (commentInput && postCommentBtn) {
                postCommentBtn.addEventListener('click', function() {
                    const comment = commentInput.value.trim();
                    if (comment && currentVideoIdForComments) {
                        postCommentBtn.disabled = true;
                        postCommentBtn.textContent = 'Posting...';
                        
                        fetch(`/videos/${currentVideoIdForComments}/comments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                content: comment
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                loadComments(currentVideoIdForComments);
                                commentInput.value = '';
                                
                                const currentVideo = document.querySelector(`.video-item[data-video-id="${currentVideoIdForComments}"]`);
                                const commentCountSpan = currentVideo.querySelector('.comment-btn').closest('.action-item').querySelector('.action-count');
                                if (commentCountSpan) {
                                    const count = parseInt(commentCountSpan.textContent);
                                    commentCountSpan.textContent = count + 1;
                                }
                            }
                        })
                        .catch(error => console.error('Error posting comment:', error))
                        .finally(() => {
                            postCommentBtn.disabled = false;
                            postCommentBtn.textContent = 'Post';
                        });
                    }
                });
                
                commentInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        postCommentBtn.click();
                    }
                });
            }
            
            // Helper functions
            function getRandomColor() {
                const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7'];
                return colors[Math.floor(Math.random() * colors.length)];
            }
            
            function getUserInitials(name) {
                const parts = name.split(' ');
                return parts.length >= 2 ? 
                    (parts[0][0] + parts[1][0]).toUpperCase() : 
                    name.substring(0, 2).toUpperCase();
            }
            
            function formatTime(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                
                const minutes = Math.floor(diff / (1000 * 60));
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                
                if (minutes < 60) {
                    return `${minutes}m ago`;
                } else if (hours < 24) {
                    return `${hours}h ago`;
                } else {
                    return `${days}d ago`;
                }
            }
            
            // Increment video views
            function incrementVideoViews(videoId) {
                fetch(`/videos/${videoId}/views`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log('Views incremented:', data.views_count);
                    }
                })
                .catch(error => console.error('Error incrementing views:', error));
            }
            
            // Update current video index on scroll
            if (videoFeed) {
                videoFeed.addEventListener('scroll', function() {
                    const videoItems = document.querySelectorAll('.video-item');
                    videoItems.forEach((item, index) => {
                        const rect = item.getBoundingClientRect();
                        if (rect.top >= 0 && rect.top < window.innerHeight / 2) {
                            currentVideoIndex = index;
                        }
                    });
                });
            }
            
            // Header Menu Modal
            const headerMenuBtn = document.getElementById('header-menu-btn');
            const headerModal = document.getElementById('header-modal');
            const headerModalClose = document.querySelector('.header-modal-close');
            
            if (headerMenuBtn && headerModal) {
                headerMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    e.preventDefault();
                    headerModal.classList.add('active');
                    document.body.classList.add('modal-open');
                    // Prevent body scroll
                    document.body.style.overflow = 'hidden';
                });
            }
            
            if (headerModalClose) {
                headerModalClose.addEventListener('click', function() {
                    headerModal.classList.remove('active');
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                });
            }
            
            // Close menu modal when clicking outside
            if (headerModal) {
                headerModal.addEventListener('click', function(e) {
                    if (e.target === headerModal) {
                        headerModal.classList.remove('active');
                        document.body.classList.remove('modal-open');
                        document.body.style.overflow = '';
                    }
                });
            }
            
            // Close modal on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && headerModal && headerModal.classList.contains('active')) {
                    headerModal.classList.remove('active');
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                }
            });
            
            // TikTok-Style Search Modal
            const searchBtn = document.getElementById('search-btn');
            const searchModal = document.getElementById('search-modal');
            const searchBackBtn = document.getElementById('search-back-btn');
            const searchInput = document.getElementById('search-input');
            const searchClearBtn = document.getElementById('search-clear-btn');
            const searchSendBtn = document.getElementById('search-send-btn');
            const searchResults = document.getElementById('search-results');
            
            function openSearchModal() {
                if (searchModal) {
                    searchModal.classList.add('active');
                    setTimeout(() => {
                        if (searchInput) {
                            searchInput.focus();
                        }
                    }, 300);
                }
            }
            
            function closeSearchModal() {
                if (searchModal) {
                    searchModal.classList.remove('active');
                }
                if (searchInput) {
                    searchInput.value = '';
                    updateClearButton();
                }
                if (searchResults) {
                    searchResults.innerHTML = '<div class="search-placeholder">Start typing to search...</div>';
                }
            }
            
            if (searchBtn) {
                searchBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    openSearchModal();
                });
            }
            
            if (searchBackBtn) {
                searchBackBtn.addEventListener('click', function() {
                    closeSearchModal();
                });
            }
            
            function updateClearButton() {
                if (searchClearBtn && searchInput) {
                    if (searchInput.value.trim().length > 0) {
                        searchClearBtn.classList.add('visible');
                    } else {
                        searchClearBtn.classList.remove('visible');
                    }
                }
            }
            
            if (searchClearBtn) {
                searchClearBtn.addEventListener('click', function() {
                    if (searchInput) {
                        searchInput.value = '';
                        searchInput.focus();
                        updateClearButton();
                        if (searchResults) {
                            searchResults.innerHTML = '<div class="search-placeholder">Start typing to search...</div>';
                        }
                    }
                });
            }
            
            // Search functionality with suggestions
            if (searchInput && searchResults) {
                let searchTimeout;
                
                searchInput.addEventListener('input', function() {
                    updateClearButton();
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length > 0) {
                        searchTimeout = setTimeout(() => {
                            performSearch(query);
                        }, 300);
                    } else {
                        searchResults.innerHTML = '<div class="search-placeholder">Start typing to search...</div>';
                    }
                });
                
                searchInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        const query = this.value.trim();
                        if (query.length > 0) {
                            performSearch(query);
                        }
                    }
                });
            }
            
            if (searchSendBtn) {
                searchSendBtn.addEventListener('click', function() {
                    if (searchInput) {
                        const query = searchInput.value.trim();
                        if (query.length > 0) {
                            performSearch(query);
                        }
                    }
                });
            }
            
            function performSearch(query) {
                if (!searchResults) return;
                
                searchResults.innerHTML = '<div class="search-loading">Searching...</div>';
                
                // Fetch both videos and users
                Promise.all([
                    fetch(`/api/videos/search?query=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    }).then(r => {
                        if (!r.ok) throw new Error('Video search failed');
                        return r.json();
                    }).catch(e => ({ success: false, videos: [] })),
                    fetch(`/search/users?query=${encodeURIComponent(query)}`, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        }
                    }).then(r => {
                        if (!r.ok) throw new Error('User search failed');
                        return r.json();
                    }).catch(e => ({ success: false, data: [] }))
                ])
                .then(([videosData, usersData]) => {
                    let html = '';
                    
                    // Show search term suggestions
                    if (query.length > 0 && query.length < 15) {
                        html += '<div class="search-suggestions">';
                        // Generate suggestions based on query
                        const suggestions = [
                            query + ' edit',
                            query + ' therapy',
                            query + ' video'
                        ].slice(0, 3);
                        
                        suggestions.forEach(suggestion => {
                            html += `
                                <div class="search-suggestion-item" onclick="searchFor('${suggestion.replace(/'/g, "\\'")}')">
                                    <div class="suggestion-icon">
                                        <i class="fas fa-search"></i>
                                    </div>
                                    <div class="suggestion-text">${escapeHtml(suggestion)}</div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    
                    // Show user results - handle both paginated and non-paginated responses
                    let users = [];
                    if (usersData.success) {
                        if (Array.isArray(usersData.data)) {
                            users = usersData.data;
                        } else if (usersData.data && Array.isArray(usersData.data.data)) {
                            users = usersData.data.data;
                        }
                    }
                    
                    if (users.length > 0) {
                        html += '<div class="search-suggestions">';
                        users.slice(0, 5).forEach(user => {
                            // Handle different user object structures
                            const userObj = user.user || user;
                            const username = userObj.username || userObj.name || 'user';
                            const displayName = userObj.name || username;
                            const avatar = userObj.avatar || '';
                            const initials = getUserInitials(displayName);
                            const isVerified = userObj.is_verified || userObj.verified || false;
                            const bio = userObj.bio || '';
                            
                            html += `
                                <div class="user-profile-suggestion" onclick="goToProfile('${escapeHtml(username)}')">
                                    <div class="user-avatar-search">
                                        ${avatar ? `<img src="${escapeHtml(avatar)}" alt="${escapeHtml(displayName)}">` : initials}
                                    </div>
                                    <div class="user-info-search">
                                        <div class="username">
                                            ${escapeHtml(displayName)}
                                            ${isVerified ? '<i class="fas fa-check-circle verified-badge"></i>' : ''}
                                        </div>
                                        <div class="user-desc">@${escapeHtml(username)}${bio ? ' • ' + escapeHtml(bio.substring(0, 50)) : ''}</div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    
                    // Show video results with thumbnails
                    if (videosData.success && videosData.videos && videosData.videos.length > 0) {
                        html += '<div class="search-suggestions video-grid">';
                        videosData.videos.slice(0, 12).forEach(video => {
                            const videoTitle = video.title || 'Untitled Video';
                            const userName = video.user_name || video.user?.name || 'Unknown';
                            const videoId = video.id;
                            const thumbnail = video.thumbnail_url || video.video_url || '';
                            const likesCount = video.likes_count || 0;
                            const viewsCount = video.views_count || 0;
                            
                            // Format numbers (1000 -> 1K, 1000000 -> 1M)
                            const formatCount = (count) => {
                                if (count >= 1000000) return (count / 1000000).toFixed(1) + 'M';
                                if (count >= 1000) return (count / 1000).toFixed(1) + 'K';
                                return count;
                            };
                            
                            html += `
                                <div class="search-result-video" onclick="goToVideo('${videoId}')">
                                    <div class="video-thumbnail">
                                        ${thumbnail ? `
                                            <video 
                                                src="${escapeHtml(thumbnail)}" 
                                                poster="${escapeHtml(thumbnail)}"
                                                muted 
                                                loop 
                                                playsinline
                                                onmouseover="this.play()" 
                                                onmouseout="this.pause()"
                                                style="width: 100%; height: 100%; object-fit: cover;"
                                            ></video>
                                        ` : `
                                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-video" style="font-size: 32px; color: rgba(255,255,255,0.5);"></i>
                                            </div>
                                        `}
                                        <div class="video-overlay-stats">
                                            <span><i class="fas fa-heart"></i> ${formatCount(likesCount)}</span>
                                        </div>
                                    </div>
                                    <div class="video-info-search">
                                        <h4>${escapeHtml(videoTitle)}</h4>
                                        <p><i class="fas fa-user-circle"></i> ${escapeHtml(userName)}</p>
                                        ${viewsCount > 0 ? `<p class="video-views"><i class="fas fa-eye"></i> ${formatCount(viewsCount)} views</p>` : ''}
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';
                    }
                    
                    if (html === '') {
                        html = `<div class="no-search-results">No results found for "${escapeHtml(query)}"</div>`;
                    }
                    
                    searchResults.innerHTML = html;
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="no-search-results">Search failed. Please try again.</div>';
                });
            }
            
            function searchFor(query) {
                if (searchInput) {
                    searchInput.value = query;
                    updateClearButton();
                    performSearch(query);
                }
            }
            
            function goToProfile(username) {
                window.location.href = '/' + username;
            }
            
            function escapeHtml(text) {
                const div = document.createElement('div');
                div.textContent = text;
                return div.innerHTML;
            }
            
            window.searchFor = searchFor;
            window.goToProfile = goToProfile;
            
            window.goToVideo = function(videoId) {
                const videoElement = document.querySelector(`.video-item[data-video-id="${videoId}"]`);
                if (videoElement) {
                    closeSearchModal();
                    setTimeout(() => {
                        videoElement.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }, 300);
                } else {
                    // If video not found in current feed, redirect to video page
                    window.location.href = `/videos/${videoId}`;
                }
            };
            
            // Profile click functionality - using event delegation for dynamic content
            document.addEventListener('click', function(e) {
                const userInfo = e.target.closest('.user-info.clickable');
                if (!userInfo) return;
                
                e.stopPropagation();
                e.preventDefault();
                
                const username = userInfo.getAttribute('data-user-username');
                
                @auth
                    // If logged in, go to user's profile
                    if (username) {
                        window.location.href = '/' + username;
                    }
                @else
                    // If not logged in, show login prompt
                    if (confirm('Please login to view profiles')) {
                        window.location.href = '{{ route("login") }}';
                    }
                @endauth
            });
        });
    </script>
@stop