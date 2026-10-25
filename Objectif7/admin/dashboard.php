<?php
session_start();
require_once "../db_connection.php";

// Only administrators can access this page.
if (!isset($_SESSION["admin_id"])) {
    header("Location: login.php");
    exit;
}

$sql = "SELECT
            projects.id,
            projects.title,
            projects.description,
            projects.program,
            projects.calls,
            projects.status,
            projects.submitted_at,
            users.fullname,
            users.email
        FROM projects
        INNER JOIN users ON projects.user_id = users.id
        ORDER BY projects.submitted_at DESC";

$result = mysqli_query($conn, $sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 25px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #082c47;
            color: white;
        }

        textarea {
            width: 150px;
        }
    </style>
</head>
<body>

    <h1>Administrator Dashboard</h1>

    <p>
        Welcome,
        <?php echo htmlspecialchars($_SESSION["admin_username"]); ?>!
    </p>

    <h2>Project Support Requests</h2>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Researcher</th>
                <th>Email</th>
                <th>Project</th>
                <th>Description</th>
                <th>Program</th>
                <th>Call</th>
                <th>Date</th>
                <th>Status</th>
                <th>Update</th>
            </tr>
        </thead>

        <tbody>

        <?php if ($result && mysqli_num_rows($result) > 0): ?>

            <?php while ($row = mysqli_fetch_assoc($result)): ?>

                <tr>
                    <td>
                        <?php echo (int) $row["id"]; ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["fullname"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["email"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["title"]); ?>
                    </td>

                    <td>
                        <?php echo nl2br(htmlspecialchars($row["description"])); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["program"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["calls"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["submitted_at"]); ?>
                    </td>

                    <td>
                        <?php echo htmlspecialchars($row["status"]); ?>
                    </td>

                    <td>
                        <form method="post" action="update_status.php">

                            <input
                                type="hidden"
                                name="project_id"
                                value="<?php echo (int) $row["id"]; ?>"
                            >

                            <select name="status" required>
                                <option value="Pending"
                                    <?php if ($row["status"] == "Pending") echo "selected"; ?>>
                                    Pending
                                </option>

                                <option value="In Progress"
                                    <?php if ($row["status"] == "In Progress") echo "selected"; ?>>
                                    In Progress
                                </option>

                                <option value="Accepted"
                                    <?php if ($row["status"] == "Accepted") echo "selected"; ?>>
                                    Accepted
                                </option>

                                <option value="Rejected"
                                    <?php if ($row["status"] == "Rejected") echo "selected"; ?>>
                                    Rejected
                                </option>
                            </select>

                            <button type="submit" name="update">
                                Update
                            </button>

                        </form>
                    </td>
                </tr>

            <?php endwhile; ?>

        <?php else: ?>

            <tr>
                <td colspan="10">No project requests have been submitted yet.</td>
            </tr>

        <?php endif; ?>

        </tbody>
    </table>

    <br>

    <a href="logout.php">Log out</a>

</body>
</html>