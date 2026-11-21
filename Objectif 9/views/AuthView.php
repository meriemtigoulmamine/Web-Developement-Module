<?php
class AuthView
{
    private $base;

    public function __construct($base = "")
    {
        $this->base = $base;
    }

    private function h($value)
    {
        return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
    }

    private function page_start($title)
    {
        ?>
<!DOCTYPE html><html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title><?= $this->h($title) ?></title><link rel="stylesheet" href="<?= $this->base ?>styleauth.css"></head><body>
<?php
    }

    private function page_end()
    {
        ?>
</body></html>
<?php
    }

    public function login_page($message = "")
    {
        $this->page_start("Researcher Login");
        ?>
<h1>Researcher Login</h1><p><?= $this->h($message) ?></p>
<form method="post"><label for="email">ESI email:</label><input type="email" id="email" name="email" required><br><br><label for="password">Password:</label><input type="password" id="password" name="password" required><br><br><button type="submit" name="login">Log in</button></form>
<p>Not registered yet? <a href="inscription.php">Register</a></p><p><a href="admin/login.php">Administrator login</a></p>
<?php
        $this->page_end();
    }

    public function register_page($message = "")
    {
        $this->page_start("ESI Registration - International Mobility");
        ?>
<h1>Create a researcher account</h1><p><?= $this->h($message) ?></p>
<form method="post"><label for="fullname">Full name:</label><input type="text" id="fullname" name="fullname" maxlength="150" required><br><br><label for="email">ESI email:</label><input type="email" id="email" name="email" required><br><br><label for="password">Password (8 characters minimum):</label><input type="password" id="password" name="password" minlength="8" required><br><br><label for="confirm_password">Confirm password:</label><input type="password" id="confirm_password" name="confirm_password" minlength="8" required><br><br><button type="submit" name="register">Register</button></form>
<p>Already have an account? <a href="login.php">Log in</a></p>
<?php
        $this->page_end();
    }

    public function admin_login_page($message = "")
    {
        $this->page_start("Administrator Login");
        ?>
<h1>Administrator Login</h1><p><?= $this->h($message) ?></p>
<form method="post"><label for="username">Username:</label><input type="text" id="username" name="username" required autocomplete="username"><br><br><label for="password">Password:</label><input type="password" id="password" name="password" required autocomplete="current-password"><br><br><button type="submit" name="login">Log in</button></form>
<p><a href="../login.php">Researcher login</a></p>
<?php
    }
}