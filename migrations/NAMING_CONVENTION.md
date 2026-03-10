# Migration Naming Convention

Every migration file must follow this exact format:

`m{YYMMDD}_{6_DIGIT_SEQUENCE}_{DESCRIPTIVE_NAME}.php`

### Parts:
- `YYMMDD`: Date of creation (e.g., 260311 for March 11, 2026)
- `6_DIGIT_SEQUENCE`: Incremental counter for that specific day (starts with 000001)
- `DESCRIPTIVE_NAME`: Snake-case description of the change.

### Examples:
- `m260311_000001_create_audit_table.php`
- `m260311_000002_add_index_to_user_phone.php`

### Why?
Predictable ordering prevents merge conflicts and ensures logical execution flow across multiple developers.
