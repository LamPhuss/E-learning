<?php
require 'database.php';
include('auth.php');

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["course_title"])) {
        $course_title = $_GET["course_title"];
    } else {
        header("/search_course.php?course_title=");
    }
}
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = array();
}
$cart = $_SESSION["cart"];
$cartQuantity = array_count_values($cart);
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
                <ul class="cart-list">

                    <li>
                        <img src="https://gamek.mediacdn.vn/zoom/310_200/133514250583805952/2024/4/3/photo-1712115378001-17121153780921990182544-0-120-675-1200-crop-17121154235981022045473.png">
                        <h3 style="font-weight: 700">
                            <a href="#">
                                Learn about basic HTML
                            </a>
                        </h3>
                        <h3 style="font-weight: 400; font-size: 15px">
                            Author: HTML
                        </h3>
                        <h3>
                            Price: 500$
                        </h3>
                        <hr>
                    </li>
                    <li>
                        <img src="https://gamek.mediacdn.vn/zoom/310_200/133514250583805952/2024/4/3/photo-1712115378001-17121153780921990182544-0-120-675-1200-crop-17121154235981022045473.png">
                        <h3 style="font-weight: 700">
                            <a href="#">
                                Learn about basic HTML
                            </a>
                        </h3>
                        <h3 style="font-weight: 400; font-size: 15px">
                            Author: HTML
                        </h3>
                        <h3>
                            Price: 500$
                        </h3>
                        <hr>
                    </li>
                    <li>
                        <img src="https://gamek.mediacdn.vn/zoom/310_200/133514250583805952/2024/4/3/photo-1712115378001-17121153780921990182544-0-120-675-1200-crop-17121154235981022045473.png">
                        <h3 style="font-weight: 700">
                            <a href="#">
                                Learn about basic HTML
                            </a>
                        </h3>
                        <h3 style="font-weight: 400; font-size: 15px">
                            Author: HTML
                        </h3>
                        <h3>
                            Price: 500$
                        </h3>
                        <hr>
                    </li>
                    <li>
                        <h2 style="font-weight: 700">Total: 1500$</h2>
                        <button type="button" class="cart-pucharse-button">Pucharse</button>

                    </li>
                </ul>

            </div>
        </div>
    </header>
    <div class="wrapper">
        <div class="search_wrap">
            <div class="search_item">
                <div class="search_box">
                    <input type="text" class="input_search" placeholder="Search..." id="input_search">
                </div>
                <button class="icon" onclick="handleSearch()"><i class="fa fa-search"></i></button>

            </div>
        </div>
    </div>
    <div>
        <div class="listsubject">

            <h2 class="_style_top"></h2>
            <?php
            $sql = "SELECT * FROM courses WHERE title LIKE CONCAT( '%', ?, '%')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $course_title);
            $stmt->execute();
            $result = $stmt->get_result();
            $courses = array();

            while ($row = $result->fetch_assoc()) {
                $courses[] = $row;
            }
            ?>
            <?php
            if (count($courses) > 0) :
            ?>
                <?php
                $page_number = isset($_GET['page']) ? $_GET['page'] : 1;
                $total_courses = count($courses); // Tổng số bài viết
                $courses_per_page = 5; // Số bài viết trên mỗi trang

                $total_pages = ceil($total_courses / $courses_per_page); // Số trang
                // Kiểm tra xem trang hiện tại có lớn hơn tổng số trang không
                if ($page_number > $total_pages) {
                    $page_number = $total_pages; // Đặt trang hiện tại là trang cuối cùng
                }

                $start_index = ($page_number - 1) * $courses_per_page; // Vị trí bắt đầu của bài viết trên trang hiện tại
                $remaining_courses = $total_courses - $start_index; // Số bài viết còn lại trên trang hiện tại

                // Điều chỉnh số bài viết trên trang cuối cùng nếu không đủ 5 bài viết
                if ($page_number == $total_pages && $remaining_courses < $courses_per_page) {
                    $courses_per_page = $remaining_courses;
                }

                $current_courses = array_slice($courses, $start_index, $courses_per_page); // Lấy danh sách bài viết của trang hiện tại 
                ?>
                <ul id="fistUpload1">
                    <?php foreach ($current_courses as $course) : ?>
                        <li class="top"><a href="/learning.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>" title="<?php echo htmlspecialchars($course['title']); ?>">
                                <img loading="lazy" src="<?php echo htmlspecialchars($course['slide1']); ?>" alt="<?php echo htmlspecialchars($course['title']); ?>" width="310" height="200"> </a>
                            <div class="info">
                                <h3><a title="<?php echo htmlspecialchars($course['title']); ?>" href="/learning.php?course_id=<?php echo htmlspecialchars($course['course_id']); ?>"><?php echo htmlspecialchars($course['title']); ?></a></h3>
                                <p><?php echo htmlspecialchars($course['description']); ?>.</p>
                                <p class="time"><a href="#" class="author"><?php echo htmlspecialchars($course['author']); ?></a> - <a href="" class="categame"></a><?php echo htmlspecialchars($course['date_created']); ?></p>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php
            else :
                $total_pages = 0;
            ?>

                <h5>No courses found</h5>

            <?php
            endif;
            ?>
            <div class="pagination-wrapper">
                <div class="search-pagination">
                    <?php
                    if ($total_pages > 0) :
                    ?>
                        <?php
                        if ($total_pages < 5) :
                        ?>
                            <?php
                            for ($i = 1; $i <= $total_pages; $i++) :
                            ?>
                                <?php if ($i == $page_number) : ?>
                                    <span aria-current="page" class="page-numbers current"><?php echo htmlspecialchars($i); ?></span>
                                <?php else : ?>
                                    <a class="page-numbers" href="javascript:;"><?php echo htmlspecialchars($i); ?></a>
                                <?php endif; ?>
                            <?php endfor; ?>
                        <?php else : ?>
                            <?php if ($page_number - 3 <= 0) : ?>
                                <?php
                                for ($i = 1; $i <= $page_number + 4; $i++) :
                                ?>
                                    <?php if ($i == $page_number) : ?>
                                        <span aria-current="page" class="page-numbers current"><?php echo htmlspecialchars($i); ?></span>
                                    <?php else : ?>
                                        <a class="page-numbers" href="javascript:;"><?php echo htmlspecialchars($i); ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <a class="next-page page-numbers" href="javascript:;">Last</a>
                            <?php elseif ($page_number + 4 >= $total_pages) : ?>
                                <a class="prev-page page-numbers" href="javascript:;">First</a>
                                <?php for ($i = $page_number; $i <= $total_pages; $i++) : ?>
                                    <?php if ($i == $page_number) : ?>
                                        <span aria-current="page" class="page-numbers current"><?php echo htmlspecialchars($i); ?></span>
                                    <?php else : ?>
                                        <a class="page-numbers" href="javascript:;"><?php echo htmlspecialchars($i); ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            <?php else : ?>
                                <a class="prev-page page-numbers" href="javascript:;">First</a>
                                <?php for ($i = $page_number - 2; $i <= $page_number + 2; $i++) : ?>
                                    <?php if ($i == $page_number) : ?>
                                        <span aria-current="page" class="page-numbers current"><?php echo htmlspecialchars($i); ?></span>
                                    <?php else : ?>
                                        <a class="page-numbers" href="javascript:;"><?php echo htmlspecialchars($i); ?></a>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php else : ?>
                        <pre>
                    <?php endif; ?>  
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
    <span><a href="logout.php" style="font-size: 20px;">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a></span><br>
        <span><i class="fa fa-pencil-square-o"></i> Contact</span><a href="#"></a><br>

    </div>
    <script type="text/javascript">
        var cartIcon = document.querySelector(".fa-shopping-cart");
        var cartList = document.querySelector(".cart-list");

        cartIcon.addEventListener("click", function() {
            var isCartListVisible = cartList.style.display === "block";
            cartList.style.display = isCartListVisible ? "none" : "block";
        });
        // For search bar
        const searchButton = document.getElementsByClassName('icon');
        var searchInput = document.getElementById('input_search');

        // Định nghĩa hàm xử lý tìm kiếm
        function handleSearch() {
            // Lấy giá trị từ khóa tìm kiếm và loại tìm kiếm đã chọn
            const searchKeyword = searchInput.value;
            window.location.href = "search_course.php?course_title=" + encodeURIComponent(searchKeyword) + "&page=1";
            searchInput.value = '';
        }
        //
        
    </script>
</body>
</html>