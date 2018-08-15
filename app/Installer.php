<?php
use phpseclib\Net\SSH2;
class Installer{
	public function copyfiles($logins){		
		$dbtest =  $this->databasetest($logins['databaseHost'], $logins['databaseName'], $logins['databaseUsername'], $logins['databasePassword']);
		if ($dbtest) {
			$license = $this->wapp_check_license($logins['licensekey']);
			if ($license === 'Active') {
				try {
					$ssh = new SSH2($logins['ipaddress'], $logins['sshport']);
				    $sshconnect = $ssh->login($logins['cpanelusername'], $logins['keypassword']);
				} catch (\Exception $e) {
					return $e->getMessage();				
				}
				if (!$sshconnect) {
				    return 'SSH connection failed, make sure you provide correct cpanel username and password';
				}else{					
					$ssh->setTimeout(2000);
		            $ssh->exec(
						"rm -rf writersbayapp" . PHP_EOL
						. "mkdir writersbayapp" . PHP_EOL
						. "cd writersbayapp" . PHP_EOL
						. "composer clear-cache" . PHP_EOL
						. "git clone https://github.com/muyaedward/wbapp.git" . PHP_EOL
						. "cd wbapp" . PHP_EOL						
						. "mv public public_bak" . PHP_EOL
						. "ln -s ~/".$logins['sitepath']." public" . PHP_EOL
						. "cp -a public_bak/* public/" . PHP_EOL
						. "cp public_bak/.htaccess public/" . PHP_EOL
						. "echo '".$logins['licensekey']."' > storage/app/license.txt" . PHP_EOL
						. "echo '".$this->generateenvfile($logins['databaseName'], $logins['databaseUsername'], $logins['databasePassword'])."' > .env" . PHP_EOL
						. "php artisan key:generate" . PHP_EOL
						. "php artisan migrate:fresh --seed" . PHP_EOL
						. "php artisan queue:restart" . PHP_EOL
						. "php artisan config:cache" . PHP_EOL
						. "php artisan passport:install" . PHP_EOL
						. "php artisan config:clear" . PHP_EOL
						. "cd ../" . PHP_EOL
						. "find . -type d -print0 | xargs -0 chmod 0755" . PHP_EOL
						. "find . -type f -print0 | xargs -0 chmod 0644"  . PHP_EOL
						. "cd ../../".$logins['sitepath'] . PHP_EOL
						. "find . -type d -print0 | xargs -0 chmod 0755" . PHP_EOL
						. "find . -type f -print0 | xargs -0 chmod 0644"
		            );
		            //correct the file permissions
		            $perm = $this->correctperm($logins['ipaddress'], $logins['sshport'], $logins['cpanelusername'], $logins['keypassword'], $logins['sitepath']);
		            if($perm == 'created'){
		            	return 'created';
		            }else{
						return 'The system has added the app successfully, but failed to set the correct permissions';
					}
				}				
			}else{
				return 'Error in license key: Status '.$license;
			}						
		} else {
			return 'System could not make connection to the database';
		}
	}

	private function correctperm($ip, $port, $username, $password, $path){
		try {
			$ssh = new SSH2($ip, $port);
			$sshconnect = $ssh->login($username, $password);
			$ssh->setTimeout(2000);
			$ssh->exec(
				"cd ~" . PHP_EOL
				. "find writersbayapp -type d -print0 | xargs -0 chmod 0755" . PHP_EOL
				. "find writersbayapp -type f -print0 | xargs -0 chmod 0644" . PHP_EOL
				. "find ".$path." -type d -print0 | xargs -0 chmod 0755" . PHP_EOL
				. "find ".$path." -type f -print0 | xargs -0 chmod 0644" . PHP_EOL
			);
			return 'created';
		} catch (\Exception $e) {
			return $e->getMessage();				
		}
	}

