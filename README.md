# eeiblog.com

Migration of [eeiblog.com](https://eeiblog.com) from Squarespace to WordPress.

## Contents

- `wordpress-theme/eeiblog-theme/` — custom WordPress theme matching the original EEi Blog design (dark header, list-style blog, hero banner, feature boxes).

## Installing the theme

1. Zip the theme directory: `cd wordpress-theme && zip -r eeiblog-theme.zip eeiblog-theme/`
2. In WordPress admin: **Appearance → Themes → Add New → Upload Theme**
3. Upload the zip, activate.
4. Configure menus (**Appearance → Menus**): create *Primary* and *Footer* menus.
5. Upload site logo via **Appearance → Customize → Site Identity**.
6. Set hero image via **Appearance → Customize → Header Image**.

## Theme structure

```
wordpress-theme/eeiblog-theme/
├── style.css          # theme metadata + all styles
├── functions.php      # setup, menus, widgets, helpers
├── header.php         # dark site header + primary nav (with dropdowns)
├── footer.php         # footer with logo, copyright, footer nav
├── front-page.php     # homepage: hero + feature boxes + recent posts
├── home.php           # blog listing (list layout)
├── single.php         # single post: title, featured image, content, tags, prev/next
├── archive.php        # category/tag/author/date archives
├── page.php           # static pages
├── 404.php
├── sidebar.php
├── searchform.php
├── index.php          # fallback template
├── screenshot.png
└── assets/js/navigation.js  # mobile menu + accessible dropdowns
```
