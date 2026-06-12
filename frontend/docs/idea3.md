# Software Requirements Specification (SRS)

## Role Expansion & System Analysis for Laravel Pharmacy Inventory System

### Project Context

Analyze the entire Laravel project structure, existing MVC flow, database schema, routing system, sidebar navigation, controllers, Blade views, middleware, and current business logic before making any changes.

This project is a web-based pharmacy inventory management system for Apotek Sena Farma that implements:

* FIFO (First In First Out) inventory management
* Batch-based stock tracking
* Expired-date monitoring
* Stock mutation between branches
* Stock opname
* Drug sales tracking
* Decision Tree stock prediction
* Multi-branch pharmacy management

The current system already contains:

* Dashboard
* Master Data
* Transaction Stock
* Monitoring Stock
* Prediction
* Reports
* Account Management

The system styling, layout structure, sidebar component, Blade components, route naming, controllers, and UI flow have already been developed. Maintain all existing UI themes, styling consistency, component structures, JavaScript flow, modal flow, and database relationships.

Do NOT redesign the UI from scratch.

---

# Main Objective

Analyze the existing system automatically and determine:

* What kind of system this project represents
* Existing workflow architecture
* Existing stock management flow
* Existing MVC structure
* Existing route structure
* Existing role limitations
* Existing batch and FIFO flow

Then extend the system by implementing:

* Pharmacist Role
* Manager Role

without breaking:

* Existing database flow
* Existing styling
* Existing routes
* Existing controller logic
* Existing Blade component structure

---

# Existing System Concept

The system uses:

* Central warehouse concept
* Multi-branch pharmacies
* Batch-based stock
* FIFO stock distribution
* FEFO expired prioritization implicitly inside FIFO logic
* Drug stock prediction using Decision Tree

Main business flow:

1. Drug stock enters central warehouse
2. Batch stock is created
3. Stock distributed to branches through stock mutation
4. Branches sell medicines to customers
5. Sales history becomes prediction dataset
6. Prediction generates next month stock quantity

---

# Required Role System

## 1. Administrator

Full access to:

* Master Data
* User Management
* Stock Mutation
* Drug Entry
* Stock Monitoring
* Prediction
* Reports
* Dashboard

Can:

* Manage branches
* Manage users
* Access all reports
* Manage all inventory

---

## 2. Pharmacist

Focus only on operational stock activities.

Accessible menus:

* Dashboard
* Drug Entry
* Drug Sales
* Stock Mutation
* Batch & Expired Monitoring
* Stock Opname

Restrictions:

* Cannot manage users
* Cannot access prediction settings
* Cannot modify master system settings
* Cannot delete critical historical data

---

## 3. Manager

Focus on monitoring and analytics.

Accessible menus:

* Dashboard
* Reports
* Prediction
* Stock Monitoring
* Expired Monitoring

Can:

* View analytics
* View prediction results
* View stock movement
* Export reports

Restrictions:

* Cannot modify stock
* Cannot create mutations
* Cannot input transactions

---

# Required System Analysis

Before implementing role management:

1. Analyze all existing controllers
2. Analyze all routes
3. Analyze sidebar navigation structure
4. Analyze middleware usage
5. Analyze authentication flow
6. Analyze database relationships
7. Analyze current stock lifecycle
8. Analyze FIFO implementation
9. Analyze batch management logic
10. Analyze prediction workflow

Generate a simple system architecture summary before modification.

---

# Required Database Analysis

Analyze:

* tb_obat
* tb_stok_batch
* tb_obat_masuk
* tb_obat_keluar
* tb_mutasi
* tb_stok_opname
* users
* all related foreign keys

Determine:

* Current stock lifecycle
* Batch flow
* Sales flow
* Mutation flow
* Prediction dataset source

---

# Required Role Implementation

Implement:

* Role middleware
* Role-based route protection
* Role-based sidebar rendering
* Role-based controller access
* Role-based dashboard content

Suggested roles:

* admin
* apoteker
* manager

Do not hardcode role checks repeatedly inside controllers.
Use middleware and helper abstraction when possible.

---

# Sidebar Requirement

Maintain current sidebar styling and structure.

Only adjust:

* visibility based on role
* permission-based rendering
* active menu synchronization

Do not redesign components.

---

# Prediction System Requirement

Analyze existing prediction structure and extend it.

Prediction objective:

* Predict next month stock quantity
* Use sales history as dataset
* Use stock opname as validation reference
* Use Decision Tree algorithm
* Support 5000+ records efficiently

Prediction flow:

1. Collect sales history
2. Aggregate monthly sales
3. Generate training CSV
4. Train Decision Tree
5. Save model
6. Flask API serves prediction
7. Laravel consumes prediction API
8. Prediction displayed in dashboard and reports

---

# EDA Requirement

Create a clearer EDA workflow:

1. Data cleaning
2. Missing value analysis
3. Duplicate checking
4. Monthly sales trends
5. Top selling medicines
6. Outlier analysis
7. Feature correlation
8. Feature importance
9. Decision Tree visualization
10. Model evaluation

Use:

* matplotlib
* seaborn
* plotly if needed

EDA should be generated step-by-step and stored properly.

---

# Architecture Requirement

Refactor carefully:

* Keep MVC stable
* Avoid breaking routes
* Avoid breaking Blade components
* Avoid changing current styling
* Avoid modifying unrelated logic

If refactor is needed:

* Prefer Service Layer
* Prefer Repository Pattern if useful
* Keep controllers cleaner

---

# Expected Output

Generate:

1. System architecture summary
2. Current flow analysis
3. Role implementation strategy
4. Middleware strategy
5. Updated route structure
6. Sidebar role rendering strategy
7. Prediction architecture
8. Suggested folder structure
9. Service layer recommendation
10. Database relationship explanation

Store analysis documentation safely without overwriting existing important files.
