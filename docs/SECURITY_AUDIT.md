# Security Audit Checklist: University Admission System

This document outlines the security measures implemented within the platform to ensure data privacy, integrity, and protection against common vulnerabilities.

## 1. Input Validation & Filtering (XSS & SQLi Protection)
- [x] **Yii2 Input Sanitization:** Global `\common\components\ContentFilter::cleanText` filter applied across major models (e.g., `Student::first_name`, `address`) to strip `<script>` tags and prevent Reflected/Stored XSS.
- [x] **ActiveRecord Enforcement:** All database writes strictly utilize Yii2 ActiveQuery/ActiveRecord bindings, naturally neutralizing SQL Injection (SQLi) attempts.
- [x] **Strict Model Rules:** Checksums implemented for PINFL sequences, custom ranges enforced for age algorithms (16-35) directly inside `Student::rules()`.

## 2. Authentication & Authorization (CSRF, JWT, RBAC)
- [x] **CSRF Protection:** Enabled by default on all web controllers via component settings and `<meta>` tags in layouts (`$this->registerCsrfMetaTags()`).
- [x] **JWT Stateless Security:** Native API endpoints utilize `Firebase/PHP-JWT` inside `BaseRest`, enforcing short-lived `Bearer` tokens without exposing session cookies to external mobile clients.
- [x] **Branch Isolation (RBAC/Scoping):** Queries strictly bound by `BranchActiveRecord` scopes. Branch Admin A cannot alter or even query Student data assigned to Branch B.

## 3. Server Configuration & File Security
- [x] **No Direct PHP Execution in Webroot:** Nginx settings block URL access to `.php` files inside `/uploads/`.
- [x] **File Upload Restrictions:** `Student::photo` rules strictly limit uploads to `[png, jpg, jpeg]`, forcing `image/jpeg, image/png` MIME checks, and limiting size to 5MB. Uploads are aggressively resized via GD to strip potential EXIF payloads.
- [x] **Permission Lockdown:** The `deploy/security_check.sh` script enforces `755/644` baseline permissions across the codebase, opening only `runtime/` and `web/assets/` to daemon access (`777` strictly bounded in Docker).

## 4. Environment Secrecy
- [x] **Credential Segregation:** Production credentials never inhabit tracked `.php` files; all DB connections dynamically pull from a `.git-ignored` `.env` loaded via `Dotenv`.

---
*Date of Audit Protocol Formulation: 2026-03-11*
