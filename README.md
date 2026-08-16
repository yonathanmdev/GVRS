# GVRS - Government Vehicle Registration System

GVRS is a PHP MVC application for registering and managing government-owned
vehicles across the Amhara regional government's organizational structure.
It supports registration under regional bureaus, their accountable
institutions, internal departments, and woredas - reflecting both the
administrative and functional hierarchies used across government offices.

## Overview

- **Dual hierarchy support** - offices are organized along two parallel
  structures stored in a shared `branches` table:
  - **Administrative hierarchy** (`admin_parent_id`, `admin_path`) - zones,
    woredas, city administrations
  - **Functional hierarchy** (`functional_parent_id`, `functional_path`) -
    bureaus, their accountable institutions, and internal departments
- **UUID public identifiers** - every branch and record exposes a UUID
  externally, while internal joins use BIGINT primary keys for performance
- **Category-driven vehicle registration** - a vehicle's owning office is
  resolved through a cascading selection flow depending on category
  (regional bureau, institution under a bureau, department + zone, or
  woreda under a zone)

## Tech Stack

- PHP (Vanilla MVC, no framework)
- MariaDB
- AdminLTE 3 (Bootstrap 4) for the UI
- Vanilla JavaScript (no build step required for the frontend)

## Requirements

- PHP 8.2+
- MariaDB / MySQL
- Composer
- A web server (Apache/Nginx) with `mod_rewrite` enabled

## Installation

```bash
git clone git@github-yonathanmdev:yonathanmdev/GVRS.git
cd GVRS

composer install

cp .env.example .env
# edit .env with your database credentials and base URL
```

## Database Setup

Import the schema (adjust path to wherever your migration/schema files live):

```bash
mysql -u your_user -p your_database < database/schema.sql
```

## Configuration

Key values expected in `.env`:

```
DB_HOST=localhost
DB_NAME=gvrs
DB_USER=
DB_PASS=

BASE_URL=http://localhost/GVRS
```

## Project Structure

```
GVRS/
├── public/           # Web root - entry point, assets, public .htaccess
├── src/
│   ├── Controllers/
│   ├── Models/
│   └── Helpers/
├── views/             # PHP view templates
├── storage/           # Logs, cache, generated files (git-ignored)
├── vendor/            # Composer dependencies (git-ignored)
└── .env               # Environment config (git-ignored)
```

## Development Workflow

This repo is mirrored across two remotes - see project notes for the exact
`git remote` setup if you're pushing to both `yonathanmdev/GVRS` and the
collaborator repo.

```bash
git pull origin main
# ... make changes ...
git add .
git commit -m "Describe the change"
git push origin main
```

## License

Internal government client project - not currently licensed for public
redistribution.