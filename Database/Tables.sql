-- Create database (optional)
CREATE DATABASE IF NOT EXISTS ScheduleDB;
USE ScheduleDB;

-- Table: Department
CREATE TABLE Department 
(
    Department_ID INT AUTO_INCREMENT PRIMARY KEY,
    Department_Name VARCHAR(255) NOT NULL
);
-- Table: Course
CREATE TABLE Course 
(
    Course_Code VARCHAR(20) PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Credit_Hours INT NOT NULL,
    Department_ID INT,
    FOREIGN KEY (Department_ID) REFERENCES Department(Department_ID)
        ON DELETE SET NULL ON UPDATE CASCADE
);
-- Table: Prerequisite
CREATE TABLE Prerequisite 
(
    Course_Code VARCHAR(20) NOT NULL,
    Prerequisite_Code VARCHAR(20) NULL,
    PRIMARY KEY (Course_Code, Prerequisite_Code),
    FOREIGN KEY (Course_Code) REFERENCES Course(Course_Code)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Prerequisite_Code) REFERENCES Course(Course_Code)
        ON DELETE CASCADE ON UPDATE CASCADE
);
-- Table: Lecturer
CREATE TABLE Lecturer 
(
    Lecturer_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Department_ID INT,
    FOREIGN KEY (Department_ID) REFERENCES Department(Department_ID)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: Tutor
CREATE TABLE Tutor 
(
    Tutor_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    Department_ID INT,
    FOREIGN KEY (Department_ID) REFERENCES Department(Department_ID)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: Section
CREATE TABLE Section 
(
    Section_ID INT AUTO_INCREMENT PRIMARY KEY,
    Max_Students INT NOT NULL
);


-- Table: Lecture
CREATE TABLE Lecture 
( 
    Lecture_ID INT AUTO_INCREMENT PRIMARY KEY,
    Section_ID INT,
    FOREIGN KEY (Section_ID) REFERENCES Section(Section_ID)
        ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table: SectionTime
CREATE TABLE SectionTime 
(
    SectionTime_ID INT AUTO_INCREMENT PRIMARY KEY,
    Section_ID INT,
    Day_of_Week VARCHAR(10) NOT NULL,
    Start_Time TIME NOT NULL,
    End_Time TIME NOT NULL,
    Room VARCHAR(50),
    FOREIGN KEY (Section_ID) REFERENCES Section(Section_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: LectureTime
CREATE TABLE LectureTime 
(
    LectureTime_ID INT AUTO_INCREMENT PRIMARY KEY,
    Lecture_ID INT,
    Day_of_Week VARCHAR(10) NOT NULL,
    Start_Time TIME NOT NULL,
    End_Time TIME NOT NULL,
    Room VARCHAR(50),
    FOREIGN KEY (Lecture_ID) REFERENCES Lecture(Lecture_ID)
        ON DELETE CASCADE ON UPDATE CASCADE
);


-- Table: LecturerCourse
CREATE TABLE LecturerCourse 
(
    Lecturer_ID INT,
    Course_Code VARCHAR(20),
    Course_Lecture_ID INT,
    PRIMARY KEY (Lecturer_ID, Course_Code, Course_Lecture_ID),
    FOREIGN KEY (Course_Lecture_ID) REFERENCES Lecture(Lecture_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Lecturer_ID) REFERENCES Lecturer(Lecturer_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Course_Code) REFERENCES Course(Course_Code)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: TutorCourse
CREATE TABLE TutorCourse 
(
    Tutor_ID INT,
    Course_Code VARCHAR(20),
    Course_Section_ID INT,
    PRIMARY KEY (Tutor_ID, Course_Code, Course_Section_ID),
    Foreign Key (Course_Section_ID) REFERENCES Section(Section_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Tutor_ID) REFERENCES Tutor(Tutor_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Course_Code) REFERENCES Course(Course_Code)
        ON DELETE CASCADE ON UPDATE CASCADE
);


-- Table: Student
CREATE TABLE Student 
(
    Student_ID INT AUTO_INCREMENT PRIMARY KEY,
    Name VARCHAR(255) NOT NULL,
    PIN VARCHAR(255) NOT NULL,
    Term INT NOT NULL,
    Department_ID INT,
    FOREIGN KEY (Department_ID) REFERENCES Department(Department_ID)
        ON DELETE SET NULL ON UPDATE CASCADE
);


-- Table: CompletedCourses
CREATE TABLE CompletedCourses 
(
    Student_ID INT,
    Course_Code VARCHAR(20),
    Completion_Date DATE NOT NULL,
    Grade VARCHAR(10),
    PRIMARY KEY (Student_ID, Course_Code),
    FOREIGN KEY (Student_ID) REFERENCES Student(Student_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Course_Code) REFERENCES Course(Course_Code)
        ON DELETE CASCADE ON UPDATE CASCADE
);

-- Table: Enroll
CREATE TABLE Enrollment 
(
    Student_ID INT,
    Course_Code VARCHAR(20),
    Enrollment_Date DATE NOT NULL,
    Grade VARCHAR(10),
    LectureTime_ID INT NULL,
    SectionTime_ID INT NULL,
    PRIMARY KEY (Student_ID, Course_Code),
    FOREIGN KEY (Student_ID) REFERENCES Student(Student_ID)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (Course_Code) REFERENCES Course(Course_Code)
        ON DELETE CASCADE ON UPDATE CASCADE,
    FOREIGN KEY (LectureTime_ID) REFERENCES LectureTime(LectureTime_ID)
        ON DELETE SET NULL ON UPDATE CASCADE,
    FOREIGN KEY (SectionTime_ID) REFERENCES SectionTime(SectionTime_ID)
        ON DELETE SET NULL ON UPDATE CASCADE
);

CREATE TABLE Admin 
(
    Admin_ID INT PRIMARY KEY,
    PIN VARCHAR(255) NOT NULL
);