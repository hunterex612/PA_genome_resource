<?php
// Orchidstra3 Website dev 1.2
// Auther: Jay Yu
// Update: 2018/07/18
// Note: 1.2 search multiple keywords | 1.1 Add function HighLight

require_once("functions/db.php");
$type = $_GET['type'];
if ( isset($_GET['keyword_gene']) ) { $keyword_gene = $_GET['keyword_gene']; } else { $keyword_gene = ""; }
if ( isset($_GET['keyword_id']) ) { $keyword_id = $_GET['keyword_id']; } else { $keyword_id = ""; }
if ( isset($_GET['keyword_desc']) ) { $keyword_desc = $_GET['keyword_desc']; } else { $keyword_desc = ""; }
if ( isset($_GET['keyword_go']) ) { $keyword_go = $_GET['keyword_go']; } else { $keyword_go = ""; }
if ( isset($_GET['keyword_enzyme']) ) { $keyword_enzyme = $_GET['keyword_enzyme']; } else { $keyword_enzyme = ""; }
if ( isset($_GET['keyword_interpro']) ) { $keyword_interpro = $_GET['keyword_interpro']; } else { $keyword_interpro = ""; }

function HighLight($target, $keyword) {
	$keywordlen = strlen($keyword);
	$replace = "<span class='highlight'>".substr($target, stripos($target, $keyword), $keywordlen)."</span>";
	return str_ireplace($keyword, $replace, $target);
}

