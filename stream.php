<?php
date_default_timezone_set("Australia/Brisbane	");
header("Access-Control-Allow-Origin: *");
header("Cache-Control: no-cache");
header("Content-Type: text/event-stream");

/*
// Example lines from tcpdump
14:24:36.461298 IP 103.16.131.90.80 > 84.17.133.6.13740: Flags [S.], seq 4223734959, ack 1774382165, win 28200, options [mss 1410], length 0
14:24:36.717304 IP 103.16.131.90.80 > 84.17.134.0.33696: Flags [S.], seq 195334641, ack 3083769129, win 28200, options [mss 1410], length 0
*/

// IP address to look out for in the dump file
$myIP = "";

$file = "./livedump.txt";

$lastpos = 0;
while (true) {
	usleep(1000000);
	echo "event: ping\n";
	$curDate = date(DATE_ISO8601);
	echo 'data: {"time": "' . $curDate . '"}';
	echo "\n\n";
	ob_flush(); flush();


	clearstatcache(false, $file);
	$len = filesize($file);
	if ($len < $lastpos)
	{
		//file deleted or reset
		$lastpos = $len;
	}
	elseif ($len > $lastpos)
	{
		$f = fopen($file, "rb");
		if ($f === false)
			die();

		fseek($f, $lastpos);

		while (!feof($f))
		{
			$buffer = fread($f, 4096);
			$buffer_bits = explode("\n", $buffer);

			foreach ($buffer_bits as $line)
			{
			//	print $line."\n";

				$bits = explode(" ", $line, 6);

				$time = $bits[0];
				$proto = trim($bits[1]);
				$fromIPPort = $bits[2];
				$toIPPort = $bits[4];

				// filter out non-IP traffic like ARP
				if ($proto !== "IP")
					continue;

				// break up IP into IP:port
				$lastdot = strrpos($fromIPPort, ".");
				$fromIP = substr($fromIPPort, 0, $lastdot);
				$fromPort = substr($fromIPPort, $lastdot+1);

				$lastdot = strrpos($toIPPort, ".");
				$toIP = substr($toIPPort, 0, $lastdot);
				$toPort = substr($toIPPort, $lastdot+1, -1);
				//	print $fromIP.":$fromPort\n";;
				//	print $toIP.":$toPort\n";;
				$fromIPInt = ip2long($fromIP);
				$toIPInt = ip2long($toIP);


				//	print "data: { \"fromIP\": $fromIPInt, \"fromPort\": $fromPort, \"toIP\": $toIPInt, \"toPort\": $toPort }\n\n";

				// we only want to look at connections made to us
				if ($toIP === $myIP)
					print "data: { \"fromIP\": \"$fromIP\", \"fromPort\": $fromPort, \"toIP\": \"$toIP\", \"toPort\": $toPort }\n\n";
				flush();
				ob_flush();
			}
		}
		$lastpos = ftell($f);
		fclose($f);
	}
}
