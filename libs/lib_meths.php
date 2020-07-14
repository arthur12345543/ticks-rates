<?php
class lib_meths {

    /**
     * [validate description]
     * @param  String $address BTC Address string
     * @return Boolean validation result
     */
    public function validate($address)
    {        
        $addr = $this->decode_base58($address);
        if (strlen($addr) != 50)
        {
          return false;
        }        
        $check = substr($addr, 0, strlen($addr) - 8);
        $check = pack("H*", $check);
        $check = strtoupper(hash("sha256", hash("sha256", $check, true)));
        $check = substr($check, 0, 8);
        return $check == substr($addr, strlen($addr) - 8);
    }
    public function valid_data($data)
    {
        $data = trim(htmlspecialchars($data));
        return $data;
    }
    public function is_decimal($val)
    {
        return is_numeric($val);
    }
    public function valid_nickname($data)
    {
        if(preg_match("/\A[A-Za-z0-9]+\z/", $data)){
            return true;
        }else{
            return false;
        }
    }
    public function valid_size_nickname($data)
    {
        $sizeof = strlen($data);
        if($sizeof<2 || $sizeof>20){
            return false;
        }else{
            return true;
        }
    }
    public function cut_middle_of_address($data){
        $maxLength = 25;
        $numRightChars = ceil($maxLength / 2);
        $numLeftChars = floor($maxLength / 2) - 3; // to accommodate the "..."

        return sprintf("%s...%s", substr($data, 0, $numLeftChars), substr($data, 0 - $numRightChars));
    }
    public function getUserIP(){
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];
        if(filter_var($client, FILTER_VALIDATE_IP)){
            $ip = $client;
        }elseif(filter_var($forward, FILTER_VALIDATE_IP)){
            $ip = $forward;
        }else{
            $ip = $remote;
        }
        return $ip;
    }
    public function sum_lr($amount,$IntVal,$FloatVal)
    {
        $parts = explode('.', (string)$amount);
        $aIntVal = $parts[0];
        $aFloatVal = ($amount-$aIntVal)*100000000;
        $sumFloats = $aFloatVal+$FloatVal;
        $sumInts = $aIntVal+$IntVal;
        $check = $sumFloats/100000000;
        if($check>=1){
            $sumFloats = $sumFloats-100000000;
            $sumInts = $sumInts+1;
        }
        return $result = array($sumInts,$sumFloats);
    }
    public function getInt($amount)
    {
        if($amount==0){
            return $aIntVal=0;
        }else{
            $parts = explode('.', (string)$amount);
            $aIntVal = $parts[0];
            return $aIntVal;
        }
    }
    public function getFloat($amount)
    {
        if($amount==0){
            return $aFloatVal=0;
        }else{
            $parts = explode('.', (string)$amount);
            if(isset($parts[1])){
                $aFloatVal = $parts[1];
            }else{
                $aFloatVal = 0;
            }
            return $aFloatVal;
        }
    }
    public function int_to_btc($data)
    {
        return bcdiv((float)($data), 100000000, 8);
    }
    public function int_to_usd($data)
    {
        return bcdiv((float)($data), 100, 2);
    }
    public function safe_format($data)
    {
        //return str_replace('\r\n', "<br/>", $data);
        return $data = nl2br($data);
    }
    public function unsafe_format($data)
    {
        return $data = preg_replace('#<br\s*?/?>#i', "", $data);
    }
    private function encode_hex($dec)
    {
        $hexchars = "0123456789ABCDEF";
        $return = "";
        while (bccomp($dec, 0) == 1)
        {
            $dv = (string) bcdiv($dec, "16", 0);
            $rem = (integer) bcmod($dec, "16");
            $dec = $dv;
            $return = $return . $hexchars[$rem];
        }
        return strrev($return);
   }
    /**
    * Convert a Base58-encoded integer into the equivalent hex string representation
    *
    * @param string $base58
    * @return string
    * @access private
    */
    private function decode_base58($base58)
    {
        $origbase58 = $base58;    
        $base58chars = "123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz"; 
        $return = "0";
        for ($i = 0; $i < strlen($base58); $i++)
        {
          $current = (string) strpos($base58chars, $base58[$i]);
          $return = (string) bcmul($return, "58", 0);
          $return = (string) bcadd($return, $current, 0);
        }
        $return = $this->encode_hex($return);
        //leading zeros
        for ($i = 0; $i < strlen($origbase58) && $origbase58[$i] == "1"; $i++)
        {
          $return = "00" . $return;
        }
        if (strlen($return) % 2 != 0)
        {
          $return = "0" . $return;
        }
        return $return;
    }
}
/**
 * 
 */
