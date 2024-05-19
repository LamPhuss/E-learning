<?php
require 'database.php';
include('auth.php');
include('validation.php');
if (strcmp($user['user_role'], "admin") == 0) {
    if (
        isset($_POST["title"]) && isset($_POST["course_description"])  && isset($_POST["course_detail"]) && isset($_POST["course_slide1"]) && isset($_POST["course_slide2"])
        && isset($_POST["course_slide3"]) && isset($_POST["course_author"]) && isset($_POST["course_link"]) && isset($_POST["course_price"])
        && isset($_POST["course_view"])
    ) {
        $errorMsg = null;
        $error = 0;
        $title = trimAndCheckNull($_POST["title"]);
        $course_description = trimAndCheckNull($_POST["course_description"]);
        $course_detail = trimAndCheckNull($_POST["course_detail"]);
        $course_slide1 = trimAndCheckNull($_POST["course_slide1"]);
        $course_slide2 = trimAndCheckNull($_POST["course_slide2"]);
        $course_slide3 = trimAndCheckNull($_POST["course_slide3"]);
        $course_author = trimAndCheckNull($_POST["course_author"]);
        $course_link = trimAndCheckNull($_POST["course_link"]);
        $course_price = floatval($_POST["course_price"]);
        $course_view = intval($_POST["course_view"]);
        if (is_null($title)) {
            $error = 1;
            $errorMsg = $errorMsg . "&tnull_var";
        }
        if (is_null($course_description)) {
            $error = 1;
            $errorMsg = $errorMsg . "&dsnull_var";
        }
        if (is_null($course_detail)) {
            $error = 1;
            $errorMsg = $errorMsg . "&dtnull_var";
        }
        if (is_null($course_slide1) || is_null($course_slide2) || is_null($course_slide3)) {
            $error = 1;
            $errorMsg = $errorMsg . "&snull_var";
        }
        if (is_null($course_author)) {
            $error = 1;
            $errorMsg = $errorMsg . "&anull_var";
        }
        if (is_null($course_link)) {
            $error = 1;
            $errorMsg = $errorMsg . "&lnull_var";
        } else {
            if (checkDuplicateLink($conn, $course_link)) {
                $error = 1;
                $errorMsg = $errorMsg . "&duplicate";
            }
        }
        if (is_null($course_price)) {
            $error = 1;
            $errorMsg = $errorMsg . "&pnull_var";
        }
        if ($error == 0) {
            $sql = "INSERT INTO courses(course_id, title, description, detail, slide1, slide2, slide3, author, download_link, price, view) VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssssdi", $title, $course_description, $course_detail, $course_slide1, $course_slide2, $course_slide3, $course_author, $course_link, $course_price, $course_view);
            if ($stmt->execute()) {
                header("Location:add_course.php?success");
                exit;
            } else {
                echo $stmt->error;
            }
        } else {

            $errorMsg = substr($errorMsg, 1);
            header("Location:add_course.php?" . $errorMsg);
            exit;
        }
    }
}
function checkDuplicateLink($conn, $course_link)
{
    $sql = "SELECT * FROM `courses` WHERE download_link = ? ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $course_link);
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
    // Loại bỏ khoảng trắng ở đầu và cuối chuỗi
    $trimmedString = trim($string);

    // Kiểm tra xem chuỗi đã được cắt tỉa có null hay không
    if (is_null($trimmedString) || empty($trimmedString)) {
        return null;
    } else {
        // Trả về chuỗi đã được cắt tỉa và loại bỏ null
        return $trimmedString;
    }
}


include("resources/static/html/header.html");

?>
<html>

