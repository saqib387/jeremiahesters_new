# Project Overview - JustFans Cryptocurrency Platform

## Executive Summary

**JustFans** is a comprehensive paid creator social media platform built on Laravel 9, featuring advanced cryptocurrency/NFT functionality, video streaming, subscription management, and a full payment ecosystem. The platform enables creators to monetize their content through subscriptions, pay-per-post content, live streaming, cryptocurrency tokens, and NFT marketplace.

---

## Technology Stack

### Backend
- **Framework**: Laravel 9.x (PHP 8.0.2+)
- **Database**: MySQL/MariaDB (5.7/8.X)
- **Admin Panel**: TCG Voyager 1.5
- **Payment Processors**: 
  - Stripe
  - PayPal
  - Paystack
  - Coinbase
  - NowPayments
  - CCBill
  - Mercado Pago
- **File Storage**: Local/S3 compatible storage
- **Video Processing**: FFmpeg (pbmedia/laravel-ffmpeg)
- **Streaming**: Nginx RTMP module for live streaming

### Frontend
- **CSS Framework**: Bootstrap 4.6.0
- **JavaScript**: jQuery, Laravel Mix
- **Video Player**: Video.js with quality selector
- **Icons**: Ionicons
- **Charts**: Chart.js
- **UI Libraries**: 
  - Swiper.js
  - Animate.css
  - Photoswipe
  - Dropzone
  - EasyQRCodeJS

### Blockchain/Crypto
- **Smart Contracts**: Solidity (Hardhat)
- **Networks Supported**: Ethereum, Binance Smart Chain, Polygon, Solana, Avalanche, Cardano, Arbitrum, Optimism
- **Web3**: Web3.js integration
- **NFT Standard**: ERC-721 (OpenZeppelin contracts)

---

## Core Features

### 1. User Management & Authentication
- Email verification
- Two-factor authentication (2FA)
- Social login (OAuth)
- User roles & permissions
- Device verification
- User profiles with verification badges
- Public/private profiles
- Subscription-based profile access

### 2. Content Management
- **Posts**: Text, image, and video posts with pay-per-view
- **Videos**: Upload, process, and stream videos
- **Live Streaming**: RTMP-based live streaming with WebRTC support
- **Bookmarks**: Save content for later
- **Lists**: Create custom user lists (followers, following, blocked)
- **Comments & Reactions**: Engage with content
- **Pinned Posts**: Highlight important content

### 3. Subscription System
- Monthly, 3-month, 6-month, and 12-month subscription tiers
- Recurring payments via Stripe/PayPal/CCBill
- Subscription cancellation handling
- Revenue sharing between platform and creators
- Subscription analytics

### 4. Payment & Wallet System
- **Payment Methods**: Multiple gateways (Stripe, PayPal, Paystack, etc.)
- **Wallet System**: Internal wallet with credits
- **Deposits**: Add funds to wallet
- **Withdrawals**: Request payouts (admin approval required)
- **Transactions**: Complete transaction history
- **Invoices**: Generate invoices for transactions
- **Tax Management**: Country-based tax calculation

### 5. Cryptocurrency Module
- **Token Creation**: Users can create their own tokens
- **Token Types**: Utility, Security, Governance, Payment, NFT, DeFi, Gaming, Social
- **Token Features**:
  - Custom pricing (initial and current)
  - Supply management (total, available, circulating)
  - Fee structure (creator fee, platform fee, liquidity pool)
  - Burning and minting capabilities
  - Transferability controls
- **Wallet Integration**: Crypto wallets for each user/token
- **Trading**: Buy/sell tokens with fee distribution
- **Transaction History**: Complete audit trail
- **Revenue Sharing**: Automatic distribution of creator fees

### 6. NFT Marketplace
- **NFT Creation**: Create and mint NFTs
- **Marketplace**: Browse, buy, and sell NFTs
- **Smart Contract**: ERC-721 standard (OpenZeppelin)
- **Resale**: Resell owned NFTs
- **Listing Management**: View your listings and NFTs
- **Blockchain Integration**: MetaMask connectivity

### 7. Live Streaming
- **RTMP Streaming**: Nginx RTMP module for streaming
- **HLS Playback**: HTTP Live Streaming for viewers
- **WebRTC Support**: Peer-to-peer streaming option
- **Stream Management**: Create, start, end streams
- **Chat Integration**: Live chat during streams
- **VOD (Video on Demand)**: Recorded streams available after broadcast
- **Thumbnail Generation**: Automatic thumbnail creation

### 8. Messenger System
- Real-time messaging between users
- Contact management
- Message authorization (approve contacts)
- Message deletion
- Read receipts
- Pusher integration for real-time updates

