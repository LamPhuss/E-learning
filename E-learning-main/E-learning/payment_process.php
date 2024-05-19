<?php
require 'database.php';

include('auth.php');

if (
    !isset($_POST["owner_card"])  || !isset($_POST["card_num"])
    || !isset($_POST["card_date"]) || !isset($_POST["card_cvc"])
) {
    header("Location: start.php");
    exit;
}
$username = $_SESSION["username"];
include("resources/static/html/header.html");
?>
<html>

<body>
    <header>
        <div class="main-header">
        <ul class="nav-list">
            <li class="nav-item"><a href="/start.php">Home</a></li>
            <li class="nav-item"><a href="/search_course.php?course_title=&page=1">Searching</a></li>
            <?php if (strcmp($user['user_role'], "admin") == 0) : ?>
                <li class="nav-item">
                <a href="#">Manage</a>
                    <ul class="subnav">
                        <li>
                        <a href="/user_manage.php">Users</a>
                        </li>
                        <li>
                            <a href="/course_manage.php">Courses</a>
                        </li>
                    </ul>
                </li>
            <?php endif; ?>
        </ul>
            <div class="user-profile">
                <a href="/user_profile.php">
                    PP
                </a>
            </div>
    </header>
    <div class="container">
        <a href="/start.php" style="padding: 20px 20px 0px 50px;"> <i class="fa fa-share-square" style="display:inline-block"></i>
            <h3 style="display:inline-block"> Return</h3>
        </a>
        <?php

        use PHPMailer\PHPMailer\PHPMailer;
        use PHPMailer\PHPMailer\SMTP;

        require 'phpmailer/src/Exception.php';
        require 'phpmailer/src/PHPMailer.php';
        require 'phpmailer/src/SMTP.php';
        $mail = new PHPMailer(true);


        // SMTP config
        $mail->isSMTP();
        $mail->Host = "live.smtp.mailtrap.io";
        $mail->SMTPAuth = true;
        $mail->Username = "api";
        $mail->Password = "c40e79572b4c07d2d6809a3e5776b152";
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        // Sender and recipient settings
        $mail->setFrom('mailtrap@demomailtrap.com', 'Elearning Web');
        $mail->addReplyTo('mailtrap@demomailtrap.com', 'Elearning Web');
        $mail->addAddress('pholopho315@gmail.com', 'Phu'); // Replace with recipient's email and name

        // Setting the email content
        $mail->isHTML(true); // Set email format to plain text
        $mail->Subject = 'Thank you for your payment';
        $mail->Body = "<h1>All course content is attached here</h1><p>Here is your link to download course content: <a href='example.com'>example.com</a></p><p>If you have any problems, please contact us via email: elearninghust@gmail.com</p>";

        if ($mail->send()) : ?>
            <?php
                $sql = "DELETE FROM cart WHERE username = ? ";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                if ($stmt->execute()) :
            ?>
            <h3 style="padding: 20px 20px 0px 50px;">Thank you for your payment, please check your email to receive course-related materials &lt3 </h3>
            <?php else : ?>
                <h3 style="padding: 20px 20px 0px 50px;">An error occurred, please try again after a few minutes</h3>
            <?php endif; ?>
        <?php else : ?>
            <h3 style="padding: 20px 20px 0px 50px;">An error occurred, please try again after a few minutes</h3>
        <?php endif; ?>
    </div>
    <div class="footer">
    <span><a href="logout.php" style="font-size: 20px;">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a></span><br>
        <span><i class="fa fa-pencil-square-o"></i> Contact</span><a href="#"></a><br>

    </div>

</body>

</html>