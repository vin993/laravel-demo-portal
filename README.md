
# Laravel Demo Portal

A comprehensive Laravel-based web application demonstrating advanced PHP development skills, including role-based user management, media file handling, dealer and manufacturer management, and robust API integrations. This project showcases modern web development practices with Laravel 11, PHP 8.2+, and a responsive frontend using Tailwind CSS and Vite.

## Features

### User Management & Authentication
- **Role-Based Access Control**: Supports multiple user roles (Super Admin, Admin, User, Secondary User) with granular permissions
- **User Approval System**: Admin-controlled user activation and approval workflows
- **Secondary User Invitations**: Invite additional users to accounts with token-based registration
- **Account Inactivity Management**: Automatic deactivation after 90 days of inactivity
- **Secure Authentication**: Laravel Sanctum for API token management

### Business Logic
- **Dealer & Manufacturer Management**: CRUD operations for managing business entities
- **Industry Categorization**: Organize users and entities by industry sectors
- **Company Management**: Handle company profiles and associations
- **Announcements System**: Admin-created announcements with user targeting and reordering

### Media & File Management
- **File Upload System**: Secure media file uploads with category and tag support
- **Marketing Materials**: Dedicated section for promotional content with likes, views, and comments
- **Access Control**: User-specific media permissions and sharing
- **Image Processing**: Intervention Image integration for image manipulation

### Location Services
- **Geographic Data**: Countries, states, and cities management
- **Search APIs**: Efficient search endpoints for location data

### Additional Features
- **Saved Links**: User bookmarking functionality
- **Email Integration**: Mailgun/SMTP support for notifications
- **reCAPTCHA Integration**: Spam protection for forms
- **Responsive Design**: Mobile-friendly interface with Tailwind CSS

## 🛠 Tech Stack

### Backend
- **Laravel 11.9**: Modern PHP framework with MVC architecture
- **PHP 8.2+**: Latest PHP features and performance optimizations
- **MySQL**: Relational database for data persistence
- **Laravel Sanctum**: API authentication and token management

### Frontend
- **Tailwind CSS**: Utility-first CSS framework for responsive design
- **Vite**: Fast build tool and development server
- **Axios**: HTTP client for API communications

### Key Libraries
- **Intervention Image**: Image processing and manipulation
- **Google reCAPTCHA**: Bot protection
- **Symfony Mailgun Mailer**: Email delivery
- **getID3**: Media file metadata extraction

### Development Tools
- **Composer**: PHP dependency management
- **NPM**: Node.js package management
- **PHPUnit**: Unit and feature testing
- **Laravel Pint**: Code style enforcement

## Prerequisites

Before running this project, ensure you have the following installed:

- **PHP 8.2 or higher**
- **Composer** (PHP dependency manager)
- **Node.js 16+ and NPM**
- **MySQL 5.7+ or MariaDB 10.2+**
- **Git** (for cloning the repository)

## 🔧 Installation & Setup

1. **Clone the Repository**
   ```bash
   git clone <your-repository-url>
   cd laravel-demo-portal
   ```

2. **Environment Configuration**
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your local settings:
   - Database credentials (DB_HOST, DB_DATABASE, etc.)
   - Mailgun API keys (MAILGUN_DOMAIN, MAILGUN_SECRET)
   - reCAPTCHA keys (RECAPTCHA_SITE_KEY, RECAPTCHA_SECRET_KEY)
   - Application URL and other environment variables

3. **Install PHP Dependencies**
   ```bash
   composer install
   ```

4. **Install Node.js Dependencies**
   ```bash
   npm install
   ```

5. **Build Frontend Assets**
   ```bash
   npm run build
   ```

6. **Generate Application Key**
   ```bash
   php artisan key:generate
   ```

7. **Database Setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

8. **Start the Development Server**
   ```bash
   php artisan serve
   ```

The application will be available at `http://localhost:8000`

## Database Schema

The application includes the following key entities:

- **Users**: Core user management with roles and approval status
- **Roles**: Permission-based role definitions
- **Industries**: Business sector categorization
- **Companies**: Organization profiles
- **Dealers**: Business partner management
- **Manufacturers**: Product manufacturer records
- **Media Files**: File uploads with metadata and access control
- **Marketing Materials**: Promotional content management
- **Locations**: Geographic data (Countries, States, Cities)
- **Announcements**: Admin communications
- **User Invitations**: Secondary user invitation system

## API Endpoints

The application provides RESTful API endpoints for data retrieval:

### Location APIs
- `GET /api/countries/search` - Search countries
- `GET /api/states/search` - Search states
- `GET /api/cities/search` - Search cities
- `GET /api/states/{country}` - Get states by country
- `GET /api/cities/{state}` - Get cities by state

### Business Entity APIs
- `GET /api/dealers/search` - Search dealers
- `GET /api/manufacturers/search` - Search manufacturers
- `GET /api/industries/search` - Search industries

### Media APIs
- `POST /media/assigned-users` - Get users assigned to media
- `POST /marketing-materials/assigned-users` - Get users assigned to marketing materials

## Testing

Run the test suite using PHPUnit:

```bash
php artisan test
```

The project includes unit tests for critical business logic and feature tests for API endpoints and user workflows.

## Deployment

For production deployment:

1. Set `APP_ENV=production` in `.env`
2. Configure production database and mail settings
3. Run `php artisan config:cache` and `php artisan route:cache`
4. Set up web server (Apache/Nginx) with proper document root
5. Ensure proper file permissions for storage directories

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

##  Author

Vinayak Chandran - Laravel Developer

## 📞 Support

For questions or support, please open an issue in the GitHub repository.

---

**Note**: This is a demo/portfolio project. All sensitive information has been sanitized, and it uses generic data for demonstration purposes.

