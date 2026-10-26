<?php session_start(); ?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit a project | International Mobility</title>
    <link rel="stylesheet" href="style.css">
    <style>.nav-cta { background:#e67e22; border-radius:4px; } .menu > li > a.nav-cta:hover { background:#c75d0b; }</style>
</head>
<body>
<header class="header"><div class="topbar">
    <a class="brand" href="index.php#home"><img class="icon-logo" src="assets/logo.png" alt="University logo"><h3 class="logo">International Mobility</h3></a>
    <div>University | Research | Mobility</div><div id="researcher-space">
    <?php if(isset($_SESSION["user_id"]) && !isset($_SESSION["admin_id"])): ?><p>Welcome, <?=htmlspecialchars($_SESSION["user_name"] ?? "")?></p><a href="logout.php">Log out</a><?php else: ?><a href="inscription.php">Register</a> <a href="login.php">User Login</a><?php endif; ?>
    </div>
</div></header>
<nav class="navbar-section"><div class="navbar"><ul class="menu">
    <li><a href="index.php#home">Home</a></li><li><a class="nav-cta" href="project_form.php">Submit a project</a></li>
    <li><a href="index.php#about">About</a></li><li><a href="index.php#contact">Contact</a></li>
</ul></div></nav>
<main>
<?php if (isset($_SESSION["user_id"]) && !isset($_SESSION["admin_id"])): ?>
    <section class="form-section" id="project-form">
        <div class="form-container">
            <h1>Project Suggestion</h1><?php if (isset($_GET["submitted"])): ?><p class="success">Your request was submitted successfully.</p><?php elseif (isset($_GET["error"])): ?><p class="error">There was a problem saving your request. Please check the required fields and try again.</p><?php endif; ?>
            <form method="post" action="soumetre_projet.php">
                <div>
                    <fieldset class="project-info">
                        <legend>Info about the project</legend>
                        <div>
                            <div class="field">
                                <label for="title">Project Title<span class="req">*</span></label>
                                <input id="title" name="title" required type="text"
                                    placeholder="Ex :Intelligence artificielle et systÃ¨mes intelligents pour lâ€™aide Ã  la dÃ©cision">
                            </div>
                            <div class="field">
                                <label for="duration">Duration (months )</label>
                                <input id="duration" name="duration" type="number" min="1" placeholder="Ex: 12">
                            </div>
                            <div class="field">
                                <label for="description">Description<span class="req">*</span></label>
                                <textarea id="description" name="description" required rows="4"
                                    placeholder="Objectifs, context,expected results..."></textarea>
                            </div>
                            <div class="field">
                                <label for="budget">Estimated Budget (DA) </label>
                                <input id="budget" name="budget" min="0" type="text" placeholder="Ex : 1500000 ">
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="programs">
                        <legend>Programs and Calls</legend>
                        <div>
                            <div class="field">
                                <label for="program">Program<span class="req">*</span></label>
                                <select id="program" name="program" required>
                                    <option value="">-- Select a program --</option>
                                    <option>Horizon</option>
                                    <option>CBHE</option>
                                    <option>PNR</option>
                                    <option>CNEPRU</option>
                                    <option>Erasmus</option>
                                    <option>Prima</option>
                                </select>
                            </div>
                            <div class="field">
                                <label for="call">Calls<span class="req">*</span></label>
                                <select id="call" name="calls" required>
                                    <option value="">-- Select Call --</option>
                                    <option>Horizons</option>
                                    <option>Erasmus</option>
                                    <option>Prima</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                    <fieldset class="partners">
                        <legend>Partners</legend>
                        <div>
                            <div class="field">
                                <label for="institution">Institution</label>
                                <input id="institution" name="institution" type="text" placeholder="Ex : LMCS">
                            </div>
                            <div class="field">
                                <label for="country">Country</label>
                                <input id="country" name="country" type="text" placeholder="Ex : France">
                            </div>
                            <div class="field">
                                <label for="contact">Contact (e-mail)</label>
                                <input id="contact" name="contact" type="email" placeholder="partner@gmail.com">
                            </div>
                        </div>
                    </fieldset>
                    <fieldset>
                        <legend>Support Needs</legend>
                        <div>
                            <label><input type="checkbox" name="needs[]" value="writing"> Help with writing
                                proposal</label>
                            <label><input type="checkbox" name="needs[]" value="budget"> Budget preparation</label>
                            <label><input type="checkbox" name="needs[]" value="legal"> Legal support</label>
                            <label><input type="checkbox" name="needs[]" value="partners"> Partner search</label>
                            <label for="other">Additional details</label>
                            <textarea id="other" name="other" rows="3"></textarea>
                        </div>
                    </fieldset>
                    <div class="buttons">
                        <input type="submit" value="Submit request" name="submit_project">
                    </div>


                </div>
            </form>

        </div>

    </section>

<?php else: ?>
    <section class="form-section" id="project-form"><div class="form-container"><h1>Project Suggestion</h1><?php if (isset($_GET["submitted"])): ?><p class="success">Your request was submitted successfully.</p><?php elseif (isset($_GET["error"])): ?><p class="error">There was a problem saving your request. Please check the required fields and try again.</p><?php endif; ?><p>Please register or log in with your ESI email address to access the project form.</p><a class="button" href="inscription.php">Register</a> <a class="button" href="login.php">User login</a></div></section>
<?php endif; ?>
</main>
</body>
</html>