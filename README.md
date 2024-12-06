# HRMS Web Application

This HRMS (Human Resource Management System) web application is designed to streamline and simplify common HR tasks, making it easier for both administrators and employees to manage essential information and daily operations. Featuring a clean UI, role-based access, and intuitive workflows, this system ensures that your organization’s HR processes are efficient, secure, and user-friendly.

## Key Features

- **Role-Based Dashboard**  
  Users log in as either an Admin or an Employee. The navigation and available features adapt dynamically based on their role:
  - **Admin**: Manage employees, attendance records, and payroll details. Gain direct access to add, edit, or remove employee records, update payroll information, and oversee attendance data with just a few clicks.
  - **Employee**: View personal attendance, payroll, and profile information. Update contact details and stay informed about monthly pay and attendance history.

- **User-Friendly Interface**  
  The application uses a consistent, responsive design ensuring a seamless experience across devices. Clear labels, two-column forms, and inline feedback enhance usability and minimize errors.

- **Efficient Data Management**  
  Utilizing a MySQL database (e.g., `hrms_users`, `hrms_employees`, `hrms_attendance`, `hrms_payroll`), all employee and HR-related data is stored securely. Admins can quickly update employee roles, contact details, and payroll amounts without leaving the dashboard.

- **Secure Authentication & Sessions**  
  A reliable login system ensures that only authorized users access sensitive information. Employees can log in with their credentials, and admins have elevated permissions. Password verification and role-based redirects help maintain data integrity.

- **Inline & Global Messaging**  
  Users receive instant feedback on actions. Whether it’s a success, error, or guidance message, popups or inline alerts provide a better user experience and reduce confusion.

- **Public-Facing Pages**  
  In addition to the protected HR functionalities, the application includes a Home, About, and Contact page—ideal for introducing the product to visitors, providing company information, and offering a way to reach out.

## Technology Stack

- **Frontend**: HTML, CSS (global styles and per-page overrides), JavaScript for client-side interactions and inline messaging.
- **Backend**: PHP for server-side processing, form handling, and session management.
- **Database**: MySQL (InnoDB engine) for storing user details, payroll records, attendance logs, and employee contact information.

## Getting Started

1. **Set Up the Database**  
   Run the provided SQL scripts to create the required tables. Update database credentials in the configuration file (`config/db.php`).

2. **Configure the Project**  
   Adjust file paths, CSS/JS references, and server settings as needed in `includes/header.php` and `includes/footer.php`.

3. **Run the Application**  
   Access `home.php` from a browser. Use `login.php` to sign in as an admin or employee. Once authenticated, test the functionality by viewing the dashboard, managing employees, or checking attendance.

With these elements in place, the HRMS web application serves as a solid foundation for handling day-to-day HR operations, improving transparency, efficiency, and employee satisfaction.
