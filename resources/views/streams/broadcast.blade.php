@extends('layouts.generic')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">{{ $stream->title }}</h4>
                </div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h5>Stream Information</h5>
                        <p><strong>Stream Key:</strong> <code>{{ $stream->stream_key }}</code></p>
                        <p><strong>RTMP URL:</strong> <code>{{ $stream->rtmp_url }}</code></p>
                        <p><strong>HLS URL:</strong> <code>{{ $stream->hls_url }}</code></p>
                    </div>

                    <div class="alert alert-warning">
                        <h5>How to Stream</h5>
                        <ol>
                            <li>Download and install <a href="https://obsproject.com/" target="_blank">OBS Studio</a></li>
                            <li>Open OBS Studio and go to Settings > Stream</li>
                            <li>Select "Custom" as the service</li>
                            <li>Enter the RTMP URL in the "Server" field</li>
                            <li>Enter the Stream Key in the "Stream Key" field</li>
                            <li>Click "OK" to save settings</li>
                            <li>Click "Start Streaming" to begin broadcasting</li>
                        </ol>
                    </div>

                    <div class="alert alert-danger">
                        <h5>Important Notes</h5>
                        <ul>
                            <li>Keep your stream key secret. Anyone with this key can stream to your channel.</li>
                            <li>Recommended stream settings:
                                <ul>
                                    <li>Resolution: 1280x720 (720p)</li>
                                    <li>Frame Rate: 30 fps</li>
                                    <li>Bitrate: 2500-4000 Kbps</li>
                                    <li>Audio: 128 Kbps, 44.1 kHz</li>
                                </ul>
                            </li>
                            <li>Make sure you have a stable internet connection with sufficient upload speed.</li>
                        </ul>
                    </div>

                    <div class="text-center">
                        <a href="{{ route('streams.show', $stream) }}" class="btn btn-primary">View Stream</a>
                        <button type="button" class="btn btn-danger" onclick="endStream()">End Stream</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const streamId = {{ $stream->id }};

function endStream() {
    if (confirm('Are you sure you want to end the stream?')) {
        fetch(`/streams/${streamId}/end`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                stream_key: '{{ $stream->stream_key }}'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.message) {
                window.location.href = '{{ route('streams.index') }}';
            } else {
                alert('Failed to end stream: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while ending the stream');
        });
    }
}
</script>
@endpush
@endsection 