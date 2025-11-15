# SaveMyVCard

A powerful Laravel-based web application for creating, managing, and sharing digital business cards (vCards) with integrated WhatsApp messaging and AI-powered business card extraction capabilities.

## 🎯 Overview

SaveMyVCard is a comprehensive platform that allows users to:
- Create and manage multiple digital business cards (vCards)
- Share cards via WhatsApp with automated lead notifications
- Extract business card information from images using OpenAI's advanced vision capabilities
- Track card downloads and user engagement
- Generate QR codes for easy card sharing

## 🚀 Features

### User Management
- Secure member registration and authentication
- Admin dashboard for user and lead management
- Email and mobile number verification
- Password reset functionality via WhatsApp

### vCard Management
- Create professional digital business cards
- Customize card themes and layouts
- Manage multiple cards per user
- Export cards as VCF files or images
- QR code generation for easy distribution

### WhatsApp Integration
- Send vCards directly via WhatsApp
- Webhook receiver for incoming WhatsApp messages
- Lead notification system with template messages
- Track message delivery and read status
- Two-way communication with users

### AI-Powered Card Extraction
- Extract business card data from images using OpenAI's GPT-4o-mini
- Automatic contact information parsing (name, phone, email, address, etc.)
- Support for multiple business card formats
- Store extracted data for later use

### Location Services
- Country, state, and city management
- Location-based filtering
- Support for international users

## 🛠️ Technology Stack

### Backend
- **Framework**: Laravel 11.9
- **Language**: PHP 8.2+
- **Database**: MySQL/SQLite
- **ORM**: Eloquent

### Frontend
- **CSS Framework**: Bootstrap 5
- **Build Tool**: Vite
- **Icon Libraries**: Bootstrap Icons, Boxicons, Remix Icon
- **Rich Text Editor**: TinyMCE
- **Charts**: ApexCharts, Chart.js

### External APIs
- **WhatsApp**: Meta WhatsApp Business API (v20.0)
- **AI**: OpenAI GPT-4o-mini for card extraction
- **Storage**: AWS S3 for file storage
- **OCR**: OCR.space API for text extraction

## 📋 Requirements

- PHP 8.2 or higher
- Composer
- MySQL 5.7+ or SQLite
- Node.js 16+ (for frontend assets)
- npm or yarn
- WhatsApp Business Account (for WhatsApp features)
- OpenAI API key (for card extraction)
- AWS S3 bucket (for file storage)

## 🔧 Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/mhaque11889/savemyvcard.git
   cd savemyvcard
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Set up environment variables**
   ```bash
   cp .env.example .env
   ```

5. **Configure your `.env` file** with:
   - Database credentials
   - OpenAI API key
   - WhatsApp Business credentials
   - AWS S3 configuration
   - Mail server settings

6. **Generate application key**
   ```bash
   php artisan key:generate
   ```

7. **Run database migrations**
   ```bash
   php artisan migrate
   ```

8. **Build frontend assets**
   ```bash
   npm run build
   ```

9. **Start the development server**
   ```bash
   php artisan serve
   ```

## 📁 Project Structure

```
SaveMyVCard/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php
│   │   ├── WhatsAppController.php
│   │   ├── OpenAIController.php
│   │   ├── BusinessLogicController.php
│   │   ├── MediaController.php
│   │   └── LoginController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Vcard.php
│   │   ├── LeadNotification.php
│   │   ├── CardExtractionDetails.php
│   │   └── ...
│   └── Mail/
├── resources/
│   ├── views/
│   │   ├── admin/
│   │   ├── member/
│   │   └── emails/
│   ├── css/
│   └── js/
├── routes/
│   ├── web.php
│   ├── api.php
│   └── console.php
├── database/
│   ├── migrations/
│   ├── seeders/
│   └── factories/
├── config/
├── public/
└── storage/
```

## 🔐 Security

### Implemented Security Measures
- CSRF token validation on all forms
- Password hashing with bcrypt
- Session-based authentication
- Input validation and sanitization
- SQL injection protection via Eloquent ORM
- HTTPS enforced in production

### Environment Variables
All sensitive credentials (API keys, tokens, database credentials) are stored in the `.env` file, which is never committed to version control.

**Required Environment Variables:**
```env
OPENAI_API_KEY=your_openai_api_key
WHATSAPP_META_PHONE_ID=your_phone_id
WHATSAPP_WABA_ID=your_waba_id
WHATSAPP_SYSTEM_USER_TOKEN=your_token
WHATSAPP_WEBHOOK_VERIFY_TOKEN=your_verify_token
AWS_ACCESS_KEY_ID=your_aws_key
AWS_SECRET_ACCESS_KEY=your_aws_secret
```

## 📚 API Endpoints

### WhatsApp Webhook
- `POST /webhook` - Receive WhatsApp messages and status updates

### Member Routes
- `POST /login` - Member authentication
- `POST /register` - New member registration
- `GET /memberDashboard` - Member dashboard
- `GET /viewVCards` - View user's vCards
- `POST /createVCard` - Create new vCard
- `POST /editVCard/{id}` - Update vCard

### Admin Routes
- `GET /adminDashboard` - Admin dashboard
- `GET /registeredUsers` - View all users
- `GET /leadNotifications` - View lead notifications
- `GET /processedCards` - View extracted card data

## 🔄 WhatsApp Integration Flow

1. User sends vCard code via WhatsApp
2. System validates the code
3. Vcard data is formatted as contact card
4. Card is sent back to user via WhatsApp
5. Lead notification is sent to card owner
6. Delivery status is tracked and logged

## 🤖 AI Card Extraction Flow

1. User uploads business card image to WhatsApp
2. Image is downloaded and stored in S3
3. OpenAI Vision API analyzes the image
4. Extracted data is structured and saved
5. User can edit and save extracted information
6. Data is available for future use

## 🚀 Deployment

### Prerequisites
- Hosting provider with PHP 8.2+ support
- MySQL database
- SSL certificate
- Outbound HTTPS access for API calls

### Steps
1. Clone repository on server
2. Run `composer install --optimize-autoloader --no-dev`
3. Run `npm run build`
4. Configure `.env` with production values
5. Run `php artisan migrate --force`
6. Set proper file permissions on `storage/` and `bootstrap/cache/`
7. Configure web server (Apache/Nginx) document root to `public/`
8. Set up SSL with Let's Encrypt

## 📖 Documentation

For detailed documentation on specific features:
- [WhatsApp Integration Guide](docs/whatsapp-integration.md)
- [API Documentation](docs/api.md)
- [Database Schema](docs/database.md)

## 🐛 Issues & Bugs

Found a bug? Please create an issue on GitHub with:
- Detailed description of the issue
- Steps to reproduce
- Expected vs actual behavior
- Your environment details

## 🤝 Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit changes (`git commit -m 'Add amazing feature'`)
4. Push to branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 👨‍💻 Author

**Manzar Haque**
- GitHub: [@mhaque11889](https://github.com/mhaque11889)
- Email: manzar@outlook.in

## 🙏 Acknowledgments

- Built with [Laravel](https://laravel.com)
- WhatsApp integration via [Meta WhatsApp Business API](https://developers.facebook.com/docs/whatsapp)
- AI capabilities powered by [OpenAI](https://openai.com)
- Frontend by [Bootstrap](https://getbootstrap.com)

## 📞 Support

For support, please:
- Create an issue on GitHub
- Check existing documentation
- Review the FAQ section

---

**Last Updated**: November 2025  
**Version**: 1.0.0  
**Status**: Active Development