	public function databasetest($host, $db, $user, $pass){
		try{
		    $dbh = new pdo( 'mysql:host='.$host.';dbname='.$db,
		                    $user,
		                    $pass,
		                    array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION));
		    return true;
		}
		catch(\PDOException $ex){
		    return false;
		}
	}

	function wapp_check_license($licensekey, $localkey=''){
    	$whmcsurl = 'http://wbapp.madayer.com/';
	    $licensing_secret_key = 'UqXUry-JDXZFuk6Lt@Z5jg=P-!)7.\SLr@&#p/a3@`B)]mSt9z';
	    $localkeydays = 3;
	    $allowcheckfaildays = 2;

	    $check_token = time() . md5(mt_rand(1000000000, 9999999999) . $licensekey);
	    $checkdate = date("Ymd");
	    $domain = $_SERVER['SERVER_NAME'];
	    $usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
	    $dirpath = dirname(__FILE__);
	    $verifyfilepath = 'modules/servers/licensing/verify.php';
	    $localkeyvalid = false;
	    if ($localkey) {
	        $localkey = str_replace("\n", '', $localkey); # Remove the line breaks
	        $localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
	        $md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
	        if ($md5hash == md5($localdata . $licensing_secret_key)) {
	            $localdata = strrev($localdata); # Reverse the string
	            $md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
	            $localdata = substr($localdata, 32); # Extract License Data
	            $localdata = base64_decode($localdata);
	            $localkeyresults = unserialize($localdata);
	            $originalcheckdate = $localkeyresults['checkdate'];
	            if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {
	                $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
	                if ($originalcheckdate > $localexpiry) {
	                    $localkeyvalid = true;
	                    $results = $localkeyresults;
	                    $validdomains = explode(',', $results['validdomain']);
	                    if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
	                        $localkeyvalid = false;
	                        $localkeyresults['status'] = "Invalid";
	                        $results = array();
	                    }
	                    $validips = explode(',', $results['validip']);
	                    if (!in_array($usersip, $validips)) {
	                        $localkeyvalid = false;
	                        $localkeyresults['status'] = "Invalid";
	                        $results = array();
	                    }
	                    $validdirs = explode(',', $results['validdirectory']);
	                    if (!in_array($dirpath, $validdirs)) {
	                        $localkeyvalid = false;
	                        $localkeyresults['status'] = "Invalid";
	                        $results = array();
	                    }
	                }
	            }
	        }
	    }
	    if (!$localkeyvalid) {
	        $responseCode = 0;
	        $postfields = array(
	            'licensekey' => $licensekey,
	            'domain' => $domain,
	            'ip' => $usersip,
	            'dir' => $dirpath,
	        );
	        if ($check_token) $postfields['check_token'] = $check_token;
	        $query_string = '';
	        foreach ($postfields AS $k=>$v) {
	            $query_string .= $k.'='.urlencode($v).'&';
	        }
	        if (function_exists('curl_exec')) {
	            $ch = curl_init();
	            curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
	            curl_setopt($ch, CURLOPT_POST, 1);
	            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
	            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
	            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	            $data = curl_exec($ch);
	            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	            curl_close($ch);
	        } else {
	            $responseCodePattern = '/^HTTP\/\d+\.\d+\s+(\d+)/';
	            $fp = @fsockopen($whmcsurl, 80, $errno, $errstr, 5);
	            if ($fp) {
	                $newlinefeed = "\r\n";
	                $header = "POST ".$whmcsurl . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
	                $header .= "Host: ".$whmcsurl . $newlinefeed;
	                $header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
	                $header .= "Content-length: ".@strlen($query_string) . $newlinefeed;
	                $header .= "Connection: close" . $newlinefeed . $newlinefeed;
	                $header .= $query_string;
	                $data = $line = '';
	                @stream_set_timeout($fp, 20);
	                @fputs($fp, $header);
	                $status = @socket_get_status($fp);
	                while (!@feof($fp)&&$status) {
	                    $line = @fgets($fp, 1024);
	                    $patternMatches = array();
	                    if (!$responseCode
	                        && preg_match($responseCodePattern, trim($line), $patternMatches)
	                    ) {
	                        $responseCode = (empty($patternMatches[1])) ? 0 : $patternMatches[1];
	                    }
	                    $data .= $line;
	                    $status = @socket_get_status($fp);
	                }
	                @fclose ($fp);
	            }
	        }
	        if ($responseCode != 200) {
	            $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
	            if ($originalcheckdate > $localexpiry) {
	                $results = $localkeyresults;
	            } else {
	                $results = array();
	                $results['status'] = "Invalid";
	                $results['description'] = "Remote Check Failed";
	                return $results;
	            }
	        } else {
	            preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
	            $results = array();
	            foreach ($matches[1] AS $k=>$v) {
	                $results[$v] = $matches[2][$k];
	            }
	        }
	        if (!is_array($results)) {
	            die("Invalid License Server Response");
	        }
	        if(isset($results['md5hash'])){
			    if ($results['md5hash']) {
		            if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
		                $results['status'] = "Invalid";
		                $results['description'] = "MD5 Checksum Verification Failed";
		                return $results;
		            }
		        }
			}
			if(isset($results['md5hash'])){
		        if ($results['md5hash']) {
		            if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
		                $results['status'] = "Invalid";
		                $results['description'] = "MD5 Checksum Verification Failed";
		                return $results;
		            }
		        }
		    }
	        if ($results['status'] == "Active") {
	            $results['checkdate'] = $checkdate;
	            $data_encoded = serialize($results);
	            $data_encoded = base64_encode($data_encoded);
	            $data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
	            $data_encoded = strrev($data_encoded);
	            $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
	            $data_encoded = wordwrap($data_encoded, 80, "\n", true);
	            $results['localkey'] = $data_encoded;
	        }
	        $results['remotecheck'] = true;
	    }
	    unset($postfields,$data,$matches,$whmcsurl,$licensing_secret_key,$checkdate,$usersip,$localkeydays,$allowcheckfaildays,$md5hash);
	    return $results['status'];
    }
	private function generateenvfile($db, $user, $pass){
		$protocol = isset($_SERVER['HTTPS']) ? 'https://' : 'http://';
		$appurl =  $protocol.$_SERVER['SERVER_NAME'];
		$envfile = 'APP_NAME="Academic Writing App"'. PHP_EOL;
		$envfile .= 'APP_ENV=local'. PHP_EOL;
		$envfile .= 'APP_KEY=base64:aj/hHvIj7nedMkReaQDbO3ecg+NEDIvUTe+Yb9nGi2s='. PHP_EOL;
		$envfile .= 'APP_DEBUG=true'. PHP_EOL;
		$envfile .= 'APP_URL='.$appurl. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'APP_TIMEZONE=Africa/Nairobi'. PHP_EOL;
		$envfile .= 'APP_LOCALE=en'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'LOG_CHANNEL=slack'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'DB_CONNECTION=mysql'. PHP_EOL;
		$envfile .= 'DB_HOST=127.0.0.1'. PHP_EOL;
		$envfile .= 'DB_PORT=3306'. PHP_EOL;
		$envfile .= 'DB_DATABASE='.$db. PHP_EOL;
		$envfile .= 'DB_USERNAME='.$user. PHP_EOL;
		$envfile .= 'DB_PASSWORD='.$pass. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'BROADCAST_DRIVER=log'. PHP_EOL;
		$envfile .= 'CACHE_DRIVER=array'. PHP_EOL;
		$envfile .= 'SESSION_DRIVER=file'. PHP_EOL;
		$envfile .= 'SESSION_LIFETIME=120'. PHP_EOL;
		$envfile .= 'QUEUE_DRIVER=database'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'MAIL_DRIVER=sendmail'. PHP_EOL;
		$envfile .= 'MAIL_HOST='. PHP_EOL;
		$envfile .= 'MAIL_PORT=587'. PHP_EOL;
		$envfile .= 'MAIL_USERNAME='. PHP_EOL;
		$envfile .= 'MAIL_PASSWORD='. PHP_EOL;
		$envfile .= 'MAIL_ENCRYPTION=tls'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'MAIL_FROM_ADDRESS=help@writersbayapp.com'. PHP_EOL;
		$envfile .= 'MAIL_FROM_NAME="WritersBay App"'. PHP_EOL;
		$envfile .= 'MAIL_THEME=wbapp1'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'MAILGUN_DOMAIN='. PHP_EOL;
		$envfile .= 'MAILGUN_SECRET='. PHP_EOL;
		$envfile .= 'SPARKPOST_SECRET='. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'MAIL_SENDMAIL_PATH="/usr/sbin/sendmail -bs"'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'PAYPAL_MODE=sandbox'. PHP_EOL;
		$envfile .= 'PAYPAL_CLIENT_ID=AaA6yNNoQAmjHpwxabLBLoysk6vK8ER39TFnU8B4nhJoeo_brlY8poZqwqYj8iH4QM0eaDlw9WzIpawd'. PHP_EOL;
		$envfile .= 'PAYPAL_CLIENT_SECRET=EMAM5wTuJbRY38msPhkP8MwwoNA04ZJ8-p1dnJ5sRLEXOWJBjk7umB7OpmLXMRBAGY5oT_0XI6UNPigm'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'SELF_UPDATER_VERSION_INSTALLED=1.3.8'. PHP_EOL;
		$envfile .= 'SELF_UPDATER_DOWNLOAD_PATH="/tmp/updates"'. PHP_EOL;
		$envfile .= 'SELF_UPDATER_MAILTO_ADDRESS=help@writersbayapp.com'. PHP_EOL;
		$envfile .= 'SELF_UPDATER_MAILTO_NAME=Admin'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'SESSION_LIFETIME=120'. PHP_EOL;
		$envfile .= 'SESSION_EXPIRE_ON_CLOSE=false'. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'SQS_KEY='. PHP_EOL;
		$envfile .= 'SQS_SECRET='. PHP_EOL;
		$envfile .= 'SQS_PREFIX='. PHP_EOL;
		$envfile .= 'SQS_QUEUE='. PHP_EOL;
		$envfile .= 'SQS_REGION='. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= ''. PHP_EOL;
		$envfile .= 'PUSHER_APP_ID='. PHP_EOL;
		$envfile .= 'PUSHER_APP_KEY='. PHP_EOL;
		$envfile .= 'PUSHER_APP_SECRET='. PHP_EOL;
		$envfile .= 'PUSHER_APP_CLUSTER='. PHP_EOL;
		return $envfile;
	}

	public function checkifsslenabled(){
		if(!extension_loaded('openssl'))
		{
		  return false;		  
		}
		return true;
	}
	public function checkifpdoenabled(){
		if(!extension_loaded('pdo'))
		{
		  return false;
		}
		return true;
	}
	public function checkifmbstringenabled(){
		if(!extension_loaded('mbstring'))
		{
		  return false;
		}
		return true;
	}
	public function checkiftokenizerenabled(){
		if(!extension_loaded('tokenizer'))
		{
		  return false;
		}
		return true;
	}
	public function checkifxmlenabled(){
		if(!extension_loaded('xml'))
		{
		  return false;
		}
		return true;
	}
	public function checkifctypeenabled(){
		if(!extension_loaded('ctype'))
		{
		  return false;
		}
		return true;
	}

	public function checkifjsonenabled(){
		if(!extension_loaded('json'))
		{
		  return false;
		}
		return true;
	}

	public function checkiflibssh2enabled(){
		if(!extension_loaded('libssh2'))
		{
		  return false;
		}
		return true;
	}

	public function phpversionneeded(){
		if (version_compare(phpversion(), '7.1.3', '>=')){
			return true;		    
		}else{
			return false;
		}
	}

	public function requirements(){
		$requirements = [];
		if (!$this->phpversionneeded()) {
			$requirements[] = 'You php version is lower than 7.1.3';
		}
		if (!$this->checkifjsonenabled()) {
			$requirements[] = 'JSON PHP Extension is not enabled';
		}
		if (!$this->checkifctypeenabled()) {
			$requirements[] = 'Ctype PHP Extension is not enabled';
		}
		if (!$this->checkifxmlenabled()) {
			$requirements[] = 'XML PHP Extension is not enabled';
		}
		if (!$this->checkiftokenizerenabled()) {
			$requirements[] = 'Tokenizer PHP Extension is not enabled';
		}
		if (!$this->checkifmbstringenabled()) {
			$requirements[] = 'Mbstring PHP Extension is not enabled';
		}
		if (!$this->checkifpdoenabled()) {
			$requirements[] = 'PDO Extension is not enabled';
		}
		if (!$this->checkifsslenabled()) {
			$requirements['openssl'] = 'Open SSL Extension is not enabled';
		}
		return $requirements;
	}
	
}