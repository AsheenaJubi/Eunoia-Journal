# ✦ EUNOIA – Personal Journal & Memory Management

EUNOIA is a modern PHP + MySQL personal journal and memory management web application created as a college Web Development / Web Technology project.

> **“A quiet place for your thoughts.”**

## 1. Features

- User registration and secure login
- PHP sessions and protected pages
- Password hashing with `password_hash()`
- Password verification with `password_verify()`
- Create, read, edit and delete journal entries
- Six journal moods
- Image upload for memories
- JPG, JPEG, PNG and WEBP validation
- 5 MB journal image limit
- Personal memory gallery
- JavaScript image lightbox
- Search and mood filtering
- Dynamic dashboard statistics
- Profile editing
- Profile image upload
- Password change
- Responsive mobile navigation
- Client/server-side validation
- Prepared SQL statements
- Output escaping with `htmlspecialchars()`

## 2. Technologies

- HTML5
- CSS3
- JavaScript
- PHP 8+ recommended
- MySQL / MariaDB
- XAMPP
- Font Awesome
- Google Fonts

No React, Bootstrap, Tailwind, Laravel, Node.js or other frameworks are required.

## 3. Folder Structure

```text
eunoia/
├── index.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php
├── memories.php
├── profile.php
├── create_entry.php
├── edit_entry.php
├── delete_entry.php
├── config/
│   └── database.php
├── includes/
│   ├── header.php
│   ├── footer.php
│   └── auth.php
├── css/
│   └── style.css
├── js/
│   └── script.js
├── uploads/
│   └── .gitkeep
├── database/
│   └── eunoia.sql
└── README.md
```

## 4. XAMPP Setup

1. Install XAMPP.
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Copy the complete `eunoia` folder into:

```text
C:\xampp\htdocs\
```

The final location should be:

```text
C:\xampp\htdocs\eunoia\
```

## 5. Database Setup

1. Open:

```text
http://localhost/phpmyadmin
```

2. Select **Import**.
3. Choose:

```text
eunoia/database/eunoia.sql
```

4. Click **Go**.

The SQL file creates the `eunoia` database, `users` table and `entries` table.

### Alternative

Open phpMyAdmin → SQL and paste/import the contents of `database/eunoia.sql`.

## 6. Configure Database Connection

Open:

```text
config/database.php
```

Default XAMPP settings are:

```php
$host = 'localhost';
$dbname = 'eunoia';
$username = 'root';
$password = '';
```

If your MySQL installation uses a different username/password, change only these values.

**Do not place real database credentials in frontend files or public repositories.**

## 7. Run the Project

Open:

```text
http://localhost/eunoia/
```

Start by creating an account.

Then:

1. Register
2. Login
3. Create a journal entry
4. Upload a memory photograph
5. Search/filter entries
6. Edit an entry
7. View the Memories page
8. Open Profile
9. Test password change
10. Logout

## 8. CRUD Explanation

### Create
`create_entry.php` inserts a new record into `entries`.

### Read
`dashboard.php` retrieves entries belonging to the logged-in user. `memories.php` retrieves entries that contain images.

### Update
`edit_entry.php` updates an entry only when its `entry_id` and current `user_id` match.

### Delete
`delete_entry.php` deletes the database row and safely removes its associated uploaded image.

## 9. File Handling

Uploaded journal images are:

1. Received using `$_FILES`
2. Checked for upload errors
3. Limited to 5 MB
4. Checked using PHP MIME detection
5. Restricted to JPG/JPEG, PNG and WEBP
6. Given a random unique filename
7. Stored in `uploads/`
8. Saved in MySQL as the filename
9. Displayed in journal cards and the Memories gallery
10. Removed when the related entry is deleted

Profile images are separately validated with a 3 MB limit.

## 10. Security

EUNOIA demonstrates basic application security:

- `password_hash()` for stored passwords
- `password_verify()` for authentication
- Session-based access control
- Session ID regeneration after login
- Prepared statements using PDO
- User ownership checks
- Server-side validation
- MIME-type checking for uploads
- Random upload filenames
- `basename()` when deleting stored files
- `htmlspecialchars()` when displaying user-generated text
- No database credentials in frontend code

## 11. Important Presentation Points

For a college demonstration, explain the flow as:

```text
Browser
   ↓
HTML + CSS + JavaScript
   ↓
PHP
   ↓
Session Authentication
   ↓
PDO Prepared Statements
   ↓
MySQL Database
   ↓
CRUD Operations
   ↓
uploads/ File Handling
```

## 12. Software Engineering Documentation Ideas

The project can be documented using:

- Problem identification
- Functional requirements
- Non-functional requirements
- Use-case diagram
- System architecture
- Database ER diagram
- Data flow diagram
- Database design
- Authentication design
- CRUD implementation
- File handling
- Security testing
- Functional testing
- Responsive UI testing
- Results
- Future enhancements

## 13. Future Enhancements

Possible future additions:

- Tags for entries
- Calendar view
- Mood charts
- Export journal to PDF
- Email reminders
- Multiple image attachments
- Dark mode
- Cloud backup
- Two-factor authentication

## 14. Troubleshooting

### “Database connection failed”
Make sure MySQL is running and `config/database.php` matches your XAMPP credentials.

### “404 Not Found”
Make sure the project folder is directly inside `htdocs`:

```text
C:\xampp\htdocs\eunoia\
```

### Images are not uploading
Make sure the `uploads` folder exists and PHP has permission to write to it.

### Page looks unstyled
Check that the project contains:

```text
css/style.css
js/script.js
```

and open the project through Apache using:

```text
http://localhost/eunoia/
```

Do not open PHP files directly with a `file:///` URL.

---

**EUNOIA — a small space for the moments worth keeping.**
