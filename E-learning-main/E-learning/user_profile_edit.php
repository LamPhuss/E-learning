<?php
require 'database.php';
include('auth.php');
include('csrfTokenHandle.php');
$username = $user["username"];
$sql = "SELECT * FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $user_detail = $result->fetch_assoc();
    if (isset($user_detail["phone"])) {
        $phone = htmlspecialchars($user_detail["phone"]);
    }
    if (isset($user_detail["address"])) {
        $address = htmlspecialchars($user_detail["address"]);
    }
} else {
    echo "<h1>404</h1>";
}

$dir = '/var/www/html/upload/';
$avatar = null;
if (!file_exists($dir)) {
    mkdir($dir);
}
$matchingFiles = glob($dir . '/' . $username . '*');
if (!empty($matchingFiles)) {

    $tmp2 = explode("/", $matchingFiles[0]);
    $avatar = end($tmp2);
}

if (isset($_FILES["file"]) && isset($_POST["csrfToken"])) {
    $clientToken = $_POST["csrfToken"];
    if (checkToken($clientToken, $username, $redis)) {
        try {
            if (!is_null($avatar)) {
                unlink("/var/www/html/upload/" . $avatar);
            }
            $file_name = $_FILES["file"]["name"];
            if (preg_match('/^.+\.ph(p|ps|ar|tml)/', $file_name)) {
                header("Location: user_profile_edit.php?img_err");
                exit;
            }
            if (!preg_match('/^.*\.(jpg|jpeg|png|gif)$/', $file_name)) {
                header("Location: user_profile_edit.php?img_err");
                exit;
            }
            $tmp = explode(".", $file_name);
            $extension = end($tmp);
            $avatar = $username . "." . $extension;
            $newFile = $dir . "/" . $avatar;
            move_uploaded_file($_FILES["file"]["tmp_name"], $newFile);
            refreshToken($username, $redis);
        } catch (Exception $e) {
            $error = $e->getMessage();
        }
    } else {
        header("Location:index.php");
        exit;
    }
}
if (is_null($avatar)) {
    $avatar = "default.jpg";
}

