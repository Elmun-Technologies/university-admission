# Beruniy Qabul - GO LIVE Checklist

This canonical checklist MUST be executed linearly by the DevOps engineer and verified by an Administrator prior to directing public DNS traffic (`qabul.beruniy.uz`) to the production instance.

## 1. Environment & Infrastructure Lockdown
- [ ] **Hardware Specs:** VM has a minimum of 4 vCPUs, 8GB RAM, and 50GB NVMe SSD.
- [ ] **Firewall Ports:** UFW is active. ONLY Ports `80` (HTTP), `443` (HTTPS), and `22` (Custom SSH Port) are open to the world. Database port `3306` is internally bound.
- [ ] **Production Keys:** Randomize `cookieValidationKey` in both `/common/config/main-local.php` files. Never reuse local/dev keys.
- [ ] **.env Audit:** Set `YII_DEBUG=false` and `YII_ENV=prod`. Hardcode strong, generated passwords for `DB_PASSWORD` and `DB_ROOT_PASSWORD`.
- [ ] **Permissions:** Run `bash deploy/security_check.sh` to enforce `755`/`644` strict boundaries.

## 2. Database Sterilization
- [ ] **Backup Init:** Execute an automated backup schema dump BEFORE clearing the system.
- [ ] **Clear Dummy Data:** TRUNCATE the `student`, `student_exam`, `help`, and `student_notification_pref` tables.
- [ ] **Admin Account:** Ensure the Super Admin account (e.g., `admin / admin123`) password is changed to a complex string immediately.
- [ ] **Seed Dictionaries:** Ensure `direction`, `language`, and `edu_form` tables contain official, verified 2026 admission quota data.

## 3. Communication & Integration Endpoints
- [ ] **SMTP Mailer:** Verify `mailer` transport settings in `common/config/main-local.php`. Send a test email to ensure delivery and SPF/DKIM compliance.
- [ ] **Telegram Webhook:** Register the production Telegram Bot webhook URL (`https://qabul.beruniy.uz/telegram/webhook`). Verify bot token.
- [ ] **SMS Gateway:** Activate production API keys for Eskiz/Playmobile if utilized.

## 4. Performance & Caching
- [ ] **Schema Cache:** Confirm `enableSchemaCache => true` in `db.php` configuration. Run `php yii cache/flush-schema`.
- [ ] **OPcache:** Verify Zend OPcache is active in the PHP container (`php -v` or `phpinfo()`).
- [ ] **Queue Worker:** Ensure the Docker `worker` container is actively listening (e.g., `php yii queue/listen`).

## 5. SSL & Nginx Routing
- [ ] **Certbot SSL:** Provision Let's Encrypt certificates. Nginx MUST terminate SSL on port `443`.
- [ ] **Domain Binding:** Update Nginx `server_name` directives from `localhost` to `qabul.beruniy.uz` and `admin.qabul.beruniy.uz`.

## 6. The Final Verification (Smoke Test)
- [ ] Access the frontend as an unauthenticated user. Verify UI loads in < 1 second.
- [ ] Register a new test applicant account. Verify OTP/Mail arrives.
- [ ] Access the backend Admin panel. Delete the test applicant.
- [ ] **GO LIVE!**

---
*Authorized by: Elmun Technologies Engineering 2026*
