**PRIME AIRTIME**<br/>

**include "primeairtime.php";\**
**$pa = new primeairtime();**<br/><br/>
> echo $pa->get_key();\
> echo $pa->reauth_key();\
> echo $pa->set_key();\<br/>


**ACCOUNT STATUS**\
*print_r($pa->accounts());*\
*print_r($pa->status());*<br/>

**BATCH OPERATION**
> print_r($pa->batch())\
> print_r($pa->batch("myfeff")); method 1\
> $pa->id = "myfeff"; method 2\
> print_r($pa->batch());\
> print_r($pa->set_id("myfeff")->batch()); method 3<br/><br/>

**VERIFY TRANSACTION**\
>print_r($pa->set_ref("myfeff")->log());<br/><br/>

**AIRTIME/DATA TOPUP**\
>$mobile = array("2348063175557");  [^bulk array("2348183874966","2348183874966");]\
>print_r($pa->topup(false)->info($mobile));  *true airtime, false data*<br/><br/>


**BATCH TOPUP **
 ```
$requestBody = '{
  "numbers" : [
    {
      "msisdn" : "2348183874966",
      "product_id" : "MFIN-2-OR",
      "denomination" : 1
    },
    {
      "msisdn" : "2348183874966",
      "product_id": "MFIN-2-OR",
      "denomination" : 1
    }
  ]
}';
```

**SINGLE TOPUP**
$requestBody = '{
        "msisdn" : "2348183874966",
        "product_id": "MFIN-2-OR",
        "denomination" : 50,
        "send_sms" : false,
        "sms_text" : "",
        "customer_reference":"RE4534783248234"
    }';
print_r($pa->topup(true)->exec($requestBody));  true airtime, false data


**LIST ALL PRODUCT BY PAGE**
$page = 1;
print_r($pa->set_type("airtime")->products($page));


**BILLS**
print_r($pa->billpay()); -*** *Get Country List* ***
print_r($pa->set_iso("NG")->billpay()); -- **List of services in specified country **
print_r($pa->set_iso("NG")->set_service_id("electricity")->billpay());  **List of products available for given service in country  **
print_r($pa->set_service_id("dstv")->set_product_id("BPD-NGCA-AQA")->billpay()); **List of product options available for given service in country (multichoice) **
print_r($pa->set_service_id("electricity")->set_product_id("BPE-NGEK-OR")->billpay("027140081201"));  **VALIDATE Bills**

**Perform Electricity topup**
$requestBody = "{\"meter\":\"027140081201\",\"prepaid\":true,\"denomination\":\"50\", \"product_id\":\"BPE-NGEK-OR\",\"customer_reference\":\"myreg\"}";
print_r($pa->set_account("027140081201")->set_service_id("electricity")->set_product_id("BPE-NGEK-OR")->billpay($requestBody));** PAY Bills**
**Perform DSTV/Internet/Misc topup**
$requestBody = "{\"meter\":\"10441003943\",\"prepaid\":false, \"customer_reference\":\"32983278JsDPO\"}";
print_r($pa->set_account("10441003943")->set_service_id("dstv")->set_option("FTAE36")->set_product_id("BPD-NGCA-AQA")->billpay($requestBody)); **PAY Bills**


** ELECTRICITY REQUEST BODY**
$requestBody = '{
    "meter":"54150205877",
    "prepaid":true,
    "denomination":"50",
    "product_id":"BPE-NGIK-OR",
    "customer_reference":"myref"
}';

**DSTV/INTERNET/MISC REQUEST BODY**
$requestBodyb = '{
    "meter":"10441003943",
    "prepaid":false,
    "product_id":"BPD-NGCA-AQA",
    "option":"FTAE36",
    "customer_reference":"myref"
}';
print_r($pa->set_account("027140081201")->set_service_id("electricity")->billpay($requestBody)); Perform bill topup 




**BANK TRANSFER**
print_r($pa->ft()->access()); ** Check access**
print_r($pa->ft()->banks()); **list banks and sort code**
print_r($pa->ft("6761025013")->set_sort_code("000003")->loogup());  **Account lookup**
$requestBody = '{
    "amount" : 500.50,
    "customer_reference" : "32983298JDPOKW"
}';
print_r($pa->ft("0069317906")->set_sort_code("000014")->transfer($requestBody));**execute transfer**


$requestBody = '{
  "message" : "This is a test message from API"
}';
print_r($pa->set_account("0069317906")->sms($requestBody));**execute transfer**
?>