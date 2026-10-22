document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("addMemberForm");
    var membersBody = document.getElementById("membersBody");

    if (!form || !membersBody) {
        return;
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        var institution = document.getElementById("memberInstitution").value.trim();
        var lab = document.getElementById("memberLab").value.trim();
        var fullname = document.getElementById("memberName").value.trim();
        var profession = document.getElementById("memberProf").value.trim();
        var role = document.getElementById("memberRole").value.trim();

        if (!institution || !lab || !fullname || !profession || !role) {
            alert("Please fill in all fields.");
            return;
        }

        var newRow = document.createElement("tr");
        var values = [institution, lab, fullname, profession, role];

        values.forEach(function (value) {
            var cell = document.createElement("td");
            cell.textContent = value;
            newRow.appendChild(cell);
        });

        membersBody.appendChild(newRow);
        form.reset();
    });
});




