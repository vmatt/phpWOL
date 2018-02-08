<?php
include_once ('wol_functions.php');
?>

<?php
print_r($_POST);

if ($_POST['list'][0]==1){
    wakeUp('20:CF:30:6F:E7:9E', '192.168.1.255');
}
?>