<nav class="navbar <?php if ($_COOKIE["theme"] === "darkly") { echo "bg-dark"; } ?> navbar-expand-lg bg-body-tertiary" <?php if ($_COOKIE["theme"] === "darkly") { echo 'data-bs-theme="dark"'; }  ?> >
    <?php
    // End Point Lista De Usuarios Registrados
    $endPointListOfLoggedInUsers = [
        "/home.php",
        "/credentials.php",
        "/shared_directory_forms.php",
        "/inbox.php",
        "/shared_directory.php",
        "/shared_file.php",
        "/shared_file_forms.php",
        "/manage_shared_data.php"
    ];

    // End Point Lista De Usuarios No Registrados
    $endPointListOfUnregisteredUsers = ["/index.php" ,"/register.php", "/login.php"];
    ?>
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
        <img src="./static/img/LogoApp.png" alt="Logo Aplication">
            Local Drive
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item">
                <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfUnregisteredUsers)): ?>
                        <a class="nav-link active" aria-current="page" href="./register.php">Register</a>
                    <?php elseif (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                        <a class="nav-link" href="./home.php">Home</a>
                    <?php endif ?>
                </li>
                <li class="nav-item">
                    <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                        <a class="nav-link" href="./credentials.php">Update Credentials</a>
                    <?php endif ?>
                </li>
                
                <li class="nav-item">
                    <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                        <a class="nav-link" href="./shared_directory.php">Shared Directory</a>
                    <?php endif ?>
                </li>
                <li>
                    <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                        <a class="nav-link" href="./shared_file.php">Shared File</a>
                    <?php endif ?>
                </li>
                <li>
                    <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                        <a class="nav-link" href="./manage_shared_data.php">Manage Share Data</a>
                    <?php endif ?>
                </li>
                <li class="nav-item">
                    <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfUnregisteredUsers)): ?>
                        <a class="nav-link" href="./login.php">Login</a>
                    <?php elseif (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                        <a class="nav-link" href="./logout.php">Logout</a>
                    <?php endif ?>
                </li>
                <li>
                    <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                        <a class="nav-link" href="./delete.php?id=<?= $_SESSION['user']['id'] ?>">Delete Count</a>
                    <?php endif ?>
                </li>
                <li>
                    <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                        <a class="nav-link position-relative" href="./inbox.php?id=<?= $_SESSION['user']['id'] ?>">
                            Inbox
                            <?php 
                            $id_user = $_SESSION['user']['id'];
                            $unreadMessages = count($connection->query("SELECT * FROM notification WHERE id_user_destinatario = $id_user AND NOT visto")->fetchAll(PDO::FETCH_ASSOC));
                            if ($unreadMessages > 0):
                            ?>
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                    <?= $unreadMessages ?>
                                    <span class="visually">unread messages</span>
                                </span>
                            <?php endif ?>
                        </button>
                        </a>
                    <?php endif ?>
                </li>
            </ul>
            <?php if (in_array($_SERVER["PHP_SELF"], $endPointListOfLoggedInUsers)): ?>
                <div class="p-2">
                    <label><?= $_SESSION["user"]["email"] ?></label>
                </div>
            <?php endif ?>
            <form class="d-flex gap-2" action="<?=$_SERVER['PHP_SELF']?>" method="post">
                <button type="submit"
                    name="theme"
                    value="flatly"
                    class="btn btn-success">
                    Light
                </button>
                <button type="submit" 
                        name="theme" 
                        value="darkly" 
                        class="btn btn-secondary">
                        Dark
                </button>
            </form>
        </div>
    </div>
</nav>