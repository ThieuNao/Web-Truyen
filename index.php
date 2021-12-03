<?php
    session_start();
    require_once ('./database/connect_database.php');

    if(isset($_SESSION['user_id'])) {
        $sql = "select avatar from user where id = ".$_SESSION['user_id'];
        $user = EXECUTE_RESULT($sql);

        $sql = "SELECT * from NOTIFICATION where status = 'Chưa đọc' and id_user = ".$_SESSION['user_id']." order by created_at desc";
        $notification = EXECUTE_RESULT($sql);
    }

    $now = time();
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
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-uWxY/CJNBR+1zjPWmfnSnVxwRheevXITnMqoEIeG1LJrdI0GlVs/9cVSyPYXdcSF" crossorigin="anonymous">

        <link rel="stylesheet" type="text/css" href="./css/topbar.css">
        <link rel="stylesheet" type="text/css" href="./css/sidebar.css">
        <link rel="stylesheet" type="text/css" href="./css/story-list-style.css">
        <link rel="stylesheet" type="text/css" href="./css/footer.css">
        <link rel="stylesheet" type="text/css" href="./css/style-home.css">

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-kQtW33rZJAHjgefvhyyzcGF3C5TFyBQBA13V1RKPf4uH+bwyzQxZ6CmMZHmNBEfJ" crossorigin="anonymous"></script>
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
        </style>
        <!--header-->
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
                        #da-dang-nhap, #da-duoc-dang-nhap {
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

        <!--test banner mới-->

        <div id="banner-top" class="container-xxl">

            <!--slider start-->
            <div class="slider">
                <div class="slides">
                    <input type="radio" name="radio-btn" id="radio1">
                    <input type="radio" name="radio-btn" id="radio2">
                    <input type="radio" name="radio-btn" id="radio3">
                    <input type="radio" name="radio-btn" id="radio4">
                    <input type="radio" name="radio-btn" id="radio5">
                    <!--slider end-->

                    <!--banner start-->
                    <?php
                        $sql = "select cm.name, cm.status, cm.total_view, cm.coverphoto, cm.detail, cm.id idcomic, count(fl.id_user) tdoi from comic cm left join follow fl on cm.id = fl.id_comic where cm.status != 'Chờ duyệt' GROUP by cm.id limit 5";
                        $truyen_banner = EXECUTE_RESULT($sql);

                        $index = 1;
                        foreach ($truyen_banner as $item) {
                            $abc = "banner first";
                            if($index != 1) $abc = "banner";
                            echo '<div class="'.$abc.'" id="banner-'.($index++).'">
                            <img src="'.($item['coverphoto']).'" onclick="location.href=\'./comic.php?comic='.($item['idcomic']).'\'" style="cursor: pointer;">
                            <div class="banner-info">
                                <h1 class="banner-info-title">'.($item['name']).'</h1>
                                <p class="banner-info-detail">Tình trạng truyện: '.($item['status']).'</p>
                                <p class="banner-info-detail">Lượt xem: '.($item['total_view']).'</p>
                                <p class="banner-info-detail">Lượt theo dõi: '.($item['tdoi']).'</p>
                                <div class="story-info-category">';

                            $sql = "select * from tag_comic str join tag tg on str.id_tag = tg.id where str.id_comic = ".$item['idcomic'];
                            $theloai = EXECUTE_RESULT($sql);
                            foreach ($theloai as $tl) {
                                echo '<button class="category btn-outline-primary" onclick="location.href=\'./typecomic.php?tagid'.$tl['id'].'=on\';">'.$tl['name'].'</button>';
                            }

                            echo '</div>
                                <p class="banner-info-detail">'.($item['detail']).'</p>
                            </div>
                            </div>';
                        }
                    ?>
                    <!--banner end-->

                    <!--auto navigation start-->
                    <div class="auto-navigation">
                        <div class="auto-btn1"></div>
                        <div class="auto-btn2"></div>
                        <div class="auto-btn3"></div>
                        <div class="auto-btn4"></div>
                        <div class="auto-btn5"></div>
                    </div>
                    <!--auto navigation end-->
                </div>

                <!--manual navigation start-->
                <div class="manual-navigation">
                    <label for="radio1" class="manual-btn" onclick="set_counter(1)"></label>
                    <label for="radio2" class="manual-btn" onclick="set_counter(2)"></label>
                    <label for="radio3" class="manual-btn" onclick="set_counter(3)"></label>
                    <label for="radio4" class="manual-btn" onclick="set_counter(4)"></label>
                    <label for="radio5" class="manual-btn" onclick="set_counter(5)"></label>
                </div>
                <!--manual navigation end-->
            </div>

            <!--3 children ngoai banner-->
            <button class="change-banner-button bi bi-chevron-compact-left" type="button"
                style="top: 40%; right: 101%;"
                id="previous-banner-f"
                onclick="previous_bannerf()">
            </button>

            <button class="change-banner-button bi bi-chevron-compact-right" type="button"
                style="top: 40%; left: 101%;"
                id="previous-banner-f"
                onclick="next_bannerf()">
            </button>

            <script>
                var counter = 1;
                function play_banner() {
                    if(counter > 5) counter = 1;
                    if(counter < 1) counter = 5;
                    document.getElementById('radio' + counter).checked = true;
                    counter++;
                }

                var bannertimer

                function set_counter(index) {
                    counter = index;
                    clearInterval(bannertimer);
                    play_banner();
                    bannertimer = setInterval(play_banner, 5000);
                }

                play_banner();
                bannertimer = setInterval(play_banner, 5000);

                function next_bannerf() {
                    clearInterval(bannertimer);
                    play_banner();
                    bannertimer = setInterval(play_banner, 5000);
                }

                function previous_bannerf() {
                    clearInterval(play_banner);
                    counter=counter-2;
                    play_banner();
                    myBanner_play = setInterval(myTimer, 5000);
                }
            </script>

        </div>

        <!--Story list TRUYEN MOI NHAT -->
        <div class="container-xxl" id="contentTC">
            <!--Story list 0-->
            <ul class="stories-list" id="0-SL">
                <h1 class="caption">Truyện mới nhất</h1>
                <?php
                    $sql = "select cm.updated_at, cm.total_chapter, cm.created_at, cm.name, cm.status, cm.total_view, cm.coverphoto, cm.detail, cm.id idcomic, count(fl.id_user) tdoi from comic cm left join follow fl on cm.id = fl.id_comic where cm.status != 'Chờ duyệt' GROUP by cm.id ORDER BY cm.updated_at DESC LIMIT 10";

                    $truyen_banner = EXECUTE_RESULT($sql);

                    $index = 1;
                    foreach ($truyen_banner as $item) {
                        $inmoi = "";
                        $time = $item['created_at'];                                                                                                                                                                                                                                                                                                            
                        $time = date_parse_from_format('Y-m-d H:i:s', $time);
                        $time_stamp = mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],$time['year']);
                        if(($now - $time_stamp) <= 7*24*60*60){
                            $inmoi = "<span class=\"badge bg-warning text-dark\">Mới</span>";
                        }
                        echo '<li class="story" id="0'.($index).'-story">
                        <div class="story-i-tag">
                            <span class="badge bg-info text-dark">'.($item['updated_at']).'</span>'.$inmoi.'
                        </div>
                        <a href="./comic.php?comic='.($item['idcomic']).'">
                            <img src="'.($item['coverphoto']).'" alt="tk">
                            <h6 class="story-title">'.($item['name']).'</h6>
                        </a>               
                        <p class="story-chapter"><a href="#">'.($item['total_chapter']).'</a></p>
                        <div class="story-info"  id="0'.($index++).'-story-info">
                            <h1 class="story-info-title">'.($item['name']).'</h1>
                            <p class="story-info-detail">Tình trạng truyện: '.($item['status']).'</p>
                            <p class="story-info-detail">Lượt xem: '.($item['total_view']).'</p>
                            <p class="story-info-detail">Lượt theo dõi: '.($item['tdoi']).'</p>
                            <div class="story-info-category">';

                        $sql = "select * from tag_comic str join tag tg on str.id_tag = tg.id where str.id_comic = ".$item['idcomic'];
                        $theloai = EXECUTE_RESULT($sql);
                        foreach ($theloai as $tl) {
                            echo '<button class="category btn-outline-primary" onclick="location.href=\'./typecomic.php?tagid'.$tl['id'].'=on\';">'.$tl['name'].'</button>';
                        }

                        echo '</div>
                            <p class="story-info-detail">'.($item['detail']).'</p>
                        </div>
                    </li>';
                    }

                ?>

                <div style="width: 100%">
                    <a href="./updated.php" class="row open-list">Xem thêm</a>
                </div>
            </ul>

            <!--story list TRUYEN NHIEU NGUOI XEM NHAT -->
            <ul class="stories-list" id="1-SL">
                <h1 class="scaption">Truyện nhiều lượt xem nhất</h1>

                <?php
                    $sql = "select cm.total_chapter, cm.created_at, cm.updated_at, cm.name, cm.status, cm.total_view, cm.coverphoto, cm.detail, cm.id idcomic, count(fl.id_user) tdoi from comic cm left join follow fl on cm.id = fl.id_comic where cm.status != 'Chờ duyệt' GROUP by cm.id ORDER BY cm.total_view DESC LIMIT 10";
                    $truyen_banner = EXECUTE_RESULT($sql);

                    $index = 0;
                    foreach ($truyen_banner as $item) {
                        $inmoi = "";
                        $time = $item['created_at'];                                                                                                                                                                                                                                                                                                            
                        $time = date_parse_from_format('Y-m-d H:i:s', $time);
                        $time_stamp = mktime($time['hour'],$time['minute'],$time['second'],$time['month'],$time['day'],$time['year']);
                        if(($now - $time_stamp) <= 7*24*60*60){
                            $inmoi = "<span class=\"badge bg-warning text-dark\">Mới</span>";
                        }
                        echo '<li class="story" id="1'.($index).'-story">
                    <div class="story-i-tag">
                        <span class="badge bg-info text-dark">'.($item['updated_at']).'</span>'.$inmoi.'
                    </div>
                    <a href="./comic.php?comic='.($item['idcomic']).'">
                        <img src="'.($item['coverphoto']).'" alt="tk">
                        <h6 class="story-title">'.($item['name']).'</h6>
                    </a>               
                    <p class="story-chapter"><a href="#">'.($item['total_chapter']).'</a></p>
                    <div class="story-info"  id="1'.($index++).'-story-info">
                        <h1 class="story-info-title">'.($item['name']).'</h1>
                        <p class="story-info-detail">Tình trạng truyện: '.($item['status']).'</p>
                        <p class="story-info-detail">Lượt xem: '.($item['total_view']).'</p>
                        <p class="story-info-detail">Lượt theo dõi: '.($item['tdoi']).'</p>
                        <div class="story-info-category">';

                        $sql = "select * from tag_comic str join tag tg on str.id_tag = tg.id where str.id_comic = ".$item['idcomic'];
                        $theloai = EXECUTE_RESULT($sql);
                        foreach ($theloai as $tl) {
                            echo '<button class="category btn-outline-primary" onclick="location.href=\'./typecomic.php?tagid'.$tl['id'].'=on\';">'.$tl['name'].'</button>';
                        }

                        echo '</div>
                        <p class="story-info-detail">'.($item['detail']).'</p>
                    </div>
                    </li>';
                    }

                ?>

                <!-- <div style="width: 100%">
                    <a href="#" class="open-list">Xem thêm</a>
                </div> -->
            </ul>
        </div>



        <!--side bar-->
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
        
        <button class="btntop" id="btntop">
            <i class='bx bx-send bx-rotate-270'></i>
        </button>

        <!--tam thoi xoa truyen khoi danh sach-->
        <script>
            function delete_story (element_id) {
                document.getElementById(element_id+"-story").classList.add("d-none")
            }
        </script>


        <!--footer-->
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
                        <li class="footer_item">
                            <a href="" class="footer_item_link">Tải App</a></li>
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
        
        <script language="JavaScript" src="./js/sidebarType1.js"></script>
        <script language="JavaScript" src="./js/jsheader.js"></script>
        <script language="JavaScript" src="./js/story-list.js"></script>
    </body>
</html>