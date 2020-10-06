<?php

	$configs = include('config.php');
	#echo json_encode($configs->app_info);

	$request = $_REQUEST ;
	$korzet    = isset($request['korzet'])    ? htmlspecialchars($request['korzet'])    : "%";
	$telepules = isset($request['telepules']) ? htmlspecialchars($request['telepules']) : "%";
	$fekves    = isset($request['fekves'])    ? htmlspecialchars($request['fekves'])    : "%";

	$conn = oci_connect($configs->korzetek[$korzet]['username'], $configs->korzetek[$korzet]['password'], $configs->korzetek[$korzet]['connection_string'], $configs->korzetek[$korzet]['character_set']);
	if (!$conn) {
	$e = oci_error();
	trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$sql="
		SELECT --j.nev korzet, initcap(h.nev) telepules,
			meta_id, f.ertek fekves, hrsz_tol, hrsz_ig, min_y ||', '|| min_x meta_min_y_x, max_y ||', '|| max_x meta_max_y_x
		FROM dt_meta m
			JOIN helysegek h ON h.id = m.telepules_id
			JOIN jarasok j ON j.korzetszam = h.korzetszam
			JOIN dtc_fekves f ON f.kod = m.fekv_kod
		WHERE megsz_datum is null
		  and j.nev LIKE '%'||substr(:korzet,1,length(:korzet)-1)||'%'
		  and initcap(h.nev) like :telepules
		  --and fekv_kod like :fekves
		--ORDER BY korzet, telepules, fekv_kod, meta_id
		ORDER BY fekves, hrsz_tol, hrsz_ig";
	$stid = oci_parse($conn, $sql);
	$keys = array(
		':korzet' => $korzet,
		':telepules' => $telepules,
		#':fekves' => $fekves
	);
	// oci_bind_by_name($stid, $key, $val) does not work because it binds each placeholder to the same location: $val instead use the actual location of the data: $ba[$key] !
	foreach ($keys as $key => $val) { oci_bind_by_name($stid, $key, $keys[$key]); }

	oci_execute($stid);

	$nrows = oci_fetch_all($stid, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);

	echo "<table><caption>META adatok</caption>\n";
	echo "<thead><tr>";
	for ($i = 1; $i <= oci_num_fields($stid); $i++) {
		echo "<th>".strtolower(oci_field_name($stid, $i))."</th>";
		#echo "<td>oci_field_type($stid, $i)</td>";
	}
	echo "</tr><thead>\n";
	foreach ($res as $col) {
		echo "<tr>";
		foreach ($col as $item) {
			echo "<td>".($item !== null ? htmlentities($item, ENT_QUOTES) : "")."</td>";
		}
		echo "</tr>\n";
	}
	echo "<tfoot><tr><td colspan=".oci_num_fields($stid).">Összesen $nrows sor.</td></tr></tfoot>\n";
	echo "</table>\n";


	oci_free_statement($stid);
	oci_close($conn);

?>
