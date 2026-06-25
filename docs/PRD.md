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
  * **Generation**: Automatically calculated based on parents (e.g. max(Sire, Dam) + 1). Offspring of F6 parents and above transition automatically to `PURE`.
* **Dam Updates**: Dam's physical status transitions automatically to *Menyusui* (Lactating) upon offspring creation.

### 2.3 Colony Mating & Breeding Cycles
* **Mating Colonies**: Group multiple breeding females (Dam) with one breeding male (Sire) in a colony pen.
* **Auto-Transitions & Alerts**:
  * **Mating Period / Separation**: Spans 60 days (configurable via settings). After 60 days, a warning alert triggers to separate the Sire ("Waktunya Pisah Pejantan!"). Active colony member statuses are updated via scheduled tasks or manual ending of the mating colony (e.g., returning dams to `SIAP` status).
  * **Pregnancy Check (Cek Kebuntingan)**: Prompted 60 days after mating start. The pregnancy status change to *Bunting* (Pregnant) is updated manually by the user upon confirmation (e.g., via ultrasound or physical checks).
  * **Weaning Alert & Auto-Transition (Sapih)**: 
    * At **35 days** post-birth, a notification alert is sent to users notifying them that the offspring is approaching weaning age ("Siap Sapih").
    * At **60 days** post-birth (configurable via `weaning_age_days` setting), the scheduled task (`animal:auto-status`) automatically transitions offspring from `Cempe` status to `Bakalan` (if male) or `Dara` (if female).
    * If the Dam's physical status was *Menyusui* (Lactating), she is automatically reverted back to `Dara` (Ready to Mate) when her offspring are weaned.
  * **Post-partum Recovery (Nifas)**: Indukan have a configured post-partum recovery period (defaults to 40 days, configurable via `nifas_period_days`). This is strictly enforced as an eligibility validation check in the system, preventing users from recording a mating event for the Dam before her recovery period has elapsed.


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

### 2.7 Site Content Management (CMS)
* **Structured Editing**: All landing page text content is editable by the Owner (`PEMILIK`) through an admin interface at `/admin/site-content`. The page layout, design structure, and visual components remain locked — only text and numeric values are modifiable.
* **Editable Sections**:
  * **Hero**: Badge text, headline, headline accent, subheadline.
  * **Stats Bar**: 4 pairs of statistic number + label (e.g. "500+" / "Peternak Terdaftar").
  * **Features Header**: Section title and subtitle.
  * **Feature Cards**: Title and description for each of the 6 feature cards. Icons and route links remain fixed in code as they map to actual application features.
  * **About Section**: Heading, paragraph, and 4 checklist items.
  * **Testimonials**: 3 testimonial slots, each with quote, author name, and role.
  * **CTA Section**: Headline and subheadline.
  * **Footer**: Tagline text.
* **Storage**: Content is stored as key-value pairs in the existing `farm_settings` table with group `SITE_CONTENT`. Complex sections (stats, features, testimonials) are stored as JSON blobs in the `value` column.
* **Fallback Behavior**: If no database value exists for a given key, the original hardcoded text is used as a default. This ensures the landing page always renders correctly even with an empty database.

### 2.8 Livestock Catalogue
* **Purpose**: A public-facing catalogue page at `/katalog` where potential buyers can browse available livestock. No payment processing — all inquiries are routed through WhatsApp.
* **Catalogue Flag**: Animals are listed in the catalogue by toggling `is_for_sale = true` on the animal record via the edit form. When enabled, `sale_price` (asking price in Rupiah) and `sale_description` (marketing text) become **mandatory** fields.
* **Public Data Exposure**: The catalogue only displays safe, non-sensitive fields:
  * ✅ Displayed: Photos, breed, category, gender, age, latest weight, asking price, description.
  * ❌ Hidden: Tag ID, HPP costs, partner/ownership info, purchase price, location, health records.
* **Filters**: Visitors can filter by breed and search by description text.
* **WhatsApp Inquiry**: Each catalogue card has a "Hubungi via WhatsApp" button that opens `wa.me/{global_number}` with a pre-filled inquiry message referencing the animal's breed.
* **Lifecycle Integration**: When an animal is sold via the Exit module (`JUAL`), it is marked `is_active = false`, which automatically removes it from the catalogue — no manual cleanup needed.
* **Asking Price vs. Sale Price**: The `sale_price` on the animal record is the listed *asking price* for display only. The actual transaction price is captured separately in `ExitLog.price` during the sale process and feeds into invoicing and profit calculations. These are intentionally separate fields to avoid data conflicts.

