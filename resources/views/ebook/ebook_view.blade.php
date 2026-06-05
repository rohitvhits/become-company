@include('include/header')
@include('include/sidebar')

<style>
    .ebook-layout {
        display: flex;
        gap: 0;
        height: calc(100vh - 200px);
        min-height: 600px;
    }

    .ebook-sidebar {
        width: 320px;
        background: #fff;
        border-right: 2px solid #e9ecef;
        overflow-y: auto;
        flex-shrink: 0;
    }

    .ebook-sidebar-header {
        padding: 1rem;
        background: #b5e064;
        color: #333;
        position: sticky;
        top: 0;
        z-index: 10;
    }

    .ebook-sidebar-header h2 {
        font-size: 1.1rem;
        margin: 0;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .ebook-sidebar-header p {
        font-size: 0.85rem;
        margin: 0.25rem 0 0 0;
        opacity: 0.95;
    }

    .ebook-video-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ebook-video-item {
        border-bottom: 1px solid #e9ecef;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .ebook-video-item:hover {
        background: #f8f9fa;
    }

    .ebook-video-item.active {
        background: #f0f8e5;
        border-left: 4px solid #b5e064;
    }

    .ebook-video-item-link {
        padding: 0.85rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        text-decoration: none;
        color: #333;
    }

    .ebook-video-item.active .ebook-video-item-link {
        color: #6b9e34;
        font-weight: 600;
    }

    .ebook-video-number {
        background: #b5e064;
        color: #333;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .ebook-video-item.active .ebook-video-number {
        background: #9bc952;
        color: #fff;
    }

    .ebook-video-info {
        flex: 1;
        min-width: 0;
    }

    .ebook-video-title {
        font-size: 0.9rem;
        margin: 0;
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .ebook-main-content {
        flex: 1;
        background: #fff;
        overflow-y: auto;
        display: flex;
        flex-direction: column;
    }

    .ebook-video-content {
        display: grid;
        grid-template-columns: 70% 30%;
        height: 100%;
        gap: 0;
    }

    .ebook-player-container {
        display: flex;
        align-items: baseline;
        justify-content: center;
        padding: 1.5rem;
    }

    .ebook-video-wrapper {
        position: relative;
        width: 100%;
        padding-bottom: 56.25%;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 8px 24px rgba(181, 224, 100, 0.3);
    }

    .ebook-video-wrapper iframe {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border: none;
        border-radius: 12px;
    }

    .ebook-content-area {
        padding: 1rem;
        overflow-y: auto;
        background: #f8f9fa;
        border-left: 3px solid #b5e064;
    }

    .ebook-content-header {
        margin-bottom: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #b5e064;
    }

    .ebook-content-title {
        font-size: 1.1rem;
        color: #6b9e34;
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        line-height: 1.3;
    }

    .ebook-content-title .badge {
        font-size: 0.75rem;
    }

    .ebook-content-meta {
        color: #6c757d;
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .ebook-description-section {
        margin-top: 0;
    }

    .ebook-description-section h3 {
        font-size: 0.95rem;
        color: #333;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .ebook-description-section h3 i {
        color: #b5e064;
        font-size: 0.9rem;
    }

    .ebook-description-content {
        color: #495057;
        font-size: 0.85rem;
        line-height: 1.6;
    }

    .ebook-description-content p {
        margin-bottom: 0.6rem;
    }

    .ebook-description-content ul,
    .ebook-description-content ol {
        padding-left: 1rem;
        margin-bottom: 0.6rem;
    }

    .ebook-description-content li {
        margin-bottom: 0.3rem;
    }

    .ebook-empty-state {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        padding: 2rem;
        text-align: center;
    }

    .ebook-empty-state i {
        font-size: 4rem;
        color: #b5e064;
        margin-bottom: 1rem;
    }

    .ebook-empty-state h3 {
        font-size: 1.5rem;
        color: #495057;
        margin-bottom: 0.5rem;
    }

    .ebook-empty-state p {
        color: #6c757d;
    }

    .ebook-sidebar::-webkit-scrollbar,
    .ebook-main-content::-webkit-scrollbar,
    .ebook-content-area::-webkit-scrollbar {
        width: 6px;
    }

    .ebook-sidebar::-webkit-scrollbar-track,
    .ebook-main-content::-webkit-scrollbar-track,
    .ebook-content-area::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .ebook-sidebar::-webkit-scrollbar-thumb,
    .ebook-main-content::-webkit-scrollbar-thumb,
    .ebook-content-area::-webkit-scrollbar-thumb {
        background: #b5e064;
        border-radius: 3px;
    }

    .ebook-sidebar::-webkit-scrollbar-thumb:hover,
    .ebook-main-content::-webkit-scrollbar-thumb:hover,
    .ebook-content-area::-webkit-scrollbar-thumb:hover {
        background: #9bc952;
    }

    @media (max-width: 992px) {
        .ebook-layout {
            flex-direction: column;
            height: auto;
        }

        .ebook-sidebar {
            width: 100%;
            border-right: none;
            border-bottom: 2px solid #e9ecef;
            max-height: 300px;
        }

        .ebook-video-content {
            grid-template-columns: 1fr;
        }

        .ebook-content-area {
            border-left: none;
            border-top: 3px solid #b5e064;
        }
    }
</style>

<div class="main-panel">
    <div class="content-wrapper">
        @if(isset($ebookData) && !empty($ebookData))
            <div class="ebook-layout">
                <div class="ebook-sidebar">
                    <div class="ebook-sidebar-header">
                        <h2><i class="mdi mdi-play-circle-outline"></i> Ebook</h2>
                        <p>{{ count($ebookData) }} tutorials available</p>
                    </div>
                    <ul class="ebook-video-list">
                        @foreach($ebookData as $key => $res)
                            <li class="ebook-video-item {{ $key == 0 ? 'active' : '' }}" data-video-id="{{ $key }}">
                                <a href="javascript:void(0);" class="ebook-video-item-link" onclick="loadVideo({{ $key }})">
                                    <div class="ebook-video-number">{{ $key + 1 }}</div>
                                    <div class="ebook-video-info">
                                        <h4 class="ebook-video-title">{{ $res['title'] }}</h4>
                                    </div>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <div class="ebook-main-content">
                    @foreach($ebookData as $key => $res)
                        <div class="ebook-video-content" id="video-content-{{ $key }}" style="{{ $key == 0 ? 'display: grid;' : 'display: none;' }}">
                            <div class="ebook-player-container">
                                <div class="ebook-video-wrapper">
                                    <iframe
                                        id="videoFrame{{ $key }}"
                                        src="{{ url('ebook-show-aws') }}/{{ $res['id'] }}?type=ebook"
                                        frameborder="0"
                                        allow="autoplay; encrypted-media; fullscreen"
                                        allowfullscreen>
                                    </iframe>
                                </div>
                            </div>
                            <div class="ebook-content-area">
                                <div class="ebook-content-header">
                                    <h1 class="ebook-content-title">
                                        {{ $res['title'] }}
                                    </h1>
                                    <div class="ebook-content-meta">
                                        <i class="mdi mdi-playlist-play"></i> Tutorial {{ $key + 1 }} of {{ count($ebookData) }}
                                    </div>
                                </div>
                                <div class="ebook-description-section">
                                    <h3><i class="mdi mdi-text-box-outline"></i> Description</h3>
                                    <div class="ebook-description-content">
                                        {!! $res['content'] !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <script>
                function loadVideo(videoId) {
                    // Hide all video contents
                    document.querySelectorAll('.ebook-video-content').forEach(function(el) {
                        el.style.display = 'none';
                    });

                    // Show selected video content
                    document.getElementById('video-content-' + videoId).style.display = 'grid';

                    // Update active state in sidebar
                    document.querySelectorAll('.ebook-video-item').forEach(function(el) {
                        el.classList.remove('active');
                    });
                    document.querySelector('[data-video-id="' + videoId + '"]').classList.add('active');

                    // Scroll description area to top
                    var contentArea = document.querySelector('#video-content-' + videoId + ' .ebook-content-area');
                    if (contentArea) {
                        contentArea.scrollTop = 0;
                    }
                }
            </script>
        @else
            <div class="ebook-empty-state">
                <i class="mdi mdi-video-off-outline"></i>
                <h3>No Ebook Available</h3>
                <p>Check back later for new content</p>
            </div>
        @endif
    </div>
</div>

@include('include/footer')