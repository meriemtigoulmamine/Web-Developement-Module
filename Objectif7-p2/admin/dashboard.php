<?php
session_start();
require_once "../db_connection.php";

if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$showArchived = isset($_GET["archived"]);
$sql = "SELECT p.id, p.title, p.description, p.program, p.calls, p.status,
               p.submitted_at, p.remarks, u.fullname, u.email
        FROM projects p
        INNER JOIN users u ON p.user_id = u.id
        WHERE p.archived = ?
        ORDER BY p.submitted_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $showArchived);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$statuses = ["Submitted" => "Soumis", "Reviewed" => "Révisé", "Rejected" => "Rejeté", "Accepted" => "Accepté"];
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des soumissions</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; padding: 28px; font-family: Arial, sans-serif; color: #172b3a; background: #f4f7f9; }
        .page { max-width: 1500px; margin: 0 auto; }
        header { display: flex; justify-content: space-between; align-items: center; gap: 16px; flex-wrap: wrap; }
        h1, h2 { color: #082c47; }
        h1 { margin: 0; }
        .welcome, .hint { color: #576775; }
        nav { display: flex; gap: 10px; flex-wrap: wrap; margin: 22px 0; }
        nav a { padding: 10px 14px; border-radius: 5px; color: #075985; background: #e6eef3; text-decoration: none; }
        nav a:hover { background: #d8e6ee; }
        nav a.primary { color: white; background: #082c47; }
        h2 { margin-bottom: 6px; }
        .hint { margin-top: 0; }
        .notice { margin: 16px 0; padding: 12px; border-radius: 5px; background: #e9f7ee; color: #205a35; }
        .table-wrap { overflow-x: auto; background: white; border: 1px solid #ccd5dc; border-radius: 6px; }
        table { width: 100%; min-width: 1050px; border-collapse: collapse; font-size: 14px; }
        th, td { padding: 11px; border-bottom: 1px solid #dbe2e7; text-align: left; vertical-align: top; }
        th { color: white; background: #082c47; white-space: nowrap; }
        tbody tr:hover { background: #f7fafc; }
        .project-title { font-weight: bold; color: #082c47; }
        .secondary { margin-top: 4px; color: #647482; font-size: 12px; }
        select, textarea, button { font: inherit; }
        select, textarea { width: 100%; padding: 7px; border: 1px solid #bac7d0; border-radius: 4px; }
        textarea { min-width: 160px; min-height: 62px; resize: vertical; }
        .actions { min-width: 142px; }
        .actions form { margin: 0 0 6px; }
        button { padding: 7px 10px; border: 0; border-radius: 4px; color: white; background: #075985; cursor: pointer; }
        button:hover { background: #06466a; }
        .actions .secondary-action { color: #17384e; background: #e6eef3; }
        .actions .delete { background: #a33a3a; }
        .empty { padding: 24px; text-align: center; color: #576775; }
        @media (max-width: 700px) { body { padding: 16px; } }
    </style>
</head>
<body>
<div class="page">
    <header>
        <div>
            <h1>Gestion des soumissions</h1>
            <div class="welcome">Bienvenue, <?= h($_SESSION["admin_username"] ?? "Administrateur") ?>.</div>
        </div>
    </header>

    <nav aria-label="Navigation administrateur">
        <a class="<?= !$showArchived ? "primary" : "" ?>" href="dashboard.php">Soumissions actives</a>
        <a class="<?= $showArchived ? "primary" : "" ?>" href="dashboard.php?archived=1">Archives</a>
        <a href="users.php">Gérer les utilisateurs</a>
        <a href="logout.php">Déconnexion</a>
    </nav>

    <h2><?= $showArchived ? "Projets archivés" : "Projets soumis" ?></h2>
    <p class="hint">Liste triée par date de soumission, du plus récent au plus ancien.</p>
    <?php if (isset($_GET["saved"])): ?>
        <p class="notice">Modifications enregistrées.</p>
    <?php endif; ?>

    <div class="table-wrap">
        <table>
            <thead>
                <tr><th>Date</th><th>Utilisateur</th><th>Projet</th><th>Description</th><th>Programme / appel</th><th>Statut</th><th>Remarques</th><th>Actions</th></tr>
            </thead>
            <tbody>
            <?php if ($result && mysqli_num_rows($result) > 0): ?>
                <?php while ($project = mysqli_fetch_assoc($result)): ?>
                    <?php $formId = "project-" . (int)$project["id"]; ?>
                    <tr>
                        <td><?= h($project["submitted_at"]) ?></td>
                        <td><?= h($project["fullname"]) ?><div class="secondary"><?= h($project["email"]) ?></div></td>
                        <td><div class="project-title"><?= h($project["title"]) ?></div><div class="secondary">#<?= (int)$project["id"] ?></div><a href="edit_project.php?id=<?= (int)$project["id"] ?>">Modifier le projet</a></td>
                        <td><?= nl2br(h($project["description"])) ?></td>
                        <td><?= h($project["program"]) ?><div class="secondary"><?= h($project["calls"]) ?></div></td>
                        <td><select form="<?= h($formId) ?>" name="status" aria-label="Statut du projet">
                            <?php foreach ($statuses as $value => $label): ?>
                                <option value="<?= h($value) ?>" <?= $project["status"] === $value ? "selected" : "" ?>><?= h($label) ?></option>
                            <?php endforeach; ?>
                        </select></td>
                        <td><textarea form="<?= h($formId) ?>" name="remarks" maxlength="5000" aria-label="Remarques"><?= h($project["remarks"]) ?></textarea></td>
                        <td class="actions">
                            <form id="<?= h($formId) ?>" method="post" action="save_project.php"><input type="hidden" name="id" value="<?= (int)$project["id"] ?>"><button name="action" value="save">Enregistrer</button></form>
                            <form method="post" action="save_project.php"><input type="hidden" name="id" value="<?= (int)$project["id"] ?>"><button class="secondary-action" name="action" value="<?= $showArchived ? "unarchive" : "archive" ?>"><?= $showArchived ? "Désarchiver" : "Archiver" ?></button></form>
                            <form method="post" action="save_project.php" onsubmit="return confirm('Supprimer définitivement cette soumission ?')"><input type="hidden" name="id" value="<?= (int)$project["id"] ?>"><button class="delete" name="action" value="delete">Supprimer</button></form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td class="empty" colspan="8">Aucun projet dans cette liste.</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>