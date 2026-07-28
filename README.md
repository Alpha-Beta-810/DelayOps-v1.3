# DelayOps 1.3 — Enterprise Delay Analytics

## What's new in v1.3 (vs v1.0)

| Capability | v1.0 | v1.3 |
| --- | --- | --- |
| UI Quality | ⚠️ Basic | ✅ Premium Interactive Charts |
| Data Compression | ❌ None | ✅ Auto-groups < 5% into "Others" |
| Drill-down logic | ⚠️ Limited | ✅ Eqpt → Sub-Eqpt Filtering + Back states |
| Unassigned Insight | ❌ Hidden | ✅ Dynamic Warning Headline & Gauge |
| Data Table | ⚠️ Static | ✅ Paginated + Searchable via API |
| Demo Module | ❌ Fake JS Data | ✅ Database-Driven (`sample_demo`) |
| Layout Stability | ⚠️ Fragile | ✅ CSS-Grid protected |

# 🚀 Installation & Local Setup

Follow the steps below to set up **DelayOps 1.3** on your local machine.

---

## **1. Clone the Repository**

```bash
# Clone the project from GitHub to your local machine
git clone https://github.com/Alpha-Beta-810/DelayOps.git

# Navigate into the project directory
cd DelayOps
```

**Explanation**

- `git clone` downloads the complete project repository, including all source code and version history.
- `cd DelayOps` changes your current working directory to the project folder so that all subsequent commands are executed inside the application.

---

## **2. Install PHP Dependencies**

```bash
# Install all PHP packages required by the project
composer install
```

**Explanation**

- Reads the `composer.json` file.
- Downloads all required Laravel packages and third-party libraries.
- Generates Composer's autoloader.
- Creates the `vendor/` directory containing all project dependencies.

> **Note:** Ensure Composer is installed before running this command.

---

## **3. Configure the Database**

Before continuing, ensure that:

- MySQL is running.
- The project's `.env` file is already configured with the correct database credentials.
- The database specified in `.env` already exists.

> **⚠️ Important**
>
> This repository assumes that a valid `.env` configuration already exists.
> Do **not** overwrite or recreate the `.env` file unless you intentionally want to create a completely new environment configuration.

---

## **4. Generate the Application Key (Optional)**

```bash
# Generate a new Laravel application encryption key
php artisan key:generate
```

**Explanation**

- Generates a unique encryption key used by Laravel.
- Stores the generated key inside the `.env` file.
- Required for encrypted cookies, sessions, and other security features.

> Skip this step if a valid `APP_KEY` already exists.

---

## **5. Run Database Migrations**

```bash
# Create all required database tables
php artisan migrate
```

**Explanation**

- Reads every migration inside the `database/migrations` directory.
- Creates the required tables.
- Applies schema changes in the correct order.
- Tracks completed migrations to avoid duplicate execution.

---

## **6. Clear Cached Configuration**

```bash
# Remove cached configuration files
php artisan config:clear
```

**Explanation**

Laravel caches configuration files for faster performance.

Running this command:

- Clears previously cached configuration.
- Forces Laravel to read the latest values from the `.env` file.
- Prevents stale configuration from causing unexpected database or environment issues.

This is particularly useful after changing database credentials or application settings.

---

## **7. Start the Development Server**

```bash
# Launch Laravel's built-in development server
php artisan serve
```

**Explanation**

- Starts a local web server.
- By default, the application becomes available at:

```
http://127.0.0.1:8000
```

or

```
http://localhost:8000
```

---

## **8. Open the Dashboard**

Visit the application in your browser:

```
http://localhost:8000/dashboard
```

or

```
http://127.0.0.1:8000/dashboard
```

The interactive Delay Analytics Dashboard should now be running successfully.

---

## 📌 Prerequisites

Before installation, ensure the following software is installed:

- PHP 8.x or later
- Composer
- MySQL Server
- Git
- Laravel-compatible PHP extensions (OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath)

---

## ⚠️ Configuration Notes

- Ensure MySQL is running before executing migrations.
- Verify that the database name in the `.env` file exists.
- Avoid modifying the `.env` file unless updating environment-specific settings.
- If configuration changes are made later, execute:

```bash
php artisan config:clear
```

to refresh Laravel's cached configuration.
