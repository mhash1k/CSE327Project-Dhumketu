// renderers.js - All UI rendering functions

function renderHeader() {
    if (!isLoggedIn) return '';
    return `
        <header>
            <div class="header-container">
                <div class="branding">
                    <h1>School Management System | Demo</h1>
                </div>
                <nav>
                    <ul>
                        <li><a onclick="navigateTo('dashboard')">Home</a></li>
                        <li><a onclick="navigateTo('students')">Students</a></li>
                        <li><a onclick="navigateTo('teachers')">Teachers</a></li>
                        <li><a onclick="navigateTo('classes')">Classes</a></li>
                        <li><a onclick="navigateTo('reports')">Reports</a></li>
                        <li><a class="logout-btn" onclick="logout()">Logout (${currentUser?.username || 'Admin'})</a></li>
                    </ul>
                </nav>
            </div>
        </header>
    `;
}

function renderLogin() {
    const root = document.getElementById("app-root");
    root.innerHTML = `
        <div class="login-container">
            <h2>School Management System Login</h2>
            <div id="login-error" class="error-msg" style="display:none;"></div>
            <form id="login-form">
                <label>Username:</label>
                <input type="text" id="username" required>
                <label>Password:</label>
                <input type="password" id="password" required>
                <button type="submit">Login</button>
            </form>
            <p style="margin-top: 15px; text-align: center; color: #666;">Demo credentials: admin / admin123</p>
        </div>
    `;
    
    document.getElementById("login-form").addEventListener("submit", function(e) {
        e.preventDefault();
        const username = document.getElementById("username").value;
        const password = document.getElementById("password").value;
        const success = login(username, password);
        if (!success) {
            const errorDiv = document.getElementById("login-error");
            errorDiv.style.display = "block";
            errorDiv.innerText = "Invalid username or password";
        }
    });
}

function renderDashboard() {
    return `
        <div class="section-card">
            <h2>Dashboard</h2>
            <p>Welcome to the School Management System Demo. All data is stored in-memory for demonstration.</p>
            <div class="dashboard-grid">
                <div class="card" onclick="navigateTo('students')"><h3>Students</h3><p>Manage student records</p><button>Go</button></div>
                <div class="card" onclick="navigateTo('teachers')"><h3>Teachers</h3><p>Manage teachers</p><button>Go</button></div>
                <div class="card" onclick="navigateTo('classes')"><h3>Classes and Sections</h3><p>Organize classes and sections</p><button>Go</button></div>
                <div class="card" onclick="navigateTo('subjects')"><h3>Subjects</h3><p>Assign subjects to classes</p><button>Go</button></div>
                <div class="card" onclick="navigateTo('attendance')"><h3>Attendance</h3><p>Mark and view attendance</p><button>Go</button></div>
                <div class="card" onclick="navigateTo('exams')"><h3>Exams and Results</h3><p>Manage exams and marks</p><button>Go</button></div>
                <div class="card" onclick="navigateTo('fees')"><h3>Fees</h3><p>Fee records</p><button>Go</button></div>
                <div class="card" onclick="navigateTo('reports')"><h3>Reports</h3><p>SQL-like features</p><button>Go</button></div>
            </div>
        </div>
    `;
}

function renderStudents() {
    let filteredStudents = [...students];
    let rows = '';
    filteredStudents.forEach(s => {
        rows += `<tr>
                    <td>${s.student_id}</td>
                    <td>${s.name}</td>
                    <td>${s.roll_number || '-'}</td>
                    <td>${getClassName(s.class_id)}</td>
                    <td>${getSectionName(s.section_id)}</td>
                    <td>${s.guardian_name || '-'}</td>
                    <td>${s.contact_details || '-'}</td>
                    <td>
                        <button class="action-btn" onclick="editStudent(${s.student_id})">Edit</button>
                        <button class="action-btn delete-btn" onclick="deleteStudent(${s.student_id})">Delete</button>
                    </td>
                </tr>`;
    });
    if(students.length === 0) rows = `<tr><td colspan="8">No students found</td></tr>`;
    
    return `
        <div class="section-card">
            <h2>Students Management</h2>
            <p><strong>Total Students: ${students.length}</strong></p>
            <div class="inline-buttons">
                <button onclick="showAddStudentForm()">Add New Student</button>
            </div>
            <div style="margin: 15px 0;">
                <input type="text" id="studentSearchInput" placeholder="Search by name..." style="width: 250px;">
            </div>
            <table>
                <thead><tr><th>ID</th><th>Name</th><th>Roll No</th><th>Class</th><th>Section</th><th>Guardian</th><th>Contact</th><th>Actions</th></tr></thead>
                <tbody>${rows}</tbody>
            </table>
            <a class="back-link" onclick="navigateTo('dashboard')">Back to Dashboard</a>
        </div>
    `;
}

