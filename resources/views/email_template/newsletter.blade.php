<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Features Are Here! Check Out What's New!</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <style>
        /* General Styles */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f8fa;
            color: #333;
        }
        .email-container {
            max-width: 650px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        /* Header Section */
        .email-header {
            background: black;
            padding: 40px 30px;
            color: #ffffff;
            text-align: center;
        }
        .email-header img {
            max-width: 150px;
            margin-bottom: 20px;
        }
        .email-header h1 {
            font-size: 32px;
            margin: 0;
            font-weight: bold;
        }
        .email-header p {
            font-size: 18px;
            margin: 10px 0;
        }

        /* Body Section */
        .email-body {
            padding: 30px;
            background-color: #fafafa;
        }
        .intro-text {
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 30px;
        }

        /* Features List */
        .feature-section {
            margin-bottom: 30px;
        }
        .feature-section h2 {
            font-size: 22px;
            color: #4e73df;
            margin-bottom: 15px;
        }
        .feature-list {
            list-style: none;
            padding-left: 0;
        }
        .feature-list li {
            padding: 20px;
            background-color: #ffffff;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
        }
        .feature-list li i {
            font-size: 25px;
            color: #28a745;
            margin-right: 20px;
        }
        .feature-list li h3 {
            font-size: 18px;
            color: #333;
            margin: 0;
        }
        .feature-list li p {
            font-size: 14px;
            color: #666;
        }

        /* Call-to-Action Button */
        .cta-button {
            display: inline-block;
            padding: 15px 30px;
            background-color: #28a745;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
            font-size: 18px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
            margin-top: 20px;
        }
        .cta-button:hover {
            background-color: #218838;
        }

        /* Footer Section */
        .footer {
            padding: 20px 30px;
            text-align: center;
            background-color: #f1f1f1;
            font-size: 14px;
            color: #888;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
        }
        .footer a:hover {
            text-decoration: underline;
        }

    </style>
</head>
<body>

    <div class="email-container">
        <div class="email-header">
        	
            <img src="{{ asset('img/logo-ny.png')}}" alt="[Your Application Name] Logo"> <!-- Replace with your logo URL -->
            
        </div>
        <!-- Header Section -->
        

        <!-- Body Section -->
        <div class="email-body">
        	<h1>New Features Are Here!</h1>
            <p>Check Out What's New in [Your Application Name]</p>
            <p class="intro-text">
                Hello [User's Name],<br><br>
                We are excited to announce the launch of new features that will help you get even more out of [Your Application Name]. Here’s a quick look at what's new and how you can start using them today!
            </p>

            <!-- Features Section -->
            <div class="feature-section">
                <h2>What’s New?</h2>
                <ul class="feature-list">
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h3>[Feature 1 Name]</h3>
                            <p>A brief description of the feature and how it benefits you.</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h3>[Feature 2 Name]</h3>
                            <p>A short explanation of the second feature, highlighting its benefits.</p>
                        </div>
                    </li>
                    <li>
                        <i class="fas fa-check-circle"></i>
                        <div>
                            <h3>[Feature 3 Name]</h3>
                            <p>Another exciting update, with details on how it works and its usefulness.</p>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- CTA Button -->
            <a href="[Login URL]" class="cta-button">Start Exploring Now</a>
        </div>

        <!-- Footer Section -->
        <div class="footer">
            <p>Thank you for being a valued user of [Your Application Name]!</p>
            <p>If you have any questions or feedback, don't hesitate to <a href="mailto:[Support Email]">reach out to us</a>.</p>
            <p>Follow us on <a href="[Social Media Links]">social media</a> for more updates!</p>
        </div>
    </div>

</body>
</html>
