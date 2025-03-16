<?php
include_once 'header.php';
?>

<style>
    .right_panel {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100%;
    }
    .captcha_form {
        width: 80%;
        height: 60%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        margin-top: -100px;
        background-color: #f87575;
        box-shadow: 0px 10px 14px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
    }
    .error_btn {
        background-color: #f87575;
        color: white;
        border: 2px solid white;
        border-radius: 5px;
        padding: 10px 20px;
        font-size: 1.2em;
        margin-top: 20px;
        width: 40%;
    }
    .error_btn:hover {
        background-color: white;
        color: #f87575;
        cursor: pointer;
    }
    #loginhr{
        width: 10%;
    }
    summary {
        background-color: #f87575;
        color: white;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    details[open] summary {
        border-radius: 5px 5px 0 0;
        border-bottom: 2px solid white;
    }

    details {
        background: #b1cadf;
        border-radius: 5px;
        border: 2px solid white;
        overflow: hidden;
        transition: max-height 0.3s ease;
        max-height: 50px; /* Adjust as needed */
    }

    details[open] {
        max-height: 500px; /* Adjust as needed */
    }

    article > *:first-child {
        margin: 0;
    }

    article > * + * {
        margin: 0.75em 0 0 0;
    }

    pre {
        color: white;
        background: #455e7b;
        padding: 1em;
        border-radius: 5px;
    }

    code {
        color: white;
    }

    article {
        padding: 10px;
        margin: 0;
    }

    details code {
        font-size: 1.1em;
    }

    .error_txt {
        text-align: center;
        color: white;
    }
</style>

<div class="right_panel">
    <div class="captcha_form" id="captcha_form">
        <h1 id="loginhigh" class="highlighted-text error_txt">Erreur</h1>
        <hr id="loginhr">
        <form method="post" class="login" action="Processus/modify_captcha.php">
            <div class="entries">
                <div class="entries">
                    <p class="error_txt">ID invalide</p>
                </div>
                <div class="entries second">
                <details open>
                    <summary>Détails de l'erreur</summary>
                    <article>
                    <pre><code>Error: Unable to fetch data for ID <?=$id?> <?php echo "\r\n" ?> &#8627; Does not exist</code></pre>
                    </article>
                </details>
                </div>
            </div>
            <input type="hidden" name="id" value="<?= htmlspecialchars($id, ENT_QUOTES, 'UTF-8') ?>">
        </form>
        <button class="custom-button error_btn" onclick="window.location.href='captcha.php'" id="cancel_btn">Ok</button>
    </div>
</div>
</div>

<?php
include_once 'footer.php';
?>