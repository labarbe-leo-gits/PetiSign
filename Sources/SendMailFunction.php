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

function EnvoieMail($mail, $mailToSend, $Content)
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
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Code de validation';
        $mail->Body    = '<!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Validation de Compte</title>
            <style>
                body {
                    background-color: #000;
                    color: #1A414E;
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 0;
                }

                .container {
                    margin: 0 auto;
                    padding: 40px;
                    max-width: 600px;
                    text-align: center;
                    border-radius: 10px;
                    border: 1px solid #333;
                }

                h1 {
                    font-size: 24px;
                    color: #1A414E;
                }

                p {
                    font-size: 18px;
                    color: #1A414E;
                }

                .validation-code {
                    padding: 20px;
                    font-size: 32px;
                    color: #1A414E;
                    font-weight: bold;
                    letter-spacing: 5px;
                    margin: 20px 0;
                    border-radius: 5px;
                    border: 2px solid #1A414E;
                }
                a.more {
                    color: rgb(0, 191, 255);
                    text-decoration: none;
                    cursor:pointer;
                }
                .footer {

                    margin-top: 30px;
                    font-size: 14px;
                    color: #888;
                }
                a.more:hover {
                    cursor:pointer;
                    text-decoration: underline;
                }
                .small {
                    font-size: 10px;
                }
                a{
                    cursor:pointer;
                }
            </style>
        </head>
        <body>

            <div class="container">
                <h1>Merci d\'avoir choisi PétiSign !</h1>
                <p>Votre code de vérification mail :</p>
                <div class="validation-code">' . $Content . '</div>
                <div class="footer">
                    <p class="small">Attention : ce code est strictement personel.</p>
                    <p class="small">Vous avez reçu cet email car vous vous êtes inscrit sur notre site. Si vous n\'êtes pas à l\'origine de cette demande, veuillez ignorer cet email.</p>
                    <p class="small">© 2025 PétiSign. Tous droits réservés.</p>
                    <a href="petisign.cloud" class="more">En savoir plus</a>
                </div>
            </div>

        </body>
        </html>';
        $mail->AltBody = 'Votre code de validation est : ' . $Content;

        $mail->send();
        echo '<p class="code_info">Code de vérification envoyé à : '.$mailToSend.'</p>';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}
?>