### 9. Notification System
- Email notifications
- In-app notifications
- Notification preferences
- Notification history

### 10. Admin Panel (Voyager)
- Custom dashboard with analytics
- User management
- Content moderation
- Revenue management
- Token/cryptocurrency management
- Wallet management
- Audit logs
- System health monitoring
- Export capabilities (CSV)

---

## Database Schema

### Core Tables
- `users` - User accounts and profiles
- `posts` - Content posts
- `attachments` - Media files (images, videos)
- `subscriptions` - User subscriptions
- `transactions` - Payment transactions
- `wallets` - User wallets (internal credits)
- `withdrawals` - Withdrawal requests
- `invoices` - Generated invoices
- `notifications` - User notifications

### Cryptocurrency Tables
- `cryptocurrencies` - Token definitions
- `crypto_wallets` - User crypto wallets
- `crypto_transactions` - Crypto transaction history
- `crypto_revenue_shares` - Revenue distribution records

### NFT Tables
- `nfts` - NFT records
- `nft_listings` - Active marketplace listings
- `nft_transactions` - NFT sale history

### Streaming Tables
- `streams` - Stream definitions
- `stream_messages` - Stream chat messages
- `videos` - Video records
- `video_comments` - Video comments
- `video_likes` - Video likes
- `video_shares` - Video shares

### Supporting Tables
- `post_comments` - Post comments
- `reactions` - User reactions (likes, etc.)
- `user_lists` - Custom user lists
- `user_list_members` - List memberships
- `user_bookmarks` - Saved content
- `user_messages` - Private messages
- `countries` - Country data
- `country_taxes` - Tax rates by country
- `public_pages` - CMS pages

---

## Key Controllers

### Public/User Controllers
- `HomeController` - Homepage
- `ProfileController` - User profiles
- `PostsController` - Content management
- `FeedController` - Feed and video interactions
- `VideoController` - Video management
- `StreamController` - Live streaming
- `CryptocurrencyController` - Crypto functionality
- `NFTMarketplaceController` - NFT operations
- `PaymentsController` - Payment processing
- `SubscriptionsController` - Subscription management
- `SettingsController` - User settings
- `MessengerController` - Messaging
- `SearchController` - Search functionality

### Admin Controllers
- `Admin\DashboardController` - Admin dashboard
- `Admin\CryptocurrencyController` - Token management
- `Admin\WalletController` - Wallet management
- `Admin\RevenueController` - Revenue tracking
- `Admin\PublicPagesController` - CMS pages

---

## Service Providers

The application uses numerous service providers for modular functionality:
- `CryptocurrencyServiceProvider` - Crypto operations
- `PaymentsServiceProvider` - Payment processing
- `NotificationServiceProvider` - Notifications
- `StreamsServiceProvider` - Streaming functionality
- `AttachmentServiceProvider` - File handling
- `SettingsServiceProvider` - Configuration management
- `InvoiceServiceProvider` - Invoice generation
- `WithdrawalsServiceProvider` - Withdrawal processing
- And many more...

---

## Routes Structure

### Public Routes
- `/` - Homepage
- `/feed` - User feed
- `/profile/{username}` - User profiles
- `/videos` - Video listings
- `/videos/{video}` - Individual videos
- `/streams` - Stream listings
- `/cryptocurrency` - Crypto features
- `/nft/marketplace` - NFT marketplace

### Authenticated Routes
- `/my/settings` - User settings
- `/my/messenger` - Messaging
- `/my/bookmarks` - Bookmarks
- `/my/lists` - User lists
- `/payment/*` - Payment processing
- `/subscriptions/*` - Subscription management
- `/withdrawals/*` - Withdrawal requests

### Admin Routes (prefix: `/admin`)
- `/admin` - Dashboard
- `/admin/tokens` - Token management
- `/admin/wallets` - Wallet management
- `/admin/revenue` - Revenue management
- Voyager routes for general admin operations

---

## Key Features & Workflows

### Subscription Flow
1. User browses creator profiles
2. Selects subscription tier (monthly/3mo/6mo/12mo)
3. Payment processed via selected gateway
4. Subscription created with expiration date
5. Recurring payments handled automatically
6. Access granted to creator content

### Cryptocurrency Token Creation
1. User creates token with details (name, symbol, supply, price)
2. Token created in database
3. Wallet created for creator
4. Tokens can be bought/sold on platform
5. Fees distributed to creator and platform
6. Revenue shares tracked for distribution

### NFT Creation & Sale
1. User creates NFT with metadata
2. Smart contract interaction (via MetaMask)
3. NFT minted on blockchain
4. Listed on marketplace
5. Buyers purchase with ETH/crypto
6. Ownership transferred on blockchain
7. Transaction recorded in database

