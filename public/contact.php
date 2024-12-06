<?php require '../includes/header.php'; ?>
<link rel="stylesheet" href="../assets/css/global.css">
<div class="contact-container">
    <h1>Contact Us</h1>
    <p>We’d love to hear from you! Whether you have questions, need support, or want to share feedback, our team is here to help.</p>

    <form class="contact-form" onsubmit="handleSubmit(event)">
        <div id="successMsg" class="success-message">
            Your message has been sent successfully!
        </div>

        <div class="form-group">
            <label for="contact_name">Your Name</label>
            <input type="text" id="contact_name" name="name" placeholder="Enter your full name" required>
        </div>

        <div class="form-group">
            <label for="contact_email">Email</label>
            <input type="email" id="contact_email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group full-width">
            <label for="contact_message">Message</label>
            <textarea id="contact_message" name="message" placeholder="How can we help?" rows="5" required></textarea>
        </div>

        <div class="form-group full-width">
            <button type="submit" class="btn btn-primary">Send Message</button>
        </div>
    </form>

    <section class="contact-info">
        <h2>Other Ways to Reach Us</h2>
        <p><strong>Email:</strong> support@mkscorp.com</p>
        <p><strong>Phone:</strong> +1 (404) 123-4567</p>
        <p><strong>Address:</strong> 123 HR Lane, Suite 100, Atlanta</p>
    </section>
</div>
<?php require '../includes/footer.php'; ?>

<script>
    function handleSubmit(e) {
        e.preventDefault();
        const successDiv = document.getElementById('successMsg');
        successDiv.style.display = 'block';
        e.target.reset();
    }
</script>
