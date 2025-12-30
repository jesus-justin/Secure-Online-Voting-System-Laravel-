# 🎯 Database Import Guide

## 📁 SQL File Location
```
database/secure_voting.sql
```

## ✨ What's Included

The `secure_voting.sql` file contains a complete database setup with:

### **8 Tables:**
1. **users** - Voter accounts with verification (10,102 bytes)
2. **elections** - Election management
3. **candidates** - Candidates per election
4. **votes** - Encrypted votes with SHA-256 hashing
5. **voting_tokens** - One-time voting tokens
6. **vote_logs** - Complete audit trail
7. **jobs** - Laravel queue system
8. **failed_jobs** - Failed job tracking

### **Security Features:**
- ✅ Foreign key relationships (cascade delete)
- ✅ Unique constraints (prevent duplicates)
- ✅ Automatic timestamps (created_at, updated_at)
- ✅ 25+ performance indexes
- ✅ UTF8MB4 character set (full Unicode support)
- ✅ Sample admin account included

### **Performance Optimizations:**
- Indexed columns for fast queries
- Optimized for voting system operations
- Proper data types (BIGINT for IDs, TIMESTAMP for dates)

---

## 📋 Step-by-Step Import Guide

### **Step 1: Start XAMPP**
- Open XAMPP Control Panel
- Start Apache and MySQL services
- Ensure both show "Running" status

### **Step 2: Access phpMyAdmin**
1. Open browser → `http://localhost/phpmyadmin`
2. Log in (default: username = `root`, password = blank)

### **Step 3: Import Database**
1. Click **"Import"** tab (top menu)
2. Click **"Choose File"** button
3. Navigate to: `database/secure_voting.sql`
4. Click **Open**
5. Click **"Import"** button (blue button at bottom)

### **Step 4: Verify Import**
- Refresh the page or left sidebar
- You should see **`secure_voting`** database in the list
- Click it to see all 8 tables

---

## 🔑 Admin Account

A default admin account is pre-created:

```
Email: admin@example.com
Password: admin123
```

**⚠️ IMPORTANT:** Change this password immediately after first login!

To change the password:
1. Log in to the application
2. Go to Account Settings
3. Change your password

---

## 📊 Database Structure

### **users Table**
```sql
- id (bigint, PK)
- name, email, password
- voter_id (unique)
- is_verified (0/1)
- verified_at (timestamp)
- is_admin (0/1)
- last_login_at
- created_at, updated_at
```

### **votes Table** (Most Important)
```sql
- id (bigint, PK)
- user_id, election_id, candidate_id (FKs)
- encrypted_vote (AES-256-CBC)
- vote_hash (SHA-256)
- device_fingerprint
- ip_address
- created_at (unique per user/election)
```

### **voting_tokens Table** (Security)
```sql
- id (bigint, PK)
- user_id, election_id (FKs)
- token (one-time use)
- used_at, expires_at
- created_at
```

### **vote_logs Table** (Audit Trail)
```sql
- id (bigint, PK)
- vote_id, user_id, election_id (FKs)
- action (vote_cast, tampering_detected, etc.)
- old_value, new_value
- ip_address, user_agent
- performed_at
```

---

## ✅ Verification Steps

After import, verify everything is correct:

### **In phpMyAdmin:**
1. Click database `secure_voting`
2. Click **"Structure"** tab
3. Verify all 8 tables are listed
4. Click each table to verify columns

### **In Laravel App:**
1. Update `.env` file:
   ```env
   DB_DATABASE=secure_voting
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. Test connection:
   ```bash
   php artisan tinker
   >>> DB::connection()->getPDO();
   ```

3. If no error, connection is successful! ✅

---

## 🚀 Next Steps After Import

1. **Update `.env`:**
   ```env
   DB_DATABASE=secure_voting
   DB_USERNAME=root
   DB_PASSWORD=
   ```

2. **Log in to application:**
   - Email: `admin@example.com`
   - Password: `admin123`

3. **Change admin password immediately** (Security best practice)

4. **Create elections:**
   - Go to Admin Dashboard
   - Create your first election
   - Add candidates
   - Set dates and times

5. **Create test voters:**
   - Have test users register
   - Verify their accounts as admin
   - Test the voting flow

---

## 🔧 Troubleshooting

### **Import fails with "Access denied"**
- Solution: Ensure MySQL is running in XAMPP
- Restart MySQL service and try again

### **"Database already exists" error**
- Solution: Drop the old database first
  1. In phpMyAdmin, click `secure_voting`
  2. Click **"Operations"** tab
  3. Click **"Drop"** at bottom
  4. Try importing again

### **Can't find the SQL file**
- Solution: File location: `database/secure_voting.sql`
- From XAMPP root: `xampp/htdocs/Secure-Online-Voting-System-Laravel-/database/secure_voting.sql`

### **"Table already exists"**
- Solution: Select **"Drop existing tables"** during import

---

## 📝 Important Notes

1. **Character Set:** UTF8MB4 (supports all languages and emojis)
2. **Foreign Keys:** Enabled (cascade delete for integrity)
3. **Timestamps:** Automatic (no manual date entry needed)
4. **Indexes:** 25+ for optimal performance
5. **Data Types:** Optimized for voting system operations

---

## 🎉 You're Done!

Your database is ready to use! The complete voting system schema is installed with:
- ✅ 8 tables
- ✅ Proper relationships
- ✅ Security indexes
- ✅ Sample admin account
- ✅ Audit logging tables

**Now you can:**
- 🔐 Register voters
- 🗳️ Create elections
- 🎯 Cast votes securely
- 📊 View results
- 👨‍💼 Manage as admin

---

**Need Help?** Check the application documentation in `docs/` folder.