### Live Streaming
1. Creator creates stream
2. Gets RTMP URL and stream key
3. Configures OBS Studio or similar
4. Streams to RTMP server
5. Nginx RTMP converts to HLS
6. Viewers watch via HLS
7. Chat available during stream
8. Stream can be saved as VOD

---

## Configuration Files

- `config/app.php` - Application configuration
- `config/database.php` - Database settings
- `config/filesystems.php` - Storage configuration
- `config/broadcasting.php` - Pusher/WebSocket config
- `config/paypal.php` - PayPal settings
- `config/streaming.php` - Streaming configuration
- `config/web3.php` - Blockchain settings
- `.env` - Environment variables

---

## Security Features

- CSRF protection
- XSS protection (HTML Purifier)
- Two-factor authentication
- Email verification
- Device verification
- Rate limiting
- Admin middleware
- Permission-based access control
- Encrypted wallet private keys (for crypto)
- Secure file uploads

---

## Performance Optimizations

- Asset minification
- Caching strategies
- Image optimization
- Video transcoding
- Database indexing
- Eager loading for relationships
- CDN support (S3 compatible)
- HTML minification middleware

---

## Internationalization

- Multi-language support (English, Romanian included)
- RTL (Right-to-Left) language support
- Locale-based settings
- Currency localization
- Date/time localization

---

## Installation & Setup

1. **Requirements**
   - PHP 8.0.2+
   - MySQL/MariaDB
   - Node.js & NPM
   - Composer
   - FFmpeg (for video processing)
   - Nginx with RTMP (for streaming)

2. **Installation Steps**
   ```bash
   composer install
   npm install
   cp .env.example .env
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   npm run production
   php artisan voyager:admin your@email.com
   ```

3. **Payment Setup**
   - Configure payment gateway credentials in admin panel
   - Set up webhooks for payment providers
   - Configure Stripe/PayPal keys

4. **Streaming Setup**
   - Install Nginx with RTMP module
   - Configure nginx-rtmp.conf
   - Set RTMP_URL and HLS_URL in .env

5. **Crypto Setup**
   - Deploy NFT marketplace smart contract (if using NFTs)
   - Configure blockchain RPC URLs
   - Set contract addresses in .env

---

## File Structure Highlights

```
app/
├── Http/
│   ├── Controllers/        # All application controllers
│   ├── Middleware/         # Custom middleware
│   └── Requests/           # Form request validation
├── Model/                  # Legacy models (App\Model namespace)
├── Models/                 # New models (App\Models namespace)
├── Providers/              # Service providers
├── Helpers/                # Helper classes
├── Services/               # Business logic services
└── Observers/              # Model observers

resources/
├── views/                  # Blade templates
│   ├── cryptocurrency/     # Crypto views
│   ├── nft/               # NFT views
│   ├── streams/           # Streaming views
│   └── admin/             # Admin views
├── css/                    # SASS source files
└── js/                     # JavaScript source files

database/
├── migrations/             # Database migrations
└── seeders/                # Database seeders

public/
├── css/                    # Compiled CSS
├── js/                     # Compiled JavaScript
└── libs/                   # Third-party libraries

contracts/                  # Smart contracts (Solidity)
├── NFTMarketplace.sol
└── hardhat.config.js
```

---

## Development Notes

- The application uses both `App\Model` and `App\Models` namespaces (legacy and new)
- Some models exist in both locations (e.g., Cryptocurrency)
- Voyager admin panel is heavily customized
- Video processing uses FFmpeg with queue jobs
- Real-time features use Pusher for broadcasting
- File uploads support chunked uploads for large files
- The codebase has extensive commented-out code (likely for debugging/future features)

---

## Known Features & Limitations

### Features
- Comprehensive payment gateway integration
- Multi-blockchain cryptocurrency support
- NFT marketplace with smart contracts
- Live streaming with RTMP/HLS
- Advanced subscription system
- Revenue sharing and analytics
- Multi-language support
- Mobile-responsive design

### Areas for Improvement
- Some code duplication between Model and Models namespaces
- Extensive commented code suggests ongoing refactoring
- Documentation could be more comprehensive
- Test coverage not visible (may need PHPUnit tests)

---

## Support & Documentation

- README.md - Basic setup instructions
- cryptocurrency-readme.md - Crypto module documentation
- NFT_MARKETPLACE_SETUP.md - NFT setup guide
- STREAMING.md - Streaming setup guide
- cryptocurrency-status.md - Crypto module status

---

## License

Based on the composer.json, this appears to be a commercial project (JustFans/JustFans) with MIT license for the framework components.

---

*This overview was generated from codebase analysis. For the most up-to-date information, refer to the actual code and documentation files.*
