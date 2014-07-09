<?php
function theme_basic_element($body) {
    echo '<div>'.$body.'</div>';
}
function theme_full_page($body) {
    echo '<div style="width: 100%; height: 100%">'.$body.'</div>';
}
?>