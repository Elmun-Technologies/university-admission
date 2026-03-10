# Model Reference & State Machine

## Primary Models

### Student
The core entity in the system.
- **Purpose**: Tracks applicant profile, identity (PINFL/Passport), and admission progress.
- **Scoping**: Extends `BranchActiveRecord` — data is strictly isolated by `branch_id`.
- **Key Relationships**: `Direction`, `Branch`, `StudentExam`, `StudentOferta`.

### Exam & ExamDate
Manages university entrance tests.
- **Exam**: Defines duration and associated subjects.
- **ExamDate**: Holds specific time slots and maximum participant limits.

### StudentExam
The intersection of an applicant and an exam session.
- **Status**: `REGISTERED` -> `IN_PROGRESS` -> `FINISHED` -> `EXPIRED`.

## Student Status State Machine

Applicants move through a predefined set of states:

```ascii
[NEW] -----------------> [ANKETA] -----------------> [EXAM]
  |                         |                         |
  | (reject)                | (reject)                | (fail)
  V                         V                         V
[REJECTED] <------------ [REJECTED] <------------ [REJECTED]

[EXAM] --- (pass) ---> [CONTRACT] --- (paid) ---> [PAID]
                                                   |
                                                   |
                                                   V
                                               [ENROLLED]
```

## Validation Rules
- **PINFL**: Must be exactly 14 numeric digits. Checksum validated.
- **Phone**: Expected in `+998XXXXXXXXX` format.
- **Branch Isolation**: Unauthorized access to models from other branches via query strings is blocked at the base query level.