class Doge{
    public function gettransaction($url_pass_user,$tx)
    {        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"gettransaction\", \"params\": [\"$tx\"] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    public function validateaddress($url_pass_user,$address)
    {
        /*
            $validateaddress = $Doge->validateaddress($url_pass_user,$login);
            if($validateaddress!="Error"){
                $validateaddress = json_decode($validateaddress);
                $error = $validateaddress->error;
                if($error==""){
                    $is_valid = $validateaddress->result->isvalid;//1 true 2 false
                }else{
                    $error = $error->message;
                    echo "<div class='error'>Error, try again</div>$script_push";
                }
            }else{
                echo "<div class='error'>Error, try again</div>$script_push";
            }
        */        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"validateaddress\", \"params\": [\"$address\"] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    public function getnewaddress($url_pass_user)
    {       
        //$todeposit = $doge_new_address->result; 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"getnewaddress\", \"params\": [\"\"] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    public function sendmany($url_pass_user,$from_account,$params)
    {        
        /*
            $params = array(
                "DRAXeUEhQTpCQGRuqFGhT4DbwSUzRKk2Ai" => floatval(1.43211234),
                "D5iyPCYpeRxYBMovw2z6uau2PFwwmtj699" => floatval(1.43211234)
            );
            echo "Tx is ";echo $doge_sendmany->result;
        */
        $params = json_encode($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"sendmany\", \"params\": [\"$from_account\",{$params},1] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    public function sendtoaddress($url_pass_user,$address,$amount)
    {        
        /*
            $address = "DRAXeUEhQTpCQGRuqFGhT4DbwSUzRKk2Ai";
            $amount = floatval(1);
            $doge_sendtoaddress = $Doge->sendtoaddress($url_pass_user,$address,$amount);
            if($doge_sendtoaddress!="Error"){
                $doge_sendtoaddress = json_decode($doge_sendtoaddress);
                $error = $doge_sendtoaddress->error;
                //echo "<pre>";print_r($doge_sendtoaddress);echo "</pre>";
                if($error==""){
                    echo "Tx is ";echo $doge_sendtoaddress->result;
                }else{
                    $error = $error->message;
                    //echo "<pre>";print_r($error);echo "</pre>";
                    echo $error."<br><br>";
                }
            }else{
                echo "<div class='error'>We can't send tx</div>$script_push";
            }
        */
        $amount = floatval($amount);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"sendtoaddress\", \"params\": [\"$address\",$amount] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    public function getinfo($url_pass_user){
        /*$Doge_b = $Doge->getinfo($url_pass_user);
        if($Doge_b!="Error"){
            $Doge_b = json_decode($Doge_b);
            $error = $Doge_b->error;
            if($error==""){
                $Doge_balance = $Doge_b->result->balance;
            }else{
                //$error = $error->message;
                //echo "<pre>";print_r($error);echo "</pre>";
                //echo $error;
                echo "<div class='error'>Connection error</div>$script_push";
            }
        }else{
            echo "<div class='error'>Can not connect to the wallet</div>$script_push";
        }*/
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"getinfo\", \"params\": [] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    public function getrawmempool($url_pass_user){
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"getrawmempool\", \"params\": [] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
}
/*
    curl --data-binary '{"jsonrpc": "1.0", "method": "getblockchaininfo", "params": [] }'  -H 'Content-Type: application/json' http://dogecoin:gopro2022@127.0.0.1:8333/
*/
class Bitcoin{
    public function validateaddress($url_pass_user_btc,$address)
    {      
        /*
            Address types "legacy", "p2sh-segwit", and "bech32"
        */  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user_btc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"validateaddress\", \"params\": [\"$address\"] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    /*
        /////
            EXAMPLE IN CODE
        /////

        $validateaddress = $Bitcoin->validateaddress($url_pass_user_btc,$address);
        if($validateaddress!="Error"){
            $validateaddress = json_decode($validateaddress);
            $error = $validateaddress->error;
            if($error==""){
                $isvalid = $validateaddress->result->isvalid;//true|false
                if($isvalid==true){
                    echo "$address is valid";
                }else{
                    echo "$address invalid";
                }
            }else{
                //$error = $error->message;
                echo "<pre>";print_r($error);echo "</pre>";
                //echo $error;
                //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            }
        }else{
            //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            echo "<pre>";print_r($validateaddress);echo "</pre>";
        }
    */


    public function getnewaddress($url_pass_user_btc,$type)
    {      
        /*
            Address types "legacy", "p2sh-segwit", and "bech32"
        */  
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user_btc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"getnewaddress\", \"params\": [\"\", \"$type\"] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    /*
        /////
            EXAMPLE IN CODE
        /////

        $bitcoin_address_type = "p2sh-segwit";
        $bitcoin_new_address = $Bitcoin->getnewaddress($url_pass_user_btc,$bitcoin_address_type);
        echo "<pre>";print_r($bitcoin_new_address);echo "</pre>";
        if($bitcoin_new_address!="Error"){
            $bitcoin_new_address = json_decode($bitcoin_new_address);
            $error = $bitcoin_new_address->error;
            if($error==""){
                echo $todeposit = $bitcoin_new_address->result;//This is new address for deposit
            }else{
                //$error = $error->message;
                echo "<pre>";print_r($error);echo "</pre>";
                //echo $error;
                //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            }
        }else{
            //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            echo "<pre>";print_r($bitcoin_new_address);echo "</pre>";
        }
    */
    public function sendtoaddress($url_pass_user_btc,$address,$amount)
    {
        $amount = floatval($amount);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user_btc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"sendtoaddress\", \"params\": [\"$address\",$amount] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    /*
        /////
            EXAMPLE IN CODE
        /////

        $address = "1NEvtRM13RWcbY7P3pC9hZawSj36Kc1Tk8";
        $amount = floatval(0.1);
        $sendtoaddress = $Bitcoin->sendtoaddress($url_pass_user_btc,$address,$amount);
        if($sendtoaddress!="Error"){
            $sendtoaddress = json_decode($sendtoaddress);
            $error = $sendtoaddress->error;
            if($error==""){
                echo "Tx is ". $sendtoaddress->result;
            }else{
                //$error = $error->message;
                echo "<pre>";print_r($error);echo "</pre>";
                //echo $error;
                //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            }
        }else{
            //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            echo "<pre>";print_r($sendtoaddress);echo "</pre>";
        }
    */
    public function getwalletinfo($url_pass_user_btc)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user_btc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"getwalletinfo\", \"params\": [] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    /*
        /////
            EXAMPLE IN CODE
        /////

        $getwalletinfo = $Bitcoin->getwalletinfo($url_pass_user_btc);
        if($getwalletinfo!="Error"){
            $getwalletinfo = json_decode($getwalletinfo);
            $error = $getwalletinfo->error;
            if($error==""){
                $walletname = $getwalletinfo->result->walletname;
                $walletversion = $getwalletinfo->result->walletversion;
                $balance = $getwalletinfo->result->balance;
            }else{
                //$error = $error->message;
                echo "<pre>";print_r($error);echo "</pre>";
                //echo $error;
                //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            }
        }else{
            //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            echo "<pre>";print_r($getwalletinfo);echo "</pre>";
        }
    */
    public function gettransaction($url_pass_user_btc,$tx)
    {        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url_pass_user_btc);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"jsonrpc\": \"1.0\", \"method\": \"gettransaction\", \"params\": [\"$tx\"] }");
        curl_setopt($ch, CURLOPT_POST, 1);
        $headers = array();
        $headers[] = 'Content-Type: application/json';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        if(curl_errno($ch)){
            $error = curl_error($ch);
            //return 'Error:'.$error."<br>";
            curl_close($ch);
            return 'Error';
        }elseif($code=="403"){
            curl_close($ch);
            return 'Error';
        }else{
            curl_close($ch);
            return $result;
        }
    }
    /*
        /////
            EXAMPLE IN CODE
        /////
        $gettransaction = $Bitcoin->gettransaction($url_pass_user_btc,tx);
        if($gettransaction!="Error"){
            $gettransaction = json_decode($gettransaction);
            $error = $gettransaction->error;
            if($error==""){
                $gettransaction = $gettransaction->result;
                
            }else{
                //$error = $error->message;
                echo "<pre>";print_r($error);echo "</pre>";
                //echo $error;
                //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            }
        }else{
            //echo "<div class='error'>We can't generate new deposit address contact us if you see this message</div>";
            echo "<pre>";print_r($gettransaction);echo "</pre>";
        }
    */
}