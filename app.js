// app.js - Application logic, navigation, and event handlers

function login(username, password) {
    if (username === "admin" && password === "admin123") {
        isLoggedIn = true;
        currentUser = { username: "admin", role: "Admin" };
        currentView = "dashboard";
        renderApp();
        return true;
    }
    return false;
}

function logout() {
    isLoggedIn = false;
    currentUser = null;
    currentView = "login";
    renderLogin();
}

function navigateTo(view) {
    if (!isLoggedIn && view !== 'login') {
        renderLogin();
        return;
    }
    currentView = view;
    renderApp();
}

function editStudent(id) {
    let student = students.find(s => s.student_id === id);
    if(!student) return;
    let newName = prompt("Edit Name:", student.name);
    if(newName) student.name = newName;
    let newGuardian = prompt("Guardian Name:", student.guardian_name);
    if(newGuardian !== null) student.guardian_name = newGuardian;
    renderApp();
}

function deleteStudent(id) {
    if(confirm("Delete student? This action is irreversible.")) {
        students = students.filter(s => s.student_id !== id);
        renderApp();
    }
}

function showAddStudentForm() {
    let name = prompt("Student Name:");
    if(!name) return;
    let newId = nextStudentId++;
    students.push({
        student_id: newId, name: name, class_id: null, section_id: null, roll_number: null,
        guardian_name: prompt("Guardian Name:") || "", guardian_phone: "", contact_details: ""
    });
    renderApp();
}

function editTeacher(id) {
    let t = teachers.find(tc => tc.teacher_id === id);
    if(t) { t.name = prompt("New Name:", t.name) || t.name; renderApp(); }
}

function deleteTeacher(id) {
    teachers = teachers.filter(t => t.teacher_id !== id);
    renderApp();
}

function addTeacher() {
    let name = prompt("Teacher Name:");
    if(name) teachers.push({ teacher_id: nextTeacherId++, name: name, subject_speciality: "", contact_details: "" });
    renderApp();
}

function manageSections(classId) {
    let classSections = sections.filter(s => s.class_id === classId);
    let secRows = '';
    classSections.forEach(sec => {
        secRows += `<tr><td>${sec.section_id}</td><td>${sec.section_name}</td><td>${getTeacherName(sec.class_teacher_id)}</td>
        <td><button class="delete-btn" onclick="deleteSection(${sec.section_id}, ${classId})">Delete</button></td></tr>`;
    });
    let newSec = prompt("Add new section name (or cancel):");
    if(newSec) {
        sections.push({ section_id: nextSectionId++, class_id: classId, section_name: newSec, class_teacher_id: null });
        renderApp();
        setTimeout(() => manageSections(classId), 50);
    } else {
        alert(`Sections for class ${getClassName(classId)}:\n${classSections.map(s => s.section_name).join(", ")}`);
    }
}

function deleteSection(secId, classId) {
    sections = sections.filter(s => s.section_id !== secId);
    renderApp();
}

function addClass() {
    let name = prompt("Class Name:");
    if(name) classes.push({ class_id: nextClassId++, class_name: name });
    renderApp();
}

function deleteClass(id) {
    classes = classes.filter(c => c.class_id !== id);
    sections = sections.filter(s => s.class_id !== id);
    renderApp();
}

function addSubject() {
    let name = prompt("Subject Name:");
    if(name) subjects.push({ subject_id: nextSubjectId++, subject_name: name, subject_code: "CODE", class_id: null, teacher_id: null });
    renderApp();
}

function deleteSubject(id) {
    subjects = subjects.filter(s => s.subject_id !== id);
    renderApp();
}

