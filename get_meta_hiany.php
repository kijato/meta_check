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
	SELECT DISTINCT t.telepules, t.fekves, t.hrsz
	FROM 
	(
		SELECT DISTINCT --rpad(k.korzet,30,' ') as korzet,
				h.hely_id,
				rpad(initcap(he1.nev),30,' ') as telepules,
				h.fekv_kod,
				decode(h.fekv_kod, 1 , 'belterület' ,2 ,'külterület',3, 'zártkert') as fekves,
				h.hrsz
				--CASE h.fekv_kod WHEN 2 THEN '0' || to_char(h.hrsz) ELSE to_char(h.hrsz) END || CASE nvl(h.hrsz1,0) WHEN 0 THEN null ELSE '/' || h.hrsz1 END as helyrajziszam
		FROM --(SELECT ertek AS korzet FROM parameterek WHERE kulcs='FHCIM1') k,
			 takaros.hrszek h,
			 takaros.helysegek he1,
			 takaros.foldreszletek fo1,
			 takaros.onallo_ingatlanok on1
		WHERE initcap(he1.nev) = :telepules
		  and h.hely_id=he1.id and h.id=on1.hrsz_id
		  and on1.stat_kod in (2,8)
		  and on1.id=fo1.onin_id
		  and fo1.stat_kod in (2,8)
		  and h.stat_kod in (2,8)
		  and h.hrsz<>9999999
		ORDER BY telepules, fekves, hrsz
	) t,
	dt_meta m
	WHERE m.megsz_datum IS NULL
	  AND m.telepules_id = t.hely_id
	  AND m.fekv_kod = t.fekv_kod
	  AND t.hrsz BETWEEN m.hrsz_tol AND m.hrsz_ig
	ORDER BY 1,2,3
	";
	$stid = oci_parse($conn, $sql);
	oci_bind_by_name($stid, ":telepules", $telepules);
	oci_execute($stid);

	$nrows = oci_fetch_all($stid, $res, null, null, OCI_FETCHSTATEMENT_BY_ROW);

	echo "<table><caption>META tartományokból hiányzó TAKAROS hrsz-ok</caption>\n";
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
