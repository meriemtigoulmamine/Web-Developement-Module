<?php
require_once __DIR__ . "/../database/Database.php";

class AuthModel
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function get_user_by_email($email)
    {
        $stmt = mysqli_prepare($this->db, "SELECT id, fullname, password FROM users WHERE email = ?");
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $user ?: null;
    }

    public function email_exists($email)
    {
        return $this->get_user_by_email($email) !== null;
    }

    public function create_user($fullname, $email, $password_hash)
    {
        $stmt = mysqli_prepare($this->db, "INSERT INTO users (fullname, email, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $fullname, $email, $password_hash);
        $ok = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        return $ok;
    }

    public function get_admin_by_username($username)
    {
        $stmt = mysqli_prepare($this->db, "SELECT id, username, password FROM admins WHERE username = ?");
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $admin = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);
        return $admin ?: null;
    }
}