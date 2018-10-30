<?php
// Orchidstra3 Website dev 1.3
// Auther: Jay Yu
// Update: 2018/07/25
// Note: 1.3 make code beautiful | 1.2 search multiple keywords | 1.1 Add function HighLight

require_once("functions/db.php");
// Get parameters
$type = $_GET['type'];
if ( isset($_GET['keyword_gene']) ) { $kw_ge = $_GET['keyword_gene']; } else { $kw_ge = ""; }
if ( isset($_GET['keyword_id']) ) { $kw_id = $_GET['keyword_id']; } else { $kw_id = ""; }
if ( isset($_GET['keyword_desc']) ) { $kw_de = $_GET['keyword_desc']; } else { $kw_de = ""; }
if ( isset($_GET['keyword_go']) ) { $kw_go = $_GET['keyword_go']; } else { $kw_go = ""; }
if ( isset($_GET['keyword_enzyme']) ) { $kw_en = $_GET['keyword_enzyme']; } else { $kw_en = ""; }
if ( isset($_GET['keyword_interpro']) ) { $kw_in = $_GET['keyword_interpro']; } else { $kw_in = ""; }

// Define functions
function HighLight($target, $keyword) {
	$keywordlen = strlen($keyword);
	$replace = "<span class='highlight'>".substr($target, stripos($target, $keyword), $keywordlen)."</span>";
	return str_ireplace($keyword, $replace, $target);
}
function MultiHL($target, $keyword) {
	$retarget = $target;
	for ( $i=0; $i<count($keyword); $i++ ) {
		$retarget = HighLight($retarget, $keyword[$i]);
	}
	return $retarget;
}
function GetList($keyword) {
	$tmp_list = explode(";;", $keyword);
	for ( $i=0; $i<count($tmp_list); $i++ ) {
		if ( trim($tmp_list[$i])."xx" == "xx" ) {
			unset($tmp_list[$i]);
		} else {
			$tmp_list[$i] = trim($tmp_list[$i]);
		}
	}
	return array_values($tmp_list);
}
function SQLMultiKey($title, $keyword, $log='AND') {
	$sql_str = "(";
	for ( $k=0; $k<count($keyword); $k++ ) {
		if ( $k > 0 ) { $sql_str .= " ".$log." "; }
		$sql_str .= "(";
		for ( $t=0; $t<count($title);$t++ ) {
			if ( $t > 0 ) { $sql_str .= " OR "; }
			$sql_str .= " ".$title[$t]." LIKE '%".$keyword[$k]."%' ";
		}
		$sql_str .= ")";
	}
	$sql_str .= ")";
	return $sql_str;
}
function OptRes($opt, $gene) {
	$resh = "";
	if ( $opt == "go" ) {
		$ssql = "SELECT go_id, go_name FROM go_information b WHERE gene_id='$gene' AND ".SQLMultiKey($goa_t, $kw_go_li)." GROUP BY go_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>Blast2GO: [".MultiHL($srow['go_id'], $kw_go_li)."] ".MultiHL($srow['go_name'], $kw_go_li)."</p>";
		}
		$ssql = "SELECT GO_id FROM interpro d WHERE gene_id='$gid' AND ".SQLMultiKey($inc_t, $kw_go_li, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>InterPro: ".MultiHL($srow['GO_id'], $kw_go_li)."</p>";
		}
	}
	if ( $opt == "gowin" ) {
		$ssql = "SELECT go_id, go_name FROM go_information b WHERE gene_id='$gene' AND ".SQLMultiKey($goa_t, $kw_go_li)." GROUP BY go_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>[".MultiHL($srow['go_id'], $kw_go_li)."] ".MultiHL($srow['go_name'], $kw_go_li)."</p>";
		}
	}
	if ( $opt == "en" ) {
		$ssql = "SELECT * FROM enzyme_information c WHERE gene_id='$gene' AND ".SQLMultiKey($en_t, $kw_en_li);
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>[".MultiHL($srow['enzyme_code'], $kw_en_li)."] ".MultiHL($srow['enzyme_name'], $kw_en_li)."</p>";
		}
	}
	if ( $opt == "in" ) {
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro d WHERE gene_id='$gene' AND ".SQLMultiKey($ina_t, $kw_in_li, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>".$srow['analysis']." [ ".MultiHL($srow['signature_accession'], $kw_in_li)." ] ".MultiHL($srow['signature_description'], $kw_in_li, 'OR')."</p>";
		}
		$ssql = "SELECT IPR_code, IPR_name FROM interpro d WHERE gene_id='$gene' AND ".SQLMultiKey($inb_t, $kw_in_li, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>[ ".MultiHL($srow['IPR_code'], $kw_in_li)." ] ".MultiHL($srow['IPR_name'], $kw_in_li, 'OR')."</p>";
		}
		if ( $kw_go."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, GO_id FROM interpro d WHERE gene_id='$gene' AND ".SQLMultiKey($inc_t, $kw_in_li, 'OR')." GROUP BY GO_id";
		} else {
			$ssql = "SELECT IPR_code, GO_id FROM interpro d WHERE gene_id='$gene' AND (".SQLMultiKey($inc_t, $kw_in_li, 'OR')." OR ".SQLMultiKey($inc_t, $kw_go_li, 'OR').") GROUP BY GO_id";
		}
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$ingoh = MultiHL($srow['GO_id'], $kw_in_li);
			if ( $kw_go."xx" != "xx" ) { $ingoh = MultiHL($ingoh, $kw_go_li); }
			$resh .= "<p>[ ".$srow['IPR_code']." ] ".$ingoh."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro d WHERE gene_id='$gene' AND ".SQLMultiKey($ind_t, $kw_in_li, 'OR')." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>[ ".$srow['IPR_code']." ] ".MultiHL($srow['pathway'], $kw_in_li)."</p>";
		}
	}
	if ( $opt == "inca" ) {
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro d WHERE gene_id='$gene' AND ".SQLMultiKey($ina_t, $kw_in_li, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>".$srow['analysis']." [ ".MultiHL($srow['signature_accession'], $kw_in_li)." ] ".MultiHL($srow['signature_description'], $kw_in_li, 'OR')."</p>";
		}
	}
	if ( $opt == "inci" ) {
		$ssql = "SELECT IPR_code, IPR_name FROM interpro d WHERE gene_id='$gene' AND ".SQLMultiKey($inb_t, $kw_in_li, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>[ ".MultiHL($srow['IPR_code'], $kw_in_li)." ] ".MultiHL($srow['IPR_name'], $kw_in_li, 'OR')."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro d WHERE gene_id='$gene' AND ".SQLMultiKey($inc_t, $kw_in_li, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>[ ".$srow['IPR_code']." ] ".MultiHL($srow['GO_id'], $kw_in_li)."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro d WHERE gene_id='$gene' AND ".SQLMultiKey($ind_t, $kw_in_li, 'OR')." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$resh .= "<p>[ ".$srow['IPR_code']." ] ".MultiHL($srow['pathway'], $kw_in_li)."</p>";
		}
	}
	return $resh;
}

