<?php
include_once 'header.php';
?>
<link rel="stylesheet" href="css/don.css">
<script async
  src="https://js.stripe.com/v3/buy-button.js">
</script>

<div class="donation">
  <h1>Donation</h1>
  <p>
  Nous sommes une organisation à but non lucratif et nous dépendons des dons pour assurer le fonctionnement de nos services. Si vous souhaitez nous soutenir, n'hésitez pas à faire un don.</p>
<p>Merci pour votre soutien !</p>
<p>Nous acceptons les dons via Stripe. Vous pouvez choisir de faire un don uniquement ponctuel.</p>
<p>Tous les dons sont déductibles des impôts dans la mesure permise par la loi.</p>
<stripe-buy-button
  buy-button-id="buy_btn_1RLRwK4fUxFAbEXlVvU7VEnT"
  publishable-key="pk_test_51RLRVG4fUxFAbEXlWMznBBWeccU8riivqCroHdc2r2zc0Ml7LQcqL5tNbhEJBM31aDjlYGzXucaFzjffK88AEJZ100jPk3ulW9"
>
</stripe-buy-button>
</div>
<p>Nous vous remercions de votre soutien et de votre générosité. Votre don nous aidera à continuer à fournir des services de qualité.</p>
<p>Si vous avez des questions ou des préoccupations concernant le processus de don, n'hésitez pas à nous contacter.</p>

<?php
include_once 'footer.php';
?>