### 2.9 WhatsApp Integration
* **Global Number**: A single WhatsApp number is configured via the CMS admin page, stored as `whatsapp_number` in `farm_settings`.
* **Floating Button**: A fixed-position WhatsApp button appears on all guest-facing pages (bottom-right corner) with a pulse animation. Only rendered when a WhatsApp number is configured.
* **Contact Page Replacement**: The previous form-based contact page (`/hubungi-kami`) is replaced with a direct redirect to the WhatsApp number. Footer links are updated accordingly.

---

## 3. Dynamic Visual Configurations (Settings)
Rather than hardcoding colors and generation rules, the system handles visual parameters dynamically using database settings managed via the **Pengaturan Farm** tab in `/admin/masters`:

1. `available_necklace_colors`: Comma-separated list of colors available for necklace selectors.
2. `available_ear_tag_colors`: Comma-separated list of colors available for ear tags.
3. `eartag_map_dorper_f1` to `eartag_map_dorper_f6`: Mappings specifying colors for each generation.
4. `eartag_map_default`: Fallback ear tag color for all other breeds.

---

## 4. Extended Frontend Enhancements & Media CMS

### 4.1 Navigation Routing Refinement
* **Desktop & Mobile Navbar Redirect**: The navbar menu link "Tentang" is routed directly to the dedicated "Tentang Kami" page (`/tentang-kami`) instead of performing an anchor scroll to `#about` on the landing page.

### 4.2 Tentang Kami (About Us) Page CMS & Image Fallbacks
* **Content Source**: The dedicated "Tentang Kami" page is driven dynamically via the CMS database using the `site_about_us` configuration.
* **4-Image Aspect Grid**: The grid displaying team or aspect photos on `/tentang-kami` is configured through the CMS.
* **Fallback Asset Mapping**: For both the landing page's About section image and the four About Us page aspect grid images, the system defaults to the preconfigured `img/logo.png` logo asset if no dynamic file is uploaded. This ensures that empty CMS image fields never break layouts.

### 4.3 Hero Showcase (Tabbed Carousel Showcase)
* **Visual Presentation**: The landing page's static dashboard preview image is replaced with an interactive Tabbed Showcase component.
* **Media Formats**: Supports uploading high-quality screenshots and autoplaying, looping, muted WebM or MP4 video files representing actual software walk-throughs.
* **Interactive Controls**: Users can manually click through tabs (e.g., Dashboard, Breeding Tracker, ADG Analytics) to view features, or let them slide automatically via Alpine.js auto-rotation.

### 4.4 Article Management & Public Blog
* **Purpose**: A public blog system (`/artikel`) where users can read farm insights, livestock tips, and updates. Helps with SEO and community building.
* **Administration (CRUD)**:
  * **Role Restriction**: Only the Owner (`PEMILIK`) can create, edit, delete, or publish articles.
  * **Rich Text Editing**: Integrated with a clean Quill.js editor to allow bolding, list formats, and inline media insertion.
  * **Embedded Media**: Admins can embed photos or YouTube videos directly into the body content. Uploaded media is compressed and served via public storage.
  * **Draft & Publish states**: Articles have an `is_published` flag, allowing them to remain as drafts until ready.
* **Database Fields**: Title, slug (auto-generated from title for clean URLs like `/artikel/tips-ternak`), thumbnail image, body content (HTML), video URL, draft/publish boolean, and publication date.
* **Public Interface**:
  * **Blog Index**: A responsive card grid at `/artikel` showing thumbnails, titles, publication dates, and truncated summaries.
  * **Blog Detail Page**: A dedicated page at `/artikel/{slug}` to read the full HTML content and view embedded media.
  * **Landing Page Integration**: An "Artikel Terkini" (Latest Articles) section at the bottom of the landing page displaying the 3 most recently published articles.

---

## 5. Suggestions for Future Improvements
1. **Bluetooth/RFID Integration**: Allow Bluetooth scale/RFID scanning on mobile phones to log weight and profiles instantly.
2. **Dashboard Customization Checklist**: Add an options editor in settings allowing each user to checklist exactly which widgets (finance, feeding, reports) should show on their homepage.
3. **Advanced Breeding Warning**: Highlight potential inbreeding risks by comparing Sire and Dam pedigree charts before establishing a colony.
4. **Offline Mode Support**: Support caching records in indexDB on mobile apps for offline use in remote pens, syncing back when network returns.
