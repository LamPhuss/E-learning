<?php
require 'database.php';
include('auth.php');
include("resources/static/html/header.html");

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $duplicate = isset($_GET["duplicate"]) ? true : false;
    $nullVar = isset($_GET["null_var"]) ? true : false;
    $specialChar = isset($_GET["special_char"]) ? true : false;
    $checkDuplicate = 0;
    if ($duplicate) {
        $checkDuplicate = 1;
    }
    $checkNull = 0;
    if ($nullVar) {
        $checkNull = 1;
    }
    $checkSpecialChar = 0;
    if ($specialChar) {
        $checkSpecialChar = 1;
    }
}
if (strcmp($user['user_role'], "admin") != 0) {
    header("Location:error.php");
    exit;
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
            <div class="overlay-panel" id="overlay-panel" style="width:500px; margin-left:800px; height:720px">
                <iframe name="headerframe" width="100%" height="720px" frameborder="0" src="add_user.php"></iframe>
            </div>
        </div>
        <h2 class="manage-heading">User Management</h2>
        <div class="row">
            <table id="example" class="table table-striped table-bordered" style="width:100%">
                <thead>
                    <tr>
                        <th><input type="checkbox" onclick="checkAll(this)"></th>
                        <th>User Name</th>
                        <th>Email</th>
                        <th>Password</th>
                        <th>Phone</th>
                        <th>Address</th>
                        <th>Edit</th>
                    </tr>
                </thead>
                <?php
                $sql = "SELECT * FROM users WHERE user_role=?";
                $stmt = $conn->prepare($sql);
                $user_role = "user";
                $stmt->bind_param("s", $user_role);
                $stmt->execute();
                $result = $stmt->get_result();
                $user_list = array();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $user_list[] = $row;
                    }
                }
                ?>
                <tbody>
                    <?php foreach ($user_list as $user) : ?>
                        <tr>
                            <form method="POST" action="/user_manage_update.php" enctype="multipart/form-data">
                                <td><input type="checkbox" name="id" id="<?php echo htmlspecialchars($user['id']); ?>"></td>
                                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($user['id']); ?>">
                                <td><input type="text" id="username-input-<?php echo htmlspecialchars($user['id']); ?>" value="<?php echo htmlspecialchars($user['username']); ?>" class="input-profile-field" name="username" disabled></td>
                                <td><input type="text" id="email-input-<?php echo htmlspecialchars($user['id']); ?>" value="<?php echo htmlspecialchars($user['email']); ?>" class="input-profile-field" name="email" disabled></td>
                                <td><input type="text" id="password-input-<?php echo htmlspecialchars($user['id']); ?>" value="<?php echo htmlspecialchars($user['password']); ?>" class="input-profile-field" name="password" disabled></td>
                                <td><input type="text" id="phone-input-<?php echo htmlspecialchars($user['id']); ?>" value="<?php echo htmlspecialchars($user['phone']); ?>" class="input-profile-field" name="phone" disabled></td>
                                <td><input type="text" id="address-input-<?php echo htmlspecialchars($user['id']); ?>" value="<?php echo htmlspecialchars($user['address']); ?>" class="input-profile-field" name="address" disabled></td>
                                <td><button class="btn btn-xs" id='btn-xs' type='submit'> <i class="fa-solid fa-wrench"></i></button></td>
                            </form>
                        </tr>
                    <?php endforeach; ?>

                </tbody>
            </table>
            <button class="btn btn-delete" onclick="deleteFunction()">Delete <i class="fa fa-trash"></i></button>
            <button class="btn btn-add" onclick="toggleMenu()">Add User <i class="fas fa-user-plus"></i></button>
        </div>
    </div>
    <div class="footer">
    <span><a href="logout.php" style="font-size: 20px;">Logout <i class="fa fa-sign-out" aria-hidden="true"></i></a></span><br>
        <span><i class="fa fa-pencil-square-o"></i> Contact</span><a href="#"></a><br>

    </div>
    <script th:inline="javascript">
        $(document).ready(function() {
            $('#example').DataTable(

                {

                    "aLengthMenu": [
                        [5, 10, 25, -1],
                        [5, 10, 25, "All"]
                    ],
                    "iDisplayLength": 5
                }
            );
        });


        function checkAll(bx) {
            var cbs = document.getElementsByTagName('input');
            for (var i = 0; i < cbs.length; i++) {
                if (cbs[i].type == 'checkbox') {
                    cbs[i].checked = bx.checked;
                }
            }
        }
        /* ================================================================ */
        const menu = document.getElementById("overlay");

        function toggleMenu() {
            menu.classList.toggle("show1")
        }
        window.onclick = (event) => {
            if (!event.target.matches('.btn-add')) {
                if (menu.classList.contains("show1")) {
                    menu.classList.remove("show1")
                }
            }
        }
        const menu2 = document.getElementById("overlay-panel");
        menu2.addEventListener('click', event => event.stopPropagation())
        /* ================================================================ */
        $('.btn-xs').click(function(e) {
            var $btn = $(this);
            if ($btn.text() !== 'Submit') {
                e.preventDefault();
                $btn.text('Submit');
                $('.input-profile-field').prop('disabled', false);
            }

        });
        /* ============================================================= */
        const notification = document.querySelector(".cart-notification"),
            closeIcon = document.querySelector(".close"),
            progress = document.querySelector(".progress");
        const checkDup = <?php echo htmlspecialchars($checkDuplicate); ?>;
        const checkNull = <?php echo htmlspecialchars($checkNull); ?>;
        const checkSpecialChar = <?php echo htmlspecialchars($checkSpecialChar); ?>;
        if (checkDup > 0 || checkNull > 0 || checkSpecialChar > 0) {
            const messageDiv = $('.message');
            if (checkDup > 0) {
                const dupErrorMsg = $("<span class='text'>Insert fail, Username duplicated</span>");
                messageDiv.append(dupErrorMsg);
            }
            if (checkNull > 0) {
                const nullErrorMsg = $("<span class='text'>Username, Password and Email can not be null</span>");
                messageDiv.append(nullErrorMsg);
            }
            if (checkSpecialChar > 0) {
                const charErrorMsg = $("<span class='text'>Username, Password can not contain special character or blank space</span>");
                messageDiv.append(charErrorMsg);
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
        /* ================================================ */
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');

        function deleteFunction() {
            const checkedCheckboxes = Array.from(checkboxes).filter(checkbox => checkbox.checked);
            const checkedIds = checkedCheckboxes.map(checkbox => checkbox.id);

            // Tạo đối tượng XHR
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '/delete_user.php'); // Thay đổi URL đích cho phù hợp
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            const checkedIdsString = checkedIds.join(',');

            // Gửi yêu cầu XHR
            xhr.onload = function() {
                if (xhr.status === 200) {
                    // Xử lý phản hồi thành công
                    console.log('Xóa dữ liệu thành công!');
                    window.location.reload();
                } else {
                    // Xử lý lỗi
                    console.error('Lỗi khi xóa dữ liệu:', xhr.statusText);
                }
            };

            xhr.send(`checkedIds=${checkedIdsString}`);
        }
    </script>
</body>

</html>