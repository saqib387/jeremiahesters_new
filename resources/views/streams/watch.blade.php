@extends('layouts.generic')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-0">
                    <div class="stream-container position-relative">
                        <video 
                            id="videoPlayer"
                            class="w-100" 
                            controls
                            autoplay
                            style="height: 500px; object-fit: cover; background: #000;">
                        </video>
                        <div class="stream-status position-absolute top-0 start-0 m-2">
                            <span class="badge bg-success">LIVE</span>
                            <span class="badge bg-secondary" id="viewerCount">0 viewers</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5 class="mb-0">{{ $stream->title }}</h5>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ $stream->description }}</p>
                    <div class="d-flex align-items-center">
                        <img src="{{ $stream->user->avatar }}" alt="{{ $stream->user->name }}" class="rounded-circle me-2" width="32" height="32">
                        <div>
                            <h6 class="mb-0">{{ $stream->user->name }}</h6>
                            <small class="text-muted">{{ $stream->started_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Live Chat</h5>
                </div>
                <div class="card-body p-0">
                    <div class="chat-messages p-3" id="chatMessages" style="height: 400px; overflow-y: auto;">
                        @foreach($stream->messages as $message)
                            <div class="message mb-2">
                                @if($message->is_system)
                                    <div class="text-center text-muted small">
                                        {{ $message->message }}
                                    </div>
                                @else
                                    <div class="d-flex align-items-start">
                                        <img src="{{ $message->user->avatar }}" alt="{{ $message->user->name }}" class="rounded-circle me-2" width="24" height="24">
                                        <div>
                                            <div class="d-flex align-items-center">
                                                <strong class="me-2">{{ $message->user->name }}</strong>
                                                <small class="text-muted">{{ $message->created_at->format('H:i') }}</small>
                                            </div>
                                            <div>{{ $message->message }}</div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="chat-input p-3 border-top">
                        <form id="chatForm" class="d-flex">
                            <input type="text" class="form-control me-2" id="messageInput" placeholder="Type a message...">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const video = document.getElementById('videoPlayer');
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const viewerCount = document.getElementById('viewerCount');
    const streamId = "{{ $stream->id }}";

    // Initialize HLS
    if (Hls.isSupported()) {
        const hls = new Hls();
        hls.loadSource("{{ $stream->hls_url }}");
        hls.attachMedia(video);
        hls.on(Hls.Events.MANIFEST_PARSED, function() {
            video.play();
        });
    }
    // For browsers that support native HLS
    else if (video.canPlayType('application/vnd.apple.mpegurl')) {
        video.src = "{{ $stream->hls_url }}";
    }

    // Scroll chat to bottom
    function scrollChatToBottom() {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    scrollChatToBottom();

    // Send chat message
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (!message) return;

        fetch(`/streams/${streamId}/messages`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message })
        })
        .then(response => response.json())
        .then(data => {
            messageInput.value = '';
            appendMessage(data);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message');
        });
    });

    // Append message to chat
    function appendMessage(message) {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'message mb-2';
        
        if (message.is_system) {
            messageDiv.innerHTML = `
                <div class="text-center text-muted small">
                    ${message.message}
                </div>
            `;
        } else {
            messageDiv.innerHTML = `
                <div class="d-flex align-items-start">
                    <img src="${message.user.avatar}" alt="${message.user.name}" class="rounded-circle me-2" width="24" height="24">
                    <div>
                        <div class="d-flex align-items-center">
                            <strong class="me-2">${message.user.name}</strong>
                            <small class="text-muted">${new Date(message.created_at).toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' })}</small>
                        </div>
                        <div>${message.message}</div>
                    </div>
                </div>
            `;
        }
        
        chatMessages.appendChild(messageDiv);
        scrollChatToBottom();
    }

    // Poll for new messages
    function pollMessages() {
        fetch(`/streams/${streamId}/messages`)
            .then(response => response.json())
            .then(messages => {
                const lastMessage = messages[messages.length - 1];
                if (lastMessage && (!window.lastMessageId || lastMessage.id > window.lastMessageId)) {
                    window.lastMessageId = lastMessage.id;
                    appendMessage(lastMessage);
                }
            })
            .catch(error => console.error('Error polling messages:', error));
    }

    // Poll for viewer count
    function pollViewerCount() {
        fetch(`/streams/${streamId}/viewer-count`)
            .then(response => response.json())
            .then(data => {
                viewerCount.textContent = `${data.count} viewers`;
            })
            .catch(error => console.error('Error polling viewer count:', error));
    }

    // Start polling
    setInterval(pollMessages, 3000);
    setInterval(pollViewerCount, 5000);
});
</script>
@endpush

@push('styles')
<style>
.stream-container {
    background: #000;
    border-radius: 4px;
    overflow: hidden;
    position: relative;
}

.stream-container video {
    width: 100%;
    height: 500px;
    object-fit: cover;
}

.chat-messages {
    background: #f8f9fa;
}

.message {
    word-break: break-word;
}

.chat-input {
    background: #fff;
}
</style>
@endpush
@endsection 