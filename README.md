# Tizim haqida (О системе Beruniy-Qabul)

Beruniy-Qabul — bu universitetlar uchun abituriyentlarni onlayn ro'yxatga olish, qabul jarayonini boshqarish va integratsiyalarni(Telegram Bot, amoCRM) ta'minlovchi YII2 (PHP) asosidagi avtomatlashtirilgan tizim. (Qabul komissiyalari ishlashiga mo'ljallangan YII2 Advanced platformasi).

## Texnologik stek (Технологический стек)
Tizim quyidagi texnologiyalar ustiga qurilgan va ishlaydi:

| Texnologiya / Технология | Versiya / Версия | Tavsif / Описание |
|--------------------------|-----------------|-------------------|
| **PHP** | 8.1 | Asosiy dasturlash tili (FPM backend). |
| **Yii2 Framework** | 2.0.45+ | Tizim arxitekturasi asosi (Advanced app template). |
| **MariaDB** | 10.5 | Relyatsion ma'lumotlar bazasi (Relational Database). |
| **Nginx** | 1.19 | Veb-server (Web Server). |
| **Docker & Compose** | 3.8 | Konteynerlashtirish va izolyatsiya (Containerization). |

---

# Tezkor o'rnatish (Быстрый старт)

Yangi universitet uchun loyihani o'rnatish tartibi quyidagicha:

```bash
# 1. Loyihani yuklab oling (Cloning the repository)
git clone https://github.com/your-repo/beruniy-qabul.git /var/www/university-name
cd /var/www/university-name

# 2. Nastroyka faylini nusxalash (Setup environment variables)
cp .env.example .env

# 3. .env faylini tahrirlang (Edit environment details)
nano .env

# 4. Konteynerlarni ishga tushirish (Start docker containers)
make up

# 5. Dasturiy ta'minot paketlarini o'rnatish (Install composer packages)
docker-compose exec php composer install

# 6. Baza migratsiyalarini bajarish (Run migrations)
make migrate
```

---

# Yangi universitet uchun sozlash - Checklist (Настройка нового университета)

Yangi serverda tizimni ko'tarayotganingizda quyidagi qadamlarni **barchasini** bajarish shart:

- [ ] **Domen ulanishi (Привязка домена):** DNS yozuvlari orqali domen IP manzilga yo'naltirilganligini tekshiring.
- [ ] **Muhit fayli (Файл .env):** Parollar, ma'lumotlar bazasi va Telegram/CRM tokenlari to'g'ri kiritilganligiga ishonch hosil qiling.
- [ ] **Docker:** `make up` qilinib, Nginx va PHP konteynerlarida xatolik yo'qligini tekshiring (`make logs`).
- [ ] **Composer:** Barcha kutubxonalar va tizim modullari muvaffaqiyatli yuklangan bo'lishi kerak.
- [ ] **Jadval yaratilishi (Миграции):** `make migrate` orqali bazada tablitsalar yaratilishi.
- [ ] **Cron job (Zaxira nushalari):** `./setup_backup.sh` fayli orqali Telegram bazaviy zaxiralar avtomatlashtirilganligimi ko'rib chiqing.
- [ ] **Queue Worker (Очереди):** O'z vaqtida botga xabarlar borayotgani uchun `make worker` komandasi Supervisor orqali ishlayotgan bo'lishi zarur.

---

# .env O'zgaruvchilari (Переменные окружения)

| Variabl nomi / Переменная | Tavsif / Описание | Misol (Пример) |
|---------------------------|-------------------|----------------|
| `PROJECT_NAME` | Konteynerlar ishlatadigan unikal prefiks | `beruniy_university1` |
| `NGINX_PORT` | Veb-server ulanish porti | `8000` |
| `DB_DATABASE` | Ma'lumotlar bazasi nomi | `university_db` |
| `DB_PASSWORD` | Bazaga ulanish paroli | `Kj9d#kas!` |
| `DB_ROOT_PASSWORD` | Bazaning asosiy Root paroli | `strong_root_pass` |
| `YII_ENV` | Loyiha holati (`prod` / `dev`) | `prod` |
| `COOKIE_VALIDATION_KEY` | Xavfsizlik kaliti (Random kiritish shart) | `7A8b9C_0xLpN` |
| `TELEGRAM_BOT_TOKEN` | BotFather tomonidan beriluvchi Token API | `1234:ABCDef` |
| `TELEGRAM_CHAT_ID` | Zaxiralar yuboriladigan guruh ID-si | `-1009876543` |
| `AMOCRM_SUBDOMAIN` | CRM Subdomeni manzili | `myuniversity` |

---

# Foydali buyruqlar (Доступные команды Makefile)

Tizimni boshqarish jarayonini tezlashtirish maqsadida quyidagi qisqa buyruqlarni ishlating:

* `make help` - Barcha mavjud buyruqlar va ma'lumotlarni ko'rsatish
* `make up` - Docker tizimini orqa fonda ishga tushirish (Запуск контейнеров)
* `make down` - Barcha konteynerlarni to'xtatish (Остановка)
* `make migrate` - Baza yaratish / o'zgarishlarni qo'llash (Применение миграций)
* `make migrate-create name=xyz` - Yangi arxitektura o'zgarishi yaratish fayli (Создание миграции)
* `make shell` - PHP muhitiga kirish (bash)
* `make logs` - Real vaqtida server error va loglarini o'qish (Просмотр логов)
* `make backup` - Xozirgi vaqtda ma'lumotlar bazasini manuel saqlash (Ручное создание бэкапа)
* `make worker` - Tizim queue-larini (navbat) ishga tushirish (Запуск воркера очередей)

---

# Ommabop xatoliklar (Частые проблемы и их решение)

| Muammo (Проблема) | Sabab (Причина) | Yechim (Решение) |
|-------------------|-----------------|------------------|
| **Konteynerlar ishga tushmayapti (`port is already allocated`)** | Siz tanlagan Nginx portini boshqa loyiha band qilgan | `.env` faylida `NGINX_PORT` ni bo'sh bo'lgan boshqa portga (masalan, `8001`) almashtiring va `make up` qiling. |
| **Maxsus SQL xato: `Access denied for user`** | Parol, user yoki bazalar nomlari mos tushmayapti | `.env` dagi DB sozlamalarini tekshiring, so'ng `docker-compose down -v` orqali esski nushalarni o'chirib, boshqatdan start bering. |
| **Vebsayt 500 xato bermoqda (`File not found / Error`)** | Framework vendor, ruxsatlar(permissions) yoki .env muammosi | `.env` ni tekshiring. PHP da `docker-compose exec php composer update` ni tering. |
| **`setup_backup.sh` fayli zaxira nusxa ololmayapti / xato** | Ruxsatnomalar, noto'g'ri telegram tokenlar yki ma'lumot yo'qligi | `chmod +x setup_backup.sh` ni urib koring. `/var/log/beruniy_backup.log` dagi loglarni ko'rib chiqing. |
| **Mail/Telegram botga xabarlar bormayapti** | Queue/Navbat ishchi jarayoni(Worker) ishga tushirilmagan | Serverda `make worker` ni terminalda yoki background Supervisor process yordamida ishlashiga ishonch holis qiling. |
