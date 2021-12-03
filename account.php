<!--
    ma hoa mat khau
-->

<?php
    session_start();


    require_once ('./database/connect_database.php');


    if(isset($_SESSION['user_id'])) {
        $sql = "select * from user ur join login lg on ur.id = lg.id_user where id = ".$_SESSION['user_id'];
        $user = EXECUTE_RESULT($sql);

        $sql = "SELECT * from NOTIFICATION where status = 'Chưa đọc' and id_user = ".$_SESSION['user_id']." order by created_at desc";
        $notification = EXECUTE_RESULT($sql);

        $now = time();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Đọc truyện tranh Manga, Manhua, Manhwa, Comic online hay và cập nhật thường xuyên tại PhieuTruyen.Com">
        <meta property="og:site_name" content="PhieuTruyen.Com">
        <meta name="Author" content="PhieuTruyen.Com">
        <meta name="keyword" content="doc truyen tranh, manga, manhua, manhwa, comic">
        <title>Đọc truyện tranh Manga, Manhua, Manhwa, Comic Online</title>
        <link href='https://fonts.googleapis.com/css?family=Roboto' rel='stylesheet'>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF"
        crossorigin="anonymous">
        <link rel="stylesheet" type="text/css" href="./css/topbar.css">
        <link rel="stylesheet" type="text/css" href="./css/sidebar.css">
        <link rel="stylesheet" type="text/css" href="./css/style-QLTTTK.css">
        <link rel="stylesheet" type="text/css" href="./css/breadcrumb.css">
        <link rel="stylesheet" type="text/css" href="./css/footer.css">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kQtW33rZJAHjgefvhyyzcGF3C5TFyBQBA13V1RKPf4uH+bwyzQxZ6CmMZHmNBEfJ"
        crossorigin="anonymous"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.3.0/font/bootstrap-icons.css">
        <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

        <script language="javascript">
            function danh_dau_da_doc(){
                $data = "danh-dau-da-doc";
                $.ajax({
                    url : "notification.php",
                    type : "post",
                    dataType:"text",
                    data : {
                        data : $data
                    },
                    success : function (result){
                        $('#notification-button').html(result);
                    }
                });
            }
        </script>
    </head>
    <body>
        <style>
            .form_search {
                width: 500px;
                height: inherit;
            }
            #luu-anh-dai-dien {
                position: absolute;
                cursor: pointer;
                top: 20px;
                left: 20px;
                width: 300px;
                height: 35px;
                background:#C4C4C4;
                border-radius: 5px;
                font-weight: bold;
            }
        </style>
        <header id="top-bar">
            <div class="container-xxl d-flex justify-content-between position-relative">
                <div id="top-bar-left">
                    <a class="logo" href="./index.php">
                        <img src="./img/image.png" alt="logo">
                    </a>
                    <div class="search-bar">
                        <form class="form_search" action="./search.php" method="GET">
                            <input type="text" placeholder="Nhập tên truyện" name="search" id="search-name">
                            <button class="search-button align-content-center">
                            <i class="bi bi-search align-middle" style="font-size: 20px;"></i>
                        </button>
                        </form>
                    </div>
                </div>

                <?php
                    if (isset($_SESSION['user_id'])) {
                        echo '<style>
                        #chua-dang-nhap {
                            display: none;
                        }
                        </style>';
                    } else {
                        echo '<style>
                        #da-dang-nhap, #content {
                            display: none;
                        }
                        </style>';
                    }
                ?>

                <div class="top-bar-right" id="chua-dang-nhap">
                    <button id="login-button" onclick="location.href='./login.php';">Đăng nhập</a></button>
                    <button id="register-button" onclick="location.href='./register.php';">Đăng ký</button>
                </div>
                <!--Da dang nhap-->
                <div class="top-bar-right" id="da-dang-nhap"">
                    <div id="notification-button">
                        <button onclick="open_list('notification-list'), close_list('account-setting-list')">
                            <i class='bx bx-bell' ></i>
                            <div id="so-thong-bao">
                            <?php
                                if(isset($_SESSION['user_id']) && count($notification) > 0) {
                                    echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                    style="font-family: inherit; font-size: 0.75em;" id="hienthisotb">'.count($notification).'
                                        <span class="visually-hidden">unread messages</span>
                                    </span>';
                                }
                            ?>
                            </div>
                            
                        </button>

                        <div id="notification-list">
                            <div id="option-bar">
                                <button id="danh-dau-da-doc" onclick="danh_dau_da_doc()">
                                    <i class="bi bi-check-circle"></i>
                                    <p>Đánh dấu đã đọc</p>
                                </button>
                                <button onclick="turn_on_off_notifi('noti-btn-i', 'noti-btn-text')">
                                    <i class='bx bx-bell-off' id="noti-btn-i"></i>
                                    <p id="noti-btn-text">Tắt thông báo</p>
                                </button>
                            </div>
                            <ul id="notification-list-content">
                                <?php
                                if(isset($_SESSION['user_id'])) {
                                    $index = 0;
                                    foreach ($notification as $item) {
                                        $inmoi = "";
                                        $time = $item['created_at'];                                                                                                                                                                                                                                                                                                            
                                        $time = date_parse_from_format('Y-m-d H:i:s', $time);
                                        $time_stamp = mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],$time['year']);
                                        if(($now - $time_stamp) <= 60*60){
                                            $inmoi = "<span class=\"badge bg-warning text-dark\">Mới</span>";
                                        }

                                        echo '<li class="notification-box" id="notification-'.$index.'">
                                        <a href="'.$item['link'].'">'.$item['content'].$inmoi.'</a>
                                        <p><i class="bi bi-clock"></i>'.$item['created_at'].'</p>
                                        </li>';
                                    }
                                }
                                ?>
                            </ul>
                        </div>
                    </div>
                    <button id="account-button" onclick="open_list('account-setting-list'), close_list('notification-list')">
                            <img src="
                            <?php
                                if (isset($_SESSION['user_id']) && $user[0]['avatar'] != NULL) echo $user[0]['avatar'];
                                else echo './img/logo.png'
                            ?>
                            ">
                        </button>
                    <div id="account-setting-list">
                        <a href="./account.php">Quản lý thông tin tài khoản</a>
                        <a href="./mycomic.php">Quản lý truyện đã đăng</a>
                        <a href="./logout.php">Đăng xuất</a>
                    </div>
                </div>
            </div>
        </header>

        <!-- Thanh công cụ -->  
        <div class="sidebar">
            <div class="logo-detail" style="background-color: #B4A5FF;">
                <i class='bx bx-menu' id="btn-menu"></i>
            </div>
            <ul class="nav-list">
                <li>
                    <a href="./">
                        <i class='bx bxs-home'></i>
                        <span class="links_name">Trang chủ</span>
                    </a>
                    <span class="tooltip">Trang chủ</span>
                </li>
                <li>
                    <a href="./typecomic.php">
                        <i class='bx bxs-purchase-tag' ></i>
                        <span class="links_name">Thể loại</span>
                    </a>
                    <span class="tooltip">Thể loại</span>
                </li>
                <li>
                    <a href="./updated.php">
                        <i class='bx bxs-hourglass'></i>
                        <span class="links_name">Mới cập nhật</span>
                    </a>
                    <span class="tooltip">Mới cập nhật</span>
                </li>
                <li>
                    <a href="./following.php">
                        <i class='bx bxs-heart' ></i>
                        <span class="links_name">Theo dõi</span>
                    </a>
                    <span class="tooltip">Theo dõi</span>
                </li>
                <li>
                    <a href="./history.php">
                        <i class='bx bx-history' ></i>
                        <span class="links_name">Lịch sử đọc</span>
                    </a>
                    <span class="tooltip">Lịch sử đọc</span>
                </li>
                <li>
                    <a href="./feedback.php">
                        <i class='bx bx-mail-send' ></i>
                        <span class="links_name">Phản hồi</span>
                    </a>
                    <span class="tooltip">Phản hồi</span>
                </li>
                <li  id="btn-light-dark">
                    <a>
                        <i class='bx bxs-bulb'></i>
                        <span class="links_name">Bật/Tắt đèn</span>
                    </a>
                    <span class="tooltip">Bật/Tắt đèn</span>
                </li>   
            </ul>
        </div>
        
        <!--QLTTTK-->
        <div class="contentQLTK container-xxl" id="content">
            <div class="contain_nav_breadvrumb">
                <nav  class="nav_breadcrumb" aria-label="Page breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" aria-current="page"><i class='bx bxs-home'></i></li>
                        <li class="breadcrumb-item">Quản lý tài khoản</li>
                    </ol>
                </nav>
            </div>

            <div>
            <h1 class="caption">QUẢN LÝ THÔNG TIN TÀI KHOẢN</h1>
            <!--account-info-->
            <div class="account-info" id="acc-info">

                <div class="input-img" style="display: block; position: relative;">
                    
                    <img id="avatar-profile" src="
                        <?php
                            if ($user[0]['avatar'] != NULL) echo $user[0]['avatar'];
                            else echo './img/logo.png'
                        ?>" alt="avatar" id="acc-avatar">
                    <label for="file-input">
                        <i class="bi bi-camera-fill"></i>
                    </label>
                    <form id="abcnabx-avatar" method="POST" action="./update_photo.php" enctype="multipart/form-data">
                        <input type="file" class="form-select" class="form-control" aria-label="file example" id="file-input" name="file-input" style="display: none;">
                        <input type="submit" value="Cập nhật" id="luu-anh-dai-dien" style="display:none;">
                    </form>
                </div>

                <script>
                    document.getElementById("file-input").onchange = function () {
                        document.getElementById('luu-anh-dai-dien').setAttribute('style', 'display: inline-block');
                        var reader = new FileReader();

                        reader.onload = function (e) {
                            // get loaded data and render thumbnail.
                            document.getElementById("avatar-profile").src = e.target.result;
                        };

                        // read the image file as a data URL.
                        reader.readAsDataURL(this.files[0]);
                    };

                </script>


                <!--account info detail-->
                <form class="account-info-detail" id="acc-info-detail" action="./update_info.php" method="POST">

                    <input type="text" class="form-control" name="accout" aria-label="Disabled input example" readonly value="<?php echo $user[0]['username']?>" id="acc-username">
                    <input type="text" class="form-control" name="username" placeholder="Tên người dùng" id="acc-name" value="<?php echo $user[0]['account_name']?>">
                    <input type="email" class="form-control" name="gmail" placeholder="Email" id="acc-email" value="<?php echo $user[0]['email']?>">
                    <div class="container d-flex flex-row justify-content-between" style="padding: 0;">
                        <input type="date" class="form-control" name="dateofbith" placeholder="Ngày sinh" id="acc-dob" value="<?php echo $user[0]['dateofbirth']?>">
                        <select class="form-select" style="max-width: 400px;" name="sex" id="acc-sex">
                            <option selected disabled value="">Chọn giới tính</option>

                            <?php
                                if($user[0]['sex'] == "Nam") {
                                    echo '<option selected>Nam</option>
                                    <option>Nữ</option>
                                    <option>Khác</option>';
                                }
                                else if($user[0]['sex'] == "Nữ") {
                                    echo '<option>Nam</option>
                                    <option selected>Nữ</option>
                                    <option>Khác</option>';
                                }
                                    else if($user[0]['sex'] == "Khác") {
                                            echo '<option>Nam</option>
                                            <option>Nữ</option>
                                            <option selected>Khác</option>';
                                        }
                                        else {
                                            echo '<option>Nam</option>
                                            <option>Nữ</option>
                                            <option>Khác</option>';
                                        }
                            ?>

                        </select>
                    </div>
                    <input type="text" class="form-control" name="id" placeholder="<?php echo $user[0]['id']?>" readonly id="acc-uid">
                    <input type="url" class="form-control" name="facebook" placeholder="Facebook" id="acc-fb" value="<?php echo $user[0]['facebook']?>">
                    <input type="submit" value="Cập nhật">
                </form>

                <!--change-password-->
                <script type="text/javascript">
                    function validateForm() {
                        $password = $('#inputPassword1').val();
                        $confimpass = $('#inputPassword2').val();
                        if($password != $confimpass) {
                            alert("Mật khẩu không khớp")
                            return false
                        }
                        return true
                    }
                </script>

                <div class="change-password container d-flex flex-column" id="c-password">
                <form method="POST" action="./update_password.php" onsubmit="return validateForm();">
                    <input type="password" class="form-control" required="true" id="inputPassword0" name="current_pass" placeholder="Mật khẩu hiện tại" minlength="6">
                    <input type="password" class="form-control" required="true" id="inputPassword1" name="new_passI" placeholder="Nhập mật khẩu mới" minlength="6">
                    <input type="password" class="form-control" required="true" id="inputPassword2" name="new_passII" placeholder="Nhập lại mật khẩu mới" minlength="6">
                    <button class="btn-outline-primary">Đổi mật khẩu</button>
                </form>
                </div>
                
            </div>
            </div>
        </div>

        <footer class="site_footer">
            <div class="Grid" >
                <div class="Grid_row">
                 <div class="Grid_Column">
                     
                     <h5 class="footer_heading" >About Us</h5>                  
                     <ul class="footer_list">
                         <li class="footer_item">
                             <a href="" class="footer_item_link">Đọc truyện miễn phí</a></li>
                         <li class="footer_item">
                             <a href="" class="footer_item_link">Hỗ trợ cho anh em đồng bào</a></li>
                         <li class="footer_item">
                             <a href="" class="footer_item_link">Tạo môi trường giao lưu</a></li>
                          <li class="footer_item">
                             <a href="" class="footer_item_link">Báo cáo</a></li>
                     </ul>
                     </div>
         
                 <div class="Grid_Column">
                     <h5 class="footer_heading">Contact Us</h5>
                     <ul class="footer_list">
                         <li class="footer_item">
                             <a href="" class="footer_item_link">Email: Truyencuatui@example.com</a> </li>
                          <li class="footer_item">
                                 <a href="" class="footer_item_link">Liên hệ QC</a></li>
                         <li class="footer_item">
                             <a a href="" class="footer_item_link">Telephone Contact</a></li>
                         <li class="footer_item">
                            <a href="" class="footer_item_link"> <address>
                                Địa chỉ
                             </address></a>
                         </li>
                         
                     </ul>
                 </div>
                </div>             
            </div>
             <div class="footer_bottom">
                 <div class="Grid">
              
                     <p class="footer_foot">&#169 2020 - Bản quyền thuộc về Truyencuatui</p>
                 
                 </div>
             </div>
         </footer>
         <script language="JavaScript" src="./js/jsheader.js"></script>
         <script language="JavaScript" src="./js/sidebarType1.js"></script>
    </body>   
</html>