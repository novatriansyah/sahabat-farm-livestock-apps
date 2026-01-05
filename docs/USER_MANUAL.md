# Sahabat Farm Indonesia - User Manual

## 1. Role Overview
The system is divided into 4 specific roles, each with different access levels:

| Role | Access Level | Primary Features |
| :--- | :--- | :--- |
| **OWNER** | **Full Admin** | Financials, Admin, Reports, Breeding, Stock, Partner Management. |
| **BREEDER** | **High Level** | Breeding, Health, Stock, Inventory, Invoices, Reports. |
| **PARTNER** | **Investor (View Only)** | Dashboard (ROI), My Animals, Reports. |
| **STAFF** | **Operator** | Feeding Logs, Daily Care, QR Scanning. |

---

## 2. OWNER & BREEDER Guide
*(Managers & Decision Makers)*

### A. Dashboard & Monitoring
1.  **Login**: Access the system via `/login`.
2.  **Dashboard**:
    *   **Quick Stats**: Total Population, Sick Animals, Sales this Month.
    *   **Alerts**: Check the "Notifications" or "Pending Tasks" sections for:
        *   **Vaccine Due**: Animals needing vaccination today/tomorrow.
        *   **Weaning Ready**: Lambs >40 days old ready for separation.
    *   **Financials**: Real-time sales and profit charts (Owner only).

### B. Livestock Management (Data Ternak)
*   **Add New Animal**:
    1.  Go to **Data Ternak** > **+ Tambah Baru**.
    2.  Select **Source**: "Lahir di Farm" (Born here) or "Beli" (Purchased).
    3.  **Important**: Auto-status is applied for newborns (<40 days = Nursing).
*   **Update Status**:
    *   **Weaning**: The system automates weaning status at 40 days, but you can manually update location.
    *   **Edit**: Use the Edit button to change details.
    *   **Slaughter/Death**: Use the "Exit" (Keluar) button to record deaths or slaughter.
*   **Breeding**:
    1.  Go to **Breeding** tab on an animal's profile.
    2.  Record Mating (Kawin). The system auto-calculates the Est. Birth Date (+150 days).

### C. Finance & Inventory (Gudang & Keuangan)
*   **Inventory (Pakan & Obat)**:
    *   **Stock In**: Add new sacks of feed or medicine bottles via **Gudang & Pakan**.
    *   **Usage**: Usually recorded by STAFF, but you can manually record usage here.
*   **Invoices (Penjualan)**:
    1.  Go to **Invoices** > **Create New**.
    2.  **Select Customer**: Or create a new one (Address & Tax info enabled).
    3.  **Add Items**: Select specific animals (by Tag ID) to sell. Price is auto-filled (but editable).
    4.  **Down Payment (DP)**: Enter DP amount if applicable.
    5.  **Status**: Mark as **PAID** to automatically move animals to "SOLD" status and remove them from active stock.

### D. Reports (Laporan)
*   **Stok & Populasi**: Current herd count by Pen and Gender. Use "Print All" for A4 hardcopy.
*   **Performa (ADG)**: Weight gain analysis. Check "Top 10" to identify best genetics.
*   **Penjualan**: Monthly revenue and profit/margin (Owner only).
*   **Reproduksi**: Track dam productivity (Litter size, intervals).
*   **Laporan Mitra**: Investor specific reports (Owner/Admin only).

---

## 3. PARTNER Guide
*(Investors / Mitra)*

### A. Accessing Your Data
1.  **Login**: Use the credentials provided by SFI Admin.
2.  **Partner Dashboard**: This is your specialized home page.
    *   **Asset Value**: Current total estimation of your livestock value.
    *   **Population**: Number of animals you own.
    *   **Growth**: Weight gain trends of your specific cattle.

### B. My Animals
*   Go to **Data Ternak**. You will ONLY see animals belonging to you.
*   **Search**: Enter Tag ID to find a specific animal.
*   **Details**: Click on an animal to see its weight history, photos, and health logs.

---

## 4. STAFF Guide
*(Field Operators / Anak Kandang)*

### A. Daily Routine
1.  **Scan QR**: Use the **Scan QR** menu to quickly identify an animal in the pen (requires camera permission).
    *   This opens the animal's profile immediately.
2.  **Feeding (Pakan)**:
    *   Go to **Feeding / Usage** (if authorized).
    *   Record amount of feed (sacks/kg) taken from warehouse to the barn.
3.  **Health Check**:
    *   Report sick animals to the Breeder/Manager immediately.

---

## 5. Administration & Master Data (Admin Only)

### A. User Management
*   **Create Users**: Go to **Admin Area** > **Manajemen User**.
*   **Assign Roles**: Be careful when assigning OWNER or BREEDER roles.
*   **Link Partner**: For Investors, select Role "PARTNER" and choose their Partner Name from the dropdown.

### B. Master Data (Farm Settings)
*   **Access**: Go to **Admin Area** > **Pengaturan Farm**.
*   **Features**:
    *   **Breeds**: Add/Edit animal breeds (e.g., Dorper, Merino).
    *   **Locations**: Manage pens/cages (Kandang) names.
    *   **Diseases**: Register common diseases for health logging.
    *   **Items**: Register feed types and medicines (e.g., Konsentrat A, Vitamin B).
    *   **Categories**: Manage animal and inventory categories.

---

## 6. Deployment & System Maintenance

### A. Backup & Updates
*   **Database Backup**: Export SQL from phpMyAdmin weekly.
*   **System Update**:
    *   Upload files.
    *   Run `php artisan migrate --force` for database changes.