function MutiHL($target, $keyword) {
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

function SQLMutiKey($title, $keyword, $log='AND') {
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

if ( $keyword_gene."xx" != "xx" ) {	$kw_gene_list = GetList($keyword_gene); }
if ( $keyword_id."xx" != "xx" ) {	$kw_id_list = GetList($keyword_id); }
if ( $keyword_desc."xx" != "xx" ) {	$kw_desc_list = GetList($keyword_desc); }
if ( $keyword_go."xx" != "xx" ) {	$kw_go_list = GetList($keyword_go); }
if ( $keyword_enzyme."xx" != "xx" ) {	$kw_enzyme_list = GetList($keyword_enzyme); }
if ( $keyword_interpro."xx" != "xx" ) {	$kw_interpro_list = GetList($keyword_interpro); }

$db = new DB;
if ( $type == "ge" ) {
	$gene_title = array('a.name', 'a.description', 'b.go_id', 'b.go_name', 'c.enzyme_code', 'c.enzyme_name', 'd.signature_accession', 'd.signature_description', 'd.IPR_code', 'd.IPR_name', 'd.GO_id', 'd.pathway');
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".SQLMutiKey($gene_title, $kw_gene_list);
	if ( $keyword_id."xx" != "xx" ) {
		$geneid_title = array('a.name');
		$sql .= " AND ".SQLMutiKey($geneid_title, $kw_id_list);
	}
	if ( $keyword_desc."xx" != "xx" ) {
		$genede_title = array('a.description');
		$sql .= " AND ".SQLMutiKey($genede_title, $kw_desc_list);
	}
	if ( $keyword_go."xx" != "xx" ) {
		$genego_title = array('b.go_id', 'b.go_name', 'd.GO_id');
		$sql .= " AND ".SQLMutiKey($genego_title, $kw_go_list);
	}
	if ( $keyword_enzyme."xx" != "xx" ) {
		$geneen_title = array('c.enzyme_code', 'c.enzyme_name');
		$sql .= " AND ".SQLMutiKey($geneen_title, $kw_enzyme_list);
	}
	if ( $keyword_interpro."xx" != "xx" ) {
		$genein_title = array('d.signature_accession', 'd.signature_description', 'd.IPR_code', 'd.IPR_name', 'd.GO_id', 'd.pathway');
		$sql .= " AND ".SQLMutiKey($genein_title, $kw_interpro_list);
	}
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "id" ) {
	if ( $kw_id_list[0]."xx" != "xx" && $kw_desc_list[0]."xx" == "xx" ) {
		$id_title = array('name');
		$sql = "SELECT COUNT(DISTINCT name) AS count FROM gene_features WHERE type='gene' AND ".SQLMutiKey($id_title, $kw_id_list);
	} else if ( $kw_desc_list[0]."xx" != "xx" && $kw_id_list[0]."xx" == "xx" ) {
		$desc_title = array('description');
		$sql = "SELECT COUNT(DISTINCT name) AS count FROM gene_features WHERE type='gene' AND ".SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$id_title = array('name');
		$desc_title = array('description');
		$sql = "SELECT COUNT(DISTINCT name) AS count FROM gene_features WHERE type='gene' AND ".SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "go" ) {
	$go_title = array('a.go_id', 'a.go_name', 'b.GO_id');
	$sql = "SELECT COUNT(DISTINCT b.gene_id) AS count FROM go_information a, interpro b WHERE a.gene_id=b.gene_id AND ".SQLMutiKey($go_title, $kw_go_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "en" ) {
	$enzyme_title = array('enzyme_code', 'enzyme_name');
	$sql = "SELECT COUNT(DISTINCT gene_id) AS count FROM enzyme_information WHERE ".SQLMutiKey($enzyme_title, $kw_enzyme_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "in" ) {
	$interpro_title = array('signature_accession', 'signature_description', 'IPR_code', 'IPR_name', 'GO_id', 'pathway');
	$sql = "SELECT COUNT(DISTINCT gene_id) AS count FROM interpro WHERE ".SQLMutiKey($interpro_title, $kw_interpro_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idgo" ) {
	$id_title = array('a.name');
	$desc_title = array('a.description');
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$go_title = array('b.go_id', 'b.go_name', 'c.GO_id');
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($go_title, $kw_go_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "iden" ) {
	$id_title = array('a.name');
	$desc_title = array('a.description');
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$enzyme_title = array('enzyme_code', 'enzyme_name');
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, enzyme_information b WHERE a.type='gene' AND a.name=b.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($enzyme_title, $kw_enzyme_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idin" ) {
	$id_title = array('a.name');
	$desc_title = array('a.description');
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$ip_title = array('b.signature_accession', 'b.signature_description', 'b.IPR_code', 'b.IPR_name', 'b.GO_id', 'b.pathway');
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, interpro b WHERE a.type='gene' AND a.name=b.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($ip_title, $kw_interpro_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "goen" ) {
	$go_title = array('a.go_id', 'a.go_name', 'b.GO_id');
	$en_title = array('c.enzyme_code', 'c.enzyme_name');
	$sql = "SELECT COUNT(DISTINCT b.gene_id) AS count FROM go_information a, interpro b, enzyme_information c WHERE a.gene_id=b.gene_id AND b.gene_id=c.gene_id AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($en_title, $kw_enzyme_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "goin" ) {
	$go_title = array('a.go_id', 'a.go_name', 'b.GO_id');
	$in_title = array('b.signature_accession', 'b.signature_description', 'b.IPR_code', 'b.IPR_name', 'b.GO_id', 'b.pathway');
	$sql = "SELECT COUNT(DISTINCT b.gene_id) AS count FROM go_information a, interpro b WHERE a.gene_id=b.gene_id AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "enin" ) {
	$en_title = array('a.enzyme_code', 'a.enzyme_name');
	$in_title = array('b.signature_accession', 'b.signature_description', 'b.IPR_code', 'b.IPR_name', 'b.GO_id', 'b.pathway');
	$sql = "SELECT COUNT(DISTINCT b.gene_id) AS count FROM enzyme_information a, interpro b WHERE a.gene_id=b.gene_id AND ".SQLMutiKey($en_title, $kw_enzyme_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idgoen" ) {
	$id_title = array('a.name');
	$desc_title = array('a.description');
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$go_title = array('b.go_id', 'b.go_name', 'c.GO_id');
	$en_title = array('d.enzyme_code', 'd.enzyme_name');
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, interpro c, enzyme_information d WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($en_title, $kw_enzyme_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idgoin" ) {
	$id_title = array('a.name');
	$desc_title = array('a.description');
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$go_title = array('b.go_id', 'b.go_name', 'c.GO_id');
	$in_title = array('c.signature_accession', 'c.signature_description', 'c.IPR_code', 'c.IPR_name', 'c.GO_id', 'c.pathway');
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idenin" ) {
	$id_title = array('a.name');
	$desc_title = array('a.description');
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$en_title = array('b.enzyme_code', 'b.enzyme_name');
	$in_title = array('c.signature_accession', 'c.signature_description', 'c.IPR_code', 'c.IPR_name', 'c.GO_id', 'c.pathway');
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, enzyme_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($en_title, $kw_enzyme_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "goenin" ) {
	$go_title = array('a.go_id', 'a.go_name', 'c.GO_id');
	$en_title = array('b.enzyme_code', 'b.enzyme_name');
	$in_title = array('c.signature_accession', 'c.signature_description', 'c.IPR_code', 'c.IPR_name', 'c.GO_id', 'c.pathway');
	$sql = "SELECT COUNT(DISTINCT c.gene_id) AS count FROM go_information a, enzyme_information b, interpro c WHERE a.gene_id=c.gene_id AND b.gene_id=c.gene_id AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($en_title, $kw_enzyme_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];	
}
if ( $type == "idgoenin" ) {
	$id_title = array('a.name');
	$desc_title = array('a.description');
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$go_title = array('b.go_id', 'b.go_name', 'd.GO_id');
	$en_title = array('c.enzyme_code', 'c.enzyme_name');
	$in_title = array('d.signature_accession', 'd.signature_description', 'd.IPR_code', 'd.IPR_name', 'd.GO_id', 'd.pathway');
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($en_title, $kw_enzyme_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list);
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}

// jqGrid pages default
$page = $_GET['page']; // get the requested page 
$limit = $_GET['rows']; // get how many rows we want to have into the grid 
$sidx = $_GET['sidx']; // get index row - i.e. user click to sort 
$sord = $_GET['sord']; // get the direction 
if(!$sidx) {
	$sidx =1;
}

// jqGrid 分頁
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

if ( $type == "ge" ) {
	$sql = "SELECT DISTINCT a.name AS gid FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".SQLMutiKey($gene_title, $kw_gene_list);
	if ( $keyword_id."xx" != "xx" ) {
		$sql .= " AND ".SQLMutiKey($geneid_title, $kw_id_list);
	}
	if ( $keyword_desc."xx" != "xx" ) {
		$sql .= " AND ".SQLMutiKey($genede_title, $kw_desc_list);
	}
	if ( $keyword_go."xx" != "xx" ) {
		$sql .= " AND ".SQLMutiKey($genego_title, $kw_go_list);
	}
	if ( $keyword_enzyme."xx" != "xx" ) {
		$sql .= " AND ".SQLMutiKey($geneen_title, $kw_enzyme_list);
	}
	if ( $keyword_interpro."xx" != "xx" ) {
		$sql .= " AND ".SQLMutiKey($genein_title, $kw_interpro_list);
	}
	$sql .= " LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gid'];
		$sdb = new DB;
		$ssql = "SELECT name, description FROM gene_features WHERE name='$gid'";
		$sdb->query($ssql);
		$srow = $sdb->fetch_assoc();
		$geneidh = MutiHL($gid, $kw_gene_list);
		if ( $keyword_id."xx" != "xx" ) { $geneidh = MutiHL($geneidh, $kw_id_list); }
		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$geneidh."</a>";
		$genedesc = MutiHL($srow['description'], $kw_gene_list);
		if ( $keyword_desc."xx" != "xx" ) { $genedesc = MutiHL($genedesc, $kw_desc_list); }
		
		$genegoa_title = array('go_id', 'go_name');
		if ( $keyword_go."xx" == "xx" ) {
			$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND ".SQLMutiKey($genegoa_title, $kw_gene_list, 'OR');
		} else {
			$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (".SQLMutiKey($genegoa_title, $kw_gene_list, 'OR')." OR ".SQLMutiKey($genegoa_title, $kw_go_list).")";
		}
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$goidh = MutiHL($srow['go_id'], $kw_gene_list);
			$gonameh = MutiHL($srow['go_name'], $kw_gene_list);
			if ( $keyword_go."xx" != "xx" ) {
				$goidh = MutiHL($goidh, $kw_go_list);
				$gonameh = MutiHL($gonameh, $kw_go_list);
			}
			$GO_term .= "<p>[".$goidh."] ".$gonameh."</p>";
		}

		$geneena_title = array('enzyme_code', 'enzyme_name');
		if ( $keyword_enzyme."xx" == "xx" ) {
			$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND ".SQLMutiKey($geneena_title, $kw_gene_list, 'OR');
		} else {
			$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (".SQLMutiKey($geneena_title, $kw_gene_list, 'OR')." OR ".SQLMutiKey($geneena_title, $kw_enzyme_list).")";
		}
		$sdb->query($ssql);
		$enzyme_info = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$encoh = MutiHL($srow['enzyme_code'], $kw_gene_list);
			$ennah = MutiHL($srow['enzyme_name'], $kw_gene_list);
			if ( $keyword_enzyme."xx" != "xx" ) {
				$encoh = MutiHL($encoh, $kw_enzyme_list);
				$ennah = MutiHL($ennah, $kw_enzyme_list);
			}
			$enzyme_info .= "<p>[".$encoh."] ".$ennah."</p>";
		}

		$geneina_title = array('signature_accession', 'signature_description');
		if ( $keyword_interpro."xx" == "xx" ) {
			$ssql = "SELECT signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($geneina_title, $kw_gene_list, 'OR')." GROUP BY signature_accession";
		} else {
			$ssql = "SELECT signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($geneina_title, $kw_gene_list, 'OR')." OR ".SQLMutiKey($geneina_title, $kw_interpro_list, 'OR').") GROUP BY signature_accession";
		}
		$sdb->query($ssql);
		$interpro_info = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$sah = MutiHL($srow['signature_accession'], $kw_gene_list);
			$sdh = MutiHL($srow['signature_description'], $kw_gene_list);
			if ( $keyword_interpro."xx" != "xx" ) {
				$sah = MutiHL($sah, $kw_interpro_list);
				$sdh = MutiHL($sdh, $kw_interpro_list);
			}
			$interpro_info .= "<p>[ ".$sah." ] ".$sdh."</p>";
		}
		$geneinb_title = array('IPR_code', 'IPR_name');
		if ( $keyword_interpro."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($geneinb_title, $kw_gene_list, 'OR')." GROUP BY IPR_code";
		} else {
			$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($geneinb_title, $kw_gene_list, 'OR')." OR ".SQLMutiKey($geneinb_title, $kw_interpro_list, 'OR').") GROUP BY IPR_code";
		}
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$ipridh = MutiHL($srow['IPR_code'], $kw_gene_list);
			$iprnah = MutiHL($srow['IPR_name'], $kw_gene_list);
			if ( $keyword_interpro."xx" != "xx" ) {
				$ipridh = MutiHL($ipridh, $kw_interpro_list);
				$iprnah = MutiHL($iprnah, $kw_interpro_list);
			}
			$interpro_info .= "<p>[ ".$ipridh." ] ".$iprnah."</p>";
		}
		$geneinc_title = array('GO_id');
		if ( $keyword_interpro."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($geneinc_title, $kw_gene_list, 'OR');
		} else {
			$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($geneinc_title, $kw_gene_list, 'OR')." OR ".SQLMutiKey($geneinc_title, $kw_interpro_list);
		}
		if ( $keyword_go."xx" != "xx" ) {
			$ssql .= " OR ".SQLMutiKey($geneinc_title, $kw_go_list, 'OR');
		}
		$ssql .= ") GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$ipridh = MutiHL($srow['IPR_code'], $kw_gene_list);
			$ingoh = MutiHL($srow['GO_id'], $kw_gene_list);
			if ( $keyword_interpro."xx" != "xx" ) { 
				$ipridh = MutiHL($ipridh, $kw_interpro_list);
				$ingoh = MutiHL($ingoh, $kw_interpro_list); 
			}
			if ( $keyword_go."xx" != "xx" ) { $ingoh = MutiHL($ingoh, $kw_go_list); }
			$interpro_info .= "<p>[ ".$ipridh." ] ".$ingoh."</p>";
		}
		$geneind_title = array('pathway');
		if ( $keyword_interpro."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($geneind_title, $kw_gene_list, 'OR')." GROUP BY pathway";
		} else {
			$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($geneind_title, $kw_gene_list, 'OR')." OR ".SQLMutiKey($geneind_title, $kw_interpro_list, 'OR').") GROUP BY pathway";
		}
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$ipridh = MutiHL($srow['IPR_code'], $kw_gene_list);
			$pah = MutiHL($srow['pathway'], $kw_gene_list);
			if ( $keyword_interpro."xx" != "xx" ) { 
				$ipridh = MutiHL($ipridh, $kw_interpro_list);
				$pah = MutiHL($pah, $kw_interpro_list); 
			}
			$interpro_info .= "<p>[ ".$ipridh." ] ".$pah."</p>";
		}

		if ( $GO_term."xx" == "xx" ) { $GO_term = "Not Found."; }
		if ( $enzyme_info."xx" == "xx" ) { $enzyme_info = "Not Found."; }
		if ( $interpro_info."xx" == "xx" ) { $interpro_info = "Not Found."; }



		$responce->rows[$i]['id'] = $row['name'];
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedesc,
			$GO_term,
			$enzyme_info,
			$interpro_info
		);
		$i++;
	}
}
if ( $type == "id" ) {
	if ( $kw_id_list[0]."xx" != "xx" && $kw_desc_list[0]."xx" == "xx" ) {
		$sql = "SELECT * FROM gene_features WHERE type='gene' AND ".SQLMutiKey($id_title, $kw_id_list)." GROUP BY name LIMIT $start, $limit";
	} else if ( $kw_desc_list[0]."xx" != "xx" && $kw_id_list[0]."xx" == "xx" ) {
		$sql = "SELECT * FROM gene_features WHERE type='gene' AND ".SQLMutiKey($desc_title, $kw_desc_list)." GROUP BY name LIMIT $start, $limit";
	} else {
		$sql = "SELECT * FROM gene_features WHERE type='gene' AND ".SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list)." GROUP BY name LIMIT $start, $limit";
	}
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$geneid = "<a href='genepage.php?gid=".$row['name']."' target='_blank'>".MutiHL($row['name'], $kw_id_list)."</a>";
		$genedescription = MutiHL($row['description'], $kw_desc_list);

		$genelocation = $row['location_scaffold'].": ".$row['location_start']."...".$row['location_end'];
		$responce->rows[$i]['id'] = $row['name'];
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$genelocation
		);
		$i++;
	}
}
if ( $type == "go" ) {
	$sql = "SELECT DISTINCT b.gene_id FROM go_information a, interpro b WHERE a.gene_id=b.gene_id AND ".SQLMutiKey($go_title, $kw_go_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		$GO_term = "";
		$sdb = new DB;
		$gos_title = array('go_id', 'go_name');
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND ".SQLMutiKey($gos_title, $kw_go_list)." GROUP BY go_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>Blast2GO: [".MutiHL($srow['go_id'], $kw_go_list)."] ".MutiHL($srow['go_name'], $kw_go_list)."</p>";
		}
		$gosi_title = array('GO_id');
		$ssql = "SELECT GO_id FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($gosi_title, $kw_go_list, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>InterPro: ".MutiHL($srow['GO_id'], $kw_go_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$GO_term
		);
		$i++;
	}
}
if ( $type == "en" ) {
	$sql = "SELECT gene_id FROM enzyme_information WHERE ".SQLMutiKey($enzyme_title, $kw_enzyme_list)." GROUP BY gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		$sdb = new DB;
		$ssql = "SELECT * FROM enzyme_information WHERE gene_id='$gid' AND ".SQLMutiKey($enzyme_title, $kw_enzyme_list);
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".MutiHL($srow['enzyme_code'], $kw_enzyme_list)."] ".MutiHL($srow['enzyme_name'], $kw_enzyme_list)."</p>";
		}
		$geneid = "<a href='genepage.php?gid=".$row['gene_id']."' target='_blank'>".$row['gene_id']."</a>";
		$responce->rows[$i]['id'] = $row['gene_id'];
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$enzyme
		);
		$i++;
	}
}
if ( $type == "in" ) {
	$sql = "SELECT gene_id FROM interpro WHERE ".SQLMutiKey($interpro_title, $kw_interpro_list)." GROUP BY gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		$sdb = new DB;
		$ipa_title = array('signature_accession', 'signature_description');
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipa_title, $kw_interpro_list, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		$analysis = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$analysis .= "<p>".$srow['analysis']." [ ".MutiHL($srow['signature_accession'], $kw_interpro_list)." ] ".MutiHL($srow['signature_description'], $kw_interpro_list, 'OR')."</p>";
		}
		$IPR = "";
		$ipb_title = array('IPR_code', 'IPR_name');
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipb_title, $kw_interpro_list, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".MutiHL($srow['IPR_code'], $kw_interpro_list)." ] ".MutiHL($srow['IPR_name'], $kw_interpro_list, 'OR')."</p>";
		}
		$ipc_title = array('GO_id');
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipc_title, $kw_interpro_list, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['GO_id'], $kw_interpro_list)."</p>";
		}
		$ipd_title = array('pathway');
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipd_title, $kw_interpro_list, 'OR')." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['pathway'], $kw_interpro_list)."</p>";
		}

		if ( $analysis."xx" == "xx" ) { $analysis = "Not Found."; }
		if ( $IPR."xx" == "xx" ) { $IPR = "Not Found."; }

		$geneid = "<a href='genepage.php?gid=".$row['gene_id']."' target='_blank'>".$row['gene_id']."</a>";
		$responce->rows[$i]['id'] = $row['gene_id'];
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$analysis,
			$IPR
		);
		$i++;
	}
}
if ( $type == "idgo" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, go_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($go_title, $kw_go_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$goa = array('go_id', 'go_name');
	$ingo = array('GO_id');
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		$sdb = new DB;
		$ssql = "SELECT * FROM go_information WHERE gene_id='$gid' AND ".SQLMutiKey($goa, $kw_go_list, 'OR')." GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>Blast2GO: [".MutiHL($srow['go_id'], $kw_go_list)."] ".MutiHL($srow['go_name'], $kw_go_list)."</p>";
		}
		$ssql = "SELECT GO_id FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ingo, $kw_go_list, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>InterPro: ".MutiHL($srow['GO_id'], $kw_go_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MutiHL($gid, $kw_id_list)."</a>";
		$genedescription = MutiHL($row['description'], $kw_desc_list);
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$GO_term
		);
		$i++;
	}
}
if ( $type == "iden" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, enzyme_information b WHERE a.type='gene' AND a.name=b.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($enzyme_title, $kw_enzyme_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		$sdb = new DB;
		$ssql = "SELECT * FROM enzyme_information b WHERE b.gene_id='$gid' AND ".SQLMutiKey($enzyme_title, $kw_enzyme_list);
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".MutiHL($srow['enzyme_code'], $kw_enzyme_list)."] ".MutiHL($srow['enzyme_name'], $kw_enzyme_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MutiHL($gid, $kw_id_list)."</a>";
		$genedescription = MutiHL($row['description'], $kw_desc_list);
		$responce->rows[$i]['id'] = $row['name'];
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$enzyme
		);
		$i++;
	}
}
if ( $type == "idin" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, interpro b WHERE a.type='gene' AND a.name=b.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($ip_title, $kw_interpro_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$ipa_title = array('signature_accession', 'signature_description');
	$ipb_title = array('IPR_code', 'IPR_name');
	$ipc_title = array('GO_id');
	$ipd_title = array('pathway');

	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		$sdb = new DB;
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipa_title, $kw_interpro_list, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		$analysis = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$analysis .= "<p>".$srow['analysis']." [ ".MutiHL($srow['signature_accession'], $kw_interpro_list)." ] ".MutiHL($srow['signature_description'], $kw_interpro_list)."</p>";
		}
		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipb_title, $kw_interpro_list, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".MutiHL($srow['IPR_code'], $kw_interpro_list)." ] ".MutiHL($srow['IPR_name'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipc_title, $kw_interpro_list, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['GO_id'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipd_title, $kw_interpro_list, 'OR')." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['pathway'], $kw_interpro_list)."</p>";
		}

		if ( $analysis."xx" == "xx" ) { $analysis = "Not Found."; }
		if ( $IPR."xx" == "xx" ) { $IPR = "Not Found."; }

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MutiHL($gid, $kw_id_list)."</a>";
		$genedescription = MutiHL($row['description'], $kw_desc_list);
		$responce->rows[$i]['id'] = $row['gene_id'];
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$analysis,
			$IPR
		);
		$i++;
	}
}
if ( $type == "goen" ) {
	$sql = "SELECT DISTINCT b.gene_id FROM go_information a, interpro b, enzyme_information c WHERE a.gene_id=b.gene_id AND b.gene_id=c.gene_id AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($en_title, $kw_enzyme_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$goa_title = array('go_id', 'go_name');
	$gob_title = array('GO_id');
	$ena_title = array('enzyme_code', 'enzyme_name');
	while ( $row = $db->fetch_assoc() ){
		$gid = $row['gene_id'];

		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information a WHERE gene_id='$gid' AND ".SQLMutiKey($goa_title, $kw_go_list, 'OR')." GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>Blast2GO: [".MutiHL($srow['go_id'], $kw_go_list)."] ".MutiHL($srow['go_name'], $kw_go_list)."</p>";
		}
		$ssql = "SELECT GO_id FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($gob_title, $kw_go_list, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>InterPro: ".MutiHL($srow['GO_id'], $kw_go_list)."</p>";
		}
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND ".SQLMutiKey($ena_title, $kw_enzyme_list)." GROUP BY enzyme_code";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".MutiHL($srow['enzyme_code'], $kw_enzyme_list)."] ".MutiHL($srow['enzyme_name'], $kw_enzyme_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$GO_term,
			$enzyme
		);
		$i++;
	}
}
if ( $type == "goin" ) {
	$sql = "SELECT DISTINCT b.gene_id FROM go_information a, interpro b WHERE a.gene_id=b.gene_id AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$goa_title = array('go_id', 'go_name');
	$ipa_title = array('signature_accession', 'signature_description');
	$ipb_title = array('IPR_code', 'IPR_name');
	$ipc_title = array('GO_id');
	$ipd_title = array('pathway');
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];

		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND ".SQLMutiKey($goa_title, $kw_go_list)." GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".MutiHL($srow['go_id'], $kw_go_list)."] ".MutiHL($srow['go_name'], $kw_go_list)."</p>";
		}
		
		$interpro = "";
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipa_title, $kw_interpro_list, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>".$srow['analysis']." [ ".MutiHL($srow['signature_accession'], $kw_interpro_list)." ] ".MutiHL($srow['signature_description'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipb_title, $kw_interpro_list, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".MutiHL($srow['IPR_code'], $kw_interpro_list)." ] ".MutiHL($srow['IPR_name'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($ipc_title, $kw_interpro_list, 'OR')." OR ".SQLMutiKey($ipc_title, $kw_go_list, 'OR').") GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$goidh = MutiHL($srow['GO_id'], $kw_interpro_list);
			$goidh = MutiHL($goidh, $kw_go_list);
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".$goidh."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipd_title, $kw_interpro_list)." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['pathway'], $kw_interpro_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$GO_term,
			$interpro
		);
		$i++;
	}
}
if ( $type == "enin" ) {
	$sql = "SELECT DISTINCT b.gene_id FROM enzyme_information a, interpro b WHERE a.gene_id=b.gene_id AND ".SQLMutiKey($en_title, $kw_enzyme_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$ena_title = array('enzyme_code', 'enzyme_name');
	$ipa_title = array('signature_accession', 'signature_description');
	$ipb_title = array('IPR_code', 'IPR_name');
	$ipc_title = array('GO_id');
	$ipd_title = array('pathway');
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];

		$sdb = new DB;
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND ".SQLMutiKey($ena_title, $kw_enzyme_list)." GROUP BY enzyme_code";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".MutiHL($srow['enzyme_code'], $kw_enzyme_list)."] ".MutiHL($srow['enzyme_name'], $kw_enzyme_list)."</p>";
		}
		$interpro = "";
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipa_title, $kw_interpro_list, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>".$srow['analysis']." [ ".MutiHL($srow['signature_accession'], $kw_interpro_list)." ] ".MutiHL($srow['signature_description'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipb_title, $kw_interpro_list, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".MutiHL($srow['IPR_code'], $kw_interpro_list)." ] ".MutiHL($srow['IPR_name'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipc_title, $kw_interpro_list, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['GO_id'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipd_title, $kw_interpro_list)." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['pathway'], $kw_interpro_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$enzyme,
			$interpro
		);
		$i++;
	}
}
if ( $type == "idgoen" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, go_information b, interpro c, enzyme_information d WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($en_title, $kw_enzyme_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$goa_title = array('go_id', 'go_name');
	$gob_title = array('GO_id');
	$ena_title = array('enzyme_code', 'enzyme_name');
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];

		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND ".SQLMutiKey($goa_title, $kw_go_list, 'OR')." GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>Blast2GO: [".MutiHL($srow['go_id'], $kw_go_list)."] ".MutiHL($srow['go_name'], $kw_go_list)."</p>";
		}
		$ssql = "SELECT GO_id FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($gob_title, $kw_go_list, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>InterPro: ".MutiHL($srow['GO_id'], $kw_go_list)."</p>";
		}
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND ".SQLMutiKey($ena_title, $kw_enzyme_list)." GROUP BY enzyme_code";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".MutiHL($srow['enzyme_code'], $kw_enzyme_list)."] ".MutiHL($srow['enzyme_name'], $kw_enzyme_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MutiHL($gid, $kw_id_list)."</a>";
		$genedescription = MutiHL($row['description'], $kw_desc_list);
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$GO_term,
			$enzyme
		);
		$i++;
	}
}
if ( $type == "idgoin" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, go_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$goa_title = array('go_id', 'go_name');
	$ipa_title = array('signature_accession', 'signature_description');
	$ipb_title = array('IPR_code', 'IPR_name');
	$ipc_title = array('GO_id');
	$ipd_title = array('pathway');
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];

		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND ".SQLMutiKey($goa_title, $kw_go_list)." GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".MutiHL($srow['go_id'], $kw_go_list)."] ".MutiHL($srow['go_name'], $kw_go_list)."</p>";
		}

		$interpro = "";
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipa_title, $kw_interpro_list, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>".$srow['analysis']." [ ".MutiHL($srow['signature_accession'], $kw_interpro_list)." ] ".MutiHL($srow['signature_description'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipb_title, $kw_interpro_list, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".MutiHL($srow['IPR_code'], $kw_interpro_list)." ] ".MutiHL($srow['IPR_name'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($ipc_title, $kw_interpro_list, 'OR')." OR ".SQLMutiKey($ipc_title, $kw_go_list, 'OR').") GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$goidh = MutiHL($srow['GO_id'], $kw_interpro_list);
			$goidh = MutiHL($goidh, $kw_go_list);
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".$goidh."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipd_title, $kw_interpro_list)." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['pathway'], $kw_interpro_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MutiHL($gid, $kw_id_list)."</a>";
		$genedescription = MutiHL($row['description'], $kw_desc_list);
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$GO_term,
			$interpro
		);
		$i++;
	}
}
if ( $type == "idenin" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, enzyme_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($en_title, $kw_enzyme_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$ena_title = array('enzyme_code', 'enzyme_name');
	$ipa_title = array('signature_accession', 'signature_description');
	$ipb_title = array('IPR_code', 'IPR_name');
	$ipc_title = array('GO_id');
	$ipd_title = array('pathway');
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];

		$sdb = new DB;
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND ".SQLMutiKey($ena_title, $kw_enzyme_list)." GROUP BY enzyme_code";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".MutiHL($srow['enzyme_code'], $kw_enzyme_list)."] ".MutiHL($srow['enzyme_name'], $kw_enzyme_list)."</p>";
		}

		$interpro = "";
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipa_title, $kw_interpro_list, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>".$srow['analysis']." [ ".MutiHL($srow['signature_accession'], $kw_interpro_list)." ] ".MutiHL($srow['signature_description'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipb_title, $kw_interpro_list, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".MutiHL($srow['IPR_code'], $kw_interpro_list)." ] ".MutiHL($srow['IPR_name'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipc_title, $kw_interpro_list, 'OR')." GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['GO_id'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipd_title, $kw_interpro_list)." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['pathway'], $kw_interpro_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MutiHL($gid, $kw_id_list)."</a>";
		$genedescription = MutiHL($row['description'], $kw_desc_list);
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$enzyme,
			$interpro
		);
		$i++;
	}
}
if ( $type == "goenin" ) {
	$sql = "SELECT DISTINCT c.gene_id FROM go_information a, enzyme_information b, interpro c WHERE a.gene_id=c.gene_id AND b.gene_id=c.gene_id AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($en_title, $kw_enzyme_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$goa_title = array('go_id', 'go_name');
	$ena_title = array('enzyme_code', 'enzyme_name');
	$ipa_title = array('signature_accession', 'signature_description');
	$ipb_title = array('IPR_code', 'IPR_name');
	$ipc_title = array('GO_id');
	$ipd_title = array('pathway');
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		$sdb = new DB;

		$GO_term = "";
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND ".SQLMutiKey($goa_title, $kw_go_list)." GROUP BY go_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".MutiHL($srow['go_id'], $kw_go_list)."] ".MutiHL($srow['go_name'], $kw_go_list)."</p>";
		}

		$enzyme = "";
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND ".SQLMutiKey($ena_title, $kw_enzyme_list)." GROUP BY enzyme_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".MutiHL($srow['enzyme_code'], $kw_enzyme_list)."] ".MutiHL($srow['enzyme_name'], $kw_enzyme_list)."</p>";
		}

		$interpro = "";
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipa_title, $kw_interpro_list, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>".$srow['analysis']." [ ".MutiHL($srow['signature_accession'], $kw_interpro_list)." ] ".MutiHL($srow['signature_description'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipb_title, $kw_interpro_list, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".MutiHL($srow['IPR_code'], $kw_interpro_list)." ] ".MutiHL($srow['IPR_name'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($ipc_title, $kw_interpro_list, 'OR')." OR ".SQLMutiKey($ipc_title, $kw_go_list, 'OR').") GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$goidh = MutiHL($srow['GO_id'], $kw_interpro_list);
			$goidh = MutiHL($goidh, $kw_go_list);
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".$goidh."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipd_title, $kw_interpro_list)." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['pathway'], $kw_interpro_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$GO_term,
			$enzyme,
			$interpro
		);
		$i++;
	}
}
if ( $type == "idgoenin" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list);
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = SQLMutiKey($desc_title, $kw_desc_list);
	} else {
		$gene_fliter = SQLMutiKey($id_title, $kw_id_list)." AND ".SQLMutiKey($desc_title, $kw_desc_list);
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".$gene_fliter." AND ".SQLMutiKey($go_title, $kw_go_list)." AND ".SQLMutiKey($en_title, $kw_enzyme_list)." AND ".SQLMutiKey($in_title, $kw_interpro_list)." LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	$goa_title = array('go_id', 'go_name');
	$ena_title = array('enzyme_code', 'enzyme_name');
	$ipa_title = array('signature_accession', 'signature_description');
	$ipb_title = array('IPR_code', 'IPR_name');
	$ipc_title = array('GO_id');
	$ipd_title = array('pathway');
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		$sdb = new DB;

		$GO_term = "";
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND ".SQLMutiKey($goa_title, $kw_go_list)." GROUP BY go_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".MutiHL($srow['go_id'], $kw_go_list)."] ".MutiHL($srow['go_name'], $kw_go_list)."</p>";
		}

		$enzyme = "";
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND ".SQLMutiKey($ena_title, $kw_enzyme_list)." GROUP BY enzyme_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".MutiHL($srow['enzyme_code'], $kw_enzyme_list)."] ".MutiHL($srow['enzyme_name'], $kw_enzyme_list)."</p>";
		}

		$interpro = "";
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipa_title, $kw_interpro_list, 'OR')." GROUP BY signature_accession";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>".$srow['analysis']." [ ".MutiHL($srow['signature_accession'], $kw_interpro_list)." ] ".MutiHL($srow['signature_description'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipb_title, $kw_interpro_list, 'OR')." GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".MutiHL($srow['IPR_code'], $kw_interpro_list)." ] ".MutiHL($srow['IPR_name'], $kw_interpro_list)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (".SQLMutiKey($ipc_title, $kw_interpro_list, 'OR')." OR ".SQLMutiKey($ipc_title, $kw_go_list, 'OR').") GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$goidh = MutiHL($srow['GO_id'], $kw_interpro_list);
			$goidh = MutiHL($goidh, $kw_go_list);
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".$goidh."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND ".SQLMutiKey($ipd_title, $kw_interpro_list)." GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro .= "<p>[ ".$srow['IPR_code']." ] ".MutiHL($srow['pathway'], $kw_interpro_list)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".MutiHL($gid, $kw_id_list)."</a>";
		$genedescription = MutiHL($row['description'], $kw_desc_list);
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$GO_term,
			$enzyme,
			$interpro
		);
		$i++;		
	}
}

echo json_encode($responce);
?>
