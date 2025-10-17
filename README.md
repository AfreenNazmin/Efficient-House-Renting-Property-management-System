# Efficient-House-Renting-Property-management-System
Final Year Project: House Renting &amp; Property Management System 
## Updates - Email Verification & PHPMailer

### What's New
- Verification success page now includes a **“Go to Login” button** for better UX.
- Added **PHPMailer via Composer** to handle sending verification emails securely.
- Updated `users` table:
  - Removed `verification_code` column (no longer needed).
  - `is_verified` column now tracks verified users (0 = unverified, 1 = verified).

### How it Works
1. User signs up via the signup form.
2. Backend generates a **unique verification token** and stores it in `pending_users`.
3. PHPMailer sends a verification email containing the **link with the token**.
4. User clicks the link → `verify.php` verifies the token → user is moved to `users` table with `is_verified = 1`.
5. User clicks **“Go to Login”** → can log in and be redirected to their dashboard.

### Composer
- PHPMailer is installed via Composer.
- If cloning the repo fresh:
  ```bash
  composer install
