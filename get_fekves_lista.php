<?php
	
	$configs = include('config.php');
	
	$request = $_REQUEST ;
	$korzet    = isset($request['korzet'])    ? htmlspecialchars($request['korzet'])    : "%";
	$telepules = isset($request['telepules']) ? htmlspecialchars($request['telepules']) : "%";
	
	$conn = oci_connect($configs->korzetek[$korzet]['username'], $configs->korzetek[$korzet]['password'], $configs->korzetek[$korzet]['connection_string'], $configs->korzetek[$korzet]['character_set']);
	if (!$conn) {
		$e = oci_error();
		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$sql="
SELECT kod, ertek
FROM dtc_fekves
WHERE kod IN (
	SELECT DISTINCT fekv_kod
	FROM dt_meta
	WHERE telepules_id = ( select id from helysegek where initcap(nev) = :telepules )
	  AND megsz_datum IS NULL
)
ORDER by ertek
";
	$stid = oci_parse($conn, $sql);
	oci_bind_by_name($stid,":telepules", $telepules);
	oci_execute($stid);

	echo "<option value='%'>mindegy</option>\n";
	while (oci_fetch($stid)) {
		echo "<option value='".htmlentities(oci_result($stid,"KOD"),ENT_QUOTES)."'>".htmlentities(oci_result($stid,"ERTEK"),ENT_QUOTES)."</option>\n";
	}

	oci_free_statement($stid);
	oci_close($conn);	

?>
