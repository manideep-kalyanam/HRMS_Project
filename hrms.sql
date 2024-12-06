show databases;
use mkalyanam1;

-- Users Table
CREATE TABLE hrms_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    firstname VARCHAR(100) NOT NULL,
    lastname VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'employee') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
desc hrms_users;

-- Admin Password - Admin@123
INSERT INTO hrms_users (firstname, lastname, email, password, role) 
VALUES ('Default', 'Admin', 'mk_admin@yopmail.com', '$2y$10$27m0zG9MQmXjF.tFcm.Yb.f7eUBpfA1US.SK7Fg72iTluUsH15j56', 'admin');
select * from hrms_users;

-- Employees Table
CREATE TABLE hrms_employees (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    department VARCHAR(100),
    joining_date DATE,
    contact_info TEXT,
    FOREIGN KEY (user_id) REFERENCES hrms_users(id)
);
desc hrms_employees;

-- Attendance Table
CREATE TABLE hrms_attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('present', 'absent', 'leave') DEFAULT NULL,
    remarks TEXT,
    UNIQUE KEY unique_attendance (employee_id, date), -- Prevent duplicate entries for the same day
    FOREIGN KEY (employee_id) REFERENCES hrms_employees(id)
);
desc hrms_attendance;

CREATE TABLE hrms_payroll (
    id INT AUTO_INCREMENT PRIMARY KEY,
    employee_id INT NOT NULL,
    basic_salary DECIMAL(10, 2) NOT NULL,
    allowances DECIMAL(10, 2) DEFAULT 0,
    deductions DECIMAL(10, 2) DEFAULT 0,
    net_salary DECIMAL(10, 2) GENERATED ALWAYS AS (basic_salary + allowances - deductions) STORED,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (employee_id) REFERENCES hrms_employees(id)
);
desc hrms_attendance;