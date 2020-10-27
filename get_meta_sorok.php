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
		SELECT --j.nev korzet, initcap(h.nev) telepules, meta_id,
			f.ertek fekves, hrsz_tol, hrsz_ig,
			ju.ertek jogszabaly_utasitas,
			ma.ertek meretarany,
			v.ertek eredeti_vetulet,
			adatkeszlet_eloallit,
			v2.ertek adatkeszlet_vetulet,
			--CASE WHEN v.ertek=v2.ertek THEN v2.ertek ELSE v2.ertek || ' [' || v.ertek ||']' END vetuleti_rendszerek
			egyezoseg, forgalom_datuma, adatformatum,
			min_y ||', '|| min_x bal_also_y_x, max_y ||', '|| max_x jobb_felso_y_x
			--haszn, korlatozas, adatall_nyelve
			--kfh.ertek hivatal_neve
		FROM dat.dt_meta m
			JOIN takaros.helysegek h ON h.id = m.telepules_id
			JOIN takaros.jarasok j ON j.korzetszam = h.korzetszam AND j.megyekod = h.megyekod
			JOIN dat.dtc_fekves f ON f.kod = m.fekv_kod
			JOIN dat.dtc_jogsz_utasitas ju ON ju.kod = m.eredet_megfeleloseg_kod
			JOIN dat.dtc_vetulet v ON v.kod = m.eredet_vetulet_kod
			JOIN dat.dtc_vetulet v2 ON v2.kod = m.adatkeszlet_vetulet_kod
			JOIN dat.dtc_meretarany ma ON ma.kod = m.eredet_ma_kod
			--JOIN dat.dtc_korzeti_fh kfh ON kfh.kod = m.hiv_id
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
