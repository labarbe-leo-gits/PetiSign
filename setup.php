<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup PétiSign</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        form {
            background: #f5f5f5;
            padding: 20px;
            border-radius: 5px;
            margin-top: 20px;
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
        }
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
        }
        .success {
            background-color: #dff0d8;
            border: 1px solid #d6e9c6;
            color: #3c763d;
        }
        .error {
            background-color: #f2dede;
            border: 1px solid #ebccd1;
            color: #a94442;
        }
    </style>
</head>
<body>
    
    <?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    $setup_password = "3hIe1TuYoGUymZ8iePmwjVBniyGgYq8vmH0NM9i3PtAWKEULUS";
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['password']) || $_POST['password'] !== $setup_password) {
            echo '<div class="message error">Mot de passe incorrect. Veuillez réessayer.</div>';
        } else {
            try {
                include_once 'Sources/database/database.php';

                if (!isset($pdo)) {
                    throw new Exception("Database connection failed");
                }

                $mail = "a@a.a";
                $username = "root";
                $password = "root";
                $is_admin = 1;

                $check = $pdo->prepare("SELECT COUNT(*) FROM USER WHERE email = :mail");
                $check->bindParam(':mail', $mail);
                $check->execute();
                
                if ($check->fetchColumn() > 0) {
                    echo '<div class="message error">User already exists!</div>';
                } else {
                    
                    $stmt = $pdo->prepare("INSERT INTO USER (email, username, password, is_admin) VALUES (:mail, :username, :password, :is_admin)");
                    $stmt->bindParam(':mail', $mail);
                    $stmt->bindParam(':username', $username);
                    $stmt->bindParam(':password', password_hash($password, PASSWORD_DEFAULT));
                    $stmt->bindParam(':is_admin', $is_admin);
                    $stmt->execute();

                    echo "Root user created successfully!<br>";

                    $default_captcha_question = "Quelle est l'âge de la majorité en France ?";
                    $default_captcha_answer = "18";

                    $stmt = $pdo->prepare("INSERT INTO CAPTCHA (question, answer) VALUES (:question, :answer)");
                    $stmt->bindParam(':question', $default_captcha_question);
                    $stmt->bindParam(':answer', $default_captcha_answer);
                    $stmt->execute();

                    echo "Default CAPTCHA question and answer created successfully!<br>";

                    $categories = [
                        "Animaux",
                        "Politique",
                        "Éducation",
                        "Environnement",
                        "Droits de l'Homme",
                        "Santé",
                        "Transports et Urbanisme",
                        "Divers"
                    ];

                    foreach ($categories as $category) {
                        $stmt = $pdo->prepare("INSERT INTO CATEGORY (name) VALUES (:name)");
                        $stmt->bindParam(':name', $category);
                        $stmt->execute();
                    }

                    echo "Default categories created successfully!<br>";
                    
                    echo '<div class="message success">Setup completed successfully! Redirecting to login page...</div>';
                    echo '<script>setTimeout(function() { window.location.href = "/Sources/login.php"; }, 3000);</script>';
                }
                
            } catch (Exception $e) {
                echo '<div class="message error">';
                echo "<h2>Setup Error:</h2>";
                echo "<p>" . $e->getMessage() . "</p>";
                echo "<p>File: " . $e->getFile() . "</p>";
                echo "<p>Line: " . $e->getLine() . "</p>";
                echo '</div>';
            }
        }
    }
    ?>

    <form action="setup.php" method="POST">
        <h2>Mot de passe d'initialisation</h2>
        <p>Entrez le mot de passe fourni afin de configurer un utilisateur administrateur, un captcha par défaut et des catégories</p>
        <label for="password">Mot de passe :</label>
        <input type="password" name="password" id="password" placeholder="Mot de passe" required>
        <button type="submit" id="submit">Initialiser</button>
    </form>
</body>
</html>