<?php
require 'database.php';
include('auth.php');
include('csrfTokenHandle.php');
if (
    isset($_POST["old_password"])  && isset($_POST["new_password"]) && isset($_POST["csrfToken"])
) {
    $old_password = $_POST["old_password"];
    $new_password = $_POST["new_password"];
    $clientToken = $_POST["csrfToken"];
    $username = $user["username"];
    if (checkToken($clientToken, $username, $redis)) {
        if (checkOldPass($conn, $username, $old_password)) {
            $sql = "UPDATE users SET password = ? WHERE username = ?";
            $stmt = $conn->prepare($sql);
            if (validation($new_password)) {
                $new_password_enc = md5($new_password);
                $stmt->bind_param("ss", $new_password_enc, $username);
                if ($stmt->execute()) {
                    echo "<body style='background-color:#f2fff4'><h2>Update success, please reload the page and login again</h2></body>";
                    session_destroy();
                    exit;
                } else {
                    echo "<h1>Update password error</h1>";
                }
            } else {
                header("Location:user_profile_edit_password.php?wrong_pass_char");
                exit;
            }
        } else {
            header("Location:user_profile_edit_password.php?wrong_pass");
            exit;
        }
    } else {
        header("Location:index.php");
        exit;
    }
}


function checkOldPass($conn, $username, $old_password)
{
    $sql = "SELECT * FROM `users` WHERE username=? AND password=?";
    $stmt = $conn->prepare($sql);
    $old_password_enc = md5($old_password);
    $stmt->bind_param("ss", $username, $old_password_enc);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
function validation($password)
{
    $check = '/^[A-Za-z0-9]+$/';
    if (!preg_match($check, $password)) {
        return false;
    } else {
        return true;
    }
}
$tokens = $redis->hGetAll($username);
$csrfToken = $tokens['csrfToken'];
include("resources/static/html/header.html");

?>
<html>

<body>
    <h2 style="padding: 20px 20px 0px 20px; font-weight: 700;">Change password</h2>
    <form method="post" enctype="multipart/form-data" id="add_cart_form" onsubmit="return validateForm()">
        <div class="payment-checkout-container">
            <input type="hidden" value="<?php echo htmlspecialchars($csrfToken) ?>" name="csrfToken">
            <div class="cart-detail">
                <h4>
                    Old password
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="old_password" id="old-password-validation">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["wrong_pass"])) : ?>
                        <span class="blank-message" id="error">Wrong password!</span>
                        <script th:inline="javascript">
                            $('#old-password-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    New password
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="new_password" type="text" id="new-pass-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["wrong_pass_char"])) : ?>
                        <span class="blank-message" id="error">Password must not contain special character!</span>
                        <script th:inline="javascript">
                            $('#new-pass-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Confirm new password
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="new_password2" type="text" id="new-pass2-validation">
            </div>
        </div>

        <button type="submit" style="margin: 20px 0px 0px 150px;" class="cart-button-added" id="confirm-btn">Confirm</button>
    </form>

</body>
<script th:inline="javascript">
    $('#old-password-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#old-password-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#new-pass-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#new-pass-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#new-pass2-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#new-pass2-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    document.getElementById('confirm-btn').addEventListener('click', function(event) {
        if ($('#old-password-validation').val().length === 0) {
            $('#old-password-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#old-password-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#new-pass-validation').val().length === 0) {
            $('#new-pass-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#new-pass-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#new-pass2-validation').val().length === 0) {
            $('#new-pass2-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#new-pass2-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#old-password-validation').hasClass('invalid-blank') || $('#new-pass-validation').hasClass('invalid-blank') || $('#new-pass2-validation').hasClass('invalid-blank')) {
            event.preventDefault();
        }
        if ($('#new-pass-validation').val() != $('#new-pass2-validation').val()) {
            $('#new-pass2-validation').after('<span class="blank-message" id="error">Password must match</span>');
            $('#new-pass2-validation').addClass('invalid-blank');
            event.preventDefault();
        }
    });
</script>

</html>