// Set parameters - keywords array
if ( $kw_ge."xx" != "xx" ) { $kw_ge_li = GetList($kw_ge); }
if ( $kw_id."xx" != "xx" ) { $kw_id_li = GetList($kw_id); }
if ( $kw_de."xx" != "xx" ) { $kw_de_li = GetList($kw_de); }
if ( $kw_go."xx" != "xx" ) { $kw_go_li = GetList($kw_go); }
if ( $kw_en."xx" != "xx" ) { $kw_en_li = GetList($kw_en); }
if ( $kw_in."xx" != "xx" ) { $kw_in_li = GetList($kw_in); }

// Set parameters - sql table columns: gene_features as a, go_information as b, enzyme_information as c, interpro as d
$ge_t = array('a.name', 'a.description', 'b.go_id', 'b.go_name', 'c.enzyme_code', 'c.enzyme_name', 'd.signature_accession', 'd.signature_description', 'd.IPR_code', 'd.IPR_name', 'd.GO_id', 'd.pathway');
$id_t = array('a.name');
$de_t = array('a.description');
$go_t = array('b.go_id', 'b.go_name', 'd.GO_id');
$en_t = array('c.enzyme_code', 'c.enzyme_name');
$in_t = array('d.signature_accession', 'd.signature_description', 'd.IPR_code', 'd.IPR_name', 'd.GO_id', 'd.pathway');

