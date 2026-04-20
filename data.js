// data.js - Mock Database with all data structures

let students = [
    { student_id: 1, name: "Emily Clarke", class_id: 1, section_id: 1, roll_number: 101, guardian_name: "John Clarke", guardian_phone: "555-0101", contact_details: "emily@example.com" },

];

let teachers = [
    { teacher_id: 1, name: "Dr. Alan Grant", subject_speciality: "Mathematics", contact_details: "alan.grant@school.com" },
   ];

let classes = [
    { class_id: 1, class_name: "Grade 10" },
    { class_id: 2, class_name: "Grade 11" },
    { class_id: 3, class_name: "Grade 12" }
];

let sections = [
    { section_id: 1, class_id: 1, section_name: "Section A", class_teacher_id: 1 },
    { section_id: 2, class_id: 1, section_name: "Section B", class_teacher_id: 2 },
    { section_id: 3, class_id: 2, section_name: "Section A", class_teacher_id: 3 },
    { section_id: 4, class_id: 3, section_name: "Science", class_teacher_id: 2 }
];

let subjects = [
    { subject_id: 1, subject_name: "Algebra", subject_code: "MATH101", class_id: 1, teacher_id: 1 },
    { subject_id: 2, subject_name: "Biology", subject_code: "SCI202", class_id: 2, teacher_id: 2 },
    { subject_id: 3, subject_name: "Literature", subject_code: "ENG303", class_id: 1, teacher_id: 3 }
];

let attendance = [
    { attendance_id: 1, student_id: 1, date: "2025-04-15", status: "Present" },
    { attendance_id: 2, student_id: 2, date: "2025-04-15", status: "Absent" },
    { attendance_id: 3, student_id: 1, date: "2025-04-14", status: "Late" }
];

let exams = [
    { exam_id: 1, exam_name: "Mid-Term Exams", exam_date: "2025-05-10" },
    { exam_id: 2, exam_name: "Final Exams", exam_date: "2025-07-20" }
];

let results = [
    { result_id: 1, exam_id: 1, student_id: 1, subject_id: 1, marks: 85 },
    { result_id: 2, exam_id: 1, student_id: 2, subject_id: 1, marks: 78 },
    { result_id: 3, exam_id: 1, student_id: 1, subject_id: 2, marks: 92 }
];

let fees = [
    { fee_id: 1, student_id: 1, amount: 500.00, due_date: "2025-06-01", status: "Paid" },
    { fee_id: 2, student_id: 2, amount: 500.00, due_date: "2025-06-01", status: "Unpaid" }
];

let nextStudentId = 7;
let nextTeacherId = 4;
let nextClassId = 4;
let nextSectionId = 5;
let nextSubjectId = 4;
let nextExamId = 3;
let nextFeeId = 3;
let nextAttendanceId = 4;

let isLoggedIn = false;
let currentUser = null;
let currentView = "dashboard";

function getClassName(classId) {
    if (!classId) return "Not Assigned";
    let cls = classes.find(c => c.class_id == classId);
    return cls ? cls.class_name : "Unknown";
}

function getSectionName(sectionId) {
    if (!sectionId) return "Not Assigned";
    let sec = sections.find(s => s.section_id == sectionId);
    return sec ? sec.section_name : "Unknown";
}

function getTeacherName(teacherId) {
    let t = teachers.find(tc => tc.teacher_id == teacherId);
    return t ? t.name : "Not Assigned";
}