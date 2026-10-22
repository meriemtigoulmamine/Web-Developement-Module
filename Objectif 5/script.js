document.addEventListener("DOMContentLoaded", function () {
    var form = document.getElementById("addMemberForm");
    var errorEl = document.getElementById("memberFormError");
    var membersBody = document.getElementById("membersBody");

    if (!form || !membersBody) {
        return;
    }

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        if (errorEl) {
            errorEl.textContent = "";
        }

        var institutionInput = document.getElementById("memberInstitution");
        var labInput = document.getElementById("memberLab");
        var nameInput = document.getElementById("memberName");
        var profInput = document.getElementById("memberProf");
        var roleInput = document.getElementById("memberRole");

        var institution = institutionInput ? institutionInput.value.trim() : "";
        var lab = labInput ? labInput.value.trim() : "";
        var fullname = nameInput ? nameInput.value.trim() : "";
        var profession = profInput ? profInput.value.trim() : "";
        var role = roleInput ? roleInput.value.trim() : "";

        if (!institution || !lab || !fullname || !profession || !role) {
            if (errorEl) {
                errorEl.textContent = "Please fill in all required fields.";
            }
            return;
        }

        var groupKey = institution.toLowerCase();
        var existingRows = membersBody.querySelectorAll("tr[data-group='" + groupKey + "']");
        var institutionCell = null;

        if (existingRows.length > 0) {
            institutionCell = existingRows[0].querySelector("td.institution");
            if (institutionCell) {
                var currentRowspan = parseInt(institutionCell.getAttribute("rowspan") || "1", 10);
                institutionCell.setAttribute("rowspan", String(currentRowspan + 1));
            }
        }

        var newRow = document.createElement("tr");
        newRow.setAttribute("data-group", groupKey);

        if (!institutionCell) {
            institutionCell = document.createElement("td");
            institutionCell.className = "institution";
            institutionCell.textContent = institution;
            institutionCell.setAttribute("rowspan", "1");
            newRow.appendChild(institutionCell);
        }

        var labCell = document.createElement("td");
        labCell.textContent = lab;
        newRow.appendChild(labCell);

        var nameCell = document.createElement("td");
        nameCell.textContent = fullname;
        newRow.appendChild(nameCell);

        var profCell = document.createElement("td");
        profCell.textContent = profession;
        newRow.appendChild(profCell);

        var roleCell = document.createElement("td");
        roleCell.textContent = role;
        newRow.appendChild(roleCell);

        var actionCell = document.createElement("td");
        var deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.className = "delete-btn";
        deleteBtn.textContent = "Delete";
        actionCell.appendChild(deleteBtn);
        newRow.appendChild(actionCell);

        membersBody.appendChild(newRow);
        form.reset();
    });

    membersBody.addEventListener("click", function (event) {
        var target = event.target;
        if (!(target instanceof HTMLElement)) {
            return;
        }
        if (!target.classList.contains("delete-btn")) {
            return;
        }

        var row = target.closest("tr");
        if (!row) {
            return;
        }

        var groupKey = row.getAttribute("data-group");
        if (groupKey) {
            var groupRows = membersBody.querySelectorAll("tr[data-group='" + groupKey + "']");
            if (groupRows.length === 1) {
                row.remove();
                return;
            }

            var institutionCell = groupRows[0].querySelector("td.institution");
            if (institutionCell) {
                var currentRowspan = parseInt(institutionCell.getAttribute("rowspan") || "1", 10);
                if (currentRowspan > 1) {
                    institutionCell.setAttribute("rowspan", String(currentRowspan - 1));
                }
            }
        }

        row.remove();
    });
});
