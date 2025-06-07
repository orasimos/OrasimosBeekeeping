# Orasimos Beekeeping E‑Shop Demo

> **Assignment 2 – Practical Web‑based e‑Commerce**  
> Junior developer demo written in **PHP 8.3**, **MySQL 8** and **Bootstrap 5**.

![Project Banner](assets/img/logo-192x192.png)

---

## ✨ Features

| Area | Highlights |
|------|------------|
| **Authentication** | Secure registration & login with `password_hash()` / `password_verify()` and session handling |
| **Product Catalogue** | Dynamic listing of products and categories stored in MySQL |
| **Shopping Cart** | Add / update / remove items, server‑side total calculation |
| **Responsive UI** | Built with Bootstrap 5 – works on desktop, tablet, mobile |
| **Prepared Statements** | All SQL uses `mysqli` prepared queries → SQL‑Injection safe |
| **OOP Architecture** | Namespaces, autoload with Composer, single DB connection |

--
## 🗄️ Database Setup

1. **Create the database** – run the SQL script `db/OrasimosBeekeeping.sql` (e.g. via MySql Workbench or through phpMyAdmin).
2. **Configure the connection** – open **`appsettings.json`** and adjust the `DbConnection` section (host, port, dbName, user, password) so it matches your environment.
3. **Database data** - feel free to insert your own products and producttypes by using the included scripts (Products.sql and ProductTypes.sql)
