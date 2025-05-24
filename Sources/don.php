<?php
include_once 'header.php';

?>
<link rel="stylesheet" href="css/don.css">
<script async
  src="https://js.stripe.com/v3/buy-button.js">
</script>

<div class="donation">
  <h1>Faire un don</h1>
  <hr>
  <div class="main_text">
    <p class="donation_text">Merci pour votre intérêt à soutenir notre organisation.</p>
    <p class="donation_text">En tant qu’association à but non lucratif, nous comptons sur la générosité de nos donateurs pour continuer à mener nos actions et proposer des services de qualité.</p>

    <p class="donation_text">🧡 Votre don fait la différence</p>
    <p class="donation_text">Tous les dons sont ponctuels et traités en toute sécurité via Stripe.</p>
  </div>
<stripe-buy-button
  buy-button-id="buy_btn_1RLRwK4fUxFAbEXlVvU7VEnT"
  publishable-key="pk_test_51RLRVG4fUxFAbEXlWMznBBWeccU8riivqCroHdc2r2zc0Ml7LQcqL5tNbhEJBM31aDjlYGzXucaFzjffK88AEJZ100jPk3ulW9"
>
</stripe-buy-button>
</div>
<?php
include_once 'footer.php';
?>