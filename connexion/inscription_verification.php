<<<<<<< HEAD
    <?php
    session_start();
    date_default_timezone_set('Europe/Paris');
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $mot_de_passe = trim($_POST['mot_de_passe']);
        $pseudo = trim($_POST['pseudo']);
        $ville = trim($_POST['ville']);
        $rue = trim($_POST['rue']);
        $region = trim($_POST['region']);
        $code_postal = trim($_POST['code_postal']);
        $date = date('Y-m-d H:i:s');

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (!isset($_POST['captcha_answer']) || !isset($_SESSION['captcha_answer'])) {
                header("Location: inscription.php?error=captcha_missing");
                exit();
            }
            $user_answer = trim(strtolower($_POST['captcha_answer']));
            $correct_answer = trim(strtolower($_SESSION['captcha_answer']));
            if ($user_answer !== $correct_answer) {
                header("Location: inscription.php?error=captcha_invalid");
                exit();
            }
            unset($_SESSION['captcha_answer']);
        }
        

        if (strlen($mot_de_passe) < 8) {
            header("Location: inscription.php?error=password_length&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }
        if (!preg_match('/[\W_]/', $mot_de_passe)) {
            header("Location: inscription.php?error=password_special_char&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }
        if (!preg_match('/\d/', $mot_de_passe)) {
            header("Location: inscription.php?error=password_number&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }

        if (!ctype_digit($code_postal)) {
            header("Location: inscription.php?error=invalid_cp&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) .  "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: inscription.php?error=invalid_email");
            exit();
        }
        if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($pseudo) || empty($ville) || empty($rue) || empty($code_postal)) {
            header("Location: inscription.php?error=empty_fields");
            exit();
        }

        include('../include/database.php');
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $email_count = $stmt->fetchColumn();

        if ($email_count > 0) {
            header("Location: inscription.php?error=email_exists");
            exit();
        }
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        try {
            $stmt = $bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, pseudo, date_inscription, ville, rue, code_postal, status_ENUM) 
                                    VALUES (:nom, :prenom, :email, :mot_de_passe, :pseudo, :date_inscription, :ville, :rue, :code_postal, :status_enum)");
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':mot_de_passe', $mot_de_passe_hache, PDO::PARAM_STR);
            $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $stmt->bindParam(':date_inscription', $date, PDO::PARAM_STR);
            $stmt->bindParam(':ville', $ville, PDO::PARAM_STR);
            $stmt->bindParam(':rue', $rue, PDO::PARAM_STR);
            $stmt->bindParam(':code_postal', $code_postal, PDO::PARAM_STR);
            $status_enum = 'Client'; 
            $stmt->bindParam(':status_enum', $status_enum, PDO::PARAM_STR);

            $stmt->execute();
            session_start();
            $_SESSION['user_id'] = $bdd->lastInsertId();
            $_SESSION['user_email'] = $email;
            $_SESSION['user_pseudo'] = $pseudo;
            header("Location: ../index.php?success=1&pseudo=" . urlencode($pseudo));
            exit();
        } catch (PDOException $e) {
            header("Location: inscription.php?error=insert_failed");
            exit();
        }
    }
=======
    <?php
    date_default_timezone_set('Europe/Paris');
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
        $mot_de_passe = trim($_POST['mot_de_passe']);
        $pseudo = trim($_POST['pseudo']);
        $ville = trim($_POST['ville']);
        $rue = trim($_POST['rue']);
        $region = trim($_POST['region']);
        $code_postal = trim($_POST['code_postal']);
        $date = date('Y-m-d H:i:s');


        if (strlen($mot_de_passe) < 8) {
            header("Location: inscription.php?error=password_length&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }
        if (!preg_match('/[\W_]/', $mot_de_passe)) {
            header("Location: inscription.php?error=password_special_char&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }
        if (!preg_match('/\d/', $mot_de_passe)) {
            header("Location: inscription.php?error=password_number&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) . "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }

        if (!ctype_digit($code_postal)) {
            header("Location: inscription.php?error=invalid_cp&nom=" . urlencode($nom) . "&prenom=" . urlencode($prenom) . "&email=" . urlencode($email) .  "&pseudo=" . urlencode($pseudo) . "&ville=" . urlencode($ville) . "&rue=" . urlencode($rue) . "&code_postal=" . urlencode($code_postal) . "&region=" . urlencode($region));
            exit();
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            header("Location: inscription.php?error=invalid_email");
            exit();
        }
        if (empty($nom) || empty($prenom) || empty($email) || empty($mot_de_passe) || empty($pseudo) || empty($ville) || empty($rue) || empty($code_postal)) {
            header("Location: inscription.php?error=empty_fields");
            exit();
        }

        include('../include/database.php');
        $stmt = $bdd->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $email_count = $stmt->fetchColumn();

        if ($email_count > 0) {
            header("Location: inscription.php?error=email_exists");
            exit();
        }
        $mot_de_passe_hache = password_hash($mot_de_passe, PASSWORD_DEFAULT);
        try {
            $stmt = $bdd->prepare("INSERT INTO utilisateurs (nom, prenom, email, mot_de_passe, pseudo, date_inscription, ville, rue, code_postal) 
                                    VALUES (:nom, :prenom, :email, :mot_de_passe, :pseudo, :date_inscription, :ville, :rue, :code_postal)");
            $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
            $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':mot_de_passe', $mot_de_passe_hache, PDO::PARAM_STR);
            $stmt->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
            $stmt->bindParam(':date_inscription', $date, PDO::PARAM_STR);
            $stmt->bindParam(':ville', $ville, PDO::PARAM_STR);
            $stmt->bindParam(':rue', $rue, PDO::PARAM_STR);
            $stmt->bindParam(':code_postal', $code_postal, PDO::PARAM_STR);

            $stmt->execute();
            session_start();
            $_SESSION['user_id'] = $bdd->lastInsertId();
            $_SESSION['user_email'] = $email;
            $_SESSION['user_pseudo'] = $pseudo;
            header("Location: ../index.php?success=1&pseudo=" . urlencode($pseudo));
            exit();
        } catch (PDOException $e) {
            header("Location: inscription.php?error=insert_failed");
            exit();
        }
    }
>>>>>>> 084327abe52abe59871cc635c307ef2b601c5a28
