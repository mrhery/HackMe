# 🛡️ HackMe Web – Educational Web Hacking Lab

## 1️⃣ Project Introduction

**HackMe Web** is a deliberately vulnerable PHP + MySQL web application created in 2021 to demonstrate common web security vulnerabilities.

It is built for:

* Students learning web security
* Developers who want to understand how hacking works
* Security beginners practicing penetration testing
* Training / workshop demo purposes

> ⚠️ This project is for educational purposes only. Do NOT deploy in production.

---

## 2️⃣ Tech Stack

* PHP (no framework / or specify version)
* MySQL / MariaDB
* Apache / Nginx
* XAMPP / LAMP supported

Example:

```
PHP 7.x
MySQL 5.7+
Apache 2.4
```

---

## 3️⃣ Installation Guide

### Step 1: Clone Repository

```bash
git clone https://github.com/yourusername/hackme-web.git
cd hackme-web
```

### Step 2: Setup Database

1. Create database:

```sql
CREATE DATABASE hackme;
```

2. Import SQL file:

```bash
mysql -u root -p hackme < database.sql
```

### Step 3: Configure Database Connection

Edit:

```
config.php
```

Update:

```php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "hackme";
```

### Step 4: Run Web Server

Using XAMPP:

* Put folder into `htdocs`
* Visit:

```
http://localhost/hackme-web
```

### Support Vulnerabilities:
1. SQLi Login Bypass
2. SQLi POST
3. SQLi GET
4. SQLi Shell Code
5. Remote Code Execution (RCE)
6. XSS Attack
7. Stored XSS
8. Session Hijacking
9. Server Site Request Forgery (SSRF)
10. Cross Site Request Forgery (CSRF)
11. Spam Attack
12. Brute-force Attack
