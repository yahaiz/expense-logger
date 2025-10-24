# ExpenseLogger - Project Structure

```
ExpenseLogger/
│
├── 📁 config/                    # Configuration Files
│   ├── database.php              # Database connection & initialization
│   └── init.php                  # App initialization & helper functions
│
├── 📁 includes/                  # Reusable Components
│   ├── header.php                # Common header with navbar & theme toggle
│   └── footer.php                # Common footer
│
├── 📁 api/                       # API Endpoints
│   └── theme.php                 # Theme toggle API
│
├── 📁 data/                      # Database Storage (auto-created)
│   └── expenselogger.db          # SQLite database file
│
├── 📄 index.php                  # 🏠 Dashboard Page
│   ├── Summary cards (4)
│   ├── Pie chart (category distribution)
│   ├── Bar chart (last 7 days)
│   └── Recent expenses table
│
├── 📄 expenses.php               # 💰 Expense Management
│   ├── CRUD operations
│   ├── Filters (category, date, search)
│   ├── Pagination (10 per page)
│   └── Modal forms
│
├── 📄 categories.php             # 🏷️ Category Management
│   ├── CRUD operations
│   ├── Visual cards with stats
│   ├── Default categories
│   └── Delete protection
│
├── 📄 report.php                 # 📈 Reports & Analytics
│   ├── Date range filters
│   ├── Category filters
│   ├── 3 chart types (pie, bar, line)
│   ├── Top 10 expenses
│   └── CSV export
│
├── 📄 backup.php                 # 💾 Backup & Restore
│   ├── JSON export
│   ├── JSON import
│   ├── Database reset
│   └── Statistics dashboard
│
├── 📄 demo_data.php              # 🧪 Sample Data Generator
│   └── Loads 23 sample expenses
│
├── 📄 welcome.php                # 👋 Welcome Page
│   └── Quick start guide
│
├── 📄 .htaccess                  # 🔒 Security Configuration
│   ├── Protects database files
│   ├── Security headers
│   └── Compression & caching
│
├── 📄 README.md                  # 📚 Main Documentation
│   ├── Feature overview
│   ├── Installation guide
│   ├── Usage instructions
│   ├── Security features
│   └── Troubleshooting
│
├── 📄 INSTALL.md                 # 🚀 Installation Guide
│   ├── Quick start (5 minutes)
│   ├── Step-by-step setup
│   ├── Verification checklist
│   └── Common issues
│
├── 📄 FEATURES.md                # ✨ Feature List
│   ├── Complete feature breakdown
│   ├── Code statistics
│   └── Design principles
│
└── 📄 PROJECT_SUMMARY.md         # 📊 Project Summary
    ├── Completion status
    ├── Deliverables
    ├── Requirements checklist
    └── Support resources
```

---

## 📊 Database Schema

```
┌─────────────────────────────┐
│       categories            │
├─────────────────────────────┤
│ • id (PK, INTEGER)          │
│ • name (TEXT, UNIQUE)       │
│ • created_at (DATETIME)     │
└─────────────────────────────┘
              │
              │ 1:N relationship
              │
              ▼
┌─────────────────────────────┐
│        expenses             │
├─────────────────────────────┤
│ • id (PK, INTEGER)          │
│ • amount (REAL)             │
│ • category_id (FK)          │
│ • date (DATE)               │
│ • note (TEXT)               │
│ • created_at (DATETIME)     │
└─────────────────────────────┘
```

---

## 🎨 Page Flow

```
┌─────────────────┐
│  welcome.php    │  👋 Welcome Page
│  (First Visit)  │
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  demo_data.php  │  🧪 Load Sample Data (Optional)
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│   index.php     │  🏠 Dashboard (Main)
│   (Dashboard)   │  • View summary
└────────┬────────┘  • See charts
         │           • Quick add
         │
    ┌────┴────┬──────────┬─────────┐
    │         │          │         │
    ▼         ▼          ▼         ▼
┌──────┐ ┌──────┐ ┌──────┐ ┌──────┐
│Expense│ │Catego│ │Report│ │Backup│
│  .php │ │ry.php│ │  .php│ │  .php│
└──────┘ └──────┘ └──────┘ └──────┘
💰 CRUD  🏷️ Manage 📈 Charts 💾 Export
```

---

## 🔄 Data Flow

