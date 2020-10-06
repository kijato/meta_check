<?php
	
	$configs = include('config.php');
	
	$request = $_REQUEST ;
	$korzet    = isset($request['korzet'])    ? htmlspecialchars($request['korzet'])    : "%";

	$conn = oci_connect($configs->korzetek[$korzet]['username'], $configs->korzetek[$korzet]['password'], $configs->korzetek[$korzet]['connection_string'], $configs->korzetek[$korzet]['character_set']);
	if (!$conn) {
		$e = oci_error();
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$sql="
	SELECT initcap(nev) telepules
	FROM HELYSEGEK
	WHERE ( megyekod,korzetszam ) IN (
			SELECT megyekod, korzetszam
			FROM jarasok
			WHERE nev LIKE '%'||substr(:korzet,1,length(:korzet)-1)||'%'
			)
	  AND map_id is not null
	  --AND tullap_be = 'I'
	ORDER BY 1
	";
	$stid = oci_parse($conn, $sql);
	oci_bind_by_name($stid,":korzet", $korzet);
	oci_execute($stid);
	$nrows = oci_fetch_all($stid, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);

	foreach ($res as $col) {
		foreach ($col as $item) {
			echo "<option>".($item !== null ? htmlentities($item, ENT_QUOTES) : "")."</option>\n";
		}
	}
	
?>
