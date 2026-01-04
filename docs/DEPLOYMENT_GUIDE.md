# Safe Deployment Guide (Production)

This guide explains how to update your application code and database WITHOUT losing any real data.

## ⚠️ The Golden Rule
**NEVER** run `php artisan migrate:fresh` in production once you have real data.
*   `migrate:fresh` = Wipes ALL data and starts over.
*   `migrate` = Only adds NEW changes (Safe).

---

## Scenario: Adding a New Feature (e.g., "Notes" for Animals)

### 1. In Development (Your PC)
1.  Create a **new** migration file:
    ```bash
    php artisan make:migration add_notes_to_animals_table --table=animals
    ```
    *   *Do not edit the original 2024_01_01_create_animals... file.* Always create a new file with a new timestamp.
2.  Write the code to add the column:
    ```php
    public function up() {
        Schema::table('animals', function (Blueprint $table) {
            $table->text('notes')->nullable(); // Always nullable if adding to existing data
        });
    }
    ```
3.  Test it locally: `php artisan migrate`.

### 2. Preparation for Production
Before uploading, always **Backup your Database**.
1.  Go to **phpMyAdmin** in your hosting.
2.  Select your database -> **Export** -> **Quick** -> **Go**.
3.  Save the `.sql` file to your computer.

### 3. Deploying to Production
1.  **Upload Files**: Upload your modified PHP files (Controllers, Views) and the **new migration file** (`database/migrations/2026_..._add_notes.php`).
2.  **Maintenance Mode** (Optional but Recommended):
    *   Stop users from changing data while you update.
    *   Command: `php artisan down`
3.  **Run Migration**:
    *   Run this command in SSH (Terminal):
        ```bash
        php artisan migrate --force
        ```
    *   *Note*: The `--force` flag is required in production to confirm you really want to run it.
    *   Laravel looks at the `migrations` table in your DB, sees which files have already run, and **only runs the new file**. Your existing animals, invoices, and users remain untouched.
4.  **Live**:
    *   Command: `php artisan up`

## Summary Checklist
- [ ] **Code**: Created a NEW migration file (didn't edit old ones).
- [ ] **Backup**: Downloaded SQL backup from phpMyAdmin.
- [ ] **Upload**: Uploaded new code and migration files.
- [ ] **Execute**: Ran `php artisan migrate --force`.
