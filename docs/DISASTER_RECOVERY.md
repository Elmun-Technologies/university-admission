# Disaster Recovery Plan - Beruniy-Qabul

In the event of a system failure, follow these procedures.

## 1. Database Corruption / Loss

**Risk:** Database becomes unreadable or data is accidentally deleted.
**Strategy:** Restore from latest daily backup (stored in `/opt/beruniy/backups` and mirrored to Telegram).

### Steps to Restore:
1. Identify the latest backup file: `ls -t /opt/beruniy/backups/*.sql`
2. Run the recovery command:
   ```bash
   docker exec -i mariadb mysql -u beruniy_user -p'YOUR_PASSWORD' beruniy_db < backup.sql
   ```
3. Use the console tool:
   ```bash
   php yii recovery/restore-latest
   ```

## 2. Server Crash

**Risk:** The entire server is destroyed or unreachable.
**Strategy:** Provision new server, redeploy code, and restore data.

### Steps:
1. Provision a fresh Ubuntu server.
2. Run `curl ... | bash deploy/setup_server.sh`.
3. Clone the repository to `/opt/beruniy/`.
4. Run `onboard_university.sh` for each instance.
5. Download the latest database backup from the Telegram archive.
6. Restore the database using Step 1 above.
7. Update DNS records to point to the new IP.

## 3. Communication
- Notify University Administrators immediately of the downtime.
- Provide estimated RTO (Recovery Time Objective): **2 Hours**.
