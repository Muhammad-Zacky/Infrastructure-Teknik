<div align="center">
  <!-- Replace with actual logo if needed -->
  <img src="https://upload.wikimedia.org/wikipedia/commons/6/69/Logo_Baru_Pelindo_%282021%29.png" alt="Pelindo Logo" width="300" style="margin-bottom: 20px" />

# 🏗️ Pelindo Infrastructure Reporting System

**Divisi Teknik & Infrastructure**

Modernized application for reporting, tracking, and managing Pelindo's infrastructure assets with a professional, responsive, and data-consistent user experience.

  <br />

  <!-- Badges -->

![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)
![TailwindCSS](https://img.shields.io/badge/Tailwind_CSS-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)
![Alpine.js](https://img.shields.io/badge/Alpine.js-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-00000F?style=for-the-badge&logo=mysql&logoColor=white)

</div>

<br />

## ✨ Features & Highlights

- 🎨 **Standardized UI/UX Design**
    - Integrated _Pelindo Blue_ color palette for brand consistency.
    - Interactive and smooth UI components using **Alpine.js** (modals, dropdowns, alerts).
    - Uniform error and success messaging across the entire application.

- 📱 **Fully Responsive Layout**
    - Optimized for cross-device usability (Mobile, Tablet, Desktop).
    - Clean navigation and dashboard cards with properly managed layout and z-index components to prevent overlaps.

- 🗂️ **Dynamic Asset Management**
    - Category-specific dynamic icons for easy and quick identification of infrastructure assets.
    - Comprehensive detailed view modals for fast asset inspection without leaving the active page.

- ⚙️ **Streamlined Operations**
    - Optimized administrative CRUD operations for faster data entry and data management.
    - Improved operational workflows for generating, tracking, and managing infrastructure reports.

---

## 🛠️ Tech Stack

- **Backend Framework:** [Laravel](https://laravel.com/) (PHP)
- **Frontend Styling:** [Tailwind CSS](https://tailwindcss.com/)
- **Frontend Interactivity:** [Alpine.js](https://alpinejs.dev/)
- **Build Tool:** [Vite](https://vitejs.dev/)
- **Database:** MySQL

---

## 🚀 Getting Started

Follow these instructions to set up the project locally for development and testing.

### Prerequisites

Make sure you have the following installed on your local machine:

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL Server

### Installation Guide

1. **Clone the repository** (if you haven't already):

    ```bash
    git clone <your-repository-url>
    cd DIVISI-TEKNIK-INFRASTRUCTURE
    ```

2. **Install PHP Dependencies:**

    ```bash
    composer install
    ```

3. **Install NPM Dependencies:**

    ```bash
    npm install
    ```

4. **Environment Setup:**
   Copy the example environment file and configure your database credentials (`DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`).

    ```bash
    cp .env.example .env
    ```

    Generate the Laravel application key:

    ```bash
    php artisan key:generate
    ```

5. **Run Database Migrations (and Seeders):**

    ```bash
    php artisan migrate --seed
    ```

6. **Start the Development Servers:**
   You will need two terminal windows to run both the backend and frontend dev servers.

    _Terminal 1 (Laravel Server):_

    ```bash
    php artisan serve
    ```

    _Terminal 2 (Vite Assets Server):_

    ```bash
    npm run dev
    ```

Your application should now be accessible at `http://localhost:8000`.

---

## 📸 System Previews

> _Placeholders for actual system screenshots. Add them to an `assets` folder and update the links below._

|                                         Dashboard View                                          |                                    Asset Details Modal                                     |
| :---------------------------------------------------------------------------------------------: | :----------------------------------------------------------------------------------------: |
| ![Dashboard](https://via.placeholder.com/600x350/E2E8F0/1E293B?text=Dashboard+Overview+Preview) | ![Modal](https://via.placeholder.com/600x350/E2E8F0/1E293B?text=Asset+Detailed+View+Modal) |

---

## 👨‍💻 Development Guidelines

- **Styling**: Stick to utility classes provided by Tailwind CSS. Custom CSS should be minimal and placed in `resources/css/app.css`.
- **Scripts**: Use Alpine.js directly in Blade templates for simple interactions.
- **Components**: Reusable UI elements (like buttons, modals) should be extracted into Blade components in `resources/views/components/`.

---

<div align="center">
  <p>Built with ❤️ for Pelindo Divisi Teknik & Infrastructure</p>
</div>
