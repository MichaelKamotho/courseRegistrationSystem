CREATE DATABASE IF NOT EXISTS online_courses;

USE online_courses;

CREATE TABLE IF NOT EXISTS applicants (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT NOT NULL
);
INSERT INTO courses (name, description) VALUES
('Introduction to Programming', 'Learn the basics of programming, including variables, control structures, and functions.'),
('Web Development Basics', 'An overview of HTML, CSS, and JavaScript for building responsive web pages.'),
('Data Science 101', 'Introduction to data science concepts, data analysis, and visualization techniques.'),
('Mobile App Development', 'Learn how to create mobile applications for Android and iOS platforms.'),
('Machine Learning Fundamentals', 'Explore the foundational concepts of machine learning and its applications.'),
('Cybersecurity Essentials', 'Understand the principles of cybersecurity, including risk management and security protocols.'),
('Database Management Systems', 'Learn about relational databases, SQL, and data modeling techniques.'),
('Cloud Computing Basics', 'An introduction to cloud computing services, architectures, and deployment models.'),
('User Experience (UX) Design', 'Explore the principles of UX design and create user-centered designs for web and mobile applications.'),
('Software Development Life Cycle', 'Gain insights into the various phases of software development, from planning to deployment and maintenance.');

CREATE TABLE IF NOT EXISTS applications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    applicant_id INT NOT NULL,
    course_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (applicant_id) REFERENCES applicants(id),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);
