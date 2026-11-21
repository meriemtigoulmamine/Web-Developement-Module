(() => {
    "use strict";

    const body = document.getElementById("projectsBody");
    const statusLine = document.getElementById("tableStatus");
    const archived = document.body.dataset.archived === "1";
    const dirtyRows = new Set();
    let loading = false;
    let saving = false;

    const statuses = [
        ["Submitted", "Soumis"],
        ["Reviewed", "Révisé"],
        ["Rejected", "Rejeté"],
        ["Accepted", "Accepté"]
    ];

    function makeCell(value, className = "") {
        const cell = document.createElement("td");
        if (className) cell.className = className;
        cell.textContent = value == null ? "" : String(value);
        return cell;
    }

    function addControlForm(cell, id, action, label, className = "") {
        const form = document.createElement("form");
        form.dataset.ajaxAction = action;
        form.dataset.projectId = String(id);
        const button = document.createElement("button");
        button.type = "submit";
        button.textContent = label;
        if (className) button.className = className;
        form.append(button);
        cell.append(form);
    }

    function buildRow(project) {
        const row = document.createElement("tr");
        row.dataset.id = String(project.id);

        const date = makeCell(project.submitted_at);
        row.append(date);

        const userCell = document.createElement("td");
        userCell.textContent = project.fullname || "";
        const email = document.createElement("div");
        email.className = "muted";
        email.textContent = project.email || "";
        userCell.append(email);
        row.append(userCell);

        const projectCell = document.createElement("td");
        const title = document.createElement("div");
        title.className = "project-title";
        title.textContent = project.title || "";
        const projectId = document.createElement("div");
        projectId.className = "muted";
        projectId.textContent = "#" + project.id;
        const edit = document.createElement("a");
        edit.href = "edit_project.php?id=" + encodeURIComponent(project.id);
        edit.textContent = "Modifier le projet";
        projectCell.append(title, projectId, edit);
        row.append(projectCell);

        const description = makeCell(project.description);
        description.classList.add("description");
        row.append(description);

        const programCell = document.createElement("td");
        programCell.textContent = project.program || "";
        const call = document.createElement("div");
        call.className = "muted";
        call.textContent = project.calls || "";
        programCell.append(call);
        row.append(programCell);

        const formId = "update-" + project.id;
        const statusCell = document.createElement("td");
        const select = document.createElement("select");
        select.name = "status";
        select.setAttribute("form", formId);
        select.setAttribute("aria-label", "Statut du projet");
        statuses.forEach(([value, label]) => {
            const option = document.createElement("option");
            option.value = value;
            option.textContent = label;
            option.selected = project.status === value;
            select.append(option);
        });
        statusCell.append(select);
        row.append(statusCell);

        const remarksCell = document.createElement("td");
        const remarks = document.createElement("textarea");
        remarks.name = "remarks";
        remarks.maxLength = 5000;
        remarks.setAttribute("form", formId);
        remarks.setAttribute("aria-label", "Remarques");
        remarks.value = project.remarks || "";
        remarksCell.append(remarks);
        row.append(remarksCell);

        const actions = document.createElement("td");
        actions.className = "actions";
        const saveForm = document.createElement("form");
        saveForm.id = formId;
        saveForm.dataset.ajaxAction = "save";
        saveForm.dataset.projectId = String(project.id);
        const idInput = document.createElement("input");
        idInput.type = "hidden";
        idInput.name = "id";
        idInput.value = String(project.id);
        const saveButton = document.createElement("button");
        saveButton.type = "submit";
        saveButton.name = "action";
        saveButton.value = "save";
        saveButton.textContent = "Enregistrer";
        saveForm.append(idInput, saveButton);
        actions.append(saveForm);

        addControlForm(actions, project.id, archived ? "unarchive" : "archive",
            archived ? "Désarchiver" : "Archiver", "secondary");
        addControlForm(actions, project.id, "delete", "Supprimer", "delete");
        row.append(actions);
        return row;
    }

    async function loadProjects() {
        if (loading || saving) return;
        loading = true;
        try {
            const response = await fetch("projects_api.php?archived=" + (archived ? "1" : "0") + "&_=" + Date.now(), {
                credentials: "same-origin",
                cache: "no-store",
                headers: { "Accept": "application/json", "X-Requested-With": "XMLHttpRequest" }
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || "Chargement impossible.");

            const existingRows = new Map(
                Array.from(body.querySelectorAll("tr[data-id]")).map(row => [row.dataset.id, row])
            );
            body.replaceChildren();
            if (data.projects.length === 0) {
                const row = document.createElement("tr");
                const cell = makeCell("Aucun projet dans cette liste.", "empty");
                cell.colSpan = 8;
                row.append(cell);
                body.append(row);
            } else {
                data.projects.forEach(project => {
                    const id = String(project.id);
                    const current = existingRows.get(id);
                    body.append(dirtyRows.has(id) && current ? current : buildRow(project));
                    dirtyRows.delete(id);
                });
            }
            statusLine.textContent = "Mis à jour à " + new Date().toLocaleTimeString();
        } catch (error) {
            statusLine.textContent = error.message || "Erreur de chargement.";
        } finally {
            loading = false;
        }
    }

    body.addEventListener("input", event => {
        const row = event.target.closest("tr[data-id]");
        if (row) dirtyRows.add(row.dataset.id);
    });
    body.addEventListener("change", event => {
        const row = event.target.closest("tr[data-id]");
        if (row) dirtyRows.add(row.dataset.id);
    });

    body.addEventListener("submit", async event => {
        const form = event.target.closest("form[data-ajax-action]");
        if (!form) return;
        event.preventDefault();

        const action = form.dataset.ajaxAction;
        const id = form.dataset.projectId;
        if (action === "delete" && !window.confirm("Supprimer définitivement cette soumission ?")) return;

        saving = true;
        statusLine.textContent = "Enregistrement…";
        const payload = new URLSearchParams();
        payload.set("id", id);
        payload.set("action", action);
        if (action === "save") {
            const row = form.closest("tr");
            payload.set("status", row.querySelector('select[name="status"]').value);
            payload.set("remarks", row.querySelector('textarea[name="remarks"]').value);
        }
        try {
            const response = await fetch("projects_api.php", {
                method: "POST",
                credentials: "same-origin",
                cache: "no-store",
                headers: {
                    "Accept": "application/json",
                    "Content-Type": "application/x-www-form-urlencoded;charset=UTF-8",
                    "X-Requested-With": "XMLHttpRequest"
                },
                body: payload.toString()
            });
            const data = await response.json();
            if (!response.ok || !data.success) throw new Error(data.message || "Modification impossible.");
            dirtyRows.delete(String(id));
            statusLine.textContent = data.message || "Modification enregistrée.";
        } catch (error) {
            statusLine.textContent = error.message || "Modification impossible.";
        } finally {
            saving = false;
            await loadProjects();
        }
    });

    loadProjects();
    window.setInterval(loadProjects, 5000);
})();