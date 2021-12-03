<?php 
    session_start();


    require_once ('./database/connect_database.php');


    $data = isset($_POST['data']) ? $_POST['data'] : false;


    if ($data == "danh-dau-da-doc" && isset($_SESSION['user_id'])){
        $sql = "update notification set status = 'Đã đọc' where status = 'Chưa đọc' and id_user = ".$_SESSION['user_id'];
        EXECUTE($sql);

        $sql = "SELECT * from notification where status = 'Chưa đọc' and id_user = ".$_SESSION['user_id']." order by created_at desc";
        $notification = EXECUTE_RESULT($sql);

        echo '<button onclick="open_list(\'notification-list\'), close_list(\'account-setting-list\')">
            <i class=\'bx bx-bell\' ></i>
            <div id="so-thong-bao">';
        if(count($notification) > 0) {
            echo '<span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
            style="font-family: inherit; font-size: 0.75em;" id="hienthisotb">'.count($notification).'
                <span class="visually-hidden">unread messages</span>
            </span>';
        }
        echo '</div>
            </button>
            <div id="notification-list">
            <div id="option-bar">
                <button id="danh-dau-da-doc" onclick="danh_dau_da_doc()">
                    <i class="bi bi-check-circle"></i>
                    <p>Đánh dấu đã đọc</p>
                </button>
                <button onclick="turn_on_off_notifi(\'noti-btn-i\', \'noti-btn-text\')">
                    <i class=\'bx bx-bell-off\' id="noti-btn-i"></i>
                    <p id="noti-btn-text">Tắt thông báo</p>
                </button>
            </div>
            <ul id="notification-list-content">';
        
        $index = 0;
        foreach ($notification as $item) {
            echo '<li class="notification-box" id="notification-'.$index.'">
                <a href="#">'.$item['Content'].'
                    <span class="badge bg-warning text-dark">Mới</span>
                </a>
                <p><i class="bi bi-clock"></i>'.$item['created_at'].'</p>
            </li>';
        }

        echo '</ul>
        </div>
        </div>';
        die();
    }
?>