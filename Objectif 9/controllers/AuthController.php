<?php
require_once __DIR__ . "/../models/AuthModel.php";
require_once __DIR__ . "/../views/AuthView.php";

class AuthController
{
    private $model;
    private $view;

    public function __construct($base = "")
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $this->model = new AuthModel();
        $this->view  = new AuthView($base);
    }

    /* ---------- Researchers ---------- */

    public function login()
    {
        $message = isset($_GET["registered"]) ? "Registration successful. You can now log in." : "";

        if (isset($_POST["login"])) {
            $email    = strtolower(trim($_POST["email"] ?? ""));
            $password = $_POST["password"] ?? "";

            if (!$this->is_esi_email($email)) {
                $message = "Use a valid ESI email address ending in @esi.dz.";
            } else {
                $user = $this->model->get_user_by_email($email);
                if ($user && password_verify($password, $user["password"])) {
                    session_regenerate_id(true);
                    unset($_SESSION["admin_id"], $_SESSION["admin_username"]);
                    $_SESSION["user_id"]   = (int)$user["id"];
                    $_SESSION["user_name"] = $user["fullname"];
                    $this->redirect("index.php#home");
                }
                $message = "Incorrect ESI email or password.";
            }
        }
        $this->view->login_page($message);
    }

    public function register()
    {
        $message = "";

        if (isset($_POST["register"])) {
            $fullname = trim($_POST["fullname"] ?? "");
            $email    = strtolower(trim($_POST["email"] ?? ""));
            $password = $_POST["password"] ?? "";
            $confirm  = $_POST["confirm_password"] ?? "";

            if ($fullname === "" || !$this->is_esi_email($email)) {
                $message = "Enter your name and a valid email address ending in @esi.dz.";
            } elseif (strlen($password) < 8) {
                $message = "Password must contain at least 8 characters.";
            } elseif ($password !== $confirm) {
                $message = "Passwords do not match.";
            } elseif ($this->model->email_exists($email)) {
                $message = "This email is already registered.";
            } else {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                if ($this->model->create_user($fullname, $email, $hash)) {
                    $this->redirect("login.php?registered=1");
                }
                $message = "Registration failed.";
            }
        }
        $this->view->register_page($message);
    }

    public function logout()
    {
        $this->destroy_session();
        $this->redirect("index.php");
    }

    /* ---------- Administrator ---------- */

    public function admin_login()
    {
        $message = isset($_GET["created"]) ? "Administrator created. Please log in." : "";

        if (isset($_POST["login"])) {
            $username = trim($_POST["username"] ?? "");
            $password = $_POST["password"] ?? "";

            $admin = $this->model->get_admin_by_username($username);
            if ($admin && password_verify($password, $admin["password"])) {
                session_regenerate_id(true);
                unset($_SESSION["user_id"], $_SESSION["user_name"]);
                $_SESSION["admin_id"]       = (int)$admin["id"];
                $_SESSION["admin_username"] = $admin["username"];
                $this->redirect("dashboard.php");
            }
            $message = "Incorrect username or password.";
        }
        $this->view->admin_login_page($message);
    }

    public function admin_logout()
    {
        $this->destroy_session();
        $this->redirect("login.php");
    }

    /* ---------- Private helper methods ---------- */

    private function is_esi_email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && preg_match('/@esi\.dz$/i', $email);
    }

    private function destroy_session()
    {
        $_SESSION = [];
        session_destroy();
    }

    private function redirect($url)
    {
        header("Location: " . $url);
        exit;
    }
}