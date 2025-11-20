# ST PRINT - Complete Setup Guide

## 📦 Files Included

All the cleaned and corrected PHP files are provided above. Here's what to do:

### Step 1: Prepare Your Environment
- PHP 7.4+ with MySQLi support
- MySQL 5.7+
- Web server (Apache/Nginx)

### Step 2: Create Directory Structure

```
st-print/
├── config.php
├── admin_login.php
├── admin_panel.php
├── admin_sellers.php
├── admin_panel_navbar.php
├── change_password.php
├── seller_login.php
├── seller_dashboard.php
├── seller_action.php
├── seller_scan_lookup.php
├── seller_change_password.php
├── seller_logout.php
├── logout.php
├── submit_order.php
├── track_order.php
├── track_order_result.php
├── thank_you.php
├── index.php
├── style.css
├── create_tables.sql
└── SETUP_INSTRUCTIONS.md
```

### Step 3: Database Setup

1. **Open phpMyAdmin or MySQL CLI**

2. **Run the SQL script:**
   - In phpMyAdmin: Import `create_tables.sql`
   - Via CLI:
   ```bash
   mysql -u root -p < create_tables.sql
   ```

3. **Verify tables created:**
   ```sql
   USE st_print;
   SHOW TABLES;
   ```

### Step 4: Configure Database Connection

Edit `config.php` with your credentials:

```php
$DB_HOST = 'localhost';     // Your MySQL host
$DB_USER = 'root';          // Your MySQL username
$DB_PASS = '';              // Your MySQL password
$DB_NAME = 'st_print';      // Database name
```

### Step 5: Deploy Files

1. Copy all PHP files to your web server directory
2. Copy `style.css` to the same directory
3. Ensure proper file permissions (755 for folders, 644 for files)

### Step 6: Test Installation

1. **Admin Login Test:**
   - URL: `http://localhost/st-print/admin_login.php`
   - Username: `admin`
   - Password: `admin123`

2. **Public Order Form:**
   - URL: `http://localhost/st-print/index.php`

3. **Seller Login Test:**
   - URL: `http://localhost/st-print/seller_login.php`
   - Create a seller first from admin panel

## 🔑 Default Credentials

| Role | Username | Password |
|------|----------|----------|
| Admin | admin | admin123 |

**⚠️ IMPORTANT:** Change the admin password immediately after first login via Admin Panel > Change Password

## 🐛 Troubleshooting

### Issue: "Connection failed"
**Solution:**
- Verify MySQL is running
- Check credentials in config.php
- Ensure database name is `st_print`

### Issue: "Server error" on login
**Solution:**
- Check `create_tables.sql` was executed
- Verify `admin_users` table exists
- Check error logs

### Issue: Orders not submitting
**Solution:**
- Verify `orders` table exists
- Check database permissions
- Look at browser console for JavaScript errors

### Issue: Can't upload files
**Solution:**
- Check folder permissions (should be 755)
- Ensure PHP has write access
- Check php.ini file upload settings

## 🔒 Security Checklist

- [ ] Changed default admin password
- [ ] Updated database credentials in config.php
- [ ] Set correct file permissions
- [ ] Disabled directory listing
- [ ] Enabled HTTPS (if in production)
- [ ] Added firewall rules
- [ ] Regular backups enabled

## 📝 Key Features

✅ **Admin Panel:**
- View/filter orders
- Export to CSV
- Update order status
- Manage sellers
- Change password

✅ **Seller Dashboard:**
- Manage inventory
- QR code scanning
- Sales tracking
- Commission calculation
- Password management

✅ **Public Portal:**
- Place orders
- Track status
- View details

## 🚀 Production Deployment

Before going live:

1. **Security:**
   - Use HTTPS/SSL
   - Hide error messages from users
   - Add rate limiting
   - Implement CAPTCHA for forms

2. **Performance:**
   - Enable database indexes (included in SQL)
   - Cache frequently accessed data
   - Optimize images

3. **Backup:**
   - Set up automated daily backups
   - Test restore procedures

4. **Monitoring:**
   - Monitor error logs
   - Track database performance
   - Monitor server resources

## 📞 Support

For issues, check:
1. Error logs in your server
2. Browser console (F12)
3. Database integrity
4. File permissions

---

**Last Updated:** 2025-11-19
**Version:** 1.0 (Clean & Fixed)