function loadAttendanceStudents() {
    let classId = document.getElementById("attClassSelect").value;
    if(!classId) return;
    let filtered = students.filter(s => s.class_id == classId);
    let html = `<form id="attForm"><label>Date: <input type="date" id="attDate" value="2025-04-20"></label>
        <table><thead><tr><th>Student</th><th>Status</th></tr></thead><tbody>`;
    filtered.forEach(s => {
        let existing = attendance.find(a => a.student_id === s.student_id && a.date === document.getElementById("attDate")?.value);
        let status = existing ? existing.status : 'Present';
        html += `<tr><td>${s.name}</td>
                <td><select name="status_${s.student_id}">
                    <option ${status==='Present'?'selected':''}>Present</option>
                    <option ${status==='Absent'?'selected':''}>Absent</option>
                    <option ${status==='Late'?'selected':''}>Late</option>
                </select></td></tr>`;
    });
    html += `</tbody></table><button type="button" onclick="saveAttendance(${classId})">Save Attendance</button></form>`;
    document.getElementById("studentAttendanceList").innerHTML = html;
}

function saveAttendance(classId) {
    let date = document.getElementById("attDate").value;
    let studentsInClass = students.filter(s => s.class_id == classId);
    studentsInClass.forEach(s => {
        let select = document.querySelector(`select[name="status_${s.student_id}"]`);
        if(select) {
            let status = select.value;
            let existingIndex = attendance.findIndex(a => a.student_id === s.student_id && a.date === date);
            if(existingIndex !== -1) attendance[existingIndex].status = status;
            else attendance.push({ attendance_id: nextAttendanceId++, student_id: s.student_id, date: date, status: status });
        }
    });
    alert("Attendance saved!");
    renderApp();
}

function viewResults(examId) {
    let examResults = results.filter(r => r.exam_id === examId);
    let stats = {};
    examResults.forEach(r => { if(!stats[r.subject_id]) stats[r.subject_id] = []; stats[r.subject_id].push(r.marks); });
    let statHtml = `<h3>Exam Results Statistics (Min, Max, Avg, Count)</h3>
        <table><thead><tr><th>Subject</th><th>Students</th><th>Min</th><th>Max</th><th>Avg</th></tr></thead><tbody>`;
    for(let subId in stats) {
        let sub = subjects.find(s => s.subject_id == subId);
        let marksArr = stats[subId];
        let minVal = Math.min(...marksArr), maxVal = Math.max(...marksArr), avgVal = marksArr.reduce((a,b)=>a+b,0)/marksArr.length;
        statHtml += `<tr><td>${sub?.subject_name || subId}</td>
                <td>${marksArr.length}</td>
                <td>${minVal}</td>
                <td>${maxVal}</td>
                <td>${avgVal.toFixed(2)}</tr>`;
    }
    statHtml += `</tbody></table><button onclick="addResultPrompt(${examId})">Add Result</button>`;
    document.getElementById("resultsPanel").innerHTML = statHtml;
}

function addExam() {
    let name = prompt("Exam Name:");
    if(name) exams.push({ exam_id: nextExamId++, exam_name: name, exam_date: "2025-06-01" });
    renderApp();
}

function deleteExam(id) {
    exams = exams.filter(e => e.exam_id !== id);
    results = results.filter(r => r.exam_id !== id);
    renderApp();
}

function addResultPrompt(examId) {
    let studentId = prompt("Student ID:");
    let subjectId = prompt("Subject ID:");
    let marks = prompt("Marks:");
    if(studentId && subjectId && marks) {
        results.push({ result_id: Date.now(), exam_id: examId, student_id: parseInt(studentId), subject_id: parseInt(subjectId), marks: parseInt(marks) });
    }
    viewResults(examId);
}

function addFee() {
    let sid = prompt("Student ID:");
    let amt = prompt("Amount:");
    if(sid && amt) fees.push({ fee_id: nextFeeId++, student_id: parseInt(sid), amount: parseFloat(amt), due_date: "2025-07-01", status: "Unpaid" });
    renderApp();
}

function deleteFee(id) {
    fees = fees.filter(f => f.fee_id !== id);
    renderApp();
}

renderLogin();