$goa_t = array('b.go_id', 'b.go_name');
$ina_t = array('d.signature_accession', 'd.signature_description');
$inb_t = array('d.IPR_code', 'd.IPR_name');
$inc_t = array('d.GO_id');
$ind_t = array('d.pathway');

// Set 2 sql query
$db = new DB;
$sdb = new DB;

// Set id & description fliter
if ( $kw_id."xx" != "xx" && $kw_de."xx" != "xx" ) {
	$idde_fliter = SQLMultiKey($id_t, $kw_id_li)." AND ".SQLMultiKey($de_t, $kw_de_li);
} else if ( $kw_id."xx" != "xx" && $kw_de."xx" == "xx" ) {
	$idde_fliter = SQLMultiKey($id_t, $kw_id_li);
} else if ( $kw_de."xx" != "xx" && $kw_id."xx" == "xx" ) {
	$idde_fliter = SQLMultiKey($de_t, $kw_de_li);
} else {
	$idde_fliter = "";
}

// Caculate result
if ( $type == "ge" ) {
	$sqlb = "FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".SQLMultiKey($ge_t, $kw_ge_li);
	if ( $keyword_id."xx" != "xx" ) { $sqlb .= " AND ".SQLMultiKey($id_t, $kw_id_li); }
	if ( $keyword_desc."xx" != "xx" ) { $sqlb .= " AND ".SQLMultiKey($de_t, $kw_de_li); }
	if ( $keyword_go."xx" != "xx" ) { $sqlb .= " AND ".SQLMultiKey($go_t, $kw_go_li); }
	if ( $keyword_enzyme."xx" != "xx" ) { $sqlb .= " AND ".SQLMultiKey($en_t, $kw_en_li); }
	if ( $keyword_interpro."xx" != "xx" ) { $sqlb .= " AND ".SQLMultiKey($in_t, $kw_in_li); }
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}
if ( $type == "id" ) {
	$sqlb = "FROM gene_features a WHERE a.type='gene' AND ".$idde_fliter;
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}
if ( $type == "go" ) {
	$sqlb = "FROM go_information b, interpro d WHERE b.gene_id=d.gene_id AND ".SQLMultiKey($go_t, $kw_go_li);
	$sql = "SELECT COUNT(DISTINCT d.gene_id) AS count ".$sqlb;
}
if ( $type == "en" ) {
	$sqlb = "FROM enzyme_information c WHERE ".SQLMultiKey($en_t, $kw_en_li);
	$sql = "SELECT COUNT(DISTINCT c.gene_id) AS count ".$sqlb;
}
if ( $type == "in" ) {
	$sqlb = "FROM interpro d WHERE ".SQLMultiKey($in_t, $kw_in_li);
	$sql = "SELECT COUNT(DISTINCT d.gene_id) AS count ".$sqlb;
}
if ( $type == "idgo" ) {
	$sqlb = "FROM gene_features a, go_information b, interpro d WHERE a.type='gene' AND a.name=b.gene_id AND a.name=d.gene_id AND ".$idde_fliter." AND ".SQLMultiKey($go_t, $kw_go_li);
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}
if ( $type == "iden" ) {
	$sqlb = "FROM gene_features a, enzyme_information c WHERE a.type='gene' AND a.name=c.gene_id AND ".$idde_fliter." AND ".SQLMultiKey($en_t, $kw_en_li);
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}
if ( $type == "idin" ) {
	$sqlb = "FROM gene_features a, interpro d WHERE a.type='gene' AND a.name=d.gene_id AND ".$idde_fliter." AND ".SQLMultiKey($in_t, $kw_in_li);
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}
if ( $type == "goen" ) {
	$sqlb = "FROM go_information b, enzyme_information c, interpro d WHERE b.gene_id=d.gene_id AND c.gene_id=d.gene_id AND ".SQLMultiKey($go_t, $kw_go_li)." AND ".SQLMultiKey($en_t, $kw_en_li);
	$sql = "SELECT COUNT(DISTINCT d.gene_id) AS count ".$sqlb;
}
if ( $type == "goin" ) {
	$sqlb = "FROM go_information b, interpro d WHERE a.gene_id=d.gene_id AND ".SQLMultiKey($go_t, $kw_go_li)." AND ".SQLMultiKey($in_t, $kw_in_li);
	$sql = "SELECT COUNT(DISTINCT d.gene_id) AS count ".$sqlb;
}
if ( $type == "enin" ) {
	$sqlb = "FROM enzyme_information c, interpro d WHERE c.gene_id=d.gene_id AND ".SQLMultiKey($en_t, $kw_en_li)." AND ".SQLMultiKey($in_t, $kw_in_li);
	$sql = "SELECT COUNT(DISTINCT d.gene_id) AS count ".$sqlb;
}
if ( $type == "idgoen" ) {
	$sqlb = "FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".$idde_fliter." AND ".SQLMultiKey($go_t, $kw_go_li)." AND ".SQLMultiKey($en_t, $kw_en_li);
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}
if ( $type == "idgoin" ) {
	$sqlb = "FROM gene_features a, go_information b, interpro d WHERE a.type='gene' AND a.name=b.gene_id AND a.name=d.gene_id AND ".$idde_fliter." AND ".SQLMultiKey($go_t, $kw_go_li)." AND ".SQLMultiKey($in_t, $kw_in_li);
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}
if ( $type == "idenin" ) {
	$sqlb = "FROM gene_features a, enzyme_information c, interpro d WHERE a.type='gene' AND a.name=c.gene_id AND a.name=d.gene_id AND ".$idde_fliter." AND ".SQLMultiKey($en_t, $kw_en_li)." AND ".SQLMultiKey($in_t, $kw_in_li);
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}
if ( $type == "goenin" ) {
	$sqlb = "FROM go_information b, enzyme_information c, interpro d WHERE b.gene_id=d.gene_id AND c.gene_id=d.gene_id AND ".SQLMultiKey($go_t, $kw_go_li)." AND ".SQLMultiKey($en_t, $kw_en_li)." AND ".SQLMultiKey($in_t, $kw_in_li);
	$sql = "SELECT COUNT(DISTINCT d.gene_id) AS count ".$sqlb;
}
if ( $type == "idgoenin" ) {
	$sqlb = "FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".$idde_fliter." AND ".SQLMultiKey($go_t, $kw_go_li)." AND ".SQLMultiKey($en_t, $kw_en_li)." AND ".SQLMultiKey($in_t, $kw_in_li);
	$sql = "SELECT COUNT(DISTINCT a.name) AS count ".$sqlb;
}

