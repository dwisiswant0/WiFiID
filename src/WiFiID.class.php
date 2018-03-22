<?php
namespace dwisiswant0\WiFiID;
define("OS", strtolower(PHP_OS));
define("API", "https://api.dw1.co");
define("REPO", str_replace("\\", "/", __NAMESPACE__));

/**
 * @wifi.id Account Extractor & Checker
 *
 * @category   Networking
 * @package    dwisiswant0/WiFiID
 * @author     dw1 <iam@dw1.co>
 * @license    https://opensource.org/licenses/MIT  MIT License
 * @link       https://github.com/dwisiswant0/WiFiID
 */

class main {
	const TITLE = "@wifi.id Account Extractor & Checker";
	const VERSION = "1.0.1";

	public function __construct() {
		echo "\n\t" . $this::TITLE . " v" . $this::VERSION . "\n";
		echo "\thttps://github.com/" . REPO . "\n";
		echo "\t--\n";
		echo "\t(c) 2018, made by dw1\n\n";
		$this->LOG = "accounts/wifi.id_account-" . date('Ymd') . ".log";
	}

	private function call($data, $endpoint) {
		$context = stream_context_create(array(
			"http" => array(
				"method" => "POST",
				"header" => implode("\r\n", array(
					"User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_10; rv:33.0) Gecko/20100101 Firefox/33.0",
					"Content-Type: application/x-www-form-urlencoded; charset=UTF-8",
					"X-Requested-With: XMLHttpRequest"
				)),
				"content" => $data,
			),
		));
		$response = file_get_contents(API . $endpoint, false, $context);
		if (strpos($http_response_header[0], "200") == true) {
			return json_decode($response, true);
		} else {
			throw new \Exception("Something wrong, but Idk why.", true);
		}
	}

	public function checkVersion() {
		$get = file_get_contents("https://raw.githubusercontent.com/" . REPO . "/master/VERSION");
		if ($this::VERSION !== trim($get)) exit("This version is outdated. Please update to latest version (v" . trim($get) . "), just type `git pull`.\n");
		return true;
	}

	public function getMacList() {
		$this->checkVersion();
		$cmd = null;
		if (OS == "linux" || OS == "darwin") {
			$cmd = "arp -n";
		} elseif (OS == "windows" || OS == "winnt") {
			$cmd = "getmac /FO CSV";
		}

		if (empty($cmd) || $cmd === null) exit("Not compatible for " . OS . " system.\n");
		echo "Getting mac address in same network...\n";
		$mac_list = shell_exec($cmd);
		$mac_split = explode("\n", $mac_list);
		$mac_addr = array();
		for ($i=0; $i < count($mac_split)-1; $i++) if (strlen($mac_split[$i+1]) >= 17) $mac_addr[$i] = $this->parseMacAddr($mac_split[$i+1]);
		return $mac_addr;
	}

	public function checkAccount($mac) {
		$post = $this->call("mac=" . $mac, "/wifi.id/check");
		return $post;
	}

	private function parseMacAddr($data) {
		if (preg_match_all("/([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})/", $data, $mac)) return strtolower(str_replace("-", ":", $mac[0][0]));
	}

	public function saveAccount($data) {
		fwrite(fopen($this->LOG, "a+"), "[" . date("Y/m/d H:i:s") . "] " . $data . PHP_EOL);
		fclose(fopen($this->LOG, "a+"));
	}
}
