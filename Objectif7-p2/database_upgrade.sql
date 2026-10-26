-- Migration à exécuter une seule fois sur une base TDW déjà créée.
USE TDW;
ALTER TABLE projects ADD COLUMN remarks TEXT NULL;
ALTER TABLE projects ADD COLUMN archived TINYINT(1) NOT NULL DEFAULT 0;
-- Accepter les deux ensembles de valeurs le temps de convertir les anciens statuts.
ALTER TABLE projects MODIFY status ENUM('Pending','In Progress','Accepted','Rejected','Submitted','Reviewed') NOT NULL DEFAULT 'Submitted';
UPDATE projects SET status=CASE status WHEN 'Pending' THEN 'Submitted' WHEN 'In Progress' THEN 'Reviewed' ELSE status END;
ALTER TABLE projects MODIFY status ENUM('Submitted','Reviewed','Rejected','Accepted') NOT NULL DEFAULT 'Submitted';
CREATE INDEX idx_projects_date ON projects(submitted_at);
CREATE INDEX idx_projects_archived ON projects(archived);