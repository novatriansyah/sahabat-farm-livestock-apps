# Product Requirement Document (PRD) - Sahabat Farm Indonesia Livestock App

This document defines the features, data models, workflows, and configurations of the Sahabat Farm Indonesia Livestock Application.

---

## 1. Executive Summary
The Sahabat Farm Indonesia Livestock Application is a comprehensive web-based herd and inventory management system built to manage farm operations. The app streamlines livestock tracking, breeding cycles, weight records, financial cost tracking (HPP), inventory, sales, and task management. It accommodates multiple user roles (Owner, Staff, Breeder, Partner) and incorporates advanced automation.

---

## 2. Core Functional Modules

### 2.1 Livestock Profile Management
* **Basic Fields**: Tag ID, Gender, Breed (Jenis & Ras), Category, Physical Status (e.g. Cempe, Dara, Indukan, Ready to Mate), Location, Birth Date, Entry Date, Acquisition Type (Hasil Ternak or Beli), Purchase Price, and Google Drive attachments.
* **Photo Attachments**: Supports uploading multiple photos. Large files are compressed automatically to WebP format at 800px width and 75% quality. Unused photos can be deleted directly from the edit view.
* **Smart Identification & Visual Coding**:
  * **Warna Kalung (Collar Color)**: Dynamic dropdown color choices mapped via settings to catalog physical attributes. Used on bought animal forms and birth registration.
  * **Warna Ear Tag (Ear Tag Color)**: Automatically assigned by the backend observer (`AnimalObserver`) based on breed and generation. Mappings are fully configurable via settings (e.g. F1 Dorper = Kuning, F2 Dorper = Orange).

### 2.2 Kelahiran (Recording Birth)
* **Offspring Registration**: Allows recording new births (cempe) linking them directly to their mother (Dam) and optional father (Sire).
* **Auto-inheritance**:
  * **Mitra (Partner)**: Inherited from the Dam's partner.
  * **Breed**: Auto-detected from the Sire's breed or Dam's breed if Sire is not specified.
  * **Generation**: Automatically calculated based on parents (e.g. F2 Sire + F1 Dam = F3 offspring). F6 offspring transition automatically to `PUREBREED`.
* **Dam Updates**: Dam's physical status transitions automatically to *Menyusui* (Lactating) upon offspring creation.

### 2.3 Colony Mating & Breeding Cycles
* **Mating Colonies**: Group multiple breeding females (Dam) with one breeding male (Sire) in a colony pen.
* **Auto-Transitions & Alerts**:
  * **Mating Period**: Spans 60 days. After 60 days, animals are returned to standard status.
  * **Pregnancy Check (Cek Kebuntingan)**: Initiated 60 days after mating start. If confirmed pregnant, the Dam's status transitions to *Bunting* (Pregnant).
  * **Weaning Alert (Sapih)**: Offspring are automatically marked for weaning 35 days post-birth.
  * **Post-partum Recovery (Nifas)**: Indukan recover for 40 days post-birth, transitioning back to *Siap Kawin* (Ready to Mate) after recovery.

### 2.4 Weight & ADG Performance Tracking
* **Weight Logs**: Historic weight checks logged per animal.
* **Average Daily Gain (ADG)**: Automatically calculated upon adding a new weight log:
  $$\text{ADG} = \frac{\text{Current Weight} - \text{Previous Weight}}{\text{Days Elapsed}}$$
* **Dashboard Summary**: Displays overall average ADG per age group for tracking performance.

### 2.5 Inventory & HPP Cost Tracking
* **Inventory Categories**: Feed, Medicines, Vaccines, and Vitamins. Logs stock purchases (weight, price, total) and usage.
* **HPP Calculation**:
  * **Feed Cost**: Accumulated by dividing feed usage logs in colony locations equally among all present animals in that location on that day.
  * **Manual Costs**: Operational manual costs (salaries, electricity) are inputted monthly and distributed equally to all active animals.
  * **Current HPP**: The sum of purchase price + accumulated feed cost + accumulated medicine cost + manual costs.

### 2.6 Invoice & Sales Workflow
* **Sales Registration**: Exit of type `JUAL` registers a sale.
* **Automatic Invoice generation**: Generates a draft invoice inside the database containing sold animal details, subtotal, and customer info, automatically reducing active herd numbers.

---

## 3. Dynamic Visual Configurations (Settings)
Rather than hardcoding colors and generation rules, the system handles visual parameters dynamically using database settings managed via the **Pengaturan Farm** tab in `/admin/masters`:

1. `available_necklace_colors`: Comma-separated list of colors available for necklace selectors.
2. `available_ear_tag_colors`: Comma-separated list of colors available for ear tags.
3. `eartag_map_dorper_f1` to `eartag_map_dorper_f6`: Mappings specifying colors for each generation.
4. `eartag_map_default`: Fallback ear tag color for all other breeds.

---

## 4. Suggestions for Future Improvements
1. **Bluetooth/RFID Integration**: Allow Bluetooth scale/RFID scanning on mobile phones to log weight and profiles instantly.
2. **Dashboard Customization Checklist**: Add an options editor in settings allowing each user to checklist exactly which widgets (finance, feeding, reports) should show on their homepage.
3. **Advanced Breeding Warning**: Highlight potential inbreeding risks by comparing Sire and Dam pedigree charts before establishing a colony.
4. **Offline Mode Support**: Support caching records in indexDB on mobile apps for offline use in remote pens, syncing back when network returns.
