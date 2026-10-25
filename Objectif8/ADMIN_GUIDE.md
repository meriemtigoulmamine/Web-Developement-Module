# Objectif7 database update

The updated site connects to the MySQL database `TDW`.

For a fresh setup, import `database.sql`, then create the first administrator with `create_admin.php`.

When an admin opens the updated dashboard, it checks the TDW schema and adds missing `admin_notes` and `archived_at` columns, then migrates old `Pending` and `In Progress` statuses to `Submitted` and `Reviewed`. If you prefer a manual migration, back up TDW and run `database_upgrade.sql` before opening the updated dashboard; do not run the manual migration after the automatic migration has already completed.

Administrator functions are available after login at `admin/dashboard.php`:
- submissions are sorted newest first by submission date;
- project fields can be edited, status and remarks updated, and records archived, restored, or permanently deleted;
- registered researcher names and emails can be edited, and accounts deleted.

Deleting a researcher account also deletes their submissions because the existing projects table uses `ON DELETE CASCADE`.
