<?php
session_start();
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}
$showArchived = isset($_GET["archived"]) && $_GET["archived"] === "1";
function h($value) { return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8"); }
?>
<!doctype html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestion des soumissions</title>
    <style>
        body{font-family:Arial,sans-serif;margin:24px;color:#172b3a;background:#f5f7f9}
        h1{color:#082c47;margin-bottom:6px}nav{display:flex;gap:16px;flex-wrap:wrap;margin:18px 0}
        a{color:#075985}nav a{padding:9px 12px;border-radius:4px;background:#e8f0f5;text-decoration:none}
        nav a.active{background:#082c47;color:white}.table-wrap{overflow-x:auto;background:white}
        table{border-collapse:collapse;width:100%;min-width:1000px;font-size:14px}
        th,td{border:1px solid #ccd5dc;padding:10px;text-align:left;vertical-align:top}
        th{background:#082c47;color:white;white-space:nowrap}
        tbody tr:hover{background:#f6fafc}.project-title{font-weight:bold;color:#082c47}
        .muted{color:#647482;font-size:12px;margin-top:4px}
        textarea{width:150px;min-height:55px;box-sizing:border-box}
        select,button{padding:7px;margin:2px}button{cursor:pointer;border:0;border-radius:4px;background:#075985;color:white}
        button.secondary{background:#e6eef3;color:#17384e}button.delete{background:#a33a3a}
        .actions form{margin:0 0 5px}.status-line{color:#647482;font-size:13px}.notice{padding:9px;background:#e9f7ee}
        .empty{text-align:center;padding:25px}.description{white-space:pre-wrap;min-width:180px}
        @media(max-width:700px){body{margin:14px}}
    </style>
</head>
<body data-archived="<?= $showArchived ? "1" : "0" ?>">
    <h1>Gestion des soumissions</h1>
    <p>Bienvenue, <?= h($_SESSION["admin_username"] ?? "Administrateur") ?>.</p>
    <nav aria-label="Navigation administrateur">
        <a class="<?= !$showArchived ? "active" : "" ?>" href="dashboard.php">Soumissions actives</a>
        <a class="<?= $showArchived ? "active" : "" ?>" href="dashboard.php?archived=1">Archives</a>
        <a href="users.php">Gérer les utilisateurs</a>
        <a href="logout.php">Déconnexion</a>
    </nav>
    <h2><?= $showArchived ? "Projets archivés" : "Projets soumis" ?></h2>
    <p class="status-line"><span id="tableStatus" aria-live="polite">Chargement des soumissions…</span></p>
    <div class="table-wrap">
        <table>
            <thead><tr><th>Date</th><th>Utilisateur</th><th>Projet</th><th>Description</th><th>Programme / appel</th><th>Statut</th><th>Remarques</th><th>Actions</th></tr></thead>
            <tbody id="projectsBody"><tr><td colspan="8" class="empty">Chargement…</td></tr></tbody>
        </table>
    </div>
    <script src="dashboard.js" defer></script>
</body>
</html>
