<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us | SHRS</title>
    <link rel="stylesheet" href="/Mwaka.SHRS.2/styles/contact.css">
</head>
<body>

<div class="contact-container" >
    <div class="contact-header">
        <h1>Contact Us</h1>
        <p>We are here to help. Reach out to us for any inquiries or support.</p>
    </div>

    <div class="contact-content">

        <div class="contact-info">
            <h2>Get In Touch</h2>
            <p><strong>Address:</strong> Chuka University Health Centre</p>
            <p><strong>Email:</strong> support@shrs.ac.ke</p>
            <p><strong>Phone:</strong> +254 700 000 000</p>
            <p><strong>Working Hours:</strong> Monday - Friday (8:00 AM - 5:00 PM)</p>
        </div>

        <div class="contact-form">
            <h2>Send Message</h2>

            <form action="send_message.php" method="POST">
                <div class="input-group">
                    <label>Full Name</label>
                    <input type="text" name="fullname" required>
                </div>

                <div class="input-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>

                <div class="input-group">
                    <label>Subject</label>
                    <input type="text" name="subject" required>
                </div>

                <div class="input-group">
                    <label>Message</label>
                    <textarea name="message" rows="5" required></textarea>
                </div>

                <button type="submit" class="btn-submit">Send Message</button>
            </form>
        </div>

    </div>
</div>

<footer class="footer">
    <p>© 2026 Student Health Record System | All Rights Reserved</p>
</footer>
</body>
</html>