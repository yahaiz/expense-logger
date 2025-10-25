# ExpenseLogger - خرج‌نگار 💰

[![PHP Version](https://img.shields.io/badge/PHP-8.2+-777BB4?style=flat&logo=php)](https://php.net)
[![JavaScript](https://img.shields.io/badge/JavaScript-ES6+-F7DF1E?style=flat&logo=javascript)](https://developer.mozilla.org/en-US/docs/Web/JavaScript)
[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=flat&logo=tailwind-css)](https://tailwindcss.com)
[![DaisyUI](https://img.shields.io/badge/DaisyUI-4.x-1ad1ff?style=flat)](https://daisyui.com)
[![SQLite](https://img.shields.io/badge/SQLite-3.x-003B57?style=flat&logo=sqlite)](https://sqlite.org)
[![Electron](https://img.shields.io/badge/Electron-38.x-47848F?style=flat&logo=electron)](https://electronjs.org)

A beautiful, modern, and fully functional **expense tracking desktop application** built with PHP, Electron, SQLite, TailwindCSS, and DaisyUI. Features user authentication, advanced analytics, offline functionality, and a polished desktop experience.

## 🤖 Development Approach

**This project was completely developed using AI technology, guided by Yahya Izadi.**

The entire codebase, documentation, and project structure were created through AI-assisted development, demonstrating the capabilities of modern AI tools in producing professional-grade software applications.

## ✨ Features

### 🔐 Security & Authentication
- Secure user registration and login
- Password-based app locking/unlocking
- Session management with auto-logout
- Data encryption and secure storage

### 📊 Dashboard & Analytics
- Interactive expense overview with charts
- Category-wise spending analysis
- Monthly and daily spending trends
- Recent transactions view

### 💳 Expense Management
- Full CRUD operations for expenses
- Category-based organization
- Date range filtering and search
- Bulk operations and data export

### 🏷️ Category Management
- Custom expense categories
- Visual category statistics
- Default category presets

### 📈 Reports & Insights
- Multiple chart types (pie, bar, line)
- Date range analysis
- CSV export functionality
- Spending pattern analysis

### 💾 Backup & Restore
- JSON-based data backup
- One-click data import
- Database reset capabilities

### 🎨 Modern UI/UX
- Beautiful DaisyUI components
- Multiple theme support (light/dark/cupcake/etc.)
- Responsive design for all screen sizes
- Smooth animations and transitions
- Drag prevention for desktop feel

### 🖥️ Desktop Application
- **Standalone executable** - No installation required
- **Offline functionality** - Works without internet
- **Bundled PHP runtime** - Self-contained environment
- **Local SQLite database** - Secure local data storage
- **Cross-platform** - Windows, macOS, Linux support

## 🚀 Quick Start

### Prerequisites
- **Windows/macOS/Linux** with modern operating system
- **No additional dependencies** - Everything is bundled!

### Installation
1. **Download** the latest release from [GitHub Releases](https://github.com/yahaiz/expense-logger/releases)
2. **Extract** the ZIP file to your desired location
3. **Run** `ExpenseLogger.exe` (Windows) or the appropriate executable for your platform
4. **First Time Setup**: Register as the first user (automatically becomes admin)

### Building from Source
```bash
# Clone the repository
git clone https://github.com/yahaiz/expense-logger.git
cd expenselogger

# Install dependencies
npm install
composer install

# Build the application
npm run dist
```

## 📁 Project Structure

```
expenselogger/
├── assets/                 # Static assets (CSS, JS, fonts, icons)
│   ├── css/               # Stylesheets
│   ├── js/                # JavaScript files
│   ├── fonts/             # Font files
│   └── webfonts/          # Font Awesome fonts
├── config/                # Configuration files
│   ├── database.php       # Database setup
│   ├── init.php          # App initialization
│   └── themes.php        # Theme definitions
├── includes/              # PHP includes
│   ├── header.php        # Common header/navbar
│   └── footer.php        # Common footer
├── api/                   # API endpoints
├── data/                  # SQLite database (auto-created)
├── electron/              # Electron main process
├── php/                   # Bundled PHP runtime
├── docs/                  # Documentation
├── dist/                  # Build output (ignored)
├── .gitignore            # Git ignore rules
├── package.json          # Node.js dependencies
├── composer.json         # PHP dependencies
└── README.md             # This file
```

## 🛠️ Tech Stack

| Component | Technology | Purpose |
|-----------|------------|---------|
| **Frontend** | HTML5, JavaScript, TailwindCSS, DaisyUI | UI/UX and styling |
| **Backend** | PHP 8.2+ | Server-side logic |
| **Database** | SQLite 3 | Data storage |
| **Desktop** | Electron | Cross-platform desktop app |
| **Charts** | Chart.js | Data visualization |
| **Icons** | Font Awesome | UI icons |
| **Build** | Electron Builder | Application packaging |

## 🎯 Usage

### First Time Setup
1. Launch the application
2. Register as the first user (becomes admin)
3. Set up your expense categories
4. Start adding expenses!

### Daily Usage
- **Dashboard**: View spending overview and recent transactions
- **Add Expenses**: Quick expense entry with category selection
- **View Reports**: Analyze spending patterns with interactive charts
- **Manage Categories**: Organize expenses with custom categories
- **Backup Data**: Regular backups for data safety

### Security Features
- **Lock App**: Click the lock icon to secure the application
- **Password Protection**: Set a password for app access
- **Session Management**: Automatic logout after inactivity

## 📊 Screenshots

*Dashboard with expense overview and charts*
*Expense management with filtering*
*Reports and analytics*
*Settings and theme customization*

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is open source and available under the [MIT License](LICENSE).

## 🙏 Acknowledgments

- [TailwindCSS](https://tailwindcss.com) - Utility-first CSS framework
- [DaisyUI](https://daisyui.com) - Beautiful UI components
- [Chart.js](https://chartjs.org) - Data visualization
- [Electron](https://electronjs.org) - Desktop app framework
- [Font Awesome](https://fontawesome.com) - Icon library

## 📞 Support

- 📧 **Email**: yahyaa84iz@gmail.com
- 🐛 **Issues**: [GitHub Issues](https://github.com/yahaiz/expense-logger/issues)
- 📖 **Documentation**: See [docs/](docs/) directory

---

**ExpenseLogger - خرج‌نگار** | Track Expenses, Gain Insights 📊

*Built with ❤️ for personal finance management*
