# Netz

Netz is a lightweight, procedural network monitoring and management system designed for long-term reliability with minimal external dependencies.

Originally developed in 2005 and used in production for monitoring and managing ~6000 remote sites through 2023.

---

## Quick Start

git clone https://github.com/louiez/netz.git
cd netz
./install.sh

Then open:

http://your-server/netz/

Login:
admin / password


---

## Overview

Netz was built to solve real-world network monitoring problems without relying on heavy frameworks or external libraries.

It follows a simple philosophy:

* Keep dependencies minimal
* Keep logic transparent
* Keep systems stable over time

The system is designed to run on standard Linux systems using shell scripts, PHP, and a database backend.

---

## What It Does

* Monitor remote hosts (ping / availability)
* Collect and store network status
* Provide centralized visibility of site health
* Support large-scale deployments (thousands of nodes)
* Designed for long-term unattended operation

---

## Design Philosophy

Netz intentionally avoids:

* Complex frameworks
* Heavy external dependencies
* Rapidly changing ecosystems

Instead, it uses:

* Procedural code
* In-house libraries
* Simple, maintainable components

This approach prioritizes stability and longevity over trends.

---

## Architecture (High Level)

Netz follows a central-server model:

* A central server collects and stores monitoring data
* Remote systems are monitored via polling or scripts
* Data is stored in a database and optionally displayed via a web interface

Typical flow:

1. Server runs monitoring scripts
2. Scripts gather network data (ping, status, etc.)
3. Results are stored in the database
4. Web interface displays system status

---

## Requirements

Typical environment:

* Linux (Debian/Ubuntu or similar)
* Web server (Apache or Nginx)
* PHP
* MySQL / MariaDB
* Standard Unix tools (bash, ping, etc.)

No external frameworks or package ecosystems required.

---

## Installation (Basic Outline)

> NOTE: This project was originally deployed in controlled environments.
> Installation steps are being refined for general use.

Basic steps:

1. Clone the repository:

   ```bash
   git clone https://github.com/louiez/netz.git
   ```

2. Copy to your web directory:

   ```bash
   cp -r netz /var/www/html/
   ```

3. Set up the database:

   * Create database
   * Import schema (if provided)

4. Configure settings:

   * Edit configuration files (see config section below)

5. Ensure required services are running:

   * Web server
   * Database

6. Set up scheduled tasks (cron) if needed

---

## Configuration

Configuration is environment-specific and may include:

* Database connection settings
* Host lists or site definitions
* Script paths and system settings

Recommended approach:

* Copy any example config files (if provided)
* Adjust values for your environment

---

## Directory Structure (General)

* `web/` or root PHP files → web interface
* `scripts/` → monitoring / automation scripts
* `lib/` → shared functions / libraries
* `config/` → configuration files (may need cleanup/standardization)
* `sql/` → database schema (if included)

*(Structure may vary — this project evolved over time.)*

---

## Status

This is a production-proven system being cleaned up for public release.

Areas being improved:

* Installation process
* Documentation
* Configuration structure
* Code readability (without changing core design)

---

## Notes

* This project reflects real-world operational code, not a framework-based application.
* The coding style is intentionally procedural and dependency-light.
* Stability and long-term maintainability were prioritized over modern trends.

---

## License

(Add license here if/when you choose one)

---

## Author

Originally developed and maintained over many years for large-scale network monitoring deployments.

---
