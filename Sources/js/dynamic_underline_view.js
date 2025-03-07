document.addEventListener("DOMContentLoaded", function() {
    var petitionName = document.getElementById("lmm");
    var textContent = petitionName.textContent || petitionName.innerText;
    petitionName.setAttribute("data-content", textContent);
});