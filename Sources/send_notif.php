<?php
//Import PHPMailer classes into the global namespace
//These must be at the top of your script, not inside a function
require 'PHPMailer-master/src/Exception.php';
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

//Create an instance; passing `true` enables exceptions

function EnvoieMail($mail, $mailToSend, $username, $title, $Content)
{
    try {
        //Server settings
        $mail->SMTPDebug = 0;                      //Enable verbose debug output
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = "noreply.petisign@gmail.com";                     //SMTP username from environment variable
        $mail->Password   = "zzsqnjhfwapwcrcr";                     //SMTP password from environment variable
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
        $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        //Recipients
        $mail->setFrom($mailToSend, 'PétiSign');
        $mail->addAddress($mailToSend, 'User');    // Ajouter un destinataire
        $mail->addReplyTo('noreply.petisign@gmail.com', 'No Reply');

        $mail->isHTML(true);                                  //Set email format to HTML
        $mail->Subject = $title;
        $mail->Body = '<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Newsletter</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .header {
            background-color: #FED78B;
            color: #1A414E;
            text-align: center;
            padding: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
        }

        .content {
            padding: 20px;
            line-height: 1.6;
        }

        .content h2 {
            color: #1A414E;
            font-size: 20px;
        }

        .content p {
            margin: 10px 0;
        }

        .cta-button {
            display: inline-block;
            margin: 20px 0;
            padding: 10px 20px;
            background-color: #1A414E;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .cta-button:hover {
            background-color: #145a6b;
        }

        .footer {
            background-color: #f4f4f4;
            text-align: center;
            padding: 10px;
            font-size: 14px;
            color: #888;
        }

        .footer a {
            color: #1A414E;
            text-decoration: none;
        }

        .footer a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>' . $title . '</h1>
        </div>
        <div class="content">
            <h2>Bonjour ' . $username . ',</h2>
            <p>' . $Content . '</p>
        </div>
        <div class="footer">
            <p><a href="petisign.cloud/Sources/profile.php">Mon Compte PétiSign</a> | <a href="petisign.cloud">Visitez notre site</a></p>
            <p>© 2025 PétiSign. Tous droits réservés.</p>
        </div>
    </div>
</body>
</html>';
        $mail->AltBody = 'Votre code de validation est : ' . $Content;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

