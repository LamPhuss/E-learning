<?php
include("resources/static/html/header.html");
?>

<html>

<body>
    <div class="container">
        <div class="login-page">
            <div class="index-form">
                <form class="login-form" method="POST" action="/send_reset_link.php" enctype="multipart/form-data">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["send_success"])) : ?>
                            <h3 style="color:blue">Please check your email to get link to recover your password</h3>
                            <script th:inline="javascript">
                                $('#pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <h2 style="color: #636363;">Please enter your username and email</h2>
                    <input type="text" placeholder="username" name="username" id="login-username-validation" />
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["uname_err"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">User Name and Email not match</span>
                            <script th:inline="javascript">
                                $('#login-username-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["lspecial_char"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">This field can not contain special character</span>
                            <script th:inline="javascript">
                                $('#login-username-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <input type="email" placeholder="abc@xyz" name="email" id="regis-email-validation" />
                    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["null"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>
                            <script th:inline="javascript">
                                $('#regis-email-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <button id="login-btn">Confirm</button>
                    <p class="message">Remember your password? <a href="/index.php">Return to login</a></p>
                </form>
            </div>
        </div>
    </div>
</body>
<script th:inline="javascript">
    $('.message a').click(function() {
        $('form').animate({
            height: "toggle",
            opacity: "toggle"
        }, "slow");
    });
    /* ========================================================== */
    function checkSpecialChar(text) {
        const specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/;
        return specialCharRegex.test(text);
    }
    $('#login-username-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#login-username-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#regis-email-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#regis-email-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    document.getElementById('login-btn').addEventListener('click', function(event) {
        if ($('#login-username-validation').val().length === 0) {
            $('#login-username-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            $('#login-username-validation').addClass('invalid-blank');
            event.preventDefault();
        } else {
            if (checkSpecialChar($('#login-username-validation').val())) {
                $('#login-username-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">This field must not contain special character!</span>');
                $('#login-username-validation').addClass('invalid-blank');
                event.preventDefault();
            }
        }
        if ($('#regis-email-validation').val().length === 0) {
            $('#regis-email-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            $('#regis-email-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#login-username-validation').hasClass('invalid-blank') || $('#regis-email-validation').hasClass('invalid-blank') ) {
            event.preventDefault();
        }
    });
</script>


</html>