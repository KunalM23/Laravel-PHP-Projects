**AI Powered Lead Scoring CRMThis project is a Modular Monolith application built with Laravel 10. It focuses on lead management and intelligent scoring using the Gemini 2.0 Flash API.**  

🔑 Demo User CredentialsThe system includes four default users to test different permission levels. 

Super Admin: admin@gmail.com / password
Developer: kunal@gmail.com / password
Manager: priya@gmail.com / password
Tester: test@gmail.com / Minu@11

🚀 **Key FeaturesEmail OTP System**: When you forget your password, the system sends a 6-digit OTP to your registered email using PHPMailer and Gmail SMTP.  AI Lead Scoring: The system uses the Gemini API to automatically analyze leads.  

Smart Analysis: It calculates a score based on interaction counts; leads with more than 50% interactions receive a higher score.  

Password Security: The reset system ensures your new password is at least 6 characters, matches the confirmation, and is not the same as your old password.  

Permission Control: Action buttons like Add, Edit, and Delete are automatically hidden or shown based on your assigned access level (all_access, write, or read).  
Modern UI: Notifications are handled via SweetAlert2 for a clean and professional look.  Lead Management: Easily manage prospects, update their status, and track every interaction in one place.

📂 Simple Folder Guideroutes/web.php: Contains all page and session routes.app/Http/Controllers/Api: Contains the logic for the OTP system and Lead Scoring.  resources/views: Contains all the Blade UI pages.

**Git Commands**:
# 1. Initialize the local repository
git init

# 2. Add all project files
git add .

# 3. Commit the changes
git commit -m "Initial commit: AI Lead Scoring CRM with Gemini & OTP"

# 4. Link to your GitHub repository
git remote add origin https://github.com/KunalM23/Laravel-PHP-Projects.git

# 5. Set the branch to main
git branch -M main

# 6. Push the code to GitHub
git push -u origin main
