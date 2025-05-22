let lectureSchedulesData = {};
let sectionSchedulesData = {};
let StudentID;
let addedSubjects = [];
const maxSubjects = 6;

const request = new Request("GetCourses.php", { method: "GET"});

async function getCourses() {
    try 
    {
        const response = await fetch(request);
        if (!response.ok) 
        {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();
        const { lectureSchedules, sectionSchedules, studentID } = data;

        lectureSchedulesData = lectureSchedules;
        sectionSchedulesData = sectionSchedules;
        StudentID = studentID;

        console.log("Lecture Schedules:", lectureSchedulesData);
        console.log("Section Schedules:", sectionSchedulesData);
        console.log("Student ID:", StudentID);

        // Get unique subjects
        const subjects = [...new Set(lectureSchedules.map(l => l.Name))];
        populateSubjects(subjects);
    } 
    catch (error) 
    {
        console.error('Fetch error:', error);
    }
}
getCourses();




const request2 = new Request("loadEnrolled.php", { method: "GET"});

async function getCourses2() {
    try 
    {
        const response2 = await fetch(request2);
        if (!response2.ok) 
        {
            throw new Error(`HTTP error! status: ${response2.status}`);
        }
        const data2 = await response2.json();
        const { lectures, sections } = data2;
        console.log(data2);
        console.log("Lecture Schedules:", lectures);
        console.log("Section Schedules:", sections);


        LoadSubjectToSchedule(lectures, sections)


    } 
    catch (error) 
    {
        console.error('Fetch error:', error);
    }
}
getCourses2();



function LoadSubjectToSchedule(lectures, sections) 
{
    lectures.forEach(lecture => {
        const subject = lecture.Course_Code;
        const time = `${lecture.Day_of_Week} - ${lecture.Start_Time} - ${lecture.End_Time}`;
        const lecturer = lecture.Lecturer_Name;

        // Find corresponding section for the same course
        const section = sections.find(sec => sec.Course_Code === subject);
        if (!section) return;

        const sectionTime = `${section.Day_of_Week} - ${section.Start_Time} - ${section.End_Time}`;
        const tutor = section.Tutor_Name;

        const lectureInfo = { subject, timeslot: time, type: 'Lecture', lecturer };
        const sectionInfo = { subject, timeslot: sectionTime, type: 'Section', tutor };

        // Remove old entry if subject already exists
        if (addedSubjects.some(s => s.subject === subject)) {
            removeSubjectFromTable(subject);
            addedSubjects = addedSubjects.filter(s => s.subject !== subject);
        }

        // Check for conflicts
        if (isConflict(lectureInfo) || isConflict(sectionInfo)) {
            showAlert(`Time conflict with ${subject}. Please choose another time.`, 'danger');
            return;
        }

        insertToTable(lectureInfo);
        insertToTable(sectionInfo);
        addedSubjects.push({ subject, lecture: lectureInfo, section: sectionInfo });
    });

    if (addedSubjects.length === maxSubjects) 
    {
        document.getElementById('subjectSelect').disabled = true;
        document.getElementById('addSubjectBtn').disabled = true;
        const changeBtn = document.createElement('button');
        changeBtn.className = 'btn btn-warning mt-3';
        changeBtn.textContent = 'Change Registration';
        changeBtn.id = "btnn";
        changeBtn.onclick = resetSchedule;

        const container = document.getElementById('changeContainer');
        container.appendChild(changeBtn);
        showAlert('Loaded Schedule successfully', 'success');
    }
}





function populateSubjects(subjects) 
{
    const subjectSelect = document.getElementById('subjectSelect');
    subjects.forEach(subject => {
        const option = document.createElement('option');
        option.value = subject;
        option.textContent = subject;
        subjectSelect.appendChild(option);
    });
}

function populateLecturers() 
{
    const subject = document.getElementById('subjectSelect').value;
    const lecturerSelect = document.getElementById('lecturerSelect');
    lecturerSelect.innerHTML = '<option selected disabled>Select Lecturer</option>';
    lecturerSelect.classList.remove('hidden');

    document.getElementById('lectureTimeSelect').classList.add('hidden');
    document.getElementById('tutorSelect').classList.add('hidden');
    document.getElementById('SectionTimeSelect').classList.add('hidden');
    document.getElementById('addSubjectBtn').disabled = true;

    const lecturers = [...new Set(lectureSchedulesData.filter(l => l.Name === subject).map(l => l.Lecturer_Name))];

    lecturers.forEach(lecturer => {
        const option = document.createElement('option');
        option.value = lecturer;
        option.textContent = lecturer;
        lecturerSelect.appendChild(option);
    });
}

function populateLectureTimes() 
{
    const subject = document.getElementById('subjectSelect').value;
    const lecturer = document.getElementById('lecturerSelect').value;
    const lectureTimeSelect = document.getElementById('lectureTimeSelect');
    lectureTimeSelect.innerHTML = '<option selected disabled>Select Lecture Time</option>';
    lectureTimeSelect.classList.remove('hidden');

    document.getElementById('tutorSelect').classList.add('hidden');
    document.getElementById('SectionTimeSelect').classList.add('hidden');
    document.getElementById('addSubjectBtn').disabled = true;

    const times = [...new Set(lectureSchedulesData.filter(l =>
        l.Name === subject && l.Lecturer_Name === lecturer
    ).map(l => `${l.Day_of_Week} - ${l.Start_Time} - ${l.End_Time}`))];

    times.forEach(timeSlot => {
        const option = document.createElement('option');
        option.value = timeSlot;
        option.textContent = timeSlot;
        lectureTimeSelect.appendChild(option);
    });
}

function populateTutors() 
{
    const subject = document.getElementById('subjectSelect').value;
    const tutorSelect = document.getElementById('tutorSelect');
    tutorSelect.innerHTML = '<option selected disabled>Select Tutor</option>';
    tutorSelect.classList.remove('hidden');

    document.getElementById('SectionTimeSelect').classList.add('hidden');
    document.getElementById('addSubjectBtn').disabled = true;

    const tutors = [...new Set(sectionSchedulesData.filter(s => s.Name === subject).map(s => s.Tutor_Name))];

    tutors.forEach(tutor => {
        const option = document.createElement('option');
        option.value = tutor;
        option.textContent = tutor;
        tutorSelect.appendChild(option);
    });
}

function populateTutorTimes() 
{
    const subject = document.getElementById('subjectSelect').value;
    const tutor = document.getElementById('tutorSelect').value;
    const sectionTimeSelect = document.getElementById('SectionTimeSelect');
    sectionTimeSelect.innerHTML = '<option selected disabled>Select Section Time</option>';
    sectionTimeSelect.classList.remove('hidden');

    const times = [...new Set(sectionSchedulesData.filter(s =>
        s.Name === subject && s.Tutor_Name === tutor
    ).map(s => `${s.Day_of_Week} - ${s.Start_Time} - ${s.End_Time}`))];

    times.forEach(timeSlot => {
        const option = document.createElement('option');
        option.value = timeSlot;
        option.textContent = timeSlot;
        sectionTimeSelect.appendChild(option);
    });
}

function addSubjectToSchedule() 
{
    const subject = document.getElementById('subjectSelect').value;
    const lecturer = document.getElementById('lecturerSelect').value;
    const lectureTime = document.getElementById('lectureTimeSelect').value;
    const tutor = document.getElementById('tutorSelect').value;
    const sectionTime = document.getElementById('SectionTimeSelect').value;

    if (!subject || !lecturer || !lectureTime || !tutor || !sectionTime) return;

    const lectureInfo = { subject, timeslot: lectureTime, type: 'Lecture', lecturer };
    const sectionInfo = { subject, timeslot: sectionTime, type: 'Section', tutor };

    // Remove old entry if subject already exists
    if (addedSubjects.some(s => s.subject === subject)) {
        removeSubjectFromTable(subject);
        addedSubjects = addedSubjects.filter(s => s.subject !== subject);
    }

    // Check for conflicts
    if (isConflict(lectureInfo) || isConflict(sectionInfo)) {
        showAlert('Time conflict with existing subject. Please choose another time.', 'danger');
        return;
    }

    insertToTable(lectureInfo);
    insertToTable(sectionInfo);
    addedSubjects.push({ subject, lecture: lectureInfo, section: sectionInfo });

    // Reset selects
    ['lecturerSelect', 'lectureTimeSelect', 'tutorSelect', 'SectionTimeSelect'].forEach(id => {
        const el = document.getElementById(id);
        if (el.tagName === 'SELECT') el.selectedIndex = 0;
        el.classList.add('hidden');
    });

    document.getElementById('addSubjectBtn').disabled = true;
    document.getElementById('subjectSelect').disabled = false;
    document.getElementById('subjectSelect').selectedIndex = 0;

    // If max subjects reached
    if (addedSubjects.length === maxSubjects) {
        document.getElementById('subjectSelect').disabled = true;
        document.getElementById('confirmBtn').style.display = 'inline-block';
        document.getElementById('confirmBtn').classList.add('fadeIn');
        showAlert('All 6 subjects added. You can now confirm your registration.', 'success');
    }
}

function isConflict(scheduleInfo) 
{
    const [day, startTime] = scheduleInfo.timeslot.split(' - ');
    const row = [...document.getElementById('scheduleBody').rows].find(r => r.cells[0].innerText === day);
    const col = timeToColumnIndex(startTime);
    if (!row || col === -1) return false;

    const cell = row.cells[col];
    if (cell.innerText.trim() !== '') {
        cell.classList.add('shake');
        setTimeout(() => cell.classList.remove('shake'), 500);
        return true;
    }
    return false;
}

function insertToTable({ subject, timeslot, type, lecturer, tutor }) 
{
    const [day, startTime] = timeslot.split(' - ');
    const row = [...document.getElementById('scheduleBody').rows].find(r => r.cells[0].innerText === day);
    const col = timeToColumnIndex(startTime);
    if (!row || col === -1) return;

    const instructor = type === 'Lecture' ? lecturer : tutor;
    row.cells[col].innerHTML = `${subject}<br><small>${type} (${instructor})</small>`;
    row.cells[col].classList.add('subject', 'pop-in');
    setTimeout(() => row.cells[col].classList.remove('pop-in'), 2000);
}

function timeToColumnIndex(time) 
{

    const timeMap = {
        "08:00:00": 1,
        "10:00:00": 2,
        "12:00:00": 3,
        "14:00:00": 4,
        "16:00:00": 5
    };
    return timeMap[time] ?? -1;
}

function showAlert(message, type) 
{
    const placeholder = document.getElementById('alertPlaceholder');
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show`;
    alert.innerHTML = `${message}<button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
    placeholder.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}

function removeSubjectFromTable(subject) 
{
    document.querySelectorAll('#scheduleBody tr').forEach(row => {
        [...row.cells].slice(1).forEach(cell => {
            if (cell.innerText.includes(subject)) {
                cell.innerHTML = '';
                cell.classList.remove('subject');
            }
        });
    });
}

function finalize() 
{
    document.getElementById('confirmBtn').style.display = 'none';
    showAlert('Registration confirmed!', 'success');

    const changeBtn = document.createElement('button');
    changeBtn.className = 'btn btn-warning mt-3';
    changeBtn.textContent = 'Change Registration';
    changeBtn.id = "btnn";
    changeBtn.onclick = resetSchedule;

    const container = document.getElementById('changeContainer');
    container.appendChild(changeBtn);

    const selectedLectureSubjects = [];
    const selectedSectionSubjects = [];

    // Process each added subject
    addedSubjects.forEach(subjectObj => {
        const subjectName = subjectObj.subject;
        const lectureInfo = subjectObj.lecture;
        const sectionInfo = subjectObj.section;

        // Find matching lecture in lectureSchedulesData
        const [lectureDay, lectureStart, lectureEnd] = lectureInfo.timeslot.split(' - ');
        const matchingLecture = lectureSchedulesData.find(lecture => 
            lecture.Name === subjectName &&
            lecture.Lecturer_Name === lectureInfo.lecturer &&
            lecture.Day_of_Week === lectureDay &&
            lecture.Start_Time === lectureStart &&
            lecture.End_Time === lectureEnd
        );
        
        if (matchingLecture) {
            selectedLectureSubjects.push(matchingLecture);
        }

        // Find matching section in sectionSchedulesData
        const [sectionDay, sectionStart, sectionEnd] = sectionInfo.timeslot.split(' - ');
        const matchingSection = sectionSchedulesData.find(section => 
            section.Name === subjectName &&
            section.Tutor_Name === sectionInfo.tutor &&
            section.Day_of_Week === sectionDay &&
            section.Start_Time === sectionStart &&
            section.End_Time === sectionEnd
        );
        
        if (matchingSection) {
            selectedSectionSubjects.push(matchingSection);
        }
    });

    console.log(selectedLectureSubjects); //TODO
    console.log(selectedSectionSubjects);
    console.log(StudentID);


    const url = 'reciever.php';

    const options = 
    {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            studentID: StudentID,
            lectureArray: selectedLectureSubjects,
            sectionArray: selectedSectionSubjects,
            })
    };

    const request = new Request(url, options);

    async function sendRegistrationData() 
    {
        try 
        {
            const response = await fetch(request);

            if (!response.ok) 
            {
                const text = await response.text();
                console.error('Non-OK response:', text);
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            const data = await response.json();
            const type = data.success ? 'success' : 'danger';
            showAlert(data.success ? 'Registration saved!' : 'Error: ' + (data.message || 'Unknown'), type);
        } 
        catch (error) 
        {
            //showAlert('Failed to save registration.', 'danger');
        }   
    }

    sendRegistrationData();
}


function resetSchedule() {
    addedSubjects = [];

    document.querySelectorAll('#scheduleBody tr').forEach(row => {
        [...row.cells].slice(1).forEach(cell => {
            cell.innerHTML = '';
            cell.classList.remove('subject');
        });
    });

    document.getElementById('subjectSelect').disabled = false;
    document.getElementById('subjectSelect').selectedIndex = 0;
    ['lecturerSelect', 'lectureTimeSelect', 'tutorSelect', 'SectionTimeSelect'].forEach(id => {
        document.getElementById(id).classList.add('hidden');
    });
    document.getElementById('addSubjectBtn').disabled = true;
    document.getElementById('confirmBtn').style.display = 'none';

    // Remove change button if it exists
    const changeBtn = document.getElementById('btnn');
    if (changeBtn) {
        changeBtn.remove();
    }
}
