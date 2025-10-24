# ExpenseLogger - خرج‌نگار

![ExpenseLogger Banner](https://img.shields.io/badge/ExpenseLogger-Personal%20Expense%20Tracker-8b5cf6?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)

A beautiful, modern, and fully functional **offline expense tracking web application** built with PHP, SQLite, TailwindCSS, and DaisyUI.

---

## 🌟 Features

### 📊 Dashboard
- **Summary Cards**: Total expenses, monthly spending, entry count, and categories
- **Interactive Charts**: 
  - Pie chart showing expense distribution by category
  - Bar chart displaying spending over the last 7 days
- **Recent Expenses**: Quick view of your latest transactions
- **Quick Add Button**: Fast expense entry from the dashboard

### 💰 Expense Management
- **Full CRUD Operations**: Create, Read, Update, Delete expenses
- **Advanced Filters**: 
  - Filter by category
  - Date range selection
  - Search notes
- **Pagination**: Easy navigation through large expense lists
- **Toast Notifications**: Real-time feedback for all actions
- **Responsive Table**: Beautiful display on all screen sizes

### 🏷️ Category Management
- **Category CRUD**: Manage your expense categories
- **Visual Cards**: Each category shows expense count and total amount
- **Default Categories**: Automatic setup with Food, Transport, Health, Shopping, Other
- **Smart Delete**: Prevents deletion of categories with associated expenses

### 📈 Reports & Analytics
- **Dynamic Charts**:
  - Doughnut chart for category distribution
  - Bar chart for category comparison
  - Line chart for daily spending trends
- **Flexible Filters**: Date range and category selection
- **Quick Presets**: Last 7/30/90/365 days, or all time
- **Top 10 Expenses**: See your biggest spending items
- **CSV Export**: Download reports for external analysis
- **Category Breakdown**: Detailed percentage and progress bars

### 💾 Backup & Restore
- **JSON Export**: Download complete database backup
- **Import Functionality**: Restore from backup files
- **Database Reset**: Clear all data and start fresh
- **Statistics Dashboard**: Database size and record counts
- **Safety Features**: Confirmation dialogs for destructive actions

### 🎨 Design & UX
- **Modern UI**: Built with DaisyUI components
- **Color Palette**: Purple, Blue, Emerald, and White
- **Dark/Light Theme**: Toggle between themes with persistent storage
- **Responsive Design**: Perfect on desktop, tablet, and mobile
- **Smooth Animations**: Slide-in effects and transitions
- **Professional Icons**: Font Awesome integration

---

## 🚀 Installation

### Prerequisites
- **XAMPP** (or any PHP 7.4+ environment with SQLite support)
- Modern web browser (Chrome, Firefox, Edge, Safari)

### Step-by-Step Setup

1. **Download/Clone the Repository**
   ```bash
   # Clone to your htdocs folder
   cd C:\xampp\htdocs
   git clone <repository-url> ExpenseLogger
   ```

2. **Or Extract Files**
   - Extract the ExpenseLogger folder to `C:\xampp\htdocs\`

3. **Start XAMPP**
   - Open XAMPP Control Panel
   - Start **Apache** module
   - SQLite is built into PHP, no additional setup needed

4. **Access the Application**
   - Open your browser
   - Navigate to: `http://localhost/ExpenseLogger`
   - The database will be automatically created on first access

5. **Default Categories**
   - The system automatically creates 5 default categories:
     - Food
     - Transport
     - Health
     - Shopping
     - Other

---

## 📁 Project Structure

```
ExpenseLogger/
├── config/
│   ├── database.php       # Database configuration and initialization
│   └── init.php           # Application initialization and helpers
├── includes/
│   ├── header.php         # Common header with navbar and theme toggle
│   └── footer.php         # Common footer
├── api/
│   └── theme.php          # Theme toggle API endpoint
├── data/
│   └── expenselogger.db   # SQLite database (auto-created)
├── index.php              # Dashboard page
├── expenses.php           # Expense management page
├── categories.php         # Category management page
├── report.php             # Reports and analytics page
├── backup.php             # Backup and restore page
└── README.md              # This file
```

---

## 💻 Tech Stack

| Technology | Purpose |
|------------|---------|
| **PHP 7.4+** | Backend logic and database operations |
| **SQLite** | Lightweight database for local storage |
| **HTML5** | Structure and markup |
| **JavaScript (Vanilla)** | Client-side interactivity |
| **TailwindCSS** | Utility-first CSS framework |
| **DaisyUI** | Beautiful UI components |
| **Chart.js** | Data visualization and charts |
| **Font Awesome** | Professional icons |

---

## 🗄️ Database Schema

### Tables

#### `categories`
| Column | Type | Description |
|--------|------|-------------|
| id | INTEGER | Primary key (auto-increment) |
| name | TEXT | Category name (unique) |
| created_at | DATETIME | Timestamp |

#### `expenses`
| Column | Type | Description |
|--------|------|-------------|
| id | INTEGER | Primary key (auto-increment) |
| amount | REAL | Expense amount |
| category_id | INTEGER | Foreign key to categories |
| date | DATE | Expense date |
| note | TEXT | Optional note/description |
| created_at | DATETIME | Timestamp |

---

## 🎯 Usage Guide

### Adding an Expense
1. Click "Add Expense" button (Dashboard or Expenses page)
2. Fill in the form:
   - Amount (required)
   - Category (required)
   - Date (required)
   - Note (optional)
3. Click "Save Expense"

### Filtering Expenses
1. Go to Expenses page
2. Use the filters:
   - Select category
   - Set date range
   - Search in notes
3. Click "Apply Filters"

### Viewing Reports
1. Navigate to Reports & Analytics
2. Set date range and select categories
3. View interactive charts
4. Export data as CSV if needed

### Creating Backups
1. Go to Backup & Restore page
2. Click "Download Backup"
3. Save the JSON file to a safe location
4. Keep multiple backups for different time periods

### Restoring from Backup
1. Go to Backup & Restore page
2. Click "Choose File" and select your backup
3. Optionally check "Clear existing data"
4. Click "Import Backup"

---

## 🔒 Security Features

- **SQL Injection Prevention**: Prepared statements with PDO
- **XSS Protection**: HTML sanitization for all outputs
- **Input Validation**: Server-side and client-side validation
- **Safe File Upload**: JSON validation for imports
- **CSRF Protection**: Form token validation (via sessions)
- **Error Handling**: Graceful error messages without exposing internals

---

## 🎨 Color Palette

```css
Primary Purple:   #8b5cf6
Secondary Blue:   #3b82f6
Accent Emerald:   #10b981
White/Light:      #ffffff
```

---

## 📸 Screenshots

### Dashboard
- Summary statistics with gradient cards
- Interactive pie and bar charts
- Recent expenses table

### Expense Management
- Filterable and searchable table
- Modal forms for add/edit
- Pagination for large datasets

### Reports & Analytics
- Multiple chart types
- Flexible date range filters
- CSV export functionality

### Categories
- Beautiful card layout
- Statistics per category
- Easy CRUD operations

---

## 🧪 Sample Data

To populate the application with sample data for testing:

1. Navigate to the Expenses page
2. Add several expenses with different:
   - Categories
   - Amounts
   - Dates (spread across different days/months)
   - Notes

Or use the import feature with the provided sample backup file.

---

## 🛠️ Customization

### Changing Colors
Edit the CSS variables in `includes/header.php`:
```css
:root {
    --color-primary: #8b5cf6;
    --color-secondary: #3b82f6;
    --color-accent: #10b981;
}
```

### Adding Categories
- Use the Categories page to add custom categories
- Or modify the default categories in `config/database.php`

### Adjusting Pagination
Change the items per page in `expenses.php`:
```php
$itemsPerPage = 10; // Change to your preference
```

---

## 🐛 Troubleshooting

### Database Not Created
- Ensure the `data/` directory is writable
- Check PHP has SQLite extension enabled (`php -m | grep sqlite`)

### Charts Not Displaying
- Ensure Chart.js CDN is accessible
- Check browser console for JavaScript errors

### Theme Not Persisting
- Verify sessions are enabled in PHP
- Check browser allows session cookies

### Import Fails
- Ensure JSON file is from ExpenseLogger export
- Validate JSON syntax
- Check file upload size limits in `php.ini`

---

## 📝 Future Enhancements

Potential features for future versions:
- [ ] Multi-currency support
- [ ] Budget tracking and alerts
- [ ] Recurring expenses
- [ ] Tags/labels for expenses
- [ ] Advanced reporting (monthly/yearly comparisons)
- [ ] Multi-user support with authentication
- [ ] Mobile app version
- [ ] Cloud sync capabilities

---

## 📄 License

This project is open source and available for personal and commercial use.

---

## 👨‍💻 Author

Built with ❤️ as a comprehensive expense tracking solution.

---

## 🙏 Acknowledgments

- **TailwindCSS** - For the amazing utility-first framework
- **DaisyUI** - For beautiful pre-built components
- **Chart.js** - For powerful data visualization
- **Font Awesome** - For the extensive icon library

---

## 📞 Support

For issues or questions:
1. Check the troubleshooting section
2. Review the code comments for detailed explanations
3. Open an issue in the repository

---

**ExpenseLogger - خرج‌نگار** | Track Expenses, Gain Insights 📊
