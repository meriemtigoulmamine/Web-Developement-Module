/* Run once against an existing installation before deploying the updated pages. */
USE TDW;
ALTER TABLE projects MODIFY status ENUM('Pending', 'In Progress', 'Accepted', 'Rejected', 'Submitted', 'Reviewed') NOT NULL DEFAULT 'Pending';
UPDATE projects SET status = 'Submitted' WHERE status = 'Pending';
UPDATE projects SET status = 'Reviewed' WHERE status = 'In Progress';
ALTER TABLE projects MODIFY status ENUM('Submitted', 'Reviewed', 'Rejected', 'Accepted') NOT NULL DEFAULT 'Submitted';
ALTER TABLE projects ADD COLUMN admin_notes TEXT NULL AFTER status;
ALTER TABLE projects ADD COLUMN archived_at DATETIME NULL AFTER admin_notes;
