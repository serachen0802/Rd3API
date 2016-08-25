<?php
require_once 'Connect.php';

class API extends Connect
{
//     public function __construct()
//     {
//         if (isset($_GET["url"])) {
// 			$url = rtrim($_GET["url"], "/");
// 			$url = explode("/", $url);
//             // $params = $url[0];
//             echo $url;
//         }
//         call_user_func_array([$api, $methodName], $params);
//         // if(isset($function)){
//         //     header("/rd3Api/index.php/$function");
//         // }
//     }
    public function createAccount()
    {
        // $account = $_GET['account'];
        // $password = $_GET['password'];
        $account = "sera08024";
        $password = "123456";

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

    public function getBalance()
    {
        // $account = $_GET['account'];
        $account = "sera0802333";
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

    public function transfer()
    {
        // $account = $_GET['account'];
        // $transId = $_GET['transId'];
        // $type = $_GET['type'];
        // $amount = $_GET['amount'];
        $account = "sera0802";
        $transId = '4';
        $type = 'IN';
        $amount = 1500;

        $a = $this->db->prepare("SELECT `transId` FROM `transfer` WHERE `transId` = :transId");
        $a->bindParam(':transId', $transId);
        $a->execute();
        $data = $a->fetch(PDO::FETCH_ASSOC);
        // return  $data['transId'];
        if ($transId == $data['transId']) {
            $data['status'] = 'Repeat transfer';
            // return 'Repeat transfer';
        }else{
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
            if ($type =='OUT') {
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
            }
        }
        return $data;
    }

    public function checkTransfer()
    {
        $account = $_GET['account'];
        $transId = $_GET['transId'];
        $account = "sera0802";
        $transId = "2";

        $a = $this->db->prepare("SELECT * FROM `transfer` WHERE `transId` = :transId AND `account` = :account");
        $a->bindParam(':transId', $transId);
        $a->bindParam(':account', $account);
        $a->execute();
        $data = $a->fetch(PDO::FETCH_ASSOC);
        if (isset($data['transId'])) {
            $data['status'] = 'success';
        } else {
            $data['status'] = 'Not found transfer';
        }
        return $data;
    }

    public function parseUrl() {
		if (isset($_GET["url"])) {
		    echo $_GET['url'];
			$url = rtrim($_GET["url"], "/");
			$url = explode("/", $url);
			return $url;
		}
    }
}

$api = new API();
$data = $api->createAccount();
// $data = $api->getBalance();
// $data = $api->transfer();
// $data = $api->checkTransfer();
$response['status'] = $status;
$response['message'] = $message;
$response['data'] = $data;
echo json_encode($data);
