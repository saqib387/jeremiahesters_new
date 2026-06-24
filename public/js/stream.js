class StreamManager {
    constructor() {
        this.stream = null;
        this.peerConnection = null;
        this.streamId = null;
    }

    async startStream(type, streamId) {
        try {
            this.streamId = streamId;
            
            if (type === 'screen') {
                this.stream = await navigator.mediaDevices.getDisplayMedia({
                    video: true,
                    audio: true
                });
            } else {
                this.stream = await navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: true
                });
            }

            // Initialize WebRTC connection
            this.peerConnection = new RTCPeerConnection({
                iceServers: [
                    { urls: 'stun:stun.l.google.com:19302' }
                ]
            });
            
            // Add local stream to peer connection
            this.stream.getTracks().forEach(track => {
                this.peerConnection.addTrack(track, this.stream);
            });

            // Handle ICE candidates
            this.peerConnection.onicecandidate = (event) => {
                if (event.candidate) {
                    this.sendIceCandidate(event.candidate);
                }
            };

            // Create and send offer
            const offer = await this.peerConnection.createOffer();
            await this.peerConnection.setLocalDescription(offer);

            // Send offer to server
            const response = await fetch(`/streams/${this.streamId}/start`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ offer: offer })
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.answer) {
                await this.peerConnection.setRemoteDescription(new RTCSessionDescription(data.answer));
            }

            return this.stream;
        } catch (error) {
            console.error('Error starting stream:', error);
            throw error;
        }
    }

    async stopStream() {
        try {
            if (this.stream) {
                this.stream.getTracks().forEach(track => track.stop());
                this.stream = null;
            }

            if (this.peerConnection) {
                this.peerConnection.close();
                this.peerConnection = null;
            }

            if (this.streamId) {
                await fetch(`/streams/${this.streamId}/end`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
            }
        } catch (error) {
            console.error('Error stopping stream:', error);
            throw error;
        }
    }

    async sendIceCandidate(candidate) {
        try {
            await fetch(`/streams/${this.streamId}/ice-candidate`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ candidate: candidate })
            });
        } catch (error) {
            console.error('Error sending ICE candidate:', error);
        }
    }
}

// Initialize stream manager when document is ready
document.addEventListener('DOMContentLoaded', function() {
    const streamForm = document.getElementById('streamForm');
    if (streamForm) {
        const streamManager = new StreamManager();
        const streamId = streamForm.dataset.streamId;

        streamForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const type = document.querySelector('input[name="type"]:checked').value;
            
            try {
                await streamManager.startStream(type, streamId);
                // Don't submit the form, let the WebRTC connection handle it
            } catch (error) {
                alert('Error starting stream: ' + error.message);
            }
        });
    }
}); 