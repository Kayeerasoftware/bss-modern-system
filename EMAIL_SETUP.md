# EMAIL CONFIGURATION GUIDE FOR PASSWORD RESET

## Option 1: Gmail (Recommended - FREE)

### Step 1: Enable 2-Step Verification
1. Go to: https://myaccount.google.com/security
2. Enable "2-Step Verification"

### Step 2: Generate App Password
1. Go to: https://myaccount.google.com/apppasswords
2. Select "Mail" and "Other (Custom name)"
3. Name it "BSS System"
4. Copy the 16-character password

### Step 3: Update .env
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=youremail@gmail.com
MAIL_PASSWORD=your-16-char-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@gmail.com
MAIL_FROM_NAME="BSS System"
```

---

## Option 2: Outlook/Hotmail (FREE)

```
MAIL_MAILER=smtp
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=youremail@outlook.com
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@outlook.com
MAIL_FROM_NAME="BSS System"
```

---

## Option 3: SendGrid (FREE - 100 emails/day)

1. Sign up: https://sendgrid.com/
2. Create API Key
3. Update .env:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_PASSWORD=your-sendgrid-api-key
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=youremail@yourdomain.com
MAIL_FROM_NAME="BSS System"
```

---

## Option 4: Mailtrap (Testing Only)

For testing before going live:

```
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-mailtrap-username
MAIL_PASSWORD=your-mailtrap-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@bss.com
MAIL_FROM_NAME="BSS System"
```

---

## RECOMMENDED: Use Gmail

**Easiest and most reliable for small projects!**

Just update these 3 values in .env:
1. MAIL_USERNAME=your-gmail@gmail.com
2. MAIL_PASSWORD=your-app-password (16 chars from Google)
3. MAIL_FROM_ADDRESS=your-gmail@gmail.com

Done! Password reset emails will work.
