<?php
require 'database.php';
include('auth.php');

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
    <div class="container">
        <div class="listsubject">

            <h2 class="_style_top"><a href="#">Recent</a></h2>
            <?php
            $sql = "SELECT * FROM courses ORDER BY date_created asc";
            $result = $conn->query($sql);
            $courses_recent = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $courses_recent[] = $row;
                }
            }
            ?>
            <ul class="upload-recent">
                <?php foreach ($courses_recent as $course) : ?>
                    <li class="top"><a href="/learning.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>" title="<?php echo htmlspecialchars($course['title']); ?>">
                            <img loading="lazy" src="<?php echo htmlspecialchars($course['slide1']); ?>" width="310" height="200"> </a>
                        <div class="info">
                            <h3><a href="/learning.php?course_id=<?php echo $course['course_id']; ?>"><?php echo htmlspecialchars($course['title']); ?></a></h3>
                            <p><?php echo htmlspecialchars($course['description']); ?>.</p>
                            <p class="time"><a href="#" class="author"><?php echo htmlspecialchars($course['author']); ?></a> - <a href="/mobile-social.chn" class="categame"></a><?php echo htmlspecialchars($course['date_created']); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
            <h2 class="_style_top"><a href="#">Most viewed</a></h2>
            <?php
            $sql = "SELECT * FROM courses ORDER BY view desc";
            $result = $conn->query($sql);
            $courses_view = array();

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $courses_view[] = $row;
                }
            }
            ?>
            <ul id="upload-most">

                <?php foreach ($courses_view as $course) : ?>
                    <li class="top"><a href="/learning.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>" title="<?php echo htmlspecialchars($course['title']); ?>">
                            <img loading="lazy" src="<?php echo htmlspecialchars($course['slide1']); ?>" width="310" height="200"> </a>
                        <div class="info">
                            <h3><a href="/learning.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>"><?php echo htmlspecialchars($course['title']); ?></a></h3>
                            <p><?php echo htmlspecialchars($course['description']); ?>.</p>
                            <p class="time"><a href="#" class="author"><?php echo htmlspecialchars($course['author']); ?></a> - <a href="/mobile-social.chn" class="categame"></a><?php echo htmlspecialchars($course['date_created']); ?></p>
                        </div>
                    </li>
                <?php endforeach; ?>

            </ul>

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
    </script>
</body>

</html>