<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>International Mobility</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body>
    <header class="header">
        <div class="topbar">
                <a class="brand" href="index.php#home">
                    <img class="icon-logo" src="assets/logo.png" alt="University logo">

                    <h3 class="logo">International Mobility</h3>
                </a>
                <div>University | Research | Mobility</div>
                <div id="researcher-space">

    <?php if (isset($_SESSION["user_id"]) && !isset($_SESSION["admin_id"])): ?>

        <p>
            Welcome,
            <?php echo htmlspecialchars($_SESSION["user_name"]); ?>
        </p>

        <a href="submit_project.php">Submit a project</a>
        <a href="logout.php">Log out</a>

    <?php else: ?>

        <a href="inscription.php">Register</a>
        <a href="login.php">User Login</a>

    <?php endif; ?>

    <a href="admin/login.php">Admin Login</a>

</div>
        </div>
    </header>

    <nav class="navbar-section">
        <div class="navbar">
            <ul class="menu">
                <li><a href="#home">Home</a></li>

                <li class="sub-menu">
                    <a href="#opportunities">Calls <span class="arrow-icon">&#9662;</span></a>
                    <ul class="dropdown">
                        <li><a href="#Horizons">Horizons</a></li>
                        <li><a href="#Erasmus">Erasmus</a></li>
                        <li><a href="#Prima">Prima</a></li>
                    </ul>
                </li>

                <li class="sub-menu">
                    <a href="submit_project.php">Project<span class="arrow-icon">&#9662;</span></a>
                    <ul class="dropdown">
                        <li><a href="#horizon">Horizon</a></li>
                        <li><a href="#CBHE">CBHE</a></li>
                        <li><a href="#PNR">PNR</a></li>
                        <li><a href="#CNEPRU">CNEPRU</a></li>
                    </ul>
                </li>


                <li class="sub-menu">
                    <a href="#opportunities">Mobility<span class="arrow-icon">&#9662;</span></a>
                    <ul class="dropdown">
                        <li><a href="national">National</a></li>
                        <li><a href="#erasmus">Erasmus</a></li>
                        <li><a href="exchange">Exchange</a></li>
                    </ul>
                </li>

                <li><a href="#about">About</a></li>
                <li><a href="mailto:internationale_relations@gmail.com">Contact</a></li>
            </ul>
        </div>
    </nav>


    <main id="home"><section class="hero-section">

        <div class="hero-container">
            <div class="hero-caption">
                <h1 class="principal-title">International Cooperation Projects Tracking Portal</h1>
                <h2>Calls, projects and mobility for university researchers.</h2>
                    <p>Turn your ideas into impactful international collaborations. Submit project proposals, share your
                        drafts,
                        explore support opportunities, and track your progress every step of the way all in one place.
                    </p>
            </div>
            <div class="diapo-container">
                <div class="diapo">
                    <img src="assets/mobility-research-1.jpg" alt="International doctoral researchers collaborating in a university laboratory">
                    <img src="assets/mobility-research-2.jpg" alt="Erasmus academic exchange visit at a European university">
                    <img src="assets/mobility-research-3.jpg" alt="International students working together in a university research lab">
                    <img src="assets/mobility-research-4.jpg" alt="Researchers collaborating at Erasmus Medical Center">
                    <img src="assets/erasmus-exchange.jpg" alt="Incoming Erasmus exchange students at a university welcome ceremony">
                    <img src="assets/cern-students.jpg" alt="University students visiting CERN">
                    <img src="assets/summer-school.jpg" alt="International students attending a university summer school">
                    <img src="assets/mobility-research-1.jpg" alt="International doctoral researchers collaborating in a university laboratory">
                </div>
            </div>

        </div>
    <div class="hero-actions"><a class="button button-primary" href="submit_project.php">Submit a project idea</a><a class="text-link" href="#opportunities">Explore the process ↓</a></div>
    </section></main>

    <section class="videopresentationsection" id="opportunities">
        <div>
            <h2>Project Holder Journey</h2>
            <p>Discover the key steps in the process, from submitting your initial project idea to final submission.</p>
            <video src="assets/parcours-porteur-de-projet.mp4" controls preload="metadata" width="100%"></video>
        </div>
    </section>

    <section class="projectmembers">
        <div>
            <h3>Project Members</h3>

            <div class="add-member">
                <h4>Add a new member</h4>
                <form id="addMemberForm">
                    <div class="member-grid">
                        <div class="field">
                            <label for="memberInstitution">Institution<span class="req">*</span></label>
                            <input id="memberInstitution" name="institution" type="text" required placeholder="Ex : ESI">
                        </div>
                        <div class="field">
                            <label for="memberLab">Department/Laboratory<span class="req">*</span></label>
                            <input id="memberLab" name="lab" type="text" required placeholder="Ex : LMCS">
                        </div>
                        <div class="field">
                            <label for="memberName">Full Name<span class="req">*</span></label>
                            <input id="memberName" name="fullname" type="text" required placeholder="Ex : Meriem Tigoulmamine">
                        </div>
                        <div class="field">
                            <label for="memberProf">Profession<span class="req">*</span></label>
                            <input id="memberProf" name="profession" type="text" required placeholder="Ex : Professor">
                        </div>
                        <div class="field">
                            <label for="memberRole">Role<span class="req">*</span></label>
                            <input id="memberRole" name="role" type="text" required placeholder="Ex : Project Manager">
                        </div>
                    </div>
                    <div class="buttons">
                        <input type="submit" value="Add to table">
                    </div>
                    
                </form>
            </div>

            <table id="membersTable">
                <thead>
                    <tr>
                        <th scope="col">Institution</th>
                        <th scope="col">Department/Laboratory</th>
                        <th scope="col">Full Name</th>
                        <th scope="col">Profession</th>
                        <th scope="col">Role</th>
                     
                    </tr>
                </thead>

                <tbody id="membersBody">
                    <tr data-group="ESI">
                        <td rowspan="2" class="institution">ESI</td>
                        <td>LMCS</td>
                        <td>Full Name</td>
                        <td>Professor</td>
                        <td>Project Manager</td>
                        
                    </tr>
                    <tr data-group="ESI">
                        <td>LCSI</td>
                        <td>Full Name</td>
                        <td>Researcher</td>
                        <td>Coordinator</td>
                 
                    </tr>
                    <tr data-group="ESTIN">
                        <td class="institution">ESTIN</td>
                        <td>LITAN</td>
                        <td>Full Name</td>
                        <td>MC classe B</td>
                        <td>Developer</td>
                     
                    </tr>
                </tbody>
            </table>
        </div>
    </section>



    <section id="about">
        <div class="container">
            <h2>About Us</h2>

            <p>
                Our portal provides a centralized space for university researchers
                to explore international cooperation opportunities, develop project
                proposals, request guidance, and track their progress from initial
                idea to final submission.
            </p>
        </div>
    </section>

    <footer>
        <div class="container">
            <div>
                <h3>International Mobility</h3>
                <p>Directorate of International Relations</p>
            </div>
            <div class="contact">
                <span>Contact</span>
                <a href="mailto:internationale_relations@gmail.com">internationale_relations@gmail.com</a>
            </div>
            <div>
                <small>&copy; 2026 University International Mobility</small>
            </div>
        </div>


    </footer>

    <script src="script.js"></script>









</body>

</html>