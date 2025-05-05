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
    <div id="address-fields">
        <input type="text" name="city" id="city" placeholder="City" class="address-field">
        <label for="city">City</label>
        <input type="number" name="pcode" id="pcode" placeholder="Postal Code" class="address-field">
        <label for="pcode">Postal Code</label>
        <input type="text" name="road" id="road" placeholder="Road" class="address-field">
        <label for="road">Road</label>
        <input type="number" name="num" id="num" placeholder="Number" class="address-field">
        <label for="num">Number</label>
    </div>
    <input type="number" name="max_part" id="max_part">
    <label for="max_part">Max participants</label>
    <input type="hidden" name="id" id="id" value="<?php echo $_GET['id']; ?>">
    <br><br>
    <button type="submit">send</button>
</form>

<script>

    const addressFields = document.querySelectorAll('.address-field');
    
    addressFields.forEach(field => {
        field.addEventListener('input', validateAddressFields);
    });
    
    function validateAddressFields() {
        let hasValue = false;
        addressFields.forEach(field => {
            if (field.value.trim() !== '') {
                hasValue = true;
            }
        });
        
        addressFields.forEach(field => {
            if (hasValue) {
                field.required = true;
                field.parentElement.querySelector('label').innerHTML = 
                    field.parentElement.querySelector('label').innerHTML + 
                    (field.parentElement.querySelector('label').innerHTML.includes('*') ? '' : ' *');
            } else {
                field.required = false;
                field.parentElement.querySelector('label').innerHTML = 
                    field.parentElement.querySelector('label').innerHTML.replace(' *', '');
            }
        });
    }
</script>

<?php
include_once 'footer.php';
?>