```
User Input
    │
    ▼
┌─────────────────┐
│  PHP Pages      │  • Validate input
│  (Frontend)     │  • Sanitize data
└────────┬────────┘  • Send to backend
         │
         ▼
┌─────────────────┐
│  config/init.php│  • Process request
│  (Middleware)   │  • Helper functions
└────────┬────────┘  • Session handling
         │
         ▼
┌─────────────────┐
│ config/         │  • PDO connection
│ database.php    │  • Prepared statements
└────────┬────────┘  • Query execution
         │
         ▼
┌─────────────────┐
│ SQLite Database │  • Store data
│ (data/*.db)     │  • Return results
└────────┬────────┘
         │
         ▼
┌─────────────────┐
│  PHP Pages      │  • Render HTML
│  (Response)     │  • Include header/footer
└────────┬────────┘  • Generate charts
         │
         ▼
    User Display
```

---

## 🛠️ Technology Stack

```
┌──────────────────────────────────┐
│         FRONTEND                 │
├──────────────────────────────────┤
│ • HTML5                          │
│ • TailwindCSS (Utility CSS)      │
│ • DaisyUI (UI Components)        │
│ • JavaScript (Vanilla)           │
│ • Chart.js (Data Visualization)  │
│ • Font Awesome (Icons)           │
└──────────────────────────────────┘
              │
              ▼
┌──────────────────────────────────┐
│         BACKEND                  │
├──────────────────────────────────┤
│ • PHP 7.4+ (Server Logic)        │
│ • PDO (Database Abstraction)     │
│ • Sessions (State Management)    │
└──────────────────────────────────┘
              │
              ▼
┌──────────────────────────────────┐
│         DATABASE                 │
├──────────────────────────────────┤
│ • SQLite 3 (Embedded DB)         │
│ • 2 Tables (categories, expenses)│
└──────────────────────────────────┘
```

---

## 🎯 Feature Map

```
ExpenseLogger
│
├── 📊 Dashboard
│   ├── Statistics Cards (4)
│   ├── Pie Chart (Categories)
│   ├── Bar Chart (7 Days)
│   └── Recent Expenses (5)
│
├── 💰 Expenses
│   ├── Create (Add Modal)
│   ├── Read (Paginated Table)
│   ├── Update (Edit Modal)
│   ├── Delete (Confirmation)
│   └── Filters
│       ├── Category
│       ├── Date Range
│       └── Search Notes
│
├── 🏷️ Categories
│   ├── Create (Add Modal)
│   ├── Read (Card Grid)
│   ├── Update (Edit Modal)
│   ├── Delete (Protected)
│   └── Statistics
│       ├── Expense Count
│       └── Total Amount
│
├── 📈 Reports
│   ├── Filters
│   │   ├── Date Range
│   │   ├── Categories
│   │   └── Quick Presets
│   ├── Charts
│   │   ├── Pie (Distribution)
│   │   ├── Bar (Comparison)
│   │   └── Line (Trend)
│   ├── Top 10 Expenses
│   └── CSV Export
│
├── 💾 Backup
│   ├── Export (JSON)
│   ├── Import (JSON)
│   ├── Reset Database
│   └── Statistics View
│
└── ⚙️ Settings
    └── Theme Toggle (Dark/Light)
```

---

## 🔐 Security Layers

```
┌─────────────────────────────────────┐
│  Input Layer                        │
│  • HTML5 validation                 │
│  • Client-side checks               │
└────────────┬────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│  Processing Layer                   │
│  • Sanitize input                   │
│  • Type checking                    │
│  • Server validation                │
└────────────┬────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│  Database Layer                     │
│  • Prepared statements              │
│  • Parameter binding                │
│  • SQL injection prevention         │
└────────────┬────────────────────────┘
             │
             ▼
┌─────────────────────────────────────┐
│  Output Layer                       │
│  • htmlspecialchars()               │
│  • XSS prevention                   │
│  • Safe rendering                   │
└─────────────────────────────────────┘
```

---

## 📱 Responsive Breakpoints

```
Mobile (< 768px)
├── Single column layouts
├── Hamburger menu
├── Stacked cards
└── Full-width tables (scroll)

Tablet (768px - 1024px)
├── 2-column grids
├── Condensed navbar
├── Side-by-side cards
└── Responsive tables

Desktop (> 1024px)
├── Full navigation
├── 4-column grids
├── Expanded layouts
└── Fixed tables
```

---

## 🎨 Color System

```
Primary Colors:
├── Purple (#8b5cf6)   → Brand, Primary actions
├── Blue (#3b82f6)     → Secondary, Charts
├── Emerald (#10b981)  → Success, Positive
└── White (#ffffff)    → Backgrounds, Text

Gradient Cards:
├── Purple: Total Expenses
├── Blue: Monthly Expenses
├── Emerald: Total Entries
└── Pink: Categories
```

---

**Complete Visual Reference for ExpenseLogger Structure** 📊
