<?php



// define functions
function print_r2($val){
	echo "\n";
        print_r($val);
	echo "\n";
}

  // big blob function I stole from stackoverflow
function multi_thread_curl($urlArray, $optionArray, $nThreads) {

  $curlArray = array_chunk($urlArray, $nThreads, $preserve_keys = true);

  $ch = 'ch_';
  foreach($curlArray as $threads) {      

      foreach($threads as $thread=>$value) {

      ${$ch . $thread} = curl_init();

        curl_setopt_array(${$ch . $thread}, $optionArray); //Set your main curl options.
        curl_setopt(${$ch . $thread}, CURLOPT_URL, $value); //Set url.

        }

      $mh = curl_multi_init();

      foreach($threads as $thread=>$value) {

      curl_multi_add_handle($mh, ${$ch . $thread});

      }

      $active = null;

      do {

      $mrc = curl_multi_exec($mh, $active);

      } while ($mrc == CURLM_CALL_MULTI_PERFORM);

      while ($active && $mrc == CURLM_OK) {

          if (curl_multi_select($mh) != -1) {
              do {

                  $mrc = curl_multi_exec($mh, $active);

              } while ($mrc == CURLM_CALL_MULTI_PERFORM);
          }

      }

      foreach($threads as $thread=>$value) {

      $curlResults[$thread] = curl_multi_getcontent(${$ch . $thread});

      curl_multi_remove_handle($mh, ${$ch . $thread});

      }

      curl_multi_close($mh);

  }


  return $curlResults;

} 


// curl options
$optionArray = array(

  CURLOPT_USERAGENT        => 'twardcheck/1.0',//Pick your user agent.
  CURLOPT_RETURNTRANSFER   => TRUE,
  CURLOPT_TIMEOUT          => 10
//  CURLOPT_SSL_VERIFYSTATUS   => TRUE

);


// create array of urls and strings

$fileName = "/opt/php-stringcheck/sites.json";
$jsonData = file_get_contents("$fileName");
$listArray = json_decode($jsonData,TRUE);

//$listArray = array(
//
//    array('https://www.mojohost.com/','Cookie Notice')
//
//);

// pull out urls for curl to eat
$urlArray = array();
foreach($listArray as $i) {
  $urlArray[] = $i['url'];
}

//This is how many urls it will try to do at one time.
$nThreads = 20;


//do the thing
$curlResults = multi_thread_curl($urlArray, $optionArray, $nThreads);

function parseSite($url, $string, $index, $resultArr, $debug){
$matchRes= preg_match("/$string/i", $resultArr[$index]);

if ($matchRes == 1){
   echo $url . ",$string" . ",OK\n";
	}
	else{
   echo $url . ",$string" . ",DOWN\n";
	}
if ($debug == 1){
   print_r2($matchHit);
	echo "\n";
   echo $resultArr[$index];
//   print_r2($resultArr);
	}
}


//display the results of the fetch
echo "\n";
echo "use this command for live status, otherwise this page comes from cron every 5 minutes\n";
echo '/usr/local/sbin/php7.1 /opt/php-stringcheck/check.php';
echo "\n";
echo "\n";
echo "\n";
foreach( $listArray as $index => $url ) {
parseSite($url['url'], $listArray[$index]['string'], $index, $curlResults, 0);
//print_r($index);
//print_r($url[$index][url]);
}

