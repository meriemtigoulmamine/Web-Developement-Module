$(document).ready(function () {

    var form = $("#addMemberForm");
    var membersbody = $("#membersBody");


    form.on("submit", function (event) {
        event.preventDefault();
       

        var institution = $("#memberInstitution").val().trim();
        var lab = $("#memberLab").val().trim();
        var fullname = $("#memberName").val().trim();
        var profession = $("#memberProf").val().trim();
        var role = $("#memberRole").val().trim();

        if (!institution || !lab || !fullname || !profession || !role) {
            alert("Please fill in all fields.");
            return;
        }

        var newRow = $("<tr></tr>");
        var values = [institution, lab, fullname, profession, role];

        values.forEach(function (value) {
            var cell = $("<td></td>");
            cell.text(value);
            newRow.append(cell);
        });

        membersbody.append(newRow);
        form[0].reset();
    });
});