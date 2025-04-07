const fileInput = document.getElementById('fileToUpload');
const fileButton = document.getElementById('fileButton');

fileButton.addEventListener('click', () => {
    fileInput.click();
});

fileInput.addEventListener('change', (event) => {
    if (event.target.files.length > 0) {
        fileButton.textContent = event.target.files[0].name;
    } else {
        fileButton.textContent = 'Sélectionnez un fichier';
    }
});