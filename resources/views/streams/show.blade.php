@extends('layouts.generic')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body p-0">
                    <div class="stream-container position-relative">
                        <video 
                            id="remoteVideo"
                            class="w-100" 
                            autoplay 
                            playsinline
                            style="height: 500px; object-fit: cover; background: #000;">
                        </video>
                        <div class="stream-status position-absolute top-0 start-0 m-2">
                            <span class="badge bg-danger" id="streamStatus">OFFLINE</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card mb-3">
                <div class="card-body">
                    <h4 class="card-title">{{ $stream->title }}</h4>
                    <p class="card-text">
                        <i class="fas fa-video"></i> {{ ucfirst($stream->type) }}
                    </p>
                    <p class="card-text">
                        <i class="fas fa-user"></i> {{ $stream->user->name }}
                    </p>
                </div>
            </div>

            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Live Chat</h5>
                    <button class="btn btn-sm btn-outline-secondary" id="clearChat">
                        <i class="fas fa-trash"></i> Clear
                    </button>
                </div>
                <div class="card-body">
                    <div id="chatMessages" class="chat-messages mb-3" style="height: 300px; overflow-y: auto;">
                        <!-- Chat messages will appear here -->
                    </div>
                    @auth
                        <form id="chatForm" class="chat-form">
                            <div class="input-group">
                                <input type="text" id="chatInput" class="form-control" placeholder="Type a message...">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane"></i>
                                </button>
                            </div>
                        </form>
                    @else
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle"></i> Please <a href="{{ route('login') }}">login</a> to participate in chat
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const remoteVideo = document.getElementById('remoteVideo');
    const streamStatus = document.getElementById('streamStatus');
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const clearChatButton = document.getElementById('clearChat');
    
    let peerConnection = null;
    let streamId = '{{ $stream->id }}';

    // Initialize WebRTC
    async function initializeWebRTC() {
        try {
            peerConnection = new RTCPeerConnection({
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' }
                ]
            });

            // Handle incoming stream
            peerConnection.ontrack = event => {
                remoteVideo.srcObject = event.streams[0];
                streamStatus.textContent = 'LIVE';
                streamStatus.classList.remove('bg-danger');
                streamStatus.classList.add('bg-success');
            };

            // Handle ICE candidates
            peerConnection.onicecandidate = event => {
                if (event.candidate) {
                    fetch(`/api/streams/${streamId}/ice-candidate`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ candidate: event.candidate })
                    });
                }
            };

            // Create and send answer
            const response = await fetch(`/api/streams/${streamId}/watch`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();
            await peerConnection.setRemoteDescription(new RTCSessionDescription(data.sdp));
            
            const answer = await peerConnection.createAnswer();
            await peerConnection.setLocalDescription(answer);

            // Send answer to server
            await fetch(`/api/streams/${streamId}/answer`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ sdp: peerConnection.localDescription })
            });
        } catch (error) {
            console.error('Error initializing WebRTC:', error);
            alert('Error connecting to stream. Please try again.');
        }
    }

    // Load chat messages
    async function loadChatMessages() {
        try {
            const response = await fetch(`/api/streams/${streamId}/messages`);
            const messages = await response.json();
            
            chatMessages.innerHTML = messages.map(message => `
                <div class="chat-message ${message.is_system ? 'system-message' : ''}">
                    ${message.is_system ? 
                        `<div class="system-message">${message.message}</div>` :
                        `<div class="user-message">
                            <strong>${message.user.name}:</strong> ${message.message}
                        </div>`
                    }
                </div>
            `).join('');
            
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } catch (error) {
            console.error('Error loading chat messages:', error);
        }
    }

    // Send chat message
    async function sendChatMessage(message) {
        try {
            const response = await fetch(`/api/streams/${streamId}/messages`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ message })
            });

            if (!response.ok) {
                throw new Error('Failed to send message');
            }

            chatInput.value = '';
        } catch (error) {
            console.error('Error sending message:', error);
            alert('Error sending message. Please try again.');
        }
    }

    // Clear chat
    function clearChat() {
        chatMessages.innerHTML = '';
    }

    // Event listeners
    if (chatForm) {
        chatForm.addEventListener('submit', e => {
            e.preventDefault();
            const message = chatInput.value.trim();
            if (message) {
                sendChatMessage(message);
            }
        });
    }

    clearChatButton.addEventListener('click', clearChat);

    // Initialize
    initializeWebRTC();
    loadChatMessages();

    // Listen for new messages
    Echo.private(`stream.${streamId}`)
        .listen('StreamMessageEvent', e => {
            const message = e.message;
            const messageHtml = message.is_system ? 
                `<div class="system-message">${message.message}</div>` :
                `<div class="user-message">
                    <strong>${message.user.name}:</strong> ${message.message}
                </div>`;
            
            chatMessages.insertAdjacentHTML('beforeend', `<div class="chat-message">${messageHtml}</div>`);
            chatMessages.scrollTop = chatMessages.scrollHeight;
        });
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
    border: 1px solid #ddd;
    border-radius: 4px;
    padding: 10px;
}

.chat-message {
    margin-bottom: 10px;
}

.chat-message:last-child {
    margin-bottom: 0;
}

.system-message {
    color: #666;
    font-style: italic;
    text-align: center;
    margin: 5px 0;
}

.user-message {
    background: #f8f9fa;
    padding: 8px;
    border-radius: 4px;
}

.stream-status {
    z-index: 1000;
}
</style>
@endpush
@endsection 