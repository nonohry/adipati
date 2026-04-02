# ADIPATI - Conference Management System

## Quick Start

### 1. Install Dependencies
```bash
composer install
```

### 2. Configure Environment
```bash
cp .env.example .env
# Edit .env with your database and email settings
```

### 3. Create Database
Create a MySQL database named `adipati_db` (or as specified in .env).

### 4. Import Schema
```bash
mysql -u username -p adipati_db < database/schema.sql
```

### 5. Set Permissions
```bash
chmod -R 775 storage/
chmod -R 775 bootstrap/cache/
```

### 6. Access Application
Point your web server document root to the `public/` directory.
Visit: http://localhost

## Default Credentials (After Seeding)
- **Email:** admin@adipati.local
- **Password:** admin123

## Features
- Multi-role authentication (Author, Reviewer, Editor, Chair, Admin)
- Paper submission & peer review workflow
- AI-powered editorial assistant
- Dynamic pricing & payment verification
- Public proceedings portal with OAI-PMH
- Certificate generation
- QR code check-in
- And more...

## Documentation
See INSTALL.md, DATABASE.md, and MODULES.md for detailed guides.
