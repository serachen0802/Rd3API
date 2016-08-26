<?php
require_once 'Connect.php';
class API extends Connect
{
     public function transfer()
    {
        $account = $_GET['account'];
        $transId = $_GET['transId'];
        $type = $_GET['type'];
        $amount = $_GET['amount'];

        if ($account && $transId && $type && $amount) {

            if (!preg_match("/^([A-Za-z0-9]+)$/", $account)) {
            $data['status'] = "account Only use number and English alphabet";

            return $data;
            }

            if (!preg_match("/^([0-9]+)$/", $amount)) {
                $data['status'] = "Amount can only be a number";

                return $data;
            }

            if (!preg_match("/^([A-Z]+)$/", $type)) {
                $data['status'] = "type Only English uppercase";

                return $data;
            }

            if (!preg_match("/^([0-9]+)$/", $transId)) {
                $data['status'] = "transId can only be a number";

                return $data;
            }


            $a = $this->db->prepare("SELECT `transId` FROM `transfer` WHERE `transId` = :transId");
            $a->bindParam(':transId', $transId);
            $a->execute();
            $data = $a->fetch(PDO::FETCH_ASSOC);

            if ($transId == $data['transId']) {
                $data['status'] = 'Repeat transfer';
            } else {
                    $insert = $this->db->prepare("INSERT INTO `transfer`" .
                        "(`account`, `transId`, `type`, `amount`)" .
                        "VALUES (:account, :transId, :type, :amount)");
                    $insert->bindParam(':account', $account);
                    $insert->bindParam(':transId', $transId);
                    $insert->bindParam(':type', $type);
                    $insert->bindParam(':amount', $amount, PDO::PARAM_INT);
                    $insert->execute();

                if ($type == 'IN') {
                    $sql = "UPDATE `account` SET `balance` = `balance` + :amount WHERE `account` = :account";
                    $update = $this->db->prepare($sql);
                    $update->bindParam(':account', $account);
                    $update->bindParam(':amount', $amount, PDO::PARAM_INT);
                    $update->execute();
                    $data['account'] = $account;
                    $data['transId'] = $transId;
                    $data['type'] = $type;
                    $data['amount'] = $amount;
                    $data['status'] = 'Success';
                }

                if ($type == 'OUT') {
                    $b = $this->db->prepare("SELECT `balance` FROM `account` WHERE `account` = :account");
                    $b->bindParam(':account', $account);
                    $b->execute();
                    $data = $b->fetch(PDO::FETCH_ASSOC);
                    if ($data['balance'] > $amount) {
                        $sql = "UPDATE `account` SET `balance` = `balance` - :amount WHERE `account` = :account";
                        $update = $this->db->prepare($sql);
                        $update->bindParam(':account', $account);
                        $update->bindParam(':amount', $amount, PDO::PARAM_INT);
                        $update->execute();
                        $data['account'] = $account;
                        $data['transId'] = $transId;
                        $data['type'] = $type;
                        $data['amount'] = $amount;
                        $data['status'] = 'Success';
                    } else {
                        // 餘額不足無法轉出
                        $data['status'] = 'Insufficient balance';
                    }
                }
            }
        } else {
            $data['status'] = 'Insufficient balance';
        }
        return $data;
    }
}
$api = new API();
$data = $api->transfer();
if ($data != null) {
    echo json_encode($data);
}