<body>
    <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
        <?php if (isset($_GET["success"])) : ?>
            <h3 style="color:blue">Update success, please reload page to see new result</h3>
            <script th:inline="javascript">
                $('#description-validation').addClass('invalid-blank');
            </script>
        <?php endif; ?>
    <?php endif; ?>
    <h2 style="padding: 20px 20px 0px 20px; font-weight: 700;">Add Course</h2>
    <form method="post" enctype="multipart/form-data" id="add_cart_form">
        <div class="payment-checkout-container">

            <div class="cart-detail">
                <h4>
                    Title
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="title" id="title-validation">
                <?php
                if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["tnull_var"])) : ?>
                        <span class="blank-message" id="error">Title can not be null</span>
                        <script th:inline="javascript">
                            $('#title-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Description
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_description" type="text" id="description-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["dsnull_var"])) : ?>
                        <span class="blank-message" id="error">Description can not be null</span>
                        <script th:inline="javascript">
                            $('#description-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Course Detail
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_detail" type="text" id="detail-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["dtnull_var"])) : ?>
                        <span class="blank-message" id="error">Course detail can not be null</span>
                        <script th:inline="javascript">
                            $('#detail-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    First slide
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_slide1" type="text" id="slide1-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["snull_var"])) : ?>
                        <span class="blank-message" id="error">Slide can not be null</span>
                        <script th:inline="javascript">
                            $('#slide1-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>

                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Second slide
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_slide2" type="text" id="slide2-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["snull_var"])) : ?>
                        <span class="blank-message" id="error">Slide can not be null</span>
                        <script th:inline="javascript">
                            $('#slide2-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Third slide
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_slide3" type="text" id="slide3-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["snull_var"])) : ?>
                        <span class="blank-message" id="error">Slide can not be null</span>
                        <script th:inline="javascript">
                            $('#slide3-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Author
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_author" type="text" id="author-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["anull_var"])) : ?>
                        <span class="blank-message" id="error">Author can not be null</span>
                        <script th:inline="javascript">
                            $('#author-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Download link
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_link" type="text" id="link-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["lnull_var"])) : ?>
                        <span class="blank-message" id="error">Link can not be null</span>
                        <script th:inline="javascript">
                            $('#link-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                    <?php if (isset($_GET["duplicate"])) : ?>
                        <span class="blank-message" id="error">Link existed</span>
                        <script th:inline="javascript">
                            $('#link-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    Price
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_price" type="text" id="price-validation">
                <?php if ($_SERVER["REQUEST_METHOD"] == "GET") : ?>
                    <?php if (isset($_GET["pnull_var"])) : ?>
                        <span class="blank-message" id="error">Don't miss the price</span>
                        <script th:inline="javascript">
                            $('#price-validation').addClass('invalid-blank');
                        </script>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="cart-detail">
                <h4>
                    View
                </h4>
                <input placeholder="XXXXXXXXXXXX" name="course_view" type="text" id="view-validation">
            </div>
        </div>

        <button type="submit" style="margin: 20px 0px 0px 150px;" class="cart-button-added" id="confirm-btn">Confirm</button>
    </form>

</body>
<script th:inline="javascript">
    $('#title-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#title-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#description-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#description-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#detail-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#detail-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#slide1-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#slide1-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#slide2-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#slide2-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#slide3-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#slide3-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#author-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#author-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#link-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#link-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#price-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#price-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    $('#detail-validation').on('input', function(evt) {
        var value = evt.target.value;
        $('span#error').remove();
        if (value.length === 0) {
            $('#detail-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            evt.target.className = 'invalid-blank';

        } else {
            evt.target.className = 'valid';
        }
    })
    document.getElementById('confirm-btn').addEventListener('click', function(event) {
        if ($('#title-validation').val().length === 0) {
            $('#title-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#title-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#description-validation').val().length === 0) {
            $('#description-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#description-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#detail-validation').val().length === 0) {
            $('#detail-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#detail-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#slide1-validation').val().length === 0) {
            $('#slide1-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#slide1-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#slide2-validation').val().length === 0) {
            $('#slide2-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#slide2-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#slide3-validation').val().length === 0) {
            $('#slide3-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#slide3-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#author-validation').val().length === 0) {
            $('#author-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#author-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#link-validation').val().length === 0) {
            $('#link-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#link-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#price-validation').val().length === 0) {
            $('#price-validation').after('<span class="blank-message" id="error">Dont leave it blank!</span>');
            $('#price-validation').addClass('invalid-blank');
            event.preventDefault();
        }
        if ($('#title-validation').hasClass('invalid-blank') || $('#description-validation').hasClass('invalid-blank') || $('#detail-validation').hasClass('invalid-blank') ||
            $('#slide1-validation').hasClass('invalid-blank') || $('#slide2-validation').hasClass('invalid-blank') || $('#slide3-validation').hasClass('invalid-blank') ||
            $('#author-validation').hasClass('invalid-blank') || $('#link-validation').hasClass('invalid-blank') || $('#price-validation').hasClass('invalid-blank')) {
            event.preventDefault();
        }
    });
</script>

</html>