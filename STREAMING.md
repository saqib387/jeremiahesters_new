# Streaming Setup Guide

This guide will help you set up the streaming environment for the application.

## Prerequisites

1. Nginx with RTMP module
2. FFmpeg
3. OBS Studio (or similar streaming software)

## Installation Steps

### 1. Install Nginx with RTMP Module

#### On Ubuntu/Debian:
```bash
# Install dependencies
sudo apt-get update
sudo apt-get install build-essential libpcre3 libpcre3-dev libssl-dev zlib1g-dev

# Download and compile nginx with rtmp module
wget http://nginx.org/download/nginx-1.24.0.tar.gz
wget https://github.com/arut/nginx-rtmp-module/archive/master.zip
tar -zxvf nginx-1.24.0.tar.gz
unzip master.zip
cd nginx-1.24.0
./configure --with-http_ssl_module --add-module=../nginx-rtmp-module-master
make
sudo make install
```

#### On Windows:
1. Download the pre-built nginx with rtmp module from [here](https://github.com/illuspas/nginx-rtmp-win32)
2. Extract the files to a directory of your choice
3. Add the nginx directory to your system PATH

### 2. Install FFmpeg

#### On Ubuntu/Debian:
```bash
sudo apt-get install ffmpeg
```

#### On Windows:
1. Download FFmpeg from [here](https://ffmpeg.org/download.html)
2. Extract the files to a directory of your choice
3. Add the FFmpeg directory to your system PATH

### 3. Configure Nginx

1. Copy the example configuration file:
```bash
cp nginx-rtmp.conf.example /usr/local/nginx/conf/nginx.conf  # Linux
# or
copy nginx-rtmp.conf.example C:\nginx\conf\nginx.conf  # Windows
```

2. Update the paths in the configuration file to match your system:
- Replace `/path/to/your/project` with your actual project path
- Replace `/path/to/nginx-rtmp-module` with your nginx-rtmp module path

3. Start Nginx:
```bash
sudo nginx  # Linux
# or
nginx.exe  # Windows
```

### 4. Configure the Application

1. Run the streaming setup command:
```bash
php artisan streaming:setup
```

2. Update your `.env` file with the following variables:
```
RTMP_URL=rtmp://localhost:1935/live
HLS_URL=http://localhost:8080/hls
```

### 5. Configure OBS Studio

1. Open OBS Studio
2. Go to Settings > Stream
3. Select "Custom" as the service
4. Enter your RTMP URL (e.g., `rtmp://localhost:1935/live`)
5. Enter your stream key (generated when you create a stream)

## Testing the Setup

1. Create a new stream in the application
2. Copy the RTMP URL and stream key
3. Configure OBS Studio with these details
4. Start streaming in OBS Studio
5. Open the stream page in your browser to verify everything is working

## Troubleshooting

### Common Issues

1. **Nginx won't start**
   - Check the nginx error log: `tail -f /usr/local/nginx/logs/error.log`
   - Verify the configuration: `nginx -t`

2. **Stream won't connect**
   - Verify the RTMP URL and stream key
   - Check if the RTMP port (1935) is open
   - Ensure nginx-rtmp module is properly installed

3. **HLS playback issues**
   - Check if the HLS directory exists and is writable
   - Verify the HLS URL is accessible
   - Check browser console for any errors

### Getting Help

If you encounter any issues not covered in this guide, please:
1. Check the nginx error logs
2. Verify all paths in the configuration
3. Ensure all required software is installed and in your PATH
4. Check the application logs: `storage/logs/laravel.log` 