<div align="center">
  <img src="https://upload.wikimedia.org/wikipedia/commons/6/69/Logo_Baru_Pelindo_%282021%29.png" alt="Pelindo Logo" width="300" style="margin-bottom: 20px" />

# Pelindo Infrastructure Reporting System

**PT Pelabuhan Indonesia (Persero) Regional 2 Teluk Bayur**
**Divisi Teknik & Infrastruktur**

Modernized application for reporting, tracking, and managing Pelindo's infrastructure assets, ensuring data consistency and streamlined operational workflows.

  <br />

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)

</div>

<br />

## System Features & Capabilities

- **Standardized User Interface**
    - Integrated Pelindo corporate color palette for brand consistency across all modules.
    - Responsive and interactive UI components utilizing Alpine.js for modals, dynamic dropdowns, and system alerts.
    - Uniform error handling and success messaging to ensure clarity for operational users.

- **Responsive & Optimized Layout**
    - Engineered for cross-device compatibility, ensuring full accessibility via mobile, tablet, and desktop environments.
    - Structured navigation and dashboard architecture with strict z-index management to prevent element overlapping during data presentation.

- **Dynamic Asset Management**
    - Category-specific dynamic indicators for rapid identification of physical infrastructure and technical assets.
    - Comprehensive data retrieval via detailed view modals, allowing administrative users to inspect asset conditions without disrupting the active workflow.

- **Operational Efficiency**
    - Optimized CRUD operations configured for high-speed data entry and bulk data management.
    - Automated operational workflows for generating, tracking, and archiving internal infrastructure reports.

---

## Technical Stack

- **Backend Architecture:** [Laravel](https://laravel.com/) (PHP)
- **Frontend Framework:** [Tailwind CSS](https://tailwindcss.com/)
- **Client-side Scripting:** [Alpine.js](https://alpinejs.dev/)
- **Asset Bundler:** [Vite](https://vitejs.dev/)
- **Database Management:** MySQL

---

## Deployment & Installation Guide

The following instructions are intended for the internal development and engineering team to establish a local environment for maintenance and feature testing.

### System Prerequisites

Ensure the following dependencies are installed on the local or staging server:

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL Server

### Initialization Steps

1. **Repository Cloning:**
   Clone the secure repository to your local directory.

    ```bash
    git clone <internal-repository-url>
    cd DIVISI-TEKNIK-INFRASTRUCTURE
    ```

2. **Backend Dependency Installation:**
   Install required PHP packages via Composer.

    ```bash
    composer install
    ```

3. **Frontend Dependency Installation:**
   Install necessary Node modules.

    ```bash
    npm install
    ```

4. **Environment Configuration:**
   Duplicate the example environment file and assign the appropriate database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

    ```bash
    cp .env.example .env
    ```

   Generate the application encryption key:

    ```bash
    php artisan key:generate
    ```

5. **Database Migration & Seeding:**
   Execute migrations to build the schema and populate initial reference data.

    ```bash
    php artisan migrate --seed
    ```

6. **Server Execution:**
   Initialize the dual-server setup for full-stack development.

   *Terminal 1 (Backend Service):*

    ```bash
    php artisan serve
    ```

   *Terminal 2 (Asset Compilation):*

    ```bash
    npm run dev
    ```

The application interface will be available at `http://localhost:8000`.

---

## System Previews

> *Internal Note: Replace placeholders with official system screenshots before finalizing documentation.*

| Dashboard Overview | Asset Details Interface |
| :---: | :---: |
| ![Dashboard](https://via.placeholder.com/600x350/E2E8F0/1E293B?text=Dashboard+Overview+Preview) | ![Modal](https://via.placeholder.com/600x350/E2E8F0/1E293B?text=Asset+Detailed+View+Modal) |

---

## Development Standards & Guidelines

- **Styling Protocol:** Utilize Tailwind CSS utility classes strictly. Custom styling should be minimal and centralized within `resources/css/app.css`.
- **Interactivity:** Implement Alpine.js directly within Blade templates to manage state and simple DOM manipulation.
- **Component Modularity:** Reusable interface elements (buttons, forms, modals) must be extracted and maintained as Blade components within the `resources/views/components/` directory.

---

<div align="center">
  <p><strong>Internal Document - PT Pelabuhan Indonesia (Persero) Regional 2 Teluk Bayur</strong></p>
  <p>Maintained by Divisi Teknik & Infrastruktur</p>
</div>
