# Special Scientists Management System (ΕΕ)

## Overview
The Special Scientists Management System (ΕΕ) is a web-based application designed to manage special scientists working at TEPAK. The system streamlines the hiring process, enrollment, and automatic integration with the Moodle Learning Management System (LMS).

## Features
- **Admin Module**: User and system management.
- **Recruitment Module**: Special scientist hiring process.
- **Enrollment Module**: Automated registration to Moodle via API.

## Technologies Used
- **Backend**: PHP, MySQL/MariaDB
- **Frontend**: HTML, CSS, JavaScript
- **API**: Moodle integration

## Installation

### Prerequisites
- Web server (Apache/Nginx) with PHP support
- MySQL/MariaDB database
- Moodle installed locally or on a server
- Required PHP extensions: cURL, JSON, MySQLi

### Setup Steps
1. **Database Configuration**
   - Create a database named `scientists_db`.
   - Import the schema from `schema.sql`.

2. **Moodle Setup**
   - Install Moodle and configure API access.

3. **Application Deployment**
   - Upload project files to the server.
   - Edit `config.php` with database and Moodle API details.

## Usage
### Admin Module
- Manage system users and settings.
- View reports and statistics.

### Recruitment Module
- Add new scientist applications.
- Approve or reject applications.

### Enrollment Module
- Automatically enroll approved scientists in Moodle.
- Notify users upon successful enrollment.

## API Integration
The system connects with Moodle via API for automated user enrollment. Ensure API settings in Moodle are correctly configured to allow communication.
