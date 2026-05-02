# চ্যানেল বিডিএন

A full-stack Bangla news portal built with PHP & MySQL. Features a complete admin panel, reader accounts with bookmarks/reactions/comments, live weather forecasts, newsletter subscriptions, breaking news ticker, category management, and dark mode. Optimized for Bengali typography and mobile responsiveness.

## Features

- **Admin Panel** — posts, categories, users, ads, polls, homepage layout, settings
- **Reader Accounts** — register/login, bookmarks, emoji reactions, comments, reading history
- **Newsletter** — subscription management with category preferences
- **Comment Moderation** — approve/reject reader comments from admin
- **Weather Page** — live data for all 8 Bangladesh divisions via Open-Meteo (ECMWF model)
- **Breaking News Ticker** — live scrolling ticker with admin control
- **Dark Mode** — site-wide and admin panel dark mode with localStorage persistence
- **Bangabda & Hijri Dates** — Bengali calendar and Islamic date in the date bar
- **SEO Ready** — Open Graph, Twitter Card, JSON-LD structured data, AMP support
- **Responsive** — mobile-friendly layout with Bengali typography (Hind Siliguri + Tiro Bangla)

## Tech Stack

- **Backend:** PHP 8+, MySQL
- **Frontend:** Vanilla JS, CSS3
- **Server:** Apache (XAMPP)
- **Weather API:** Open-Meteo (no API key required)

## Installation

1. Clone the repo into your XAMPP `htdocs` folder:
   ```bash
   git clone https://github.com/yourusername/channelbdn.git
   ```

2. Import the database and run the migration:
   ```
   http://localhost/channelbdn/reader_migrate.php
   ```

3. Copy and configure your database credentials:
   ```bash
   cp config.example.php config.php
   ```
   Edit `config.php` with your MySQL credentials.

4. Visit `http://localhost/channelbdn/`

## Admin Panel

Access at `http://localhost/channelbdn/admin/`

Default credentials are set during installation.

## License

MIT
