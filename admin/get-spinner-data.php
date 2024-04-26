<?php
include('session.php');
include('common_functions.php');

$code = $_POST['code'];
$page  = $_POST['page'];

$code = stripslashes($code);
$page =  stripslashes($page);

$error = '';  // Variable To Store Error Message

//echo "admin is ".$_SESSION['admin'];
if (!isset($_SESSION['admin'])) {
    header("Location: index.php");

} else if ($code == $_SESSION['_token']) {

    try {
        if ($_POST['perpage']) {
            $limit = $_POST['perpage'];
        } else {
            $limit = 10;
        }

        $start = ($page - 1) * $limit;
        $totalrow = 0;

        $status = 0;
        $msg = "Unable to Get Data";
        $return = array();

        $inactive = "active";
        $stmt = $conn->prepare("SELECT id, name, value, image FROM spinners");

        $stmt->execute();
        $data = $stmt->bind_result($col1, $col2, $col3, $col4);
        $return = array();
        $i = 0;
        while ($stmt->fetch()) {

            $return[$i] =
                array(
                    'id' => $col1,
                    'name' => $col2,
                    'value' => $col3,
                    'image' => $col4 ? '<img src="' . MEDIA_URL . $col4 . '" style="height: 75px; margin-right: 6px">' : '',
                );
            $i = $i + 1;
            $status = 1;
            $msg = "Details here";
        }

        $information = array(
            'status' => $status,
            'msg' =>   $msg,
            'data' => $return
        );


        $stmt12 = $conn->prepare("SELECT count(id) FROM spinners ");

        $stmt12->execute();
        $stmt12->store_result();
        $stmt12->bind_result($col55);

        while ($stmt12->fetch()) {
            $totalrow = $col55;
        }

        $page_html =  pagination('getSpinners', $page, $limit, $totalrow);

        echo json_encode(array("status" => 1, "page_html" => $page_html, "data" => $return, "totalrowvalue" => $totalrow));
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
