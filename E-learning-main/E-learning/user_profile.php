<?php
require 'database.php';
include('auth.php');

$username = $user["username"];
$sql = "SELECT * FROM users WHERE username=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $user_detail = $result->fetch_assoc();
} else {
    echo "<h1>404</h1>";
}
$dir = '/var/www/html/upload/';
$avatar = null;
if (!file_exists($dir)) {
    mkdir($dir);
}
$matchingFiles = glob($dir . '/test*');
if (!empty($matchingFiles)){

    $tmp2 = explode("/", $matchingFiles[0]);
    $avatar = end($tmp2);
}

if (isset($_FILES["file"])) {
    try {
        if (!is_null($avatar)){
        unlink("/var/www/html/upload/" . $avatar);
        }
        $file_name = $_FILES["file"]["name"];
        if (preg_match('/^.+\.ph(p|ps|ar|tml)/', $file_name)) {
            header("Location: user_profile.php?img_err");
            exit;
        }
        if (!preg_match('/^.*\.(jpg|jpeg|png|gif)$/', $file_name)) {
            header("Location: user_profile.php?img_err");
            exit;
        }
        $tmp = explode(".", $file_name);
        $extension = end($tmp);
        $avatar = $username . "." . $extension;
        $newFile = $dir . "/" . $avatar;
        move_uploaded_file($_FILES["file"]["tmp_name"], $newFile);
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
if(is_null($avatar)){
    $avatar = "default.jpg";
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $wrongFile = isset($_GET["img_err"]) ? true : false;
    $checkWrongFile = 0;
    if ($wrongFile) {
        $checkWrongFile = 1;
    }

}
include("resources/static/html/header.html");
?>
<html>

<body>
    <header>
        <div class="main-header">
        <ul class="nav-list">
                <li class="nav-item"><a href="/start.php">Home</a></li>
                <li class="nav-item"><a href="/search_course.php?course_title=&page=1">Searching</a></li>
                <?php if(strcmp($user['user_role'],"admin")==0): ?>
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
                                    <?php if(isset($user_detail["address"])) :?>
                                        <?php echo htmlspecialchars($user_detail["address"]) ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-8">
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">User Name</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <?php echo htmlspecialchars($user_detail["username"]) ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Email</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <?php echo htmlspecialchars($user_detail["email"]) ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Password</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                **********
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Phone</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <?php if(isset($user_detail["phone"])) :?>
                                    <?php echo htmlspecialchars($user_detail["phone"]) ?>
                                <?php endif ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-3">
                                <h6 class="mb-0">Address</h6>
                            </div>
                            <div class="col-sm-9 text-secondary">
                                <?php if(isset($user_detail["address"])) :?>
                                    <?php echo htmlspecialchars($user_detail["address"]) ?>
                                <?php endif ?>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-sm-12">
                                <a class="btn btn-info " href="/user_profile_edit.php">Edit</a>
                            </div>
                        </div>
                    </div>
                </div>
                <!--   
                <div class="card mb-3">
                    <div class="card-body">
                        <div class="row">
                            <h6 class="mb-0">Purchased courses</h6>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="text-secondary"><a href="#">
                                    Introduction to HTML
                                </a>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="text-secondary"><a href="#">
                                    Introduction to CSS
                                </a>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="text-secondary"><a href="#">
                                    Introduction to Javascript
                                </a>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="text-secondary"><a href="#">
                                    Introduction to PHP
                                </a>
                            </div>
                        </div>
                        <hr>

                    </div>
                </div>-->
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
        /* ============================================================== */
        /*
        const notification = document.querySelector(".cart-notification"),
            closeIcon = document.querySelector(".close"),
            progress = document.querySelector(".progress");
        const checkWrongFile =
        if (checkWrongFile > 0) {
            const messageDiv = $('.message');
            const wrongFileMsg = $("<span class='text'>Wrong image format</span>");
            messageDiv.append(wrongFileMsg);
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
        });*/
    </script>
</body>

</html>