<?php
require_once 'captcha/Captcha_verify.php';
include("resources/static/html/header.html");
?>

<html>

<body>
    <div class="container">
        <div class="login-page">
            <div class="index-form">
                <form class="register-form" method="POST" action="/register.php" enctype="multipart/form-data">
                    <h2 style="color: #636363;">Sign up</h2>
                    <input type="text" placeholder="username" name="username" id="regis-username-validation" />
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["duplicate"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">User Name already existed</span>
                            <script th:inline="javascript">
                                $('#regis-username-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["rspecial_char"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">This field can not contain special character</span>
                            <script th:inline="javascript">
                                $('#regis-username-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["null"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>
                            <script th:inline="javascript">
                                $('#regis-username-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <input type="password" placeholder="password" name="password" id="regis-pass-validation" />
                    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["rspecial_char"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">This field can not contain special character</span>
                            <script th:inline="javascript">
                                $('#regis-pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["null"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>
                            <script th:inline="javascript">
                                $('#regis-pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["too_small"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Password must contain at least 8 characters</span>
                            <script th:inline="javascript">
                                $('#regis-pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <input type="password" placeholder="confirm password" name="confirm_password" id="re-pass-validation" />
                    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["rspecial_char"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">This field can not contain special character</span>
                            <script th:inline="javascript">
                                $('#regis-pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["null"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>
                            <script th:inline="javascript">
                                $('#regis-pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["not_match"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Confirm password must match above password</span>
                            <script th:inline="javascript">
                                $('#re-pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <input type="email" placeholder="email" id="regis-email-validation" name="email" />
                    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["null"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>
                            <script th:inline="javascript">
                                $('#regis-email-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <div id="captcha_img_house" style="text-align:center;margin-bottom:10px">
                        <img style="width:80%"id="captcha_img" src="captcha/Captcha_image.php">
                    </div>
                    <input type="text" placeholder="enter captcha number" id="captcha_key" name="captcha"/>
                    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["captcha_err"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Wrong captcha</span>
                            <script th:inline="javascript">
                                $('#captcha_key').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <button id="register-btn">Create</button>
                    <p class="message">Already registered? <a href="#">Sign In</a></p>
                </form>
                <form class="login-form" method="POST" action="/login.php" enctype="multipart/form-data">
                    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["success"])) : ?>
                            <h4 style="color:blue">Register success, please login here</h4>
                            <script th:inline="javascript">
                                $('#pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["update_pass"])) : ?>
                            <h4 style="color:blue">Update new password success, please login here</h4>
                            <script th:inline="javascript">
                                $('#pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <h2 style="color: #636363;">Sign in</h2>
                    <input type="text" placeholder="username" name="username" id="login-username-validation" />
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["log_err"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Wrong username or password</span>
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
                    <input type="password" placeholder="password" name="password" id="login-pass-validation" />
                    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                        <?php if (isset($_GET["log_err"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">Wrong username or password</span>
                            <script th:inline="javascript">
                                $('#login-pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                        <?php if (isset($_GET["lspecial_char"])) : ?>
                            <span class="blank-message" id="error" style="margin-top: -10px;">This field can not contain special character</span>
                            <script th:inline="javascript">
                                $('#login-pass-validation').addClass('invalid-blank');
                            </script>
                        <?php endif; ?>
                    <?php endif; ?>
                    <button id="login-btn">login</button>
                    <p class="message">Not registered? <a href="#">Create an account</a></p>
                    <p class="message2"><a href="forgot_password.php" style="color: #0d6efd;">Forgot password? </a></p>
                </form>
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["duplicate"]) || isset($_GET["rspecial_char"]) || isset($_GET["null"]) || isset($_GET["not_match"])) : ?>
                        <script th:inline="javascript">
                            $('form').animate({
                                height: "toggle",
                                opacity: "toggle"
                            }, "fast");
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
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
    $('#login-pass-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#login-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#regis-username-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#regis-username-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#regis-pass-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length < 8) {
            $('#regis-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Password must contain at least 8 characters</span>');
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
    $('#re-pass-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#re-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    document.getElementById('login-btn').addEventListener('click', function(event) {
        if ($('#login-pass-validation').val().length === 0) {
            $('#login-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            $('#login-pass-validation').addClass('invalid-blank');
            event.preventDefault();
        } else {
            if (checkSpecialChar($('#login-pass-validation').val())) {
                $('#login-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">This field must not contain special character!</span>');
                $('#login-pass-validation').addClass('invalid-blank');
                event.preventDefault();
            }
        }
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
        if ($('#login-pass-validation').hasClass('invalid-blank') || $('#login-username-validation').hasClass('invalid-blank')) {
            event.preventDefault();
        }
    });
    document.getElementById('register-btn').addEventListener('click', function(event) {
        if ($('#regis-pass-validation').val().length < 8 ) {
            $('#regis-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Password must contain at least 8 characters</span>');
            $('#regis-pass-validation').addClass('invalid-blank');
            event.preventDefault();
        } else {
            if (checkSpecialChar($('#regis-pass-validation').val())) {
                $('#regis-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">This field must not contain special character!</span>');
                $('#regis-pass-validation').addClass('invalid-blank');
                event.preventDefault();
            }
        }
        if ($('#re-pass-validation').val().length === 0) {
            $('#re-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            $('#re-pass-validation').addClass('invalid-blank');
            event.preventDefault();
        } else {
            if (checkSpecialChar($('#re-pass-validation').val())) {
                $('#re-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">This field must not contain special character!</span>');
                $('#re-pass-validation').addClass('invalid-blank');
                event.preventDefault();
            }
        }
        if ($('#regis-username-validation').val().length === 0) {
            $('#regis-username-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            $('#regis-username-validation').addClass('invalid-blank');
            event.preventDefault();
        } else {
            if (checkSpecialChar($('#regis-username-validation').val())) {
                $('#regis-username-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">This field must not contain special character!</span>');
                $('#regis-username-validation').addClass('invalid-blank');
                event.preventDefault();
            }
        }
        if ($('#regis-email-validation').val().length === 0) {
            $('#regis-email-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            $('#regis-email-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#regis-pass-validation').hasClass('invalid-blank') || $('#regis-username-validation').hasClass('invalid-blank') || $('#regis-email-validation').hasClass('invalid-blank') || $('#re-pass-validation').hasClass('invalid-blank')) {
            event.preventDefault();
        }
    });
</script>


</html>