<?php
require_once 'Connect.php';
class API extends Connect
{
     public function getBalance()
    {
        $account = $_GET['account'];

        if ($account) {
            if (!preg_match("/^([A-Za-z0-9]+)$/", $account)) {
                $data['status'] = "account Only use number and English alphabet";

                return $data;
            }

            $a = $this->db->prepare("SELECT `account`,`balance` FROM `account` WHERE `account` = :account");
            $a->bindParam(':account', $account);
            $a->execute();
            $data = $a->fetch(PDO::FETCH_ASSOC);
            if (isset($data['account'])) {
                $data['status'] = "success";
            } else {
                $data['status'] = "Not found account";
            }
        return $data;
        }
    }
}
$api = new API();
$data = $api->getBalance();

if ($data != null) {
    echo json_encode($data);
}