<?php
include_once 'header.php';
?>

<form action="Processus/event_creation.php" method="post">
    <input type="text" name="title" id="title" placeholder="Event Title" required>
    <label for="title">Event title</label>
    <input type="text" name="description" id="description" placeholder="Event Description" required>
    <label for="description">Event description</label>
    <input type="date" name="date" id="date" placeholder="Event Date" required>
    <label for="date">Event date</label>
    <br>
    <br>
    <p>facultatif</p>
    <input type="text" name="city" id="city" placeholder="City">
    <label for="city">City</label>
    <input type="number" name="pcode" id="pcode" placeholder="Postal Code">
    <label for="pcode">Postal Code</label>
    <input type="text" name="road" id="road" placeholder="Road">
    <label for="road">Road</label>
    <input type="number" name="num" id="num">
    <label for="num">Number</label>
    <input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>">
    <br><br>
    <button type="submit">send</button>
</form>

<?php
include_once 'footer.php';
?>