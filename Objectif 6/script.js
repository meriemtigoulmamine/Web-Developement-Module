$(document).ready(function () {

    let form = $("#addMemberForm");
    let membersbody = $("#membersBody");


    form.on("submit", function (event) {
        event.preventDefault();
       

        let institution = $("#memberInstitution").val().trim();
        let lab = $("#memberLab").val().trim();
        let fullname = $("#memberName").val().trim();
        let profession = $("#memberProf").val().trim();
        let role = $("#memberRole").val().trim();

        if (!institution || !lab || !fullname || !profession || !role) {
            alert("Please fill in all fields.");
            return;
        }

        let newRow = $("<tr></tr>");
        let values = [institution, lab, fullname, profession, role];

        values.forEach(function (value) {
            let cell = $("<td></td>");
            cell.text(value);
            newRow.append(cell);
        });

        membersbody.append(newRow);
      
    });
});