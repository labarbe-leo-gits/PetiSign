document.addEventListener('DOMContentLoaded', function () {
    const leaderDivs = document.querySelectorAll('.selectable_');
    const memberDivs = document.querySelectorAll('.selectable');
    const leaderInput = document.getElementById('selected_leader');
    const memberInput = document.getElementById('selected_benevoles');
    const selectedIds = new Set();

    leaderDivs.forEach(div => {
        div.addEventListener('click', function () {

            leaderDivs.forEach(d => d.classList.remove('selected'));

            const id = this.getAttribute('data-id');
            this.classList.add('selected');
            leaderInput.value = id;

            memberDivs.forEach(memberDiv => {
                if (memberDiv.getAttribute('data-id') === id) {
                    memberDiv.classList.remove('selected');
                    memberDiv.classList.add('disabled');
                    selectedIds.delete(id);
                } else {
                    memberDiv.classList.remove('disabled');
                }
            });


            memberInput.value = selectedIds.size > 0 ? Array.from(selectedIds).join(',') : '0';
        });
    });

    memberDivs.forEach(div => {
        div.addEventListener('click', function () {
            const id = this.getAttribute('data-id');

            if (this.classList.contains('disabled')) {
                return;
            }

            if (selectedIds.has(id)) {
                selectedIds.delete(id);
                this.classList.remove('selected');
            } else {
                selectedIds.add(id);
                this.classList.add('selected');
            }

            memberInput.value = selectedIds.size > 0 ? Array.from(selectedIds).join(',') : '0';
        });
    });


    leaderDivs.forEach(leaderDiv => {
        if (!leaderDiv.classList.contains('disabled') && leaderDiv.classList.contains('selected')) {
            leaderDiv.click();
        }
    });

    memberDivs.forEach(memberDiv => {
        if (!memberDiv.classList.contains('disabled') && memberDiv.classList.contains('selected')) {
            memberDiv.click();
        }
    });
});