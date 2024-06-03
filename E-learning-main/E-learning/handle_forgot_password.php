<?php

require 'redis.php';
require "database.php";

include("resources/static/html/header.html");
?>

<html>

<body>
    <div class="container">
        <div class="login-page">
                <?php if (isset($_GET["p"]) && isset($_GET["v"])) : ?>
                    <?php
                    $username = $_GET["p"];
                    $key = $_GET["v"];
                    if ($redis->get($username)):
                    ?>
                    <?php
                    $checkKey = $redis->get($username);
                    if (strcmp($key, $checkKey) == 0) :
                    ?>
                        <div class="index-form">
                            <form class="login-form" method="POST" action="/update_password.php" enctype="multipart/form-data">
                                <h2 style="color: #636363;">Create new password</h2>
                                <input type="hidden" name="username" value="<?php echo htmlspecialchars($username) ?>" />
                                <input type="password" placeholder="new password" name="password" id="regis-pass-validation" />
                                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                                    <?php if (isset($_GET["special_char"])) : ?>
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
                                <?php endif; ?>
                                <input type="password" placeholder="confirm new password" name="confirm_password" id="re-pass-validation" />
                                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                                    <?php if (isset($_GET["special_char"])) : ?>
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
                                <button id="register-btn">create</button>
                                <p class="message">Not receive mail? <a href="/forgot_password.php">Click here to get mail again</a></p>
                            </form>
                        </div>
                    <?php else: ?>
                    <h1>Error 404</h1>
                    <?php endif; ?>
                    <?php else: ?>
                        <h4>link experied</h4>
                        <?php endif; ?>
                <?php endif; ?>
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
    $('#regis-pass-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#regis-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    document.getElementById('register-btn').addEventListener('click', function(event) {
        if ($('#regis-pass-validation').val().length === 0) {
            $('#regis-pass-validation').after('<span class="blank-message" id="error" style="margin-top: -10px;">Dont leave it blank!</span>');
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
        if ($('#regis-pass-validation').hasClass('invalid-blank') || $('#re-pass-validation').hasClass('invalid-blank')) {
            event.preventDefault();
        }
    });
</script>


</html>