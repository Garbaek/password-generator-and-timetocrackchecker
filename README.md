Password Generator & Strength Checker
This is a PHP-based tool that allows users to generate random passwords of configurable lengths and check the strength of existing passwords. The strength is estimated based on several factors, including length, character diversity, and the presence of common words or leaked passwords.

Features
Password Generator: Generate a secure random password with a user-defined length.
Password Strength Checker: Check the strength of a password by estimating how long it would take to crack based on various criteria such as character variety and common words.
Common Words Check: Passwords are checked for common words (including words from danish.txt and leaked_passwords.txt), which are penalized in the strength estimation.
Crack Time Estimation: Displays an estimated time for cracking the password, with different levels of security ranging from "Instantly" to "Many years."

Files Included
danish.txt: A list of common Danish words, used for checking against the password.
leaked_passwords.txt: A list of known leaked passwords, used for identifying weak passwords.

How It Works
Generate Password:
Choose the desired password length between 32 and 64 characters.
Click "Generate Password" to create a random password.

Check Password Strength:
Enter a password in the "Check Password Strength" field.
The system will analyze the password and estimate the time it would take to crack it based on various factors (length, diversity, common words, etc.).
Usage

Clone this repository to your local server or hosting environment.
Place the danish.txt and leaked_passwords.txt files in the same directory as the PHP script.
Access the script in a web browser and interact with the password generator and checker.

Requirements
PHP 7.0+ (the script uses the random_int() function, which requires PHP 7 or later)

Web server (e.g., Apache, Nginx)
A text editor for editing the danish.txt and leaked_passwords.txt files if needed

Security Notes
The password strength checker is not intended for actual security audits. It is a simple tool for providing an estimate of password strength.

Make sure to keep danish.txt and leaked_passwords.txt updated to enhance the tool's effectiveness.

License
This project is licensed under the MIT License. See the LICENSE file for details.

Contributions
Feel free to contribute to this project by opening issues or pull requests on the GitHub repository.
