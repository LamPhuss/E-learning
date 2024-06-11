<?php
require 'database.php';
include('auth.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["course_id"])) {
        $course_id = $_GET["course_id"];
        $sql = "SELECT * FROM courses WHERE course_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $course = $result->fetch_assoc();
            $currview = $course['view'] + 1;
            UpdateView($conn,$currview,$course_id);
        } else {
            echo "<h1>404</h1>";
        }
    } else {
        header("Location: start.php");
        exit;
    }
    $notAdded = isset($_GET["not_added"]) ? true : false;
}
function UpdateView($conn,$view,$course_id){
    $sql = "UPDATE courses SET view = ? WHERE course_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii",$view, $course_id);
    $stmt->execute();
}
$tokens = $redis->hGetAll($username);
$csrfToken = $tokens['csrfToken'];
include("resources/static/html/header.html");

$checkAdded = 0;
if ($notAdded) {
    $checkAdded = 1;
}
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
                        if ($course["course_id"] == $item["course_id"]) :
                            $exit += 1;
                        endif;
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
                    <span class="text ">This course is already in the cart</span>
                </div>
            </div>
            <i class="fa-solid fa-xmark close"></i>
            <div class="progress"></div>
        </div>
    </div>
    <div class="container" style="margin:0 , padding 0">
        <div id="learning-panel">
            <h1 class="learning-header"><?php echo htmlspecialchars($course['title']); ?></h1>
            <div class="image-container">
                <div class="slide">
                    <div class="slideNumber">1</div>
                    <img src="<?php echo htmlspecialchars($course['slide1']); ?>" style="max-height:600px">
                </div>
                <div class="slide">
                    <div class="slideNumber">2</div>
                    <img src="<?php echo htmlspecialchars($course['slide2']); ?>" style="max-height:600px">
                </div>
                <div class="slide">
                    <div class="slideNumber">3</div>
                    <img src="<?php echo htmlspecialchars($course['slide3']); ?>" style="max-height:600px">
                </div>

                <!-- Next and Previous icon to change images -->
                <a class="slider-previous" onclick="moveSlides(-1)">
                    <i class="fa fa-chevron-circle-left custom-icon"></i>
                </a>
                <a class="slider-next" onclick="moveSlides(1)">
                    <i class="fa fa-chevron-circle-right custom-icon"></i>
                </a>
            </div>
            <div class="description">
                <h2>Description</h2>
                <p id="descript-text">
                    <?php echo htmlspecialchars($course['detail']); ?>
                </p>
            </div>
            <div id="overlay" class="overlay">
                <div class="overlay-panel" id="overlay-panel">

                    <h2 style="padding: 20px; font-weight: 700;">Added to cart</h2>
                    <div class="overlay-detail">
                        <a href="/learning.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>" title="<?php echo htmlspecialchars($course['title']); ?>">
                            <img src="<?php echo htmlspecialchars($course['slide1']); ?>">
                        </a>
                        <div class="overlay-description">
                            <h3 style="font-weight: 400">
                                <a href="/learning.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>">
                                    <?php echo htmlspecialchars($course['title']); ?>
                                </a>
                            </h3>
                            <h3>
                                Author: <?php echo htmlspecialchars($course['author']); ?>
                            </h3>
                            <h3>
                                Price: <?php echo htmlspecialchars($course['price']); ?>
                            </h3>
                        </div>
                    </div>
                    <form method="post" action="/add_cart.php" id="add_cart_form" onsubmit="return false">
                        <input type="hidden" value="<?php echo htmlspecialchars($csrfToken) ?>" name="csrfToken">
                        <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['course_id']); ?>">
                        <input type="hidden" name="course_title" value="<?php echo htmlspecialchars($course['title']); ?>">
                        <input type="hidden" name="course_img" value="<?php echo htmlspecialchars($course['slide1']); ?>">
                        <input type="hidden" name="course_author" value="<?php echo htmlspecialchars($course['author']); ?>">
                        <input type="hidden" name="course_price" value="<?php echo htmlspecialchars($course['price']); ?>">
                        <button type="submit" class="cart-button-added" id="cart-button-added" onclick='return btnClick();'>Add to cart</button>
                    </form>
                </div>
            </div>
            <div class="other-component" style="margin-top: -15px;">
                <button type="button" class="cart-button" onclick="toggleAddButton()">Add to cart</button>
            </div>

        </div>
        <div class="footer">
            <span><a href="logout.php" style="font-size: 20px;">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a></span><br>
            <span><i class="fa fa-pencil-square-o"></i> Contact</span><a href="#"></a><br>

        </div>
    </div>
    <script th:inline="javascript">
        var cartIcon = document.querySelector(".fa-shopping-cart");
        var cartList = document.querySelector(".cart-list");

        cartIcon.addEventListener("click", function() {
            var isCartListVisible = cartList.style.display === "block";
            cartList.style.display = isCartListVisible ? "none" : "block";
        });
        let slideIndex = 1;
        displaySlide(slideIndex);

        function moveSlides(n) {
            displaySlide(slideIndex += n);
        }

        function activeSlide(n) {
            displaySlide(slideIndex = n);
        }

        /* Main function */
        function displaySlide(n) {
            let i;
            let totalslides =
                document.getElementsByClassName("slide");

            if (n > totalslides.length) {
                slideIndex = 1;
            }

            if (n < 1) {
                slideIndex = totalslides.length;
            }
            for (i = 0; i < totalslides.length; i++) {
                totalslides[i].style.display = "none";
            }
            totalslides[slideIndex - 1].style.display = "block";

        }

        /*===========================================================*/
        const menu = document.getElementById("overlay");

        function toggleAddButton() {
            menu.classList.toggle("show1")
        }
        window.onclick = (event) => {
            if (!event.target.matches('.cart-button')) {
                if (menu.classList.contains("show1")) {
                    menu.classList.remove("show1")
                }
            }
        }
        const menu2 = document.getElementById("overlay-panel");
        menu2.addEventListener('click', event => event.stopPropagation())
        const cartButton = document.querySelector(".cart-button-added");

        cartButton.addEventListener("click", () => {
            document.getElementById("overlay-panel").querySelector("form").submit();
        });

        /*===========================================================*/

        const notification = document.querySelector(".cart-notification"),
            closeIcon = document.querySelector(".close"),
            progress = document.querySelector(".progress");
        const checkCart = <?php echo htmlspecialchars($checkAdded); ?>;
        if (checkCart > 0) {
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