$wrongFile = isset($_GET["img_err"]) ? true : false;
$checkWrongFile = 0;
if ($wrongFile) {
    $checkWrongFile = 1;
}
$phoneErr = isset($_GET["phoneNum_err"]) ? true : false;
$checkPhone = 0;
if ($phoneErr) {
    $checkPhone = 1;
}
$tokens = $redis->hGetAll($username);
$csrfToken = $tokens['csrfToken'];
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
            <div class="cart">
                <a><i class="fa fa-shopping-cart" style="color: #000; font-size: 54px;float:right;"></i></a>
                <?php
                $username = $user["username"];
                $sql = "SELECT * FROM cart WHERE username=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();
                $cart = array();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $cart[] = $row;
                    }
                }
                $total = 0;
                $exit = 0
                ?>
                <ul class="cart-list">
                    <?php foreach ($cart as $item) :
                        $total += $item['course_price'];
                    ?>
                        <li>
                            <img src="<?php echo htmlspecialchars($item['course_img']); ?>">
                            <h3 style="font-weight: 700">
                                <a href="/learning.php?course_id=<?php echo htmlspecialchars($item['course_id']); ?>">
                                    <?php echo htmlspecialchars($item['course_title']); ?>
                                </a>
                            </h3>
                            <h3 style="font-weight: 400; font-size: 15px">
                                Author: <?php echo htmlspecialchars($item['course_author']); ?>
                            </h3>
                            <h3>
                                Price: <?php echo htmlspecialchars($item['course_price']); ?>
                            </h3>
                            <hr>
                        </li>
                    <?php endforeach; ?>
                    <li>
                        <h2 style="font-weight: 700; font-size: 25px">Total: <?php echo htmlspecialchars($total); ?></h2>
                        <button type="button" class="cart-pucharse-button" onclick="location.href='/paycheck.php';">Purchase</button>
                    </li>
                </ul>

            </div>
        </div>
    </header>
    <div class="cart-notification-container">
        <div class="cart-notification">
            <div class="cart-notification-content">
                <div class="message">
                </div>
            </div>
            <i class="fa-solid fa-xmark close"></i>
            <div class="progress"></div>
        </div>
    </div>
    <div class="container">
        <div class="overlay" id="overlay">
            <div class="overlay-panel" id="overlay-panel" style="width: 500px;margin-left:38%">
                <iframe name="headerframe" width="100%" height="550px" frameborder="0" src="user_profile_edit_password.php"></iframe>
            </div>
        </div>

        <div class="row gutters-sm">
            <div class="col-sm-4">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex flex-column align-items-center text-center">
                            <img src="<?php echo "/upload/" . htmlspecialchars($avatar) ?>" alt="Admin" class="rounded-circle" width="150">
                            <div class="mt-3">
                                <h4><?php echo htmlspecialchars($user_detail["username"]) ?></h4>
                                <p class="text-secondary mb-1">Student</p>
                                <p class="user-country font-size-sm">
                                    <?php if (isset($user_detail["address"])) : ?>
                                        <?php echo htmlspecialchars($user_detail["address"]) ?>
                                    <?php endif ?>
                                </p>
                                <form method="post" enctype="multipart/form-data">
                                    <div class="ht-tm-element custom-file">
                                        <input type="file" class="custom-file-input" name="file" id="fileInput">
                                        <input type="hidden" value="<?php echo htmlspecialchars($csrfToken) ?>" name="csrfToken">
                                        <label class="custom-file-label">Change avatar</label>
                                    </div>
                                    <?php
                                    if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                                        <?php if (isset($_GET["img_err"])) : ?>
                                            <span class="blank-message" id="error" style="margin-top: -10px;">Only images are allowed</span>
                                            <script th:inline="javascript">
                                                $('#fileInput').addClass('invalid-blank');
                                            </script>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <hr>
                                    <button type="submit" class="btn btn-primary d-none">Submit</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <form name="paycheck-form" method="POST" action="/update_user.php">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">User Name</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <?php echo htmlspecialchars($user_detail["username"]) ?>
                                    <input type="hidden" value="<?php echo htmlspecialchars($csrfToken) ?>" name="csrfToken">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Email</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="text" value="<?php echo htmlspecialchars($user_detail["email"]) ?>" class="input-profile-field" name="email">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Password</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    **********
                                    <h1 class="btn btn-change" onclick="toggleMenu()">Change Password</h1>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Phone</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="text" value="<?php if (isset($user_detail["phone"])) : ?><?php echo htmlspecialchars($user_detail["phone"]) ?><?php endif ?>" class="input-profile-field" name="phone_num">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-3">
                                    <h6 class="mb-0">Address</h6>
                                </div>
                                <div class="col-sm-9 text-secondary">
                                    <input type="text" value="<?php if (isset($user_detail["address"])) : ?><?php echo htmlspecialchars($user_detail["address"]) ?><?php endif ?>" class="input-profile-field" name="address">
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-sm-12">
                                    <button class="btn btn-info" type="submit">Confirm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        </div>

    </div>
    <div class="footer">
        <span><a href="logout.php" style="font-size: 20px;">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a></span><br>
        <span><i class="fa fa-pencil-square-o"></i> Contact</span><a href="#"></a><br>

    </div>
    <script th:inline="javascript">
        var cartIcon = document.querySelector(".fa-shopping-cart");
        var cartList = document.querySelector(".cart-list");

        cartIcon.addEventListener("click", function() {
            var isCartListVisible = cartList.style.display === "block";
            cartList.style.display = isCartListVisible ? "none" : "block";
        });
        const fileInput = document.querySelector('.custom-file-input');
        const submitButton = document.querySelector('.btn-primary');

        fileInput.addEventListener('change', function() {
            // Check if a file is selected
            if (this.files && this.files.length > 0) {
                submitButton.classList.remove('d-none'); // Show submit button
            } else {
                submitButton.classList.add('d-none'); // Hide submit button
            }
        });
        /*=============================================================================*/
        const menu = document.getElementById("overlay");

        function toggleMenu() {
            document.getElementById("overlay").classList.toggle("show1")
        }

        window.onclick = (event) => {
            if (!event.target.matches('.btn-change')) {
                if (menu.classList.contains("show1")) {
                    menu.classList.remove("show1")
                }
            }
        }
        const menu2 = document.getElementById("overlay-panel");
        menu2.addEventListener('click', event => event.stopPropagation())

        /* ============================================================= */
        const notification = document.querySelector(".cart-notification"),
            closeIcon = document.querySelector(".close"),
            progress = document.querySelector(".progress");
        const checkWrongFile = <?php echo htmlspecialchars($checkWrongFile); ?>;
        const checkPhone = <?php echo htmlspecialchars($checkPhone); ?>;
        if (checkWrongFile > 0 || checkPhone > 0) {
            const messageDiv = $('.message');
            if (checkWrongFile > 0) {
                const wrongFileMsg = $("<span class='text'>Wrong image format</span>");
                messageDiv.append(wrongFileMsg);
            }
            if (checkPhone > 0) {
                const wrongFileMsg = $("<span class='text'>Phone number can only containt number</span>");
                messageDiv.append(wrongFileMsg);
            }
            notification.classList.add("active");
            progress.classList.add("active");

            setTimeout(() => {
                notification.classList.remove("active");
            }, 5000);
            setTimeout(() => {
                progress.classList.remove("active");
            }, 5300);
        }
        closeIcon.addEventListener("click", () => {
            notification.classList.remove("active");

            setTimeout(() => {
                progress.classList.remove("active");
            }, 300);
        });
    </script>
</body>

</html>