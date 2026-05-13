// ============================================================
// DEVQUIZ ADMIN — app.js
// Full Supabase integration
// ============================================================

// ╔══════════════════════════════════════════════════════════╗
// ║  🔧 PASTE YOUR SUPABASE CREDENTIALS HERE                ║
// ║  Project Settings → API in your Supabase dashboard      ║
// ╚══════════════════════════════════════════════════════════╝
const SUPABASE_URL      = "https://beogrrghbpvuaaqhdzmk.supabase.co";
const SUPABASE_ANON_KEY = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6ImJlb2dycmdoYnB2dWFhcWhkem1rIiwicm9sZSI6ImFub24iLCJpYXQiOjE3Nzg2MzgwMTUsImV4cCI6MjA5NDIxNDAxNX0.1caisYF7GvwTOPhoEP6szHxOP_wQ2Tj-Y0pw8MNgl6M";

// ── Supabase REST helpers ──────────────────────────────────
const SB_HEADERS = {
  "Content-Type":  "application/json",
  "apikey":        SUPABASE_ANON_KEY,
  "Authorization": `Bearer ${SUPABASE_ANON_KEY}`,
  "Prefer":        "return=representation",
};

async function sbSelect(table, params = "") {
  const res = await fetch(`${SUPABASE_URL}/rest/v1/${table}?${params}`, {
    headers: SB_HEADERS,
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

async function sbInsert(table, body) {
  const res = await fetch(`${SUPABASE_URL}/rest/v1/${table}`, {
    method: "POST",
    headers: SB_HEADERS,
    body: JSON.stringify(body),
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

async function sbUpdate(table, id, idCol, body) {
  const res = await fetch(`${SUPABASE_URL}/rest/v1/${table}?${idCol}=eq.${id}`, {
    method: "PATCH",
    headers: SB_HEADERS,
    body: JSON.stringify(body),
  });
  if (!res.ok) throw new Error(await res.text());
  return res.json();
}

async function sbDelete(table, id, idCol) {
  const res = await fetch(`${SUPABASE_URL}/rest/v1/${table}?${idCol}=eq.${id}`, {
    method: "DELETE",
    headers: { ...SB_HEADERS, Prefer: "return=minimal" },
  });
  if (!res.ok) throw new Error(await res.text());
}

// ── In-memory cache (populated from Supabase on login) ─────
const DB = {
  users:     [],
  languages: [],
  questions: [],
};

// ── Admin credentials loaded from DB ───────────────────────
let ADMIN = { username: "admin", password: "admin123" };

// ============================================================
// HELPERS
// ============================================================
function getLangName(id) {
  const l = DB.languages.find(x => x.language_id === id);
  return l ? l.language_name : "Unknown";
}

function showToast(msg, type = "success") {
  const t = document.getElementById("toast");
  t.textContent = msg;
  t.className = `toast ${type}`;
  t.classList.remove("hidden");
  setTimeout(() => t.classList.add("hidden"), 2800);
}

function openModal(title, bodyHTML, onSave) {
  document.getElementById("modal-title").textContent = title;
  document.getElementById("modal-body").innerHTML = bodyHTML;
  document.getElementById("modal-overlay").classList.remove("hidden");
  document.getElementById("modal-save").onclick = onSave;
}

function closeModal() {
  document.getElementById("modal-overlay").classList.add("hidden");
}

function setLoading(show) {
  // Show a subtle overlay while async ops run
  let overlay = document.getElementById("loading-overlay");
  if (!overlay) {
    overlay = document.createElement("div");
    overlay.id = "loading-overlay";
    overlay.style.cssText = `
      position:fixed;inset:0;background:rgba(255,255,255,0.6);
      z-index:9999;display:flex;align-items:center;justify-content:center;
      font-size:1rem;color:#374151;font-family:'Segoe UI',sans-serif;
    `;
    overlay.innerHTML = `<div style="background:#fff;padding:20px 32px;border-radius:12px;
      box-shadow:0 8px 24px rgba(0,0,0,0.15);display:flex;align-items:center;gap:12px;">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#3b82f6" stroke-width="2.5"
        stroke-linecap="round" stroke-linejoin="round" style="animation:spin .8s linear infinite">
        <path d="M21 12a9 9 0 1 1-6.219-8.56"/>
      </svg>
      <style>@keyframes spin{to{transform:rotate(360deg)}}</style>
      Loading…</div>`;
    document.body.appendChild(overlay);
  }
  overlay.style.display = show ? "flex" : "none";
}

// ============================================================
// LOAD ALL DATA FROM SUPABASE
// ============================================================
async function loadAllData() {
  setLoading(true);
  try {
    const [users, languages, questions, admins] = await Promise.all([
      sbSelect("players", "select=player_id,name&order=player_id.asc"),
      sbSelect("languages",   "select=language_id,language_name&order=language_id.asc"),
      sbSelect("questions",   "select=question_id,language_id,question_text,level,category&order=question_id.asc"),
      sbSelect("admin_users", "select=username,password&limit=1"),
    ]);

    // Map players → users (admin calls them "users")
    DB.users     = users.map(u => ({ user_id: u.player_id, name: u.name, email: u.email || "" }));
    DB.languages = languages;
    DB.questions = questions;

    if (admins.length) {
      ADMIN = { username: admins[0].username, password: admins[0].password };
    }
  } catch (err) {
    showToast("Failed to connect to Supabase. Check your credentials.", "error");
    console.error(err);
  } finally {
    setLoading(false);
  }
}

// ============================================================
// LOGIN
// ============================================================
function setupLogin() {
  const btnLogin = document.getElementById("btn-login");
  const togglePw = document.getElementById("toggle-pw");
  const pwInput  = document.getElementById("login-password");

  togglePw.addEventListener("click", () => {
    const isText = pwInput.type === "text";
    pwInput.type = isText ? "password" : "text";
    togglePw.className = `bi ${isText ? "bi-eye" : "bi-eye-slash"} toggle-pw`;
  });

  async function tryLogin() {
    const u   = document.getElementById("login-username").value.trim();
    const p   = pwInput.value.trim();
    const err = document.getElementById("login-error");

    // Load admin creds from Supabase first
    setLoading(true);
    try {
      const admins = await sbSelect("admin_users", "select=username,password&limit=1");
      if (admins.length) ADMIN = { username: admins[0].username, password: admins[0].password };
    } catch (e) {
      console.warn("Could not reach Supabase — falling back to default credentials.");
    } finally {
      setLoading(false);
    }

    if (u === ADMIN.username && p === ADMIN.password) {
      err.classList.add("hidden");
      await loadAllData();
      document.getElementById("login-page").classList.add("hidden");
      document.getElementById("admin-app").classList.remove("hidden");
      renderAll();
    } else {
      err.classList.remove("hidden");
    }
  }

  btnLogin.addEventListener("click", tryLogin);
  document.getElementById("login-password").addEventListener("keydown", e => {
    if (e.key === "Enter") tryLogin();
  });
}

// ============================================================
// NAVIGATION
// ============================================================
function setupNav() {
  document.querySelectorAll(".nav-link[data-page]").forEach(link => {
    link.addEventListener("click", e => {
      e.preventDefault();
      const page = link.dataset.page;
      document.querySelectorAll(".nav-link").forEach(l => l.classList.remove("active"));
      link.classList.add("active");
      document.querySelectorAll(".page").forEach(p => p.classList.remove("active"));
      document.getElementById(`page-${page}`).classList.add("active");
    });
  });

  document.getElementById("btn-logout").addEventListener("click", e => {
    e.preventDefault();
    if (confirm("Are you sure you want to logout?")) {
      document.getElementById("admin-app").classList.add("hidden");
      document.getElementById("login-page").classList.remove("hidden");
      document.getElementById("login-username").value = "";
      document.getElementById("login-password").value = "";
      document.getElementById("login-error").classList.add("hidden");
    }
  });
}

// ============================================================
// RENDER DASHBOARD
// ============================================================
function renderDashboard() {
  document.getElementById("total-users").textContent     = DB.users.length;
  document.getElementById("total-questions").textContent = DB.questions.length;
  document.getElementById("total-languages").textContent = DB.languages.length;

  const recentUsers = [...DB.users].slice(-3).reverse();
  document.getElementById("recent-users-tbody").innerHTML = recentUsers.map(u => `
    <tr>
      <td>${u.user_id}</td>
      <td>${u.name}</td>
      <td>${u.email || "—"}</td>
    </tr>
  `).join("");

  const recentQ = [...DB.questions].slice(-3).reverse();
  document.getElementById("recent-questions-tbody").innerHTML = recentQ.map(q => `
    <tr>
      <td>${q.question_id}</td>
      <td><span class="badge-lang">${getLangName(q.language_id)}</span></td>
      <td>${q.question_text}</td>
    </tr>
  `).join("");
}

// ============================================================
// RENDER QUESTIONS
// ============================================================
function renderQuestions(filter = "") {
  const list = DB.questions.filter(q =>
    q.question_text.toLowerCase().includes(filter.toLowerCase()) ||
    getLangName(q.language_id).toLowerCase().includes(filter.toLowerCase())
  );

  document.getElementById("questions-tbody").innerHTML = list.length
    ? list.map(q => `
      <tr>
        <td>${q.question_id}</td>
        <td><span class="badge-lang">${getLangName(q.language_id)}</span></td>
        <td>${q.question_text}</td>
        <td>Lv${q.level || 1}</td>
        <td>
          <button class="btn-edit"   onclick="editQuestion(${q.question_id})"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn-delete" onclick="deleteQuestion(${q.question_id})"><i class="bi bi-trash"></i> Delete</button>
        </td>
      </tr>
    `).join("")
    : `<tr><td colspan="5" style="text-align:center;color:#9ca3af;padding:20px;">No questions found.</td></tr>`;
}

function langOptions(selectedId = null) {
  return DB.languages.map(l =>
    `<option value="${l.language_id}" ${l.language_id === selectedId ? "selected" : ""}>${l.language_name}</option>`
  ).join("");
}

function levelOptions(selected = 1) {
  return [1,2,3,4,5].map(n =>
    `<option value="${n}" ${n === selected ? "selected" : ""}>Level ${n}</option>`
  ).join("");
}

function categoryOptions(selected = "general") {
  const cats = ["javascript","python","html","css","general"];
  return cats.map(c =>
    `<option value="${c}" ${c === selected ? "selected" : ""}>${c.charAt(0).toUpperCase()+c.slice(1)}</option>`
  ).join("");
}

function addQuestion() {
  openModal("Add Question", `
    <div class="form-group">
      <label>Language</label>
      <select id="m-lang">${langOptions()}</select>
    </div>
    <div class="form-group">
      <label>Category (for game)</label>
      <select id="m-cat">${categoryOptions()}</select>
    </div>
    <div class="form-group">
      <label>Level (1–5)</label>
      <select id="m-level">${levelOptions()}</select>
    </div>
    <div class="form-group">
      <label>Question Text</label>
      <input type="text" id="m-qtext" placeholder="Enter question…" />
    </div>
    <div class="form-group">
      <label>Answer</label>
      <input type="text" id="m-answer" placeholder="Enter correct answer…" />
    </div>
  `, async () => {
    const langId = parseInt(document.getElementById("m-lang").value);
    const text   = document.getElementById("m-qtext").value.trim();
    const answer = document.getElementById("m-answer").value.trim();
    const level  = parseInt(document.getElementById("m-level").value);
    const cat    = document.getElementById("m-cat").value;
    if (!text)   { showToast("Question text is required.", "error"); return; }
    if (!answer) { showToast("Answer is required.", "error"); return; }
    setLoading(true);
    try {
      const [created] = await sbInsert("questions", {
        language_id: langId, question_text: text, answer_text: answer, level, category: cat
      });
      DB.questions.push({
        question_id: created.question_id, language_id: langId,
        question_text: text, level, category: cat
      });
      closeModal();
      renderQuestions();
      renderDashboard();
      showToast("Question added!");
    } catch (e) {
      showToast("Error adding question: " + e.message, "error");
    } finally {
      setLoading(false);
    }
  });
}

function editQuestion(id) {
  const q = DB.questions.find(x => x.question_id === id);
  if (!q) return;
  openModal("Edit Question", `
    <div class="form-group">
      <label>Language</label>
      <select id="m-lang">${langOptions(q.language_id)}</select>
    </div>
    <div class="form-group">
      <label>Category (for game)</label>
      <select id="m-cat">${categoryOptions(q.category)}</select>
    </div>
    <div class="form-group">
      <label>Level (1–5)</label>
      <select id="m-level">${levelOptions(q.level || 1)}</select>
    </div>
    <div class="form-group">
      <label>Question Text</label>
      <input type="text" id="m-qtext" value="${q.question_text}" />
    </div>
    <div class="form-group">
      <label>Answer</label>
      <input type="text" id="m-answer" value="${q.answer_text || ''}" />
    </div>
  `, async () => {
    const text   = document.getElementById("m-qtext").value.trim();
    const answer = document.getElementById("m-answer").value.trim();
    if (!text)   { showToast("Question text is required.", "error"); return; }
    if (!answer) { showToast("Answer is required.", "error"); return; }
    const langId = parseInt(document.getElementById("m-lang").value);
    const level  = parseInt(document.getElementById("m-level").value);
    const cat    = document.getElementById("m-cat").value;
    setLoading(true);
    try {
      await sbUpdate("questions", id, "question_id", {
        language_id: langId, question_text: text, answer_text: answer, level, category: cat
      });
      q.language_id   = langId;
      q.question_text = text;
      q.answer_text   = answer;
      q.level         = level;
      q.category      = cat;
      closeModal();
      renderQuestions(document.getElementById("search-questions").value);
      renderDashboard();
      showToast("Question updated!");
    } catch (e) {
      showToast("Error updating question: " + e.message, "error");
    } finally {
      setLoading(false);
    }
  });
}

async function deleteQuestion(id) {
  if (!confirm("Delete this question?")) return;
  setLoading(true);
  try {
    await sbDelete("questions", id, "question_id");
    const i = DB.questions.findIndex(x => x.question_id === id);
    if (i !== -1) DB.questions.splice(i, 1);
    renderQuestions(document.getElementById("search-questions").value);
    renderDashboard();
    showToast("Question deleted.");
  } catch (e) {
    showToast("Error deleting question: " + e.message, "error");
  } finally {
    setLoading(false);
  }
}

// ============================================================
// RENDER LANGUAGES
// ============================================================
function renderLanguages() {
  document.getElementById("lang-tbody").innerHTML = DB.languages.map(l => {
    const count = DB.questions.filter(q => q.language_id === l.language_id).length;
    return `
      <tr>
        <td>${l.language_id}</td>
        <td>${l.language_name}</td>
        <td><span class="badge-lang">${count} question${count !== 1 ? "s" : ""}</span></td>
        <td>
          <button class="btn-edit"   onclick="editLanguage(${l.language_id})"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn-delete" onclick="deleteLanguage(${l.language_id})"><i class="bi bi-trash"></i> Delete</button>
        </td>
      </tr>
    `;
  }).join("");
}

function addLanguage() {
  openModal("Add Language", `
    <div class="form-group">
      <label>Language Name</label>
      <input type="text" id="m-langname" placeholder="e.g. Korean" />
    </div>
  `, async () => {
    const name = document.getElementById("m-langname").value.trim();
    if (!name) { showToast("Language name is required.", "error"); return; }
    setLoading(true);
    try {
      const [created] = await sbInsert("languages", { language_name: name });
      DB.languages.push({ language_id: created.language_id, language_name: name });
      closeModal();
      renderLanguages();
      renderDashboard();
      showToast("Language added!");
    } catch (e) {
      showToast("Error adding language: " + e.message, "error");
    } finally {
      setLoading(false);
    }
  });
}

function editLanguage(id) {
  const l = DB.languages.find(x => x.language_id === id);
  if (!l) return;
  openModal("Edit Language", `
    <div class="form-group">
      <label>Language Name</label>
      <input type="text" id="m-langname" value="${l.language_name}" />
    </div>
  `, async () => {
    const name = document.getElementById("m-langname").value.trim();
    if (!name) { showToast("Language name is required.", "error"); return; }
    setLoading(true);
    try {
      await sbUpdate("languages", id, "language_id", { language_name: name });
      l.language_name = name;
      closeModal();
      renderLanguages();
      renderQuestions(document.getElementById("search-questions").value);
      showToast("Language updated!");
    } catch (e) {
      showToast("Error updating language: " + e.message, "error");
    } finally {
      setLoading(false);
    }
  });
}

async function deleteLanguage(id) {
  const hasQ = DB.questions.some(q => q.language_id === id);
  if (hasQ) { showToast("Cannot delete — language has questions.", "error"); return; }
  if (!confirm("Delete this language?")) return;
  setLoading(true);
  try {
    await sbDelete("languages", id, "language_id");
    const i = DB.languages.findIndex(x => x.language_id === id);
    if (i !== -1) DB.languages.splice(i, 1);
    renderLanguages();
    renderDashboard();
    showToast("Language deleted.");
  } catch (e) {
    showToast("Error deleting language: " + e.message, "error");
  } finally {
    setLoading(false);
  }
}

// ============================================================
// RENDER USERS (players from flashcard game)
// ============================================================
function renderUsers(filter = "") {
  const list = DB.users.filter(u =>
    u.name.toLowerCase().includes(filter.toLowerCase()) ||
    (u.email || "").toLowerCase().includes(filter.toLowerCase())
  );

  document.getElementById("users-tbody").innerHTML = list.length
    ? list.map(u => `
      <tr>
        <td>${u.user_id}</td>
        <td>${u.name}</td>
        <td>${u.email || "—"}</td>
        <td>
          <button class="btn-edit"   onclick="editUser(${u.user_id})"><i class="bi bi-pencil"></i> Edit</button>
          <button class="btn-delete" onclick="deleteUser(${u.user_id})"><i class="bi bi-trash"></i> Delete</button>
        </td>
      </tr>
    `).join("")
    : `<tr><td colspan="4" style="text-align:center;color:#9ca3af;padding:20px;">No users found.</td></tr>`;
}

function editUser(id) {
  const u = DB.users.find(x => x.user_id === id);
  if (!u) return;
  openModal("Edit User", `
    <div class="form-group">
      <label>Full Name</label>
      <input type="text" id="m-name" value="${u.name}" />
    </div>
    <div class="form-group">
      <label>Email (optional)</label>
      <input type="email" id="m-email" value="${u.email || ''}" />
    </div>
  `, async () => {
    const name  = document.getElementById("m-name").value.trim();
    const email = document.getElementById("m-email").value.trim();
    if (!name) { showToast("Name is required.", "error"); return; }
    setLoading(true);
    try {
      await sbUpdate("players", id, "player_id", { name, email: email || null });
      u.name  = name;
      u.email = email;
      closeModal();
      renderUsers(document.getElementById("search-users").value);
      renderDashboard();
      showToast("User updated!");
    } catch (e) {
      showToast("Error updating user: " + e.message, "error");
    } finally {
      setLoading(false);
    }
  });
}

async function deleteUser(id) {
  if (!confirm("Delete this user? This will also delete all their game history.")) return;
  setLoading(true);
  try {
    await sbDelete("players", id, "player_id");
    const i = DB.users.findIndex(x => x.user_id === id);
    if (i !== -1) DB.users.splice(i, 1);
    renderUsers(document.getElementById("search-users").value);
    renderDashboard();
    showToast("User deleted.");
  } catch (e) {
    showToast("Error deleting user: " + e.message, "error");
  } finally {
    setLoading(false);
  }
}

// Users added from the game only — admin can view/edit/delete but not add
// (registrations happen in the game). So the "Add User" button opens a note:
function setupAddUserBtn() {
  document.getElementById("btn-add-user").addEventListener("click", () => {
    openModal("About Users", `
      <p style="color:#374151;font-size:0.95rem;line-height:1.6;">
        Users (players) register through the <strong>Flashcard Dojo game</strong>.<br><br>
        Here in the admin panel you can <strong>view, edit, and delete</strong> players,
        but new accounts must be created from the game's registration screen.
      </p>
    `, () => closeModal());
    document.getElementById("modal-save").textContent = "Got it";
  });
}

// ============================================================
// RENDER ALL
// ============================================================
function renderAll() {
  renderDashboard();
  renderQuestions();
  renderLanguages();
  renderUsers();
}

// ============================================================
// SETUP BUTTONS & EVENTS
// ============================================================
function setupEvents() {
  document.getElementById("modal-close").addEventListener("click",  closeModal);
  document.getElementById("modal-cancel").addEventListener("click", closeModal);
  document.getElementById("modal-overlay").addEventListener("click", e => {
    if (e.target === document.getElementById("modal-overlay")) closeModal();
  });

  document.getElementById("btn-add-question").addEventListener("click", addQuestion);
  document.getElementById("btn-add-language").addEventListener("click", addLanguage);

  document.getElementById("search-questions").addEventListener("input", e => {
    renderQuestions(e.target.value);
  });
  document.getElementById("search-users").addEventListener("input", e => {
    renderUsers(e.target.value);
  });

  // Refresh button (re-sync with Supabase)
  const refreshBtn = document.createElement("button");
  refreshBtn.className = "btn-add";
  refreshBtn.style.cssText = "background:#6b7280;margin-left:8px;";
  refreshBtn.innerHTML = `<i class="bi bi-arrow-clockwise"></i> Refresh`;
  refreshBtn.addEventListener("click", async () => {
    await loadAllData();
    renderAll();
    showToast("Data refreshed from Supabase!");
  });
  document.querySelector("#page-dashboard h2").after(refreshBtn);
}

// ============================================================
// INIT
// ============================================================
document.addEventListener("DOMContentLoaded", () => {
  setupLogin();
  setupNav();
  setupEvents();
  setupAddUserBtn();
});