function renderTeachers() {
    let rows = '';
    teachers.forEach(t => {
        rows += `<tr>
                    <td>${t.teacher_id}</td>
                    <td>${t.name}</td>
                    <td>${t.subject_speciality || '-'}</td>
                    <td>${t.contact_details || '-'}</td>
                    <td>
                        <button class="action-btn" onclick="editTeacher(${t.teacher_id})">Edit</button>
                        <button class="action-btn delete-btn" onclick="deleteTeacher(${t.teacher_id})">Delete</button>
                    </td>
                </tr>`;
    });
    if(teachers.length === 0) rows = `<tr><td colspan="5">No teachers found</td></tr>`;
    return `<div class="section-card">
        <h2>Teachers Management</h2>
        <button onclick="addTeacher()">Add New Teacher</button>
        <table><thead><tr><th>ID</th><th>Name</th><th>Speciality</th><th>Contact</th><th>Actions</th></tr></thead><tbody>${rows}</tbody></table>
        <a class="back-link" onclick="navigateTo('dashboard')">Back to Dashboard</a>
    </div>`;
}

function renderClasses() {
    let rows = '';
    classes.forEach(c => {
        rows += `<tr>
                    <td>${c.class_id}</td>
                    <td>${c.class_name}</td>
                    <td>
                        <button class="action-btn" onclick="manageSections(${c.class_id})">Manage Sections</button>
                        <button class="action-btn delete-btn" onclick="deleteClass(${c.class_id})">Delete</button>
                    </td>
                </tr>`;
    });
    return `<div class="section-card">
        <h2>Classes and Sections</h2>
        <button onclick="addClass()">Add Class</button>
        <table><thead><tr><th>ID</th><th>Class Name</th><th>Actions</th></tr></thead><tbody>${rows}</tbody></table>
        <div id="sections-panel"></div>
        <a class="back-link" onclick="navigateTo('dashboard')">Back to Dashboard</a>
    </div>`;
}

function renderSubjects() {
    let rows = '';
    subjects.forEach(sub => {
        rows += `<tr><td>${sub.subject_id}</td><td>${sub.subject_name}</td><td>${sub.subject_code}</td>
                <td>${getClassName(sub.class_id)}</td><td>${getTeacherName(sub.teacher_id)}</td>
                <td><button class="delete-btn" onclick="deleteSubject(${sub.subject_id})">Delete</button></td></tr>`;
    });
    return `<div class="section-card"><h2>Subjects</h2><button onclick="addSubject()">Add Subject</button>
        <table><thead><tr><th>ID</th><th>Name</th><th>Code</th><th>Class</th><th>Teacher</th><th>Action</th></tr></thead><tbody>${rows}</tbody></table>
        <a class="back-link" onclick="navigateTo('dashboard')">Back to Dashboard</a></div>`;
}

function renderAttendance() {
    let classSelect = `<select id="attClassSelect" onchange="loadAttendanceStudents()"><option value="">Select Class</option>${classes.map(c => `<option value="${c.class_id}">${c.class_name}</option>`).join('')}</select>`;
    return `<div class="section-card"><h2>Mark Attendance</h2>${classSelect}<div id="studentAttendanceList"></div><a class="back-link" onclick="navigateTo('dashboard')">Back to Dashboard</a></div>`;
}

function renderExams() {
    let examRows = '';
    exams.forEach(e => {
        examRows += `<tr><td>${e.exam_id}</td><td>${e.exam_name}</td><td>${e.exam_date}</td>
                <td><button onclick="viewResults(${e.exam_id})">Manage Results</button><button class="delete-btn" onclick="deleteExam(${e.exam_id})">Delete</button></td></tr>`;
    });
    return `<div class="section-card"><h2>Exams</h2><button onclick="addExam()">Add Exam</button>
        <table><thead><tr><th>ID</th><th>Name</th><th>Date</th><th>Actions</th></tr></thead><tbody>${examRows}</tbody></table>
        <div id="resultsPanel"></div><a class="back-link" onclick="navigateTo('dashboard')">Back to Dashboard</a></div>`;
}

