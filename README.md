# 🚀 CDN Personal Full-Self-Hosted

**cdn.tudocomlizzyman.com** - A complete CDN + API solution for file hosting with user management, plans, and quotas.

## ✨ Features

- ✅ **Complete RESTful API** with token-based authentication
- ✅ **File upload** with quota control
- ✅ **Clean and direct URLs**: `https://cdn.tudocomlizzyman.com/u/{user_uuid}/path/file.ext`
- ✅ **Resizing and filters** via URL (e.g., `?w=800&format=webp&filter=grayscale`)
- ✅ **Plan system**: Free (1GB) and Paid (higher limits)
- ✅ **Multiple tokens per user** with granular permissions
- ✅ **Credit system** for plan upgrades
- ✅ **Notifications** for important events
- ✅ **Separation of responsibilities**:
  - `cdn.tudocomlizzyman.com` → Serving static files (100% performance)
  - `cdn.omeu.space` → PHP API (upload, delete, resize, etc.)

## 🏗️ Architecture

```
┌─────────────────────────────────────────────────────────┐
│                    cdn.omeu.space                      │
│                     (PHP API)                          │
│  ┌──────────────────────────────────────────────────┐ │
│  │  Fluxor PHP + Cycle ORM                          │ │
│  │  - Upload (POST /api/v1/upload)                 │ │
│  │  - Delete (DELETE /api/v1/delete)               │ │
│  │  - Info (GET /api/v1/info)                      │ │
│  │  - Resize (GET /api/v1/resize)                  │ │
│  └──────────────────────────────────────────────────┘ │
└──────────────────────────┬──────────────────────────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────┐
│                  cdn.tudocomlizzyman.com                │
│                   (Static Files)                        │
│  ┌──────────────────────────────────────────────────┐ │
│  │  Apache/Nginx serving directly from /storage    │ │
│  │  - Zero PHP execution                           │ │
│  │  - Max performance                              │ │
│  │  - Direct URLs: /u/{uuid}/path/file.ext        │ │
│  └──────────────────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────┘
```

## 🛠️ Technology Stack

| Component | Technology |
|------------|------------|
| **Framework** | Fluxor PHP (file-based routing) |
| **ORM** | Cycle ORM (DataMapper) |
| **Database** | MySQL / PostgreSQL |
| **Resize** | GD Library (PictShare inspired) |
| **Auth** | Token-based (X-User-UUID + X-Token) |
| **Hosting** | iFastNet Shared Hosting (MVP) → VPS (future) |

## 🚀 Quick Start

### Installation

```bash
# Clone the repository
git clone https://github.com/lizzyman04/cdn.tudocomlizzyman.com
cd cdn.tudocomlizzyman.com

# Install dependencies
composer install

# Configure environment
cp .env.example .env
# Edit .env with your database settings

# Configure subdomains in cPanel
# - cdn.tudocomlizzyman.com → Document Root: /storage
# - cdn.omeu.space → Document Root: /public
```

### Subdomain Configuration

**Subdomain 1 (Static CDN):**
- Domain: `cdn.tudocomlizzyman.com`
- Document Root: `/home/user/composer_apps/cdn_app/storage`
- Additional `.htaccess` configuration to block PHP

**Subdomain 2 (API):**
- Domain: `cdn.omeu.space`
- Document Root: `/home/user/composer_apps/cdn_app/public`
- PHP execution enabled

### API Usage

#### File Upload
```bash
curl -X POST https://cdn.omeu.space/api/v1/upload \
  -H "X-User-UUID: your-uuid-here" \
  -H "X-Token: your-token-here" \
  -F "file=@/path/to/image.jpg" \
  -F "path=fotos/2026/summer.jpg"
```

#### Response
```json
{
  "success": true,
  "data": {
    "url": "https://cdn.tudocomlizzyman.com/u/abc-123/fotos/2026/summer.jpg",
    "size": 245760,
    "mime": "image/jpeg"
  }
}
```

#### Resize via URL
```
https://cdn.tudocomlizzyman.com/u/abc-123/fotos/2026/summer.jpg?w=800&format=webp&filter=grayscale
```

## 📊 Data Modeling

### Main Tables

- **users** - User data (UUID, email, plans, quotas)
- **user_roles** - Multiple roles per user (admin, user, moderator)
- **api_tokens** - Multiple tokens per user with permissions
- **plans** - Available plans (Free, Pro, Business)
- **files** - File metadata (soft delete)
- **credits** - Credit system for upgrades
- **credit_transactions** - Transaction history
- **notifications** - System notifications

## 🔒 Security

- **Token-based authentication** for all operations
- **Password hashing** with bcrypt
- **CSRF protection** on sensitive operations
- **PHP execution blocked** in storage folder
- **Rate limiting** (planned)

## 📄 License

MIT License - see [LICENSE](LICENSE) file for details.