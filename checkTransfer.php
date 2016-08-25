<?php
require_once 'Connect.php';
class API extends Connect
{
   public function checkTransfer()
    {
        $account = $_GET['account'];
        $transId = $_GET['transId'];

        if($account){
            $a = $this->db->prepare("SELECT * FROM `transfer` WHERE `transId` = :transId AND `account` = :account");
            $a->bindParam(':transId', $transId);
            $a->bindParam(':account', $account);
            $a->execute();
            $data = $a->fetch(PDO::FETCH_ASSOC);
            if (isset($data['transId'])) {
                $data['status'] = 'Transfer success';
            } else {
                $data['status'] = 'Not found transfer';
            }
            return $data;
        }
    }
}
$api = new API();
$data = $api->checkTransfer();

if ($data != null) {
    echo json_encode($data);
}