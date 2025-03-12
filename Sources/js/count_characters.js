function count(counter, input, cnt_max) {
    let cnt = document.getElementById(counter);
    cnt.innerHTML = `<p>Limite de caractères : ${input.value.length} / ${cnt_max}</p>`;
}