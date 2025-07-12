# dress_code
A web application for managing custom dress requests, built with PHP and MySQL. It features user registration with email verification, dress request submission, admin management, and feedback collection.

## Features

- User registration and login with email verification
- Submit custom dress requests with image upload
- Track request status (pending, accepted, delivered, received, rejected)
- Admin dashboard for managing requests and feedback
- Feedback form for user messages
- Dark mode support
- Email notifications using PHPMailer


## Project Structure

- `index.html` – Landing page
- `register.html`, `register.php` – User registration
- `login.html`, `login.php` – User login
- `verify.php` – Email verification
- `request.html`, `request.php` – Submit dress requests
- `view_request.php` – View and track your requests
- `admin.php` – Admin dashboard for managing requests
- `admin_feedback.php` – Admin feedback management
- `feedback.html`, `feedback.php`, `submit_feedback.php` – Feedback form and processing
- `config.php` – Database connection settings
- `dress_request_db.sql` – Database schema and sample data
- `PHPMailer/` – Email sending library
- CSS files for styling and dark mode


- ## Database Setup

1. Create the database and tables using the provided SQL script:

   ```sql
   -- In phpMyAdmin or MySQL CLI:
   SOURCE dress_request_db.sql;
   ```

2. The script creates:

   - `users` table (with admin user: `admin@gmail.com` / `admin123`)
   - `dress_requests` table
   - `feedback` table

3. Update `config.php` if your database credentials differ.


## Installation & Usage

1. **Clone the repository:**

   ```sh
   git clone https://github.com/yourusername/dress_code.git
   cd dress_code
   ```
   
2. **Set up the database** as described above.

3. **Configure PHPMailer:**

   - Update SMTP credentials in `register.php` for sending verification emails.
