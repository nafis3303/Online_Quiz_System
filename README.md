📘 Online Quiz System

An Online Quiz System developed using PHP, MySQL, HTML, CSS, JavaScript following role-based access control.
The system supports Student and Teacher users with separate functionalities.

👥 User Roles
🧑‍🎓 Student

Register and login

View available quizzes

Attempt quizzes (timer based)

View quiz results

Edit profile information

👨‍🏫 Teacher

Register and login

Create quizzes

Add, edit, delete questions

View student results per quiz
🧠 Technologies Used

Frontend: HTML, CSS, JavaScript

Backend: PHP (Procedural)

Database: MySQL

AJAX: Email availability check during registration

JSON: Used in AJAX response handling

Server: Apache (XAMPP)

⚙️ Setup Instructions

Install XAMPP

Start Apache and MySQL

Copy project folder to: C:\xampp\htdocs\
Import database:

Open phpMyAdmin

Create database: quizzers

Import quizzers.sql

Open browser and go to: http://localhost/your-project-folder/
🔐 Authentication Flow

Users login through login.php

After login:

Both Student and Teacher redirect to dashboard.php

Dashboard loads menu based on user role

Unauthorized access is blocked using session checks

🔄 AJAX & JSON Usage

AJAX used in check_email.php

Validates email availability during registration without page reload

Server response handled as text/JSON-style response
👨‍💻 Developers

Student, Login, Registration: Nafis

Teacher Module: Amit
