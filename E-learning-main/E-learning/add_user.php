<?php
require 'database.php';
include('auth.php');
include('validation.php');
include('csrfTokenHandle.php');
if (strcmp($user['user_role'], "admin") == 0) {
    if (
        isset($_POST["username"]) && isset($_POST["email"])  && isset($_POST["password"]) && isset($_POST["phone"]) && isset($_POST["address"]) && isset($_POST["csrfToken"])
    ) {
        $errorMsg = null;
        $error = 0;
        $email = trimAndCheckNull($_POST["email"]);
        $username = trimAndCheckNull($_POST["username"]);
        $password = trimAndCheckNull($_POST["password"]);
        $phone = trimAndCheckNull($_POST["phone"]);
        $address = trimAndCheckNull($_POST["address"]);
        $clientToken = $_POST["csrfToken"];
        $admin = $_SESSION["username"];
        if (checkToken($clientToken, $admin, $redis)) {
            if (is_null($username)) {
                $error = 1;
                $errorMsg = $errorMsg . "&unull_var";
            } else {
                if (!validation($username, $password)) {
                    $error = 1;
                    $errorMsg = $errorMsg . "&special_char";
                } else {
                    if (checkDuplicateUsername($conn, $username)) {
                        $error = 1;
                        $errorMsg = $errorMsg . "&duplicate";
                    }
                }
            }
            if (is_null($email)) {
                $error = 1;
                $errorMsg = $errorMsg . "&mnull_var";
            }
            if (is_null($password)) {
                $error = 1;
                $errorMsg = $errorMsg . "&pnull_var";
            } else {
                if (!validation($username, $password)) {
                    $error = 1;
                    $errorMsg = $errorMsg . "&special_char";
                }
            }
            if ($error == 0) {
                $enc_password = password_hash($password, PASSWORD_BCRYPT);
                $sql = "INSERT INTO users(id,username, password, email, phone, address) VALUES (NULL, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("sssss", $username, $enc_password, $email, $phone, $address);
                if ($stmt->execute()) {
                    header("Location:add_user.php?success");
                    refreshToken($admin,$redis);
                    exit;
                } else {
                    echo $stmt->error;
                }
            } else {
                $errorMsg = substr($errorMsg, 1);
                header("Location:add_user.php?" . $errorMsg);
                exit;
            }
        } else {
            header("Location:index.php");
            exit;
        }
    }
}

function checkDuplicateUsername($conn, $username)
{
    $sql = "SELECT * FROM `users` WHERE username = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        return true;
    } else {
        return false;
    }
}
function trimAndCheckNull($string)
{
    $trimmedString = trim($string);
    if (is_null($trimmedString) || empty($trimmedString)) {
        return null;
    } else {
        return $trimmedString;
    }
}
$tokens = $redis->hGetAll($username);
$csrfToken = $tokens['csrfToken'];

include("resources/static/html/header.html");

?>
<html>

<body>
    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
        <?php if (isset($_GET["success"])) : ?>
            <h3 style="color:blue">Update success, please reload page to see new result</h3>
            <script th:inline="javascript">
                $('#pass-validation').addClass('invalid-blank');
            </script>
        <?php endif; ?>
    <?php endif; ?>
    <h2 style="padding: 20px 20px 0px 20px; font-weight: 700;">Add User</h2>
    <form method="post" enctype="multipart/form-data" id="add_cart_form" onsubmit="return validateForm()">
        <div class="payment-checkout-container">
            <input type="hidden" value="<?php echo htmlspecialchars($csrfToken) ?>" name="csrfToken">
            <div class="cart-detail">
                <h4>
                    User Name
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="username" id="username-validation">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["duplicate"])) : ?>
                        <span class="blank-message" id="error">User Name already existed</span>
                        <script th:inline="javascript">
                            $('#username-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                    <?php if (isset($_GET["unull_var"])) : ?>
                        <span class="blank-message" id="error">User Name can not be null</span>
                        <script th:inline="javascript">
                            $('#username-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                    <?php if (isset($_GET["special_char"])) : ?>
                        <span class="blank-message" id="error">This field can not contain special character</span>
                        <script th:inline="javascript">
                            $('#username-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Password
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="password" type="text" id="pass-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["pnull_var"])) : ?>
                        <span class="blank-message" id="error">Password can not be null</span>
                        <script th:inline="javascript">
                            $('#pass-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                    <?php if (isset($_GET["special_char"])) : ?>
                        <span class="blank-message" id="error">This field can not contain special character</span>
                        <script th:inline="javascript">
                            $('#pass-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Email
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="email" type="text" id="email-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["mnull_var"])) : ?>
                        <span class="blank-message" id="error">Email can not be null</span>
                        <script th:inline="javascript">
                            $('#email-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Address
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="address" type="text" id="address-validation">
            </div>
            <div class="cart-detail">
                <h4>
                    Phone
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="phone" type="text" id="phone-validation">
            </div>
        </div>

        <button type="submit" style="margin: 20px 0px 0px 150px;" class="cart-button-added" id="confirm-btn">Confirm</button>
    </form>

</body>
<script th:inline="javascript">
    $('#username-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#username-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#pass-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#pass-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#email-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#email-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    document.getElementById('confirm-btn').addEventListener('click', function(event) {
        if ($('#email-validation').val().length === 0) {
            $('#email-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#email-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#pass-validation').val().length === 0) {
            $('#pass-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#pass-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#username-validation').val().length === 0) {
            $('#username-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#username-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#email-validation').hasClass('invalid-blank') || $('#pass-validation').hasClass('invalid-blank') || $('#username-validation').hasClass('invalid-blank')) {
            event.preventDefault();
        }
    });
</script>

</html>