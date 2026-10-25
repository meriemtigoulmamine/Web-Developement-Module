# Objectif7 database update

The updated site connects to the MySQL database `TDW`.

For a fresh setup, import `database.sql`, then create the first administrator with `create_admin.php`.

For an existing TDW database, take a backup and run `database_upgrade.sql` once before deploying the updated PHP files. This migrates the old `Pending` and `In Progress` statuses to `Submitted` and `Reviewed`, and adds administrator remarks and archive fields.

Administrator functions are available after login at `admin/dashboard.php`:
- submissions are sorted newest first by submission date;
- project fields can be edited, status and remarks updated, and records archived, restored, or permanently deleted;
- registered researcher names and emails can be edited, and accounts deleted.

Deleting a researcher account also deletes their submissions because the existing projects table uses `ON DELETE CASCADE`.
