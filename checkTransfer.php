<?php
require_once 'Connect.php';
class API extends Connect
{
   public function checkTransfer()
    {
        $account = $_GET['account'];
        $transId = $_GET['transId'];
        if (!$account && $transId) {
            $data['status'] = 'Please enter your account';

            return $data;
        }

        if ($account && !$transId) {
            $data['status'] = 'Please enter your transId';

            return $data;
        }

        if ($account && $transId) {
            if (!preg_match("/^([A-Za-z0-9]+)$/", $account)) {
                $data['status'] = "account Only use number and English alphabet";

                return $data;
            }

            if (!preg_match("/^([0-9]+)$/", $transId)) {
                $data['status'] = "transId Only use number";

                return $data;
            }
            $a = $this->db->prepare("SELECT * FROM `transfer` WHERE `transId` = :transId AND `account` = :account");
            $a->bindParam(':transId', $transId);
            $a->bindParam(':account', $account);
            $a->execute();
            $data = $a->fetch(PDO::FETCH_ASSOC);
            if (isset($data['transId'])) {
                $data['status'] = 'Transfer success';
            } else {
                $data['status'] = 'Not found transfer information';
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