function renderFees() {
    let feeRows = '';
    fees.forEach(f => {
        let sName = students.find(st => st.student_id === f.student_id)?.name || "Unknown";
        feeRows += `<tr><td>${f.fee_id}</td><td>${sName}</td><td>$${f.amount}</td>
                <td>${f.due_date}</td><td>${f.status}</td>
                <td><button class="delete-btn" onclick="deleteFee(${f.fee_id})">Delete</button></td></tr>`;
    });
    return `<div class="section-card"><h2>Fee Management</h2><button onclick="addFee()">Add Fee Record</button>
        <table><thead><tr><th>ID</th><th>Student</th><th>Amount</th><th>Due Date</th><th>Status</th><th>Action</th></tr></thead><tbody>${feeRows}</tbody></table>
        <a class="back-link" onclick="navigateTo('dashboard')">Back to Dashboard</a></div>`;
}

function renderReports() {
    let classHaving = classes.filter(c => students.filter(s => s.class_id === c.class_id).length >= 2).map(c => `<tr><td>${c.class_name}</td><td>${students.filter(s => s.class_id === c.class_id).length}</td></tr>`).join('');
    let incomplete = students.filter(s => s.class_id === null || s.section_id === null).map(s => `<tr><td>${s.student_id}</td><td>${s.name}</td><td>${s.class_id===null?'Class ':''}${s.section_id===null?'Section':''}</td></tr>`).join('');
    let distinctClasses = [...new Map(students.filter(s=>s.class_id).map(s=>[s.class_id, getClassName(s.class_id)])).values()];
    return `<div class="section-card"><h2>SQL-Style Reports</h2>
        <h3>Classes with 2 or More Students (HAVING)</h3>
        <table><thead><tr><th>Class</th><th>Count</th></tr></thead><tbody>${classHaving||'<tr><td colspan="2">None found</td></tr>'}</tbody></table>
        <h3>Students with Incomplete Information (IS NULL)</h3>
        <table><thead><tr><th>ID</th><th>Name</th><th>Missing</th></tr></thead><tbody>${incomplete||'<tr><td colspan="3">All students have complete information</td></tr>'}</tbody></table>
        <h3>Distinct Classes with Students</h3><ul>${distinctClasses.map(c=>`<li>${c}</li>`).join('')}</ul>
        <a class="back-link" onclick="navigateTo('dashboard')">Back to Dashboard</a></div>`;
}

function renderApp() {
    const root = document.getElementById("app-root");
    if (!isLoggedIn) {
        renderLogin();
        return;
    }
    
    let content = "";
    if(currentView === "dashboard") content = renderDashboard();
    else if(currentView === "students") content = renderStudents();
    else if(currentView === "teachers") content = renderTeachers();
    else if(currentView === "classes") content = renderClasses();
    else if(currentView === "subjects") content = renderSubjects();
    else if(currentView === "attendance") content = renderAttendance();
    else if(currentView === "exams") content = renderExams();
    else if(currentView === "fees") content = renderFees();
    else if(currentView === "reports") content = renderReports();
    else content = renderDashboard();
    
    root.innerHTML = renderHeader() + `<div class="main-container">${content}</div>`;
    
    if (currentView === 'students') {
        setTimeout(() => {
            const searchInput = document.getElementById("studentSearchInput");
            if (searchInput) {
                searchInput.onkeyup = (e) => {
                    let val = e.target.value.toLowerCase();
                    let filtered = val ? students.filter(s => s.name.toLowerCase().includes(val)) : [...students];
                    let tbody = document.querySelector("#app-root table tbody");
                    if (tbody) {
                        let rows = '';
                        filtered.forEach(s => {
                            rows += `<tr>
                                <td>${s.student_id}</td>
                                <td>${s.name}</td>
                                <td>${s.roll_number || '-'}</td>
                                <td>${getClassName(s.class_id)}</td>
                                <td>${getSectionName(s.section_id)}</td>
                                <td>${s.guardian_name || '-'}</td>
                                <td>${s.contact_details || '-'}</td>
                                <td><button class="action-btn" onclick="editStudent(${s.student_id})">Edit</button>
                                <button class="action-btn delete-btn" onclick="deleteStudent(${s.student_id})">Delete</button></td>
                            </tr>`;
                        });
                        tbody.innerHTML = rows;
                    }
                };
            }
        }, 50);
    }
}