# Database Migration Standards for University Admission System

To ensure zero-downtime updates, all database migrations must be **backward compatible**. The old version of the code must continue to work while the new migrations are being applied and before the code is swapped.

## 🚫 Forbidden Actions (Destructive)

- **Never drop columns.** Instead, mark the column as `deprecated` in its comment and ignore it in your code.
- **Never rename columns.** Use the "Add-Copy-Deprecate" pattern:
    1. Add the new column.
    2. Copy data from the old column to the new one.
    3. Update code to write to both (if necessary) or just the new one.
    4. Deprecate the old column.
- **Never make a column NOT NULL** without a default value if existing rows have NULLs.

## ✅ Required Actions

- **New columns must have DEFAULT values.** This ensures old code (which doesn't know about the new column) can still insert rows.
- **All indexes must be named explicitly.** Following the `idx-{table}-{column}` convention.
- **Rollback logic is mandatory.** Every `safeUp()` must have a corresponding `safeDown()`.
- **Test rollback.** Always run `yii migrate/down` and then `yii migrate/up` on your local machine before pushing.

## Naming Convention

Migrations should be named: `m{YYMMDD}_{6-digit-sequence}_{descriptive_name}`.

Examples:
- `m260101_000001_create_student_table`
- `m260101_000002_add_email_to_student`
- `m260101_000003_idx_student_last_name`
