<?php


$b64Doc = base64_encode(file_get_contents('dummy.pdf'));
	 

$curl = curl_init();

curl_setopt_array($curl, array(

  CURLOPT_URL => 'https://api.notifyre.com/fax/send',

  CURLOPT_RETURNTRANSFER => true,

  CURLOPT_ENCODING => '',

  CURLOPT_MAXREDIRS => 10,

  CURLOPT_TIMEOUT => 0,

  CURLOPT_FOLLOWLOCATION => true,

  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

  CURLOPT_CUSTOMREQUEST => 'POST',

  CURLOPT_POSTFIELDS =>'{
    "Faxes": {
      "Recipients": [
        {
          "Type": "fax_number",
          "Value": "13477131105" 
        }
      ],
      "SendFrom": "",
      "ClientReference": "32188396-f59b-4581-a2e7-da4515f7d292",
      "Subject": "Test Fax fom dev",
      "IsHighQuality": false,
      "Documents": [
        {
          "Filename": "test.pdf",
          "Data": "'.$b64Doc.'"
        }
      ]
    }
  }',

  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Accept: application/json',
    'x-api-token: IoHZML2MQEL6Oo8UkmTbQG9h/JDA2Ca7pSz4fQKAJ+4.us'

  ),

));

 

$response = curl_exec($curl);

 

curl_close($curl);

echo $response;

            


die();
$op =array();
//$op["destinations"]=array("destinations"=>[])

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.securedocex.com/faxes',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>'{
    "destinations": [
        {
            "fax_number" : "19294072300",
            "to_name" : "Tilin",
            "to_company": "Ny Best Medical"
        }
    ],
    "documents": [
        {
            "document_content": "'.$b64Doc.'",
            "document_type": "PDF"
        }
    ],
   "fax_options" : {
        "image_resolution" : "FINE",
        "include_cover_page" : "true"
   }
}',
  CURLOPT_HTTPHEADER => array(
    'user-id: f9bb083a-9570-498f-a072-3829706e67ad',
    'Content-Type: application/json',
    'Authorization: Bearer eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJzZWNyZXRfaWQiOiJiYjZhMjk1MS03MmU2LTQ1NmItODIxNi04YTJjMzE0OTIzMDIiLCJzY29wZSI6WyJyZWFkIiwid3JpdGUiXSwiZXhwIjoxNzQ0NzM1NTYzLCJhcHBfaWQiOiI3MjYxNDVjOC01Yjk3LTQyOWEtYTIxYi02NGE5YzY3MGVmYjYiLCJhdXRob3JpdGllcyI6WyJST0xFX0ZTVE9SIl0sImp0aSI6IkNwOTRDV2c3Y2dZdnIzam5yc1hFLVhobDF0TSIsImNsaWVudF9pZCI6IjcyNjE0NWM4LTViOTctNDI5YS1hMjFiLTY0YTljNjcwZWZiNiJ9.fe5xHxLoddoPqdSWfTwhukfivyaHzwFzvV_J1fyfTGc-VhwjEY4sIkWuC9Y0gZB82lXF9jT31bL-oueTAWHzgpoITUbdOVymBrfsWLR-oQTiG0EO0yZOIk2FK_oqQlOWGfQolBnKjm3oaD3sIOl2Q6fU7tiZngcFNVpbeAWQofleZYCCgJKiinEc8JDG5vP8M7mx83jjByCxbPfZKMejgQo51M7rrUFceR5KEkFyH2ueVnSiLKMCZFi90JL-sWnl5jqYUVO1BX1HJFfPPNtP7vTSjw6Qo30asDK03mXv9Bum5yHrstYbjo9y8Taj2cFT4EVfRNqdqXhLdgPmts5Dmg'
  ),
));

$response = curl_exec($curl);

curl_close($curl);


echo $response;
