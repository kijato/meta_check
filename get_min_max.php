<?php

	$configs = include('config.php');
	#echo json_encode($configs->app_info);

	$request = $_REQUEST ;
	$korzet    = isset($request['korzet'])    ? htmlspecialchars($request['korzet'])    : "%";
	$telepules = isset($request['telepules']) ? htmlspecialchars($request['telepules']) : "%";
	$fekves    = isset($request['fekves'])    ? htmlspecialchars($request['fekves'])    : "%";
	$hrsz_tol  = isset($request['hrsz_tol'])  ? htmlspecialchars($request['hrsz_tol'])  : "%";
	$hrsz_ig   = isset($request['hrsz_ig'])   ? htmlspecialchars($request['hrsz_ig'])   : "%";

	$conn = oci_connect($configs->korzetek[$korzet]['username'], $configs->korzetek[$korzet]['password'], $configs->korzetek[$korzet]['connection_string'], $configs->korzetek[$korzet]['character_set']);
	if (!$conn) {
	$e = oci_error();
	trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
	}

	$sql = "
	SELECT telepules, fekves, /*hrsz,*/ min(min_y) ||', '|| min(min_x) bal_also, max(max_y) ||', '|| max(max_x) jobb_felso
	FROM (
		SELECT telepules, f.ertek fekves, CASE instr(hrsz,'/') WHEN 0 THEN to_number(hrsz) ELSE to_number(substr(hrsz,1,instr(hrsz,'/')-1)) END hrsz,
			min_y, min_x, max_y, max_x
		FROM
		( select terkep_id, replace(nev,'_jogerős','') telepules from dat.dtk_terkep where stat_kod=1 and nev like '%_jogerős' ) t,
		(	SELECT terkep_id, fekves fekves_kod, regexp_replace(helyr_szam,'^0','') hrsz, min_y, min_x, max_y, max_x
			FROM dt_obj_attrbc
			WHERE verzio_jogeros IS NOT NULL AND verzio_ki IS NULL
			union
			SELECT terkep_id, fekves fekves_kod, regexp_replace(helyr_szam,'^0','') hrsz, min_y, min_x, max_y, max_x
			FROM dt_obj_attrbd
			WHERE verzio_jogeros IS NOT NULL AND verzio_ki IS NULL ) bc_bd
		join dtc_fekves f on f.kod=bc_bd.fekves_kod
		WHERE t.terkep_id = bc_bd.terkep_id
	) A
	WHERE telepules LIKE :telepules
	  AND fekves LIKE :fekves
	  AND hrsz BETWEEN :hrsz_tol AND :hrsz_ig
	GROUP BY telepules, fekves--, hrsz
	ORDER BY 1,2,3";
	$stid = oci_parse($conn, $sql);
	$keys = array(
		#':korzet' => $korzet,
		':telepules' => $telepules,
		':fekves' => $fekves,
		':hrsz_tol' => $hrsz_tol,
		':hrsz_ig' => $hrsz_ig
	);
	// oci_bind_by_name($stid, $key, $val) does not work because it binds each placeholder to the same location: $val instead use the actual location of the data: $ba[$key] !
	foreach ($keys as $key => $val) { oci_bind_by_name($stid, $key, $keys[$key]); }

	oci_execute($stid);

	$nrows = oci_fetch_all($stid, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);

	echo "<table><caption>Befoglaló téglalap <i>(BC-BD táblák min-max értékei)</i></caption>\n";
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
