# Book&Play - Sports Venue Management System

A comprehensive web application for managing sports venues, bookings, tournaments, and user interactions.

## 🏆 Features

### User Features
- **Dashboard**: Personal dashboard with booking history and statistics
- **Venue Booking**: Browse and book sports venues
- **Tournament Participation**: Join and participate in tournaments
- **Profile Management**: User profile and settings
- **Notifications**: Real-time notifications for bookings and updates

### Manager Features (Gestionnaire)
- **Dashboard**: Management overview with key metrics
- **Terrain Management**: Add, edit, and manage sports venues
- **Reservation Management**: Handle booking requests and confirmations
- **Tournament Management**: Create and manage tournaments
- **Communication**: Mail system for user communication

### Admin Features
- **System Overview**: Complete system administration
- **User Management**: Manage users and permissions
- **Analytics**: Detailed reports and analytics
- **System Settings**: Configure application settings

## 🚀 Technology Stack

- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5.3.3
- **Backend**: PHP 8.0+
- **Database**: MySQL
- **Icons**: Bootstrap Icons
- **Server**: XAMPP (Apache, MySQL, PHP)


## 🎨 Design System

### Color Palette
- **Primary Green**: `#28a745` (Bootstrap Success)
- **Secondary Green**: `#2ecc71` (Modern Green)
- **Background**: `#f9fdf9` (Light Green Tint)
- **Text**: `#495057` (Dark Gray)

### Typography
- **Font Family**: Poppins (Google Fonts)
- **Weights**: 400, 500, 600, 700

### Components
- **Navbars**: Horizontal for users, vertical for managers/admins
- **Cards**: Glassmorphism design with backdrop blur
- **Buttons**: Gradient backgrounds with hover effects
- **Forms**: Modern styling with focus states

## 📁 Project Structure

```
/book-play-mvc
│
├── /app
│   ├── /Controllers
│   ├── /Models
│   ├── /Views
│   ├── /Core
│   │   ├── App.php
│   │   ├── Controller.php
│   │   └── Model.php
│   ├── /Helpers
│   └── /Database
│       └── Database.php
│
├── /config
│   └── config.php
│
├── /public
│   ├── index.php
│   ├── /css
│   ├── /js
│   └── /uploads
│
├── /vendor
├── .env
├── composer.json
└── README.md
```

## 👥 User Roles

### 1. Regular Users
- Browse and book venues
- Participate in tournaments
- Manage personal profile
- View booking history

### 2. Managers (Gestionnaire)
- Manage venues and bookings
- Create and manage tournaments
- Handle user communications
- View management analytics

### 3. Administrators
- Full system access
- User and role management
- System configuration
- Advanced analytics

## 🔧 Development

### Adding New Features
1. Create feature branch: `git checkout -b feature/feature-name`
2. Implement changes
3. Test thoroughly
4. Commit changes: `git commit -m "Add feature-name"`
5. Push to branch: `git push origin feature/feature-name`
6. Create pull request

### Code Style
- Use consistent indentation (2 spaces)
- Comment complex logic
- Follow PHP PSR standards
- Use meaningful variable names


## 👨‍💻 Team

- **Project Lead**: [Ismail Lyamani]
- **Backend Developer**: [Abdellatif Oumhella & Douae Rohand]
- **Frontend Developer**: [Douae Moeniss & Aya Raissouni]


**Book&Play** - Making sports venue management simple and efficient! 🏆
