# ExpenseLogger Installation Guide

## Quick Start (5 Minutes)

### For XAMPP Users (Windows)

1. **Install XAMPP**
   - Download from: https://www.apachefriends.org/
   - Install to default location: `C:\xampp`
   - Run installer and complete setup

2. **Extract Application**
   - Place the `ExpenseLogger` folder in: `C:\xampp\htdocs\`
   - Final path should be: `C:\xampp\htdocs\ExpenseLogger\`

3. **Start XAMPP**
   - Open XAMPP Control Panel
   - Click "Start" for **Apache** module
   - Wait for green highlight (running status)

4. **Access Application**
   - Open your web browser
   - Navigate to: `http://localhost/ExpenseLogger`
   - Database auto-creates on first visit
   - Default categories are automatically added

5. **Add Sample Data (Optional)**
   - Visit: `http://localhost/ExpenseLogger/demo_data.php`
   - Click to populate with 23 sample expenses
   - Perfect for testing features

### For Other PHP Environments

#### Requirements:
- PHP 7.4 or higher
- SQLite extension enabled (built into most PHP installations)
- Apache or Nginx web server

#### Setup Steps:

1. **Place files in web root**
   ```bash
   # For Apache (Ubuntu/Debian)
   /var/www/html/ExpenseLogger/
   
   # For Nginx
   /usr/share/nginx/html/ExpenseLogger/
   ```

2. **Set Permissions**
   ```bash
   # Make data directory writable
   chmod 755 /path/to/ExpenseLogger/data
   chmod 644 /path/to/ExpenseLogger/data/expenselogger.db
   ```

3. **Check PHP SQLite**
   ```bash
   php -m | grep sqlite
   # Should show: pdo_sqlite and sqlite3
   ```

4. **Access Application**
   ```
   http://your-domain.com/ExpenseLogger
   ```

---

## Verification Checklist

✅ **Apache/PHP is running**
- XAMPP Control Panel shows Apache as "Running" (green)
- Or use: `http://localhost` to see XAMPP dashboard

✅ **Files are in correct location**
- All PHP files in: `C:\xampp\htdocs\ExpenseLogger\`
- Folder structure matches the project layout

✅ **Application loads**
- Opening `http://localhost/ExpenseLogger` shows the dashboard
- No PHP errors displayed

✅ **Database created**
- File exists: `C:\xampp\htdocs\ExpenseLogger\data\expenselogger.db`
- Categories page shows 5 default categories

✅ **Theme toggle works**
- Click moon/sun icon in navbar
- Page theme changes between light and dark

---

## Common Issues & Solutions

### Issue: "Page not found" or 404 Error
**Solution:**
- Verify Apache is running in XAMPP
- Check folder name is exactly: `ExpenseLogger` (case-sensitive)
- Try: `http://localhost/ExpenseLogger/index.php`

### Issue: PHP errors about SQLite
**Solution:**
- Open XAMPP Control Panel → Apache → Config → PHP.ini
- Find line: `;extension=sqlite3`
- Remove semicolon: `extension=sqlite3`
- Save and restart Apache

### Issue: Database file not created
**Solution:**
- Check `data/` folder exists
- Verify write permissions on `data/` folder
- On Windows: Right-click → Properties → Security → Edit

### Issue: Charts not displaying
**Solution:**
- Check internet connection (CDN resources)
- Open browser Console (F12) for JavaScript errors
- Try clearing browser cache (Ctrl+F5)

### Issue: Theme not saving
**Solution:**
- Verify sessions are enabled in PHP
- Check browser allows cookies
- Try different browser to isolate issue

---

## First-Time Setup Workflow

1. **Access Application**
   ```
   http://localhost/ExpenseLogger
   ```
   - You'll see the dashboard with $0.00 (empty state)

2. **Add Sample Data**
   ```
   http://localhost/ExpenseLogger/demo_data.php
   ```
   - Adds 23 sample expenses automatically
   - Refresh dashboard to see populated charts

3. **Explore Features**
   - **Dashboard**: View summary and charts
   - **Expenses**: Add, edit, delete, filter expenses
   - **Categories**: Manage expense categories
   - **Reports**: Generate analytics with date filters
   - **Backup**: Export/import data, reset database

4. **Customize**
   - Add your own categories
   - Delete sample data and add real expenses
   - Adjust theme preference (dark/light)

---

## Production Deployment (Optional)

### For Live Server:

1. **Upload Files**
   - Use FTP/SFTP to upload all files
   - Maintain folder structure

2. **Set Permissions**
   ```bash
   chmod 755 data/
   chmod 644 config/*.php
   ```

3. **Secure Database**
   - Move `data/` outside web root if possible
   - Update path in `config/database.php`

4. **Enable HTTPS**
   - Get SSL certificate (Let's Encrypt)
   - Force HTTPS in `.htaccess`

5. **Disable Demo Script**
   - Delete or restrict `demo_data.php`

6. **Hide Errors**
   - In `config/init.php`:
   ```php
   error_reporting(0);
   ini_set('display_errors', 0);
   ```

---

## Backup Recommendations

### Regular Backups:
- Use the built-in Export feature weekly
- Store backups in multiple locations:
  - Local computer
  - Cloud storage (Google Drive, Dropbox)
  - USB drive

### Automated Backups:
```bash
# Add to cron job (Linux) or Task Scheduler (Windows)
# Visits backup URL and downloads JSON
wget http://localhost/ExpenseLogger/backup.php?action=export -O backup_$(date +%Y%m%d).json
```

---

## Updating the Application

1. **Export Your Data**
   - Go to Backup & Restore
   - Download current backup

2. **Replace Files**
   - Keep `data/` folder intact
   - Replace all other files with new version

3. **Test Application**
   - Access homepage
   - Verify data is intact
   - Check all features work

4. **Import Backup (if needed)**
   - Use backup file if any issues occur

---

## Support & Troubleshooting

### Enable PHP Error Display:
In `config/init.php`, ensure:
```php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```

### Check PHP Info:
Create `phpinfo.php`:
```php
<?php phpinfo(); ?>
```
Access: `http://localhost/ExpenseLogger/phpinfo.php`

### Database Issues:
- Delete `data/expenselogger.db`
- Reload application (auto-recreates)
- Use Import feature to restore data

---

## System Requirements

### Minimum:
- PHP 7.4+
- SQLite 3
- 10MB disk space
- Any modern browser

### Recommended:
- PHP 8.0+
- 50MB disk space (for backups)
- Chrome/Firefox/Edge (latest)
- 1920x1080 screen resolution

---

## Getting Help

1. **Check README.md** - Comprehensive documentation
2. **Review Code Comments** - Detailed explanations in PHP files
3. **Browser Console** - Check for JavaScript errors (F12)
4. **PHP Logs** - Check XAMPP logs in `C:\xampp\apache\logs\`

---

**You're all set! Enjoy tracking your expenses with ExpenseLogger! 🎉**
