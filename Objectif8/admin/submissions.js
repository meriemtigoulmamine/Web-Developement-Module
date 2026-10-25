(() => {
  const app = document.getElementById("submissionsApp");
  if (!app) return;
  const body = document.getElementById("submissionRows");
  const notice = document.getElementById("tableNotice");
  const count = document.getElementById("activeCount");
  const archived = app.dataset.archived === "1";
  const csrf = document.querySelector('meta[name="csrf-token"]').content;
  const apiUrl = "submissions_api.php";
  const dialog = document.getElementById("editDialog");
  const editForm = document.getElementById("editForm");
  const editError = document.getElementById("editError");
  let projectsById = new Map();
  const statuses = ["Submitted", "Reviewed", "Rejected", "Accepted"];
  const esc = (value) => String(value ?? "").replace(/[&<>"']/g, ch => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[ch]));
  const showMessage = (message, isError = false) => {
    notice.hidden = false;
    notice.className = isError ? "alert" : "notice";
    notice.textContent = message;
  };
  const formatDate = (value) => {
    const date = new Date(String(value || "").replace(" ", "T"));
    return Number.isNaN(date.getTime()) ? esc(value) : `${date.toLocaleDateString()}<small>${date.toLocaleTimeString([], {hour:"2-digit", minute:"2-digit"})}</small>`;
  };
  const renderRow = (p) => {
    const id = Number(p.id);
    const options = statuses.map(s => `<option ${p.status === s ? "selected" : ""}>${s}</option>`).join("");
    const description = esc(p.description).replace(/\r?\n/g, "<br>");
    const extra = esc(p.other_details).replace(/\r?\n/g, "<br>");
    return `<tr data-project-id="${id}">
      <td>${formatDate(p.submitted_at)}</td>
      <td><strong>${esc(p.fullname)}</strong><a class="mail-link" href="mailto:${esc(p.email)}">${esc(p.email)}</a></td>
      <td class="project-cell"><strong>${esc(p.title)}</strong><details><summary>View details</summary><p>${description}</p><p>Institution: ${esc(p.institution)} · Country: ${esc(p.country)}</p><p>Partner contact: ${esc(p.contact)}</p><p>Duration: ${esc(p.duration)} months · Budget: ${esc(p.budget)} DA</p><p>Support: ${esc(p.needs)} ${extra}</p></details><button type="button" class="edit-link edit-project-button" data-edit-project="${id}">Edit project information →</button></td>
      <td>${esc(p.program)}<small>${esc(p.calls)}</small></td>
      <td><form class="status-form" data-review-form="${id}"><label for="status-${id}">Status</label><select id="status-${id}" name="status">${options}</select><label for="notes-${id}">Admin remarks</label><textarea id="notes-${id}" name="admin_notes" rows="3">${esc(p.admin_notes)}</textarea><button class="button button-primary" type="submit">Save review</button></form></td>
      <td><div class="action-stack"><button class="button button-light" type="button" data-project-action="${archived ? "restore" : "archive"}" data-project-id="${id}">${archived ? "Restore" : "Archive"}</button><button class="button button-danger" type="button" data-project-action="delete" data-project-id="${id}">Delete</button></div></td>
    </tr>`;
  };
  async function loadRows(showLoading = false) {
    if (showLoading) body.innerHTML = '<tr><td colspan="6" class="empty-state">Loading submissions…</td></tr>';
    try {
      const response = await fetch(`${apiUrl}?archived=${archived ? "1" : "0"}`, {headers:{"Accept":"application/json"}, cache:"no-store"});
      const data = await response.json();
      if (!response.ok || !data.ok) throw new Error(data.message || "Could not load submissions.");
      projectsById = new Map(data.projects.map(p => [String(p.id), p]));
      count.textContent = data.activeCount;
      body.innerHTML = data.projects.length ? data.projects.map(renderRow).join("") : '<tr><td colspan="6" class="empty-state">No submissions found.</td></tr>';
    } catch (error) {
      body.innerHTML = `<tr><td colspan="6" class="empty-state">${esc(error.message)} <button type="button" class="button button-light" id="retryRows">Retry</button></td></tr>`;
    }
  }
  async function sendAction(payload) {
    const response = await fetch(apiUrl, {method:"POST", headers:{"Content-Type":"application/json","Accept":"application/json","X-CSRF-Token":csrf}, body:JSON.stringify(payload)});
    const data = await response.json();
    if (!response.ok || !data.ok) throw new Error(data.message || "The action could not be completed.");
    showMessage(data.message);
    await loadRows();
  }
  body.addEventListener("submit", async event => {
    const form = event.target.closest("[data-review-form]");
    if (!form) return;
    event.preventDefault();
    const button = form.querySelector("button[type=submit]");
    button.disabled = true;
    try {
      await sendAction({action:"review", project_id:form.dataset.reviewForm, status:form.elements.status.value, admin_notes:form.elements.admin_notes.value});
    } catch (error) { showMessage(error.message, true); }
    finally { button.disabled = false; }
  });
  body.addEventListener("click", async event => {
    if (event.target.id === "retryRows") { loadRows(true); return; }
    const editButton = event.target.closest("[data-edit-project]");
    if (editButton) {
      const project = projectsById.get(editButton.dataset.editProject);
      if (!project) return;
      for (const field of editForm.elements) if (field.name && project[field.name] !== undefined) field.value = project[field.name] ?? "";
      editError.hidden = true;
      dialog.showModal();
      return;
    }
    const actionButton = event.target.closest("[data-project-action]");
    if (!actionButton) return;
    const action = actionButton.dataset.projectAction;
    if (action === "delete" && !confirm("Permanently delete this submission? This cannot be undone.")) return;
    if (actionButton.disabled) return;
    actionButton.disabled = true;
    try { await sendAction({action, project_id:actionButton.dataset.projectId}); }
    catch (error) { showMessage(error.message, true); }
    finally { actionButton.disabled = false; }
  });
  editForm.addEventListener("submit", async event => {
    event.preventDefault();
    editError.hidden = true;
    const payload = Object.fromEntries(new FormData(editForm).entries());
    payload.action = "edit";
    try { await sendAction(payload); dialog.close(); }
    catch (error) { editError.textContent = error.message; editError.hidden = false; }
  });
  document.getElementById("closeEditDialog").addEventListener("click", () => dialog.close());
  document.getElementById("cancelEdit").addEventListener("click", () => dialog.close());
  loadRows(true);
})();
