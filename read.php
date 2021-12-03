<!--
    chua lam
-->
<?php
    session_start();


    require_once ('./database/connect_database.php');


    if(!(isset($_GET['comic']) && isset($_GET['chapter']))) {
        header("location: ./");
        die();
    }

    $comic_id = $_GET['comic'];

    $sql = "select chap.id, chap.id_comic, chap.index, chap.name, chap.created_at, chap.updated_at, cm.id_user, cm.name name_cm, count(*) total from chapter chap join comic cm on chap.id_comic = cm.id where chap.id_comic = ".$comic_id." and chap.index = ".$_GET['chapter'];
    $result = EXECUTE_RESULT($sql);

    if($result[0]['total'] == 0) {
        header("location: ./");
        die();
    }

    EXECUTE("update comic set total_view = total_view + 1 where id=".$comic_id);

    $user =[];
    
    if(isset($_SESSION['user_id'])) {
        $sql = "select avatar, account_name from user where id = ".$_SESSION['user_id'];
        $user = EXECUTE_RESULT($sql);

        $sql = "SELECT * from notification where status = 'Chưa đọc' and id_user = ".$_SESSION['user_id']." order by created_at desc";
        $notification = EXECUTE_RESULT($sql);

        $sql = "insert into readed (id_user, id_chapter) values (".$_SESSION['user_id'].", ".$result[0]['id'].")";
        EXECUTE($sql);

        $now = time();
    }


    $sql = "select * from chapter chap join comic cm on chap.id_comic = cm.id where chap.id_comic = ".$_GET['comic']." order by chap.index asc";
    $chapter = EXECUTE_RESULT($sql);

    $sql = "select * from page pg join chapter chap on pg.id_chapter = chap.id where chap.id_comic = ".$_GET['comic']." and chap.index = ".$_GET['chapter']." order by pg.index asc";
    $page = EXECUTE_RESULT($sql);
     
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
        
        <link rel="stylesheet" type="text/css" href="./css/sidebar.css">
        <link rel="stylesheet" type="text/css" href="./css/footer.css">
        <link rel="stylesheet" type="text/css" href="./css/style-DT.css">
        <link rel="stylesheet" type="text/css" href="./css/breadcrumb.css">
        <link rel="stylesheet" type="text/css" href="./css/topbar.css">
        
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
                        #da-dang-nhap{
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

                <!-- Nút thao tác khi ở trang đọc truyện -->

                <li>
                    <a href="./comic.php?<?php echo "comic=".$comic_id; ?>">
                        <i class='bx bxs-book'></i>
                        <span class="links_name">Về bìa truyện</span>
                    </a>
                    <span class="tooltip">Về bìa truyện</span>
                </li>
                <li id="trg_trc">
                    <a href="./read.php?<?php echo "comic=".$comic_id."&chapter=".($_GET['chapter'] - 1); ?>">
                        <i class='bx bx-chevrons-left'></i>
                        <span class="links_name">Chương trước</span>
                    </a>
                    <span class="tooltip">Chương trước</span>
                </li>
                <li id="Nav_ListChapBtn">
                    <a>
                        <i class='bx bx-spreadsheet'></i>
                        <span class="links_name">Danh sách chương</span>
                    </a>
                    <span class="tooltip">Danh sách chương</span>
                </li>
                <li id="trg_sau">
                    <a href="./read.php?<?php echo "comic=".$comic_id."&chapter=".($_GET['chapter'] + 1); ?>">
                        <i class='bx bx-chevrons-right' ></i>
                        <span class="links_name">Chương sau</span>
                    </a>
                    <span class="tooltip">Chương sau</span>
                </li>      
            </ul>

            <?php
                if($_GET['chapter'] <= 1){
                    echo '<style>#trg_trc{display: none;}</style>';
                }
                if($_GET['chapter'] >= count($chapter)){
                    echo '<style>#trg_sau{display: none;}</style>';
                }
            ?>
            <div class="Nav_ListChap">
                <ul>
                    <?php
                        foreach($chapter as $item) {
                            echo '<li> <a href="./read.php?comic='.$comic_id.'&chapter='.$item['index'].'"> <span class="links_name">Chương '.$item['index'].'</span> </a> </li>';
                        }
                    ?>
                </ul>
            </div>
        </div>

        <button class="btntop" id="btntop">
            <i class='bx bx-send bx-rotate-270'></i>
        </button>

        <script>
            $("#btntop").click(function () {
                $("html").animate({
                    scrollTop:0
                }, 750);
            })
        </script>

        <!--Khối tiêu đề đầu trang, dùng để đổi server ảnh-->
        <div class="container-xxl" id="read-story-info">
            <!-- Thanh breadcrumb --> 
            <div class="contain_nav_breadvrumb">
                <nav  class="nav_breadcrumb" aria-label="Page breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item" aria-current="page"><i class='bx bxs-home'></i></li>
                        <li class="breadcrumb-item"><?php echo $result[0]['name_cm']; ?></li>
                        <li class="breadcrumb-item active">Chap <?php echo $_GET['chapter']." - ".$result[0]['name']; ?></li>
                    </ol>
                </nav>
            </div>
            <!--  -->
            <div class="caption">
                <a id="story-title"><?php echo $result[0]['name_cm']; ?></a>
            </div>
            <div class="caption">
                <a style="font-size: .75em;" id="story-chapter">Chap <?php echo $_GET['chapter']; ?></a>
            </div>
            <p style="font-size: 1em; text-align: center;">Nếu không xem được ảnh vui lòng chọn server khác dưới đây</p>
            <div id="server-option">
                <a class="btn btn-primary" href="#" role="button">Server 1</a>
                <a class="btn btn-primary" href="#" role="button">Server 2</a>
                <a class="btn btn-primary" href="#" role="button">Server 3</a>
            </div>
        </div>

        <!--Hình truyện đọc-->
        <div  id="contentDT">
            <?php
                foreach($page as $item) {
                    echo '<img src="'.$item['link_page'].'" alt="ayame">';
                }
            ?>
        </div>

        <!--Bình luận-->
        <div class="container-xxl" id="comment-area">
            <h2 class="caption">BÌNH LUẬN</h2>
            <!--nhap binh luan-->
            <div class="comment comment-input" id="comment-0" style="display: inline-block;">
                <div class="comment-info">
                    <a class="user-avt"><img src=
                        "<?php
                            if(isset($_SESSION['user_id'])) {
                                echo $user[0]['avatar'];
                            }
                            else {
                                echo "./img/logo.png";
                            }
                        ?>"
                    ></a>
                    <p class="user-name">
                        <?php
                            if(isset($_SESSION['user_id'])) {
                                echo $user[0]['account_name'];
                            }
                            else {
                                echo "taikhoan";
                            }
                        ?>
                    </p>
                </div>
                <form id="user-comment-0" name="comment" method="POST" action="./action_postcomment.php">
                    <textarea name="content" placeholder="Bình luận..."></textarea>
                    <input type="text" name="id_replay" value="<?php echo '-1'; ?>" style="display: none;">
                    <input type="text" name="id_comic" value="<?php echo $comic_id; ?>" style="display: none;">
                    <input type="text" name="chapter" value="<?php echo $_GET['chapter']; ?>" style="display: none;">
                    <input type="text" name="user_comic" value="<?php echo $result[0]['id_user']; ?>" style="display: none;">
                    <input type="text" name="link" value="./comic.php?comic=<?php echo $comic_id; ?>" style="display: none;">
                    <input type="text" name="account" value="<?php echo $user[0]['account_name']; ?>" style="display: none;">
                    <input type="submit"  class="send-cmt">
                </form>
            </div>
            
            <?php
                function print_comment($id_cm, $comic_id, $id_us_comic, $user) {
                    $sql = "select cm.id idm, cm.id_user, cm.content, cm.created_at, us.id, us.account_name, us.avatar from comment cm join user us on cm.id_user = us.id ";
                    if($id_cm == -1 ) $sql = $sql." where cm.id_reply is null and cm.id_comic = ".$comic_id." order by cm.created_at desc";
                    else $sql = $sql." where cm.id_reply = ".$id_cm." and cm.id_comic = ".$comic_id." order by cm.created_at desc";
                    $comment_ = EXECUTE_RESULT($sql);
                    foreach($comment_ as $item) {
                        echo '<div class="comment" id="comment-'.$item['idm'].'">
                        <div class="comment-info">
                            <a class="user-avt"><img src="'.$item['avatar'].'"></a>
                            <a class="user-name">'.$item['account_name'].'</a>
                            <p class="comment-time">'.$item['created_at'].'</p>
                        </div>
                        <div class="comment-content">
                            <p>'.$item['content'].'</p>
                        </div>
                        <div class="comment-reaction">
                            <button class="bi ';
                            if(isset($_SESSION['user_id'])) {
                                $ktra = EXECUTE_RESULT("select count(*) total from like_comment where id_comment = ".$item['idm']." and id_user = ".$_SESSION['user_id']);
                                if($ktra[0]['total'] != 0)
                                    echo 'bi-suit-heart-fill" type="button" id="like-comment-'.$item['idm'].'" onclick="thump_up('.$item['idm'].', '.$_SESSION['user_id'].', \''.$user[0]['account_name'].'\', '.$item['id_user'].', '.$comic_id.')"> Đã thích</button>';
                                else 
                                    echo 'bi-suit-heart" type="button" id="like-comment-'.$item['idm'].'" onclick="thump_up('.$item['idm'].', '.$_SESSION['user_id'].', \''.$user[0]['account_name'].'\', '.$item['id_user'].', '.$comic_id.')"> Thích</button>';
                            }
                            else
                                echo 'bi-suit-heart"  type="button" id="like-comment-'.$item['idm'].'"> Thích</button>';
                            
                            echo '<button class="bi bi-reply" type="button" onclick="reply_comment('.$item['idm'].')"> Phản hồi</button>
                        </div>';
                        
                        print_comment($item['idm'], $comic_id, $id_us_comic, $user);

                        echo '<!--đây là reply 1 cmt-->
                        <div class="comment comment-input" id="comment-'.$item['idm'].'-reply">
                            <div class="comment-info">
                                <a class="user-avt"><img src="';
                        
                        if(isset($_SESSION['user_id'])) echo $user[0]['avatar'];
                        else echo './img/logo.png';
                        
                        echo '"></a>
                                <a class="user-name">';
                                
                        if(isset($_SESSION['user_id'])) echo $user[0]['account_name'];
                        else echo 'taikhoan';
        
                        echo '</a>   
                            </div>
                            <form id="user-comment-0" name="comment" method="POST" action="./action_postcomment.php">
                                <textarea name="content" placeholder="Bình luận..."></textarea>
                                <input type="text" name="id_replay" value="'.$item['idm'].'" style="display: none;">
                                <input type="text" name="link" value="./comic.php?comic='.$comic_id.'" style="display: none;">
                                <input type="text" name="id_usreplay" value="'.$item['id_user'].'" style="display: none;">
                                <input type="text" name="id_comic" value="'.$comic_id.'" style="display: none;">
                                <input type="text" name="account" value="'.$user[0]['account_name'].'" style="display: none;">
                                <input type="text" name="chapter" value="'.$_GET['chapter'].'" style="display: none;">
                                <input type="text" name="user_comic" value="'.$id_us_comic.'" style="display: none;">
                                <input type="submit"  class="send-cmt">
                            </form>
                            <button class="close-cmt btn btn-danger delete-button" type="button">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>';
                    }
                }

                print_comment(-1, $comic_id, $result[0]['id_user'], $user);
            ?>


            <script>
                function reply_comment(button) {
                    document.getElementById("comment-"+button+"-reply").classList.toggle("d-inline-block")
                }
                function close_comment(button) {
                    document.getElementById("comment-"+button+"-reply").classList.toggle("d-inline-block")
                }
                function execute_query(query) {
                    $data = "query";
                    $.ajax({
                        url : "action_query.php",
                        type : "post",
                        dataType:"text",
                        data : {
                            data: $data,
                            query : $query
                        },
                        success : function (result){}
                    });
                }
                function thump_up(id_cm, user, name, us_cm, cm) {
                    let like = document.getElementById("like-comment-"+id_cm);
                    if (like.classList.contains("bi-suit-heart"))
                    {
                        like.classList.replace("bi-suit-heart", "bi-suit-heart-fill")
                        like.innerHTML=" Đã thích"
                        $query = "insert into like_comment (id_comment, id_user) values ('"+id_cm+"', '"+user+"')"
                        execute_query($query)
                        $query = "insert into notification (id_user, type, content, link) values ('"+us_cm+"', 'Thích bình luận', '"+name+" đã thích bình luận của bạn.', './comic.php?comic="+cm+"')"
                        execute_query($query)
                    }
                    else {
                        like.classList.replace("bi-suit-heart-fill", "bi-suit-heart")
                        like.innerHTML=" Thích"
                        $query = "delete from like_comment where id_comment="+id_cm+" and id_user="+user
                        execute_query($query)
                    }
                }
            </script>
        </div>
         
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
        <script language="javascript" src="./js/jsheader.js"></script>
        <script language="javascript" src="./js/sidebarType2.js"></script>
    </body>
</html>