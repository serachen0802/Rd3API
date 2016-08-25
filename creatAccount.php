<?php
require_once 'Connect.php';
class API extends Connect
{
    public function createAccount()
    {
        $account = $_GET['account'];
        $password = $_GET['password'];

        if (!preg_match("/^([A-Za-z0-9]+)$/",$account)) {
            $data['status'] = "account Only use number and English alphabet";
            return $data;
        }
        if (!preg_match("/^([A-Za-z0-9]+)$/",$password)) {
            $data['status'] = "password Only use number and English alphabet";
            return $data;
        }

        if ($account && $password) {
            $a = $this->db->prepare("SELECT `account` FROM `account` WHERE `account` = :account");
            $a->bindParam(':account', $account);
            $a->execute();
            $data = $a->fetch(PDO::FETCH_ASSOC);

            if ($account == $data['account']) {
                $data['status'] = 'This account is repeat';
            } else {
                $insert = $this->db->prepare("INSERT INTO `account`" .
                        "(`account`, `password`)" .
                        "VALUES (:account, :password)");
                $insert->bindParam(':account', $account);
                $insert->bindParam(':password', $password);
                $insert->execute();
                $data['account'] = $account;
                $data['status'] = 'Create account success';
            }
        return $data;
        }
    }
}
$api = new API();
$data = $api->createAccount();
if ($data != null){
    echo json_encode($data);
}