$db->query($sql);
$row = $db->fetch_assoc();
$count = $row['count'];

// jqGrid pages default
$page = $_GET['page']; // get the requested page 
$limit = $_GET['rows']; // get how many rows we want to have into the grid 
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
$sord = $_GET['sord']; // get the direction 
if(!$sidx) {
	$sidx =1;
}

// jqGrid pagination
if( $count >0 ) { 
$total_pages = ceil($count/$limit); 
} else { 
$total_pages = 0; 
} 
if ($page > $total_pages) $page=$total_pages; 
$start = $limit*$page - $limit; // do not put $limit*($page - 1) 
if($start < 0) $start = 0;

$responce->page = $page; 
$responce->total = $total_pages; 
$responce->records = $count; 

// Output result
if ( $type == "ge" ) {
	$sql = "SELECT DISTINCT a.name ".$sqlb." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		// Output id and description
		$ssql = "SELECT name, description FROM gene_features WHERE name='$gid'";
		$sdb->query($ssql);
		$srow = $sdb->fetch_assoc();
		$idh = MultiHL($srow['name'], $kw_ge_li);
		if ( $kw_id."xx" != "xx" ) { $idh = MultiHL($idh, $kw_id_li); }
		$idlh = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$idh."</a>";
		$deh = MultiHL($srow['description'], $kw_ge_li);
		if ( $kw_de."xx" != "xx" ) { $deh = MultiHL($idh, $kw_id_li); }
		// Output GO term
		if ( $kw_go."xx" == "xx" ) {
			$ssql = "SELECT go_id, go_name FROM go_information b WHERE gene_id='$gid' AND ".SQLMultiKey($goa_t, $kw_ge_li, 'OR');
		} else {
			$ssql = "SELECT go_id, go_name FROM go_information b WHERE gene_id='$gid' AND (".SQLMultiKey($goa_t, $kw_ge_li, 'OR')." OR ".SQLMultiKey($goa_t, $kw_go_li).")";
		}
		$sdb->query($ssql);
		$GOh = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$goidh = MultiHL($srow['go_id'], $kw_ge_li);
			$gonah = MultiHL($srow['go_name'], $kw_ge_li);
			if ( $kw_go."xx" != "xx" ) {
				$goidh = MultiHL($goidh, $kw_go_li);
				$gonah = MultiHL($gonah, $kw_go_li);
			}
			$GOh .= "<p>[".$goidh."] ".$gonah."</p>";
		}
		// Output Enzyme
		if ( $kw_en."xx" == "xx" ) {
			$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information c WHERE gene_id='$gid' AND ".SQLMultiKey($en_t, $kw_ge_li, 'OR');
		} else {
			$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information c WHERE gene_id='$gid' AND (".SQLMultiKey($en_t, $kw_ge_li, 'OR')." OR ".SQLMultiKey($en_t, $kw_en_li).")";
		}
		$sdb->query($ssql);
		$enh = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$encoh = MultiHL($srow['enzyme_code'], $kw_ge_li);
			$ennah = MultiHL($srow['enzyme_name'], $kw_ge_li);
			if ( $kw_en."xx" != "xx" ) {
				$encoh = MultiHL($encoh, $kw_en_li);
				$ennah = MultiHL($ennah, $kw_en_li);
			}
			$enh .= "<p>[".$encoh."] ".$ennah."</p>";
		}
		// Output Interpro
		// 1. Analysis signature
		if ( $kw_in."xx" == "xx" ) {
			$ssql = "SELECT signature_accession, signature_description FROM interpro d WHERE gene_id='$gid' AND ".SQLMultiKey($ina_t, $kw_ge_li, 'OR');
		} else {
			$ssql = "SELECT signature_accession, signature_description FROM interpro d WHERE gene_id='$gid' AND (".SQLMultiKey($ina_t, $kw_ge_li, 'OR')." OR ".SQLMultiKey($ina_t, $kw_in_li, 'OR').")";
		}
		$sdb->query($ssql);
		$inh = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$insah = MultiHL($srow['signature_accession'], $kw_ge_li);
			$insdh = MultiHL($srow['signature_description'], $kw_ge_li);
			if ( $kw_in."xx" != "xx" ) {
				$insah = MultiHL($insah, $kw_in_li);
				$insdh = MultiHL($insdh, $kw_in_li);
			}
			$inh .= "<p>[ ".$insah." ] ".$insdh."</p>";
		}
		// 2. IPR
		if ( $kw_in."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, IPR_name FROM interpro d WHERE gene_id='$gid' AND ".SQLMultiKey($inb_t, $kw_ge_li, 'OR')." GROUP BY IPR_code";
		} else {
			$ssql = "SELECT IPR_code, IPR_name FROM interpro d WHERE gene_id='$gid' AND (".SQLMultiKey($inb_t, $kw_ge_li, 'OR')." OR ".SQLMultiKey($inb_t, $kw_in_li, 'OR').") GROUP BY IPR_code";
		}
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$inich = MultiHL($srow['IPR_code'], $kw_ge_li);
			$ininh = MultiHL($srow['IPR_name'], $kw_ge_li);
			if ( $kw_in."xx" != "xx" ) {
				$inich = MultiHL($inich, $kw_in_li);
				$ininh = MultiHL($ininh, $kw_in_li);
			}
			$inh .= "<p>[ ".$inich." ] ".$ininh."</p>";
		}
		// 3. GO
		if ( $kw_in."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, GO_id FROM interpro d WHERE gene_id='$gid' AND (".SQLMultiKey($inc_t, $kw_ge_li, 'OR');
		} else {
			$ssql = "SELECT IPR_code, GO_id FROM interpro d WHERE gene_id='$gid' AND (".SQLMultiKey($inc_t, $kw_ge_li, 'OR')." OR ".SQLMultiKey($inc_t, $kw_in_li);
		}
		if ( $kw_go."xx" != "xx" ) {
			$ssql .= " OR ".SQLMultiKey($inc_t, $kw_go_li, 'OR');
		}
		$ssql .= ") GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$ingoh = MultiHL($srow['GO_id'], $kw_ge_li);
			if ( $kw_in."xx" != "xx" ) { $ingoh = MultiHL($inigh, $kw_in_li); }
			if ( $kw_go."xx" != "xx" ) { $ingoh = MultiHL($inigh, $kw_go_li); }
			$inh .= "<p>[ ".$srow['IPR_code']." ] ".$ingoh."</p>";
		}
		// 4. Pathway
		if ( $kw_in."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, pathway FROM interpro d WHERE gene_id='$gid' AND ".SQLMultiKey($ind_t, $kw_ge_li, 'OR')." GROUP BY pathway";
		} else {
			$ssql = "SELECT IPR_code, pathway FROM interpro d WHERE gene_id='$gid' AND (".SQLMultiKey($ind_t, $kw_ge_li, 'OR')." OR ".SQLMultiKey($ind_t, $kw_in_li, 'OR').") GROUP BY pathway";
		}
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$inpah = MultiHL($srow['pathway'], $kw_ge_li);
			if ( $kw_in."xx" != "xx" ) { $inpah = MultiHL($inpah, $kw_in_li); }
			$inh .= "<p>[ ".$srow['IPR_code']." ] ".$inpah."</p>";
		}
		// Not found message
		if ( $GOh."xx" == "xx" ) { $GOh = "Not Found."; }
		if ( $enh."xx" == "xx" ) { $enh = "Not Found."; }
		if ( $inh."xx" == "xx" ) { $inh = "Not Found."; }
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $GOh, $enh, $inh );
		$i++;
	}
}
if ( $type == "id" ) {
	$sql = "SELECT * ".$sqlb."GROUP BY name LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$idlh = "<a href='genepage.php?gid=".$row['name']."' target='_blank'>".MultiHL($row['name'], $kw_id_li)."</a>";
		$deh = MultiHL($row['description'], $kw_de_li);
		$lo = $row['location_scaffold'].": ".$row['location_start']."...".$row['location_end'];

		$responce->rows[$i]['id'] = $row['name'];
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $lo );
		$i++;
	}
}
if ( $type == "go" ) {
	$sql = "SELECT b.gene_id ".$sqlb." GROUP BY b.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		// Output ID with link
		$idl = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		// Output GO result
		$goh = OptRes('go', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idl, $goh );
		$i++;
	}
}
if ( $type == "en" ) {
	$sql = "SELECT gene_id ".$sqlb." GROUP BY gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		// Output ID with link
		$idl = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		// Output Enzyme
		$enh = OptRes('en', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idl, $enh );
		$i++;
	}
}
if ( $type == "in" ) {
	$sql = "SELECT gene_id ".$sqlb." GROUP BY gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		// Output ID with link
		$idl = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		// Output Analysis
		$anah = OptRes('inca', $gid);
		// Output IPR
		$iprh = OptRes('inci', $gid);
		// Set Not Found
		if ( $anah."xx" == "xx" ) { $anah = "Not Found."; }
		if ( $iprh."xx" == "xx" ) { $iprh = "Not Found."; }
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idl, $anah, $iprh );
		$i++;
	}
}
if ( $type == "idgo" ) {
	$sql = "SELECT a.name, a.description ".$sqlb." GROUP BY a.name LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID & description
		$idlh = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MultiHL($gid, $kw_id_li)."</a>";
		$deh = MultiHL($srow['description'], $kw_de_li);
		// Output GO
		$goh = OptRes('go', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $goh );
		$i++;
	}
}
if ( $type == "iden" ) {
	$sql = "SELECT a.name, a.description ".$sqlb." GROUP BY a.name LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID & description
		$idlh = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MultiHL($gid, $kw_id_li)."</a>";
		$deh = MultiHL($srow['description'], $kw_de_li);
		// Output Enzyme
		$enh = OptRes('en', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $enh );
		$i++;
	}
}
if ( $type == "idin" ) {
	$sql = "SELECT a.name, a.description ".$sqlb." GROUP BY a.name LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID & description
		$idlh = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MultiHL($gid, $kw_id_li)."</a>";
		$deh = MultiHL($srow['description'], $kw_de_li);
		// Output Analysis
		$anah = OptRes('inca', $gid);
		// Output IPR
		$iprh = OptRes('inci', $gid);
		// Set Not Found
		if ( $anah."xx" == "xx" ) { $anah = "Not Found."; }
		if ( $iprh."xx" == "xx" ) { $iprh = "Not Found."; }
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $anah, $iprh );
		$i++;
	}
}
if ( $type == "goen" ) {
	$sql = "SELECT d.gene_id ".$sqlb." GROUP BY d.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID with link
		$idl = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		// Output GO
		$goh = OptRes('go', $gid);
		// Output Enzyme
		$enh = OptRes('en', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idl, $goh, $enh );
		$i++;
	}
}
if ( $type == "goin" ) {
	$sql = "SELECT d.gene_id ".$sqlb." GROUP BY d.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID with link
		$idl = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		// Output GO
		$goh = OptRes('gowin', $gid);
		// Output Interpro
		$inh = OptRes('in', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idl, $goh, $inh );
		$i++;
	}
}
if ( $type == "enin" ) {
	$sql = "SELECT d.gene_id ".$sqlb." GROUP BY d.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID with link
		$idl = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		// Output Enzyme
		$enh = OptRes('en', $gid);
		// Output Interpro
		$inh = OptRes('in', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idl, $enh, $inh );
		$i++;
	}	
}
if ( $type == "idgoen" ) {
	$sql = "SELECT a.name, a.description ".$sqlb." GROUP BY a.name LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID & description
		$idlh = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MultiHL($gid, $kw_id_li)."</a>";
		$deh = MultiHL($srow['description'], $kw_de_li);
		// Output GO
		$goh = OptRes('go', $gid);
		// Output Enzyme
		$enh = OptRes('en', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $goh, $enh );
		$i++;
	}	
}
if ( $type == "idgoin" ) {
	$sql = "SELECT a.name, a.description ".$sqlb." GROUP BY a.name LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID & description
		$idlh = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MultiHL($gid, $kw_id_li)."</a>";
		$deh = MultiHL($srow['description'], $kw_de_li);
		// Output GO
		$goh = OptRes('gowin', $gid);
		// Output Interpro
		$inh = OptRes('in', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $goh, $inh );
		$i++;
	}
}
if ( $type == "idenin" ) {
	$sql = "SELECT a.name, a.description ".$sqlb." GROUP BY a.name LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID & description
		$idlh = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MultiHL($gid, $kw_id_li)."</a>";
		$deh = MultiHL($srow['description'], $kw_de_li);
		// Output Enzyme
		$enh = OptRes('en', $gid);
		// Output Interpro
		$inh = OptRes('in', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $enh, $inh );
		$i++;
	}
}
if ( $type == "goenin" ) {
	$sql = "SELECT d.gene_id ".$sqlb." GROUP BY d.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['gene_id'];
		// Output ID with link
		$idl = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		// Output GO
		$goh = OptRes('gowin', $gid);
		// Output Enzyme
		$enh = OptRes('en', $gid);
		// Output Interpro
		$inh = OptRes('in', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idl, $goh, $enh, $inh );
		$i++;
	}
}
if ( $type == "idgoenin" ) {
	$sql = "SELECT a.name, a.description ".$sqlb." GROUP BY a.name LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $srow = $sdb->fetch_assoc() ) {
		$gid = $srow['name'];
		// Output ID & description
		$idlh = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MultiHL($gid, $kw_id_li)."</a>";
		$deh = MultiHL($srow['description'], $kw_de_li);
		// Output GO
		$goh = OptRes('gowin', $gid);
		// Output Enzyme
		$enh = OptRes('en', $gid);
		// Output Interpro
		$inh = OptRes('in', $gid);
		// Output jason
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array( $idlh, $deh, $goh, $enh, $inh );
		$i++;
	}
}
// Return Result
echo json_encode($responce);
?>