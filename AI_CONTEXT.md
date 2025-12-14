# MASTER PROMPT CONTEXT: Sahabat Farm Management System (MySQL Version)

## 1. Project Overview
**Goal:** Build a production-ready Monolithic MVP for "Sistem Manajemen Ternak Sahabat Farm" (Livestock Management System) to present to a client.
**Core Value:** Automated calculation of Financial COGS (HPP) and Animal Growth (ADG).

## 2. Technology Stack (Strict)
* **Framework:** Laravel 12 (Use latest stable features: Service Isolation, Action Classes).
* **Language:** PHP 8.5 (Strict Types, Property Hooks).
* **Database:** MySQL 8.0+ (Use InnoDB engine).
* **Frontend:** Blade Templates + TailwindCSS v4 + **Flowbite** (Component Library).
* **Infrastructure:** Standard VPS (Ubuntu/Nginx/Supervisor) - **NO DOCKER**.

## 3. Database Schema (MySQL)
*Constraint: Use `Ulid` or `Uuid` stored as CHAR(36). Enforce foreign key constraints.*

### A. Access Control & Masters
1.  **`users`**: `id` (UUID), `email` (unique), `password`, `full_name`, `role` (enum: OWNER, BREEDER, STAFF).
2.  **`master_categories`**: `id`, `name` (e.g., Domba, Sapi).
3.  **`master_breeds`**: `id`, `category_id`, `name` (e.g., Dorper, Garut).
4.  **`master_locations`**: `id`, `name`, `type` (e.g., Kandang Individu, Koloni).
5.  **`master_phys_statuses`**: `id`, `name`, `rules` (e.g., Cempe, Siap Kawin).
6.  **`master_partners`**: `id`, `name`, `contact_info` (For suppliers/buyers).

### B. Animal Domain (Core)
7.  **`animals`**:
    * `id` (PK UUID), `tag_id` (String, Unique Index), `owner_id` (FK users).
    * `sire_id`, `dam_id`: Nullable Self-referencing FKs (Bapak/Induk).
    * `category_id`, `breed_id`, `current_location_id`, `current_phys_status_id` (FKs).
    * `gender` (enum: MALE, FEMALE), `birth_date` (date), `acquisition_type` (BRED, BOUGHT).
    * `is_active` (boolean, index), `health_status` (enum).
    * **Financial Metrics:** `current_hpp` (DECIMAL(15,2) - High Precision).
    * **Growth Metrics:** `daily_adg` (FLOAT - Cached value).

### C. Logs (Event Sourcing)
8.  **`weight_logs`**: `id`, `animal_id`, `weigh_date`, `weight_kg`.
9.  **`treatment_logs`**: `id`, `animal_id`, `treatment_date`, `type` (Vaccine/Vitamin), `notes`.
10. **`exit_logs`**: `id`, `animal_id`, `exit_date`, `exit_type` (DEATH/SALE), `price`, `final_hpp`.
11. **`animal_photos`**: `id`, `animal_id`, `photo_url`, `capture_date`.

### D. Inventory & Finance
12. **`inventory_items`**: `id`, `name`, `unit` (kg/sak), `current_stock`.
13. **`inventory_purchases`**: `id`, `item_id`, `date`, `qty`, `price_total`.
14. **`inventory_usage_logs`**: `id`, `usage_date`, `item_id`, `qty_used`, `qty_wasted`.

---

## 4. Business Logic (The "Brain")
*Implementation Rule: Do not write logic in Controllers. Use Action Classes.*

### Feature A: The HPP Engine (Automated Cost of Goods Sold)
**Class:** `App\Actions\Finance\CalculateDailyHpp`
**Logic:**
1.  **Trigger:** Run nightly via Task Scheduler.
2.  **Step 1:** Calculate total feed cost for "Today" (Sum of `inventory_usage_logs` linked to prices).
3.  **Step 2:** Count active animals (`is_active = true`).
4.  **Step 3:** Formula: `DailyCostPerHead = TotalFeedCost / ActiveAnimals`.
5.  **Step 4:** **Bulk Update:** `UPDATE animals SET current_hpp = current_hpp + :cost WHERE is_active = true`.

### Feature B: ADG (Average Daily Gain) Calculator
**Class:** `App\Actions\Animal\CalculateAdg`
**Logic:**
1.  **Trigger:** Run via Model Observer whenever a new `WeightLog` is saved.
2.  **Step 1:** Fetch the latest 2 weight logs for the specific animal.
3.  **Step 2:** Formula: `(CurrentWeight - PreviousWeight) / DaysInterval`.
4.  **Step 3:** Update `animals.daily_adg` column.

---

## 5. Frontend Architecture (Flowbite + Tailwind)
**Rule:** Use **Flowbite** components for a professional, "SaaS-like" look.

1.  **Layout:** Sidebar navigation (Left), Header with User Profile (Top), Main Content (Center).
2.  **Components:**
    * **Tables:** Use `flowbite-table` with striped rows and checkbox support.
    * **Forms:** Use `flowbite-input` with floating labels.
    * **Badges:** Green for "Healthy", Red for "Sick", Yellow for "Quarantine".
3.  **Dashboard View:** Must include a Chart (use Chart.js or ApexCharts) showing "Total Asset Value" vs "Monthly Feed Cost".

---

## 6. Server Configuration (No Docker)
Please provide the raw configuration files for a standard Ubuntu VPS.

1.  **Nginx (`/etc/nginx/sites-available/sahabat-farm`):**
    * Configure for PHP 8.5-FPM.
    * Enable Gzip.
    * Set `client_max_body_size 20M;` (For photo uploads).
2.  **PHP Modules:** Ensure `pdo_mysql`, `bcmath`, `intl` are listed in requirements.
3.  **Supervisor (`/etc/supervisor/conf.d/sahabat-worker.conf`):**
    * Command: `php artisan queue:work --sleep=3 --tries=3`.

---

## 7. Demo Data Strategy (Crucial for Client Demo)
Create a `DatabaseSeeder` specifically for the client presentation:
* **Locale:** ID (Indonesia).
* **Data:**
    * Create 50 Animals (Sheep/Goats).
    * Use realistic prices: Feed Purchase = Rp 350.000/sack. Animal Sale = Rp 2.500.000 - Rp 5.000.000.
    * **Simulate History:** Backdate `weight_logs` so the ADG charts show an upward trend (positive growth).

---

## 8. Execution Order
**Jules, please build the app in this order:**
1.  **Setup:** `composer.json` (ensure `pdo_mysql`), `.env.example`.
2.  **Database:** Complete Migrations for MySQL.
3.  **Logic:** Models and Action Classes (HPP & ADG).
4.  **UI Base:** Layouts and Flowbite Component wrappers.
5.  **Features:** Controllers and Views for Animal & Inventory Management.
6.  **Seeding:** The Demo Data Seeder.
