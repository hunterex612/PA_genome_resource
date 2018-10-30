<?php
// Orchidstra3 Website dev 1.1
// Auther: Jay Yu
// Update: 2018/07/18
// Note: Add function HighLight

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
$db = new DB;
if ( $type == "ge" ) {
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND (a.name LIKE '%$keyword_gene%' OR a.description LIKE '%$keyword_gene%' OR b.go_id LIKE '%$keyword_gene%' OR b.go_name LIKE '%$keyword_gene%' OR c.enzyme_code LIKE '%$keyword_gene%' OR c.enzyme_name LIKE '%$keyword_gene%' OR d.signature_description LIKE '%$keyword_gene%' OR d.signature_accession LIKE '%$keyword_gene%' OR d.IPR_code LIKE '%$keyword_gene%' OR d.IPR_name LIKE '%$keyword_gene%' OR d.GO_id LIKE '%$keyword_gene%' OR d.pathway LIKE '%$keyword_gene%')";
	if ( $keyword_id."xx" != "xx" ) {
		$sql .= " AND a.name LIKE '%$keyword_id%' ";
	}
	if ( $keyword_desc."xx" != "xx" ) {
		$sql .= " AND a.description LIKE '%$keyword_desc%' ";
	}
	if ( $keyword_go."xx" != "xx" ) {
		$sql .= " AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR d.GO_id LIKE '%$keyword_go%') ";
	}
	if ( $keyword_enzyme."xx" != "xx" ) {
		$sql .= " AND (c.enzyme_code LIKE '%$keyword_enzyme%' OR c.enzyme_name LIKE '%$keyword_enzyme%')";
	}
	if ( $keyword_interpro."xx" != "xx" ) {
		$sql .= " AND (d.signature_description LIKE '%$keyword_interpro%' OR d.signature_accession LIKE '%$keyword_interpro%' OR d.IPR_code LIKE '%$keyword_interpro%' OR d.IPR_name LIKE '%$keyword_interpro%' OR d.GO_id LIKE '%$keyword_interpro%' OR d.pathway LIKE '%$keyword_interpro%')";
	}
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "id" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$sql = "SELECT COUNT(*) AS count FROM gene_features WHERE type='gene' AND name LIKE '%$keyword_id%'";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$sql = "SELECT COUNT(*) AS count FROM gene_features WHERE type='gene' AND description LIKE '%$keyword_desc%'";
	} else {
		$sql = "SELECT COUNT(*) AS count FROM gene_features WHERE type='gene' AND name LIKE '%$keyword_id%' AND description LIKE '%$keyword_desc%'";
	}
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "go" ) {
	$sql = "SELECT COUNT(DISTINCT b.gene_id) AS count FROM go_information a, interpro b WHERE (a.go_id LIKE '%$keyword_go%' OR a.go_name LIKE '%$keyword_go%' OR b.GO_id LIKE '%$keyword_go%') AND a.gene_id=b.gene_id";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "en" ) {
	$sql = "SELECT COUNT(DISTINCT gene_id) AS count FROM enzyme_information WHERE enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%'";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "in" ) {
	$sql = "SELECT COUNT(DISTINCT gene_id) AS count FROM interpro WHERE signature_accession LIKE '%$keyword_interpro%' OR signature_description LIKE '%$keyword_interpro%' OR IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%' OR GO_id LIKE '%$keyword_interpro%' OR pathway LIKE '%$keyword_interpro%'";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idgo" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, interpro c WHERE a.type='gene' AND ".$gene_fliter." AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR c.GO_id LIKE '%$keyword_go%') AND a.name=b.gene_id AND a.name=c.gene_id";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "iden" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, enzyme_information b WHERE a.type='gene' AND ".$gene_fliter." AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%') AND a.name=b.gene_id";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idin" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, interpro b WHERE a.type='gene' AND a.name=b.gene_id AND ".$gene_fliter." AND (b.signature_accession LIKE '%$keyword_interpro%' OR b.signature_description LIKE '%$keyword_interpro%' OR b.IPR_code LIKE '%$keyword_interpro%' OR b.IPR_name LIKE '%$keyword_interpro%' OR b.GO_id LIKE '%$keyword_interpro%' OR b.pathway LIKE '%$keyword_interpro%')";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "goen" ) {
	$sql = "SELECT COUNT(DISTINCT b.gene_id) AS count FROM go_information a, interpro b, enzyme_information c WHERE (a.go_id LIKE '%$keyword_go%' OR a.go_name LIKE '%$keyword_go%' OR b.GO_id LIKE '%$keyword_go%') AND (c.enzyme_code LIKE '%$keyword_enzyme%' OR c.enzyme_name LIKE '%$keyword_enzyme%') AND a.gene_id=b.gene_id AND b.gene_id=c.gene_id";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "goin" ) {
	$sql = "SELECT COUNT(DISTINCT b.gene_id) AS count FROM go_information a, interpro b WHERE a.gene_id=b.gene_id AND (a.go_id LIKE '%$keyword_go%' OR a.go_name LIKE '%$keyword_go%' OR b.GO_id LIKE '%$keyword_go%') AND (b.signature_accession LIKE '%$keyword_interpro%' OR b.signature_description LIKE '%$keyword_interpro%' OR b.IPR_code LIKE '%$keyword_interpro%' OR b.IPR_name LIKE '%$keyword_interpro%' OR b.GO_id LIKE '%$keyword_interpro%' OR b.pathway LIKE '%$keyword_interpro%')";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "enin" ) {
	$sql = "SELECT COUNT(DISTINCT b.gene_id) AS count FROM enzyme_information a, interpro b WHERE a.gene_id=b.gene_id AND (a.enzyme_code LIKE '%$keyword_enzyme%' OR a.enzyme_name LIKE '%$keyword_enzyme%') AND (b.signature_accession LIKE '%$keyword_interpro%' OR b.signature_description LIKE '%$keyword_interpro%' OR b.IPR_code LIKE '%$keyword_interpro%' OR b.IPR_name LIKE '%$keyword_interpro%' OR b.GO_id LIKE '%$keyword_interpro%' OR b.pathway LIKE '%$keyword_interpro%')";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idgoen" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, interpro c, enzyme_information d WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".$gene_fliter." AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR c.GO_id LIKE '%$keyword_go%') AND (d.enzyme_code LIKE '%$keyword_enzyme%' OR d.enzyme_name LIKE '%$keyword_enzyme%')";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idgoin" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR c.GO_id LIKE '%$keyword_go%') AND (c.signature_accession LIKE '%$keyword_interpro%' OR c.signature_description LIKE '%$keyword_interpro%' OR c.IPR_code LIKE '%$keyword_interpro%' OR c.IPR_name LIKE '%$keyword_interpro%' OR c.GO_id LIKE '%$keyword_interpro%' OR c.pathway LIKE '%$keyword_interpro%')";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "idenin" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, enzyme_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND (b.enzyme_code LIKE '%$keyword_enzyme%' OR b.enzyme_name LIKE '%$keyword_enzyme%') AND (c.signature_accession LIKE '%$keyword_interpro%' OR c.signature_description LIKE '%$keyword_interpro%' OR c.IPR_code LIKE '%$keyword_interpro%' OR c.IPR_name LIKE '%$keyword_interpro%' OR c.GO_id LIKE '%$keyword_interpro%' OR c.pathway LIKE '%$keyword_interpro%')";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];
}
if ( $type == "goenin" ) {
	$sql = "SELECT COUNT(DISTINCT c.gene_id) AS count FROM go_information a, enzyme_information b, interpro c WHERE a.gene_id=c.gene_id AND b.gene_id=c.gene_id AND (a.go_id LIKE '%$keyword_go%' OR a.go_name LIKE '$%keyword_go%' OR c.GO_id LIKE '$%keyword_go%') AND (b.enzyme_code LIKE '%$keyword_enzyme%' OR b.enzyme_name LIKE '%$keyword_enzyme%') AND (c.signature_accession LIKE '%$keyword_interpro%' OR c.signature_description LIKE '%$keyword_interpro%' OR c.IPR_code LIKE '%$keyword_interpro%' OR c.IPR_name LIKE '%$keyword_interpro%' OR c.GO_id LIKE '%$keyword_interpro%' OR c.pathway LIKE '%$keyword_interpro%')";
	$db->query($sql);
	$row = $db->fetch_assoc();
	$count = $row['count'];	
}
if ( $type == "idgoenin" ) {
	$sql = "SELECT COUNT(DISTINCT a.name) AS count FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR d.GO_id LIKE '%$keyword_go%') AND (c.enzyme_code LIKE '%$keyword_enzyme%' OR c.enzyme_name LIKE '%$keyword_enzyme%') AND (d.signature_description LIKE '%$keyword_interpro%' OR d.signature_accession LIKE '%$keyword_interpro%' OR d.IPR_code LIKE '%$keyword_interpro%' OR d.IPR_name LIKE '%$keyword_interpro%' OR d.GO_id LIKE '%$keyword_interpro%' OR d.pathway LIKE '%$keyword_interpro%')";
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
	$sql = "SELECT DISTINCT a.name AS gid FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND (a.name LIKE '%$keyword_gene%' OR a.description LIKE '%$keyword_gene%' OR b.go_id LIKE '%$keyword_gene%' OR b.go_name LIKE '%$keyword_gene%' OR c.enzyme_code LIKE '%$keyword_gene%' OR c.enzyme_name LIKE '%$keyword_gene%' OR d.signature_description LIKE '%$keyword_gene%' OR d.signature_accession LIKE '%$keyword_gene%' OR d.IPR_code LIKE '%$keyword_gene%' OR d.IPR_name LIKE '%$keyword_gene%' OR d.GO_id LIKE '%$keyword_gene%' OR d.pathway LIKE '%$keyword_gene%')";
	if ( $keyword_id."xx" != "xx" ) {
		$sql .= " AND a.name LIKE '%$keyword_id%'";
	}
	if ( $keyword_desc."xx" != "xx" ) {
		$sql .= " AND a.description LIKE '%$keyword_desc%' ";
	}
	if ( $keyword_go."xx" != "xx" ) {
		$sql .= " AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR d.GO_id LIKE '%$keyword_go%') ";
	}
	if ( $keyword_enzyme."xx" != "xx" ) {
		$sql .= " AND (c.enzyme_code LIKE '%$keyword_enzyme%' OR c.enzyme_name LIKE '%$keyword_enzyme%')";
	}
	if ( $keyword_interpro."xx" != "xx" ) {
		$sql .= " AND (d.signature_description LIKE '%$keyword_interpro%' OR d.signature_accession LIKE '%$keyword_interpro%' OR d.IPR_code LIKE '%$keyword_interpro%' OR d.IPR_name LIKE '%$keyword_interpro%' OR d.GO_id LIKE '%$keyword_interpro%' OR d.pathway LIKE '%$keyword_interpro%')";
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
		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".HighLight($gid, $keyword_gene)."</a>";
		$genedesc = HighLight($srow['description'], $keyword_gene);
		if ( $keyword_go."xx" == "xx" ) {
			$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_gene%' OR go_name LIKE '%$keyword_gene%')";
		} else {
			$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_gene%' OR go_name LIKE '%$keyword_gene%' OR go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%')";
		}
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".HighLight($srow['go_id'], $keyword_gene)."] ".HighLight($srow['go_name'], $keyword_gene)."</p>";
		}
		if ( $keyword_enzyme."xx" == "xx" ) {
			$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_gene%' OR enzyme_name LIKE '%$keyword_gene%')";
		} else {
			$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_gene%' OR enzyme_name LIKE '%$keyword_gene%' OR enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%')";
		}
		$sdb->query($ssql);
		$enzyme_info = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme_info .= "<p>[".HighLight($srow['enzyme_code'], $keyword_gene)."] ".HighLight($srow['enzyme_name'], $keyword_gene)."</p>";
		}
		if ( $keyword_interpro."xx" == "xx" ) {
			$ssql = "SELECT signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND (signature_description LIKE '%$keyword_gene%' OR signature_accession LIKE '%$keyword_gene%') GROUP BY signature_accession";
		} else {
			$ssql = "SELECT signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND (signature_description LIKE '%$keyword_gene%' OR signature_accession LIKE '%$keyword_gene%' OR signature_description LIKE '%$keyword_interpro%' OR signature_accession LIKE '%$keyword_interpro%') GROUP BY signature_accession";
		}
		$sdb->query($ssql);
		$interpro_info = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro_info .= "<p>[ ".HighLight($srow['signature_accession'], $keyword_gene)." ] ".HighLight($srow['signature_description'], $keyword_gene)."</p>";
		}
		if ( $keyword_interpro."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_gene%' OR IPR_name LIKE '%$keyword_gene%') GROUP BY IPR_code";
		} else {
			$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_gene%' OR IPR_name LIKE '%$keyword_gene%' OR IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		}
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro_info .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_gene)." ] ".HighLight($srow['IPR_name'], $keyword_gene)."</p>";
		}
		if ( $keyword_interpro."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_gene%' GROUP BY GO_id";
		} else {
			$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_gene%' OR GO_id LIKE '%$keyword_interpro%' GROUP BY GO_id";
		}
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro_info .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_gene)." ] ".HighLight($srow['GO_id'], $keyword_gene)."</p>";
		}
		if ( $keyword_interpro."xx" == "xx" ) {
			$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_gene%' GROUP BY pathway";
		} else {
			$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_gene%' OR pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		}
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$interpro_info .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_gene)." ] ".HighLight($srow['pathway'], $keyword_gene)."</p>";
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
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$sql = "SELECT * FROM gene_features WHERE type='gene' AND name LIKE '%$keyword_id%' LIMIT $start, $limit";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$sql = "SELECT * FROM gene_features WHERE type='gene' AND description LIKE '%$keyword_desc%' LIMIT $start, $limit";
	} else {
		$sql = "SELECT * FROM gene_features WHERE type='gene' AND name LIKE '%$keyword_id%' AND description LIKE '%$keyword_desc%' LIMIT $start, $limit";
	}
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$geneid = "<a href='genepage.php?gid=".$row['name']."' target='_blank'>".HighLight($row['name'], $keyword_id)."</a>";
		$genedescription = HighLight($row['description'], $keyword_desc);
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
	$sql = "SELECT DISTINCT b.gene_id FROM go_information a, interpro b WHERE (a.go_id LIKE '%$keyword_go%' OR a.go_name LIKE '%$keyword_go%' OR b.GO_id LIKE '%$keyword_go%') AND a.gene_id=b.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		$GO_term = "";
		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%') GROUP BY go_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>Blast2GO: [".HighLight($srow['go_id'], $keyword_go)."] ".HighLight($srow['go_name'], $keyword_go)."</p>";
		}
		$ssql = "SELECT GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_go%' GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>InterPro: ".HighLight($srow['GO_id'], $keyword_go)."</p>";
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
	$sql = "SELECT DISTINCT gene_id FROM enzyme_information WHERE enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%' LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		$sdb = new DB;
		$ssql = "SELECT * FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%')";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".HighLight($srow['enzyme_code'], $keyword_enzyme)."] ".HighLight($srow['enzyme_name'], $keyword_enzyme)."</p>";
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
	$sql = "SELECT DISTINCT gene_id FROM interpro WHERE signature_accession LIKE '%$keyword_interpro%' OR signature_description LIKE '%$keyword_interpro%' OR IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%' OR GO_id LIKE '%$keyword_interpro%' OR pathway LIKE '%$keyword_interpro%' LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		$sdb = new DB;
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND (signature_description LIKE '%$keyword_interpro%' OR signature_accession LIKE '%$keyword_interpro%') GROUP BY signature_accession";
		$sdb->query($ssql);
		$analysis = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$analysis .= "<p>".$srow['analysis']." [ ".HighLight($srow['signature_accession'], $keyword_interpro)." ] ".HighLight($srow['signature_description'], $keyword_interpro)."</p>";
		}
		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_interpro)." ] ".HighLight($srow['IPR_name'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_interpro%' GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['GO_id'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['pathway'], $keyword_interpro)."</p>";
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
	$gene_fliter = "";
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, go_information b, interpro c WHERE a.type='gene' AND ".$gene_fliter." AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR c.GO_id LIKE '%$keyword_go%') AND a.name=b.gene_id AND a.name=c.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		$sdb = new DB;
		$ssql = "SELECT * FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%') GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>Blast2GO: [".HighLight($srow['go_id'], $keyword_go)."] ".HighLight($srow['go_name'], $keyword_go)."</p>";
		}
		$ssql = "SELECT GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_go%' GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>InterPro: ".HighLight($srow['GO_id'], $keyword_go)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".HighLight($gid, $keyword_id)."</a>";
		$genedescription = HighLight($row['description'], $keyword_desc);
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
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, enzyme_information b WHERE a.type='gene' AND ".$gene_fliter." AND (b.enzyme_code LIKE '%$keyword_enzyme%' OR b.enzyme_name LIKE '%$keyword_enzyme%') AND a.name=b.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		$sdb = new DB;
		$ssql = "SELECT * FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%')";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".HighLight($srow['enzyme_code'], $keyword_enzyme)."] ".HighLight($srow['enzyme_name'], $keyword_enzyme)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".HighLight($gid, $keyword_id)."</a>";
		$genedescription = HighLight($row['description'], $keyword_desc);
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
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, interpro b WHERE a.type='gene' AND a.name=b.gene_id AND ".$gene_fliter." AND (b.signature_accession LIKE '%$keyword_interpro%' OR b.signature_description LIKE '%$keyword_interpro%' OR b.IPR_code LIKE '%$keyword_interpro%' OR b.IPR_name LIKE '%$keyword_interpro%' OR b.GO_id LIKE '%$keyword_interpro%' OR b.pathway LIKE '%$keyword_interpro%') LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		$sdb = new DB;
		$ssql = "SELECT analysis, signature_accession, signature_description FROM interpro WHERE gene_id='$gid' AND (signature_description LIKE '%$keyword_interpro%' OR signature_accession LIKE '%$keyword_interpro%') GROUP BY signature_accession";
		$sdb->query($ssql);
		$analysis = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$analysis .= "<p>".$srow['analysis']." [ ".HighLight($srow['signature_accession'], $keyword_interpro)." ] ".HighLight($srow['signature_description'], $keyword_interpro)."</p>";
		}
		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_interpro)." ] ".HighLight($srow['IPR_name'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_interpro%' GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['GO_id'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['pathway'], $keyword_interpro)."</p>";
		}

		if ( $analysis."xx" == "xx" ) { $analysis = "Not Found."; }
		if ( $IPR."xx" == "xx" ) { $IPR = "Not Found."; }

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".HighLight($gid, $keyword_id)."</a>";
		$genedescription = HighLight($row['description'], $keyword_desc);
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
	$sql = "SELECT DISTINCT b.gene_id FROM go_information a, interpro b, enzyme_information c WHERE (a.go_id LIKE '%$keyword_go%' OR a.go_name LIKE '%$keyword_go%' OR b.GO_id LIKE '%$keyword_go%') AND (c.enzyme_code LIKE '%$keyword_enzyme%' OR c.enzyme_name LIKE '%$keyword_enzyme%') AND a.gene_id=b.gene_id AND b.gene_id=c.gene_id LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ){
		$gid = $row['gene_id'];

		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%') GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>Blast2GO: [".HighLight($srow['go_id'], $keyword_go)."] ".HighLight($srow['go_name'], $keyword_go)."</p>";
		}
		$ssql = "SELECT GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_go%' GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>InterPro: ".HighLight($srow['GO_id'], $keyword_go)."</p>";
		}
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%') GROUP BY enzyme_code";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".HighLight($srow['enzyme_code'], $keyword_enzyme)."] ".HighLight($srow['enzyme_name'], $keyword_enzyme)."</p>";
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
	$sql = "SELECT DISTINCT b.gene_id FROM go_information a, interpro b WHERE a.gene_id=b.gene_id AND (a.go_id LIKE '%$keyword_go%' OR a.go_name LIKE '%$keyword_go%' OR b.GO_id LIKE '%$keyword_go%') AND (b.signature_accession LIKE '%$keyword_interpro%' OR b.signature_description LIKE '%$keyword_interpro%' OR b.IPR_code LIKE '%$keyword_interpro%' OR b.IPR_name LIKE '%$keyword_interpro%' OR b.GO_id LIKE '%$keyword_interpro%' OR b.pathway LIKE '%$keyword_interpro%') LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];

		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%') GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".HighLight($srow['go_id'], $keyword_go)."] ".HighLight($srow['go_name'], $keyword_go)."</p>";
		}
		
		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_interpro)." ] ".HighLight($srow['IPR_name'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (GO_id LIKE '%$keyword_interpro%' OR GO_id LIKE '%$keyword_go%') GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GOIDH = HighLight($srow['GO_id'], $keyword_go);
			$GOIDH = HighLight($GOIDH, $keyword_interpro);
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".$GOIDH."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['pathway'], $keyword_interpro)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$GO_term,
			$IPR
		);
		$i++;
	}
}
if ( $type == "enin" ) {
	$sql = "SELECT DISTINCT b.gene_id FROM enzyme_information a, interpro b WHERE a.gene_id=b.gene_id AND (a.enzyme_code LIKE '%$keyword_enzyme%' OR a.enzyme_name LIKE '%$keyword_enzyme%') AND (b.signature_accession LIKE '%$keyword_interpro%' OR b.signature_description LIKE '%$keyword_interpro%' OR b.IPR_code LIKE '%$keyword_interpro%' OR b.IPR_name LIKE '%$keyword_interpro%' OR b.GO_id LIKE '%$keyword_interpro%' OR b.pathway LIKE '%$keyword_interpro%') LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];

		$sdb = new DB;
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%') GROUP BY enzyme_code";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".HighLight($srow['enzyme_code'], $keyword_enzyme)."] ".HighLight($srow['enzyme_name'], $keyword_enzyme)."</p>";
		}
		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_interpro)." ] ".HighLight($srow['IPR_name'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_interpro%' GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['GO_id'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['pathway'], $keyword_interpro)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$enzyme,
			$IPR
		);
		$i++;
	}
}
if ( $type == "idgoen" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, go_information b, interpro c, enzyme_information d WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND ".$gene_fliter." AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR c.GO_id LIKE '%$keyword_go%') AND (d.enzyme_code LIKE '%$keyword_enzyme%' OR d.enzyme_name LIKE '%$keyword_enzyme%') LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];

		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%') GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>Blast2GO: [".HighLight($srow['go_id'], $keyword_go)."] ".HighLight($srow['go_name'], $keyword_go)."</p>";
		}
		$ssql = "SELECT GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_go%' GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>InterPro: ".HighLight($srow['GO_id'], $keyword_go)."</p>";
		}
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%') GROUP BY enzyme_code";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".HighLight($srow['enzyme_code'], $keyword_enzyme)."] ".HighLight($srow['enzyme_name'], $keyword_enzyme)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".HighLight($gid, $keyword_id)."</a>";
		$genedescription = HighLight($row['description'], $keyword_desc);
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
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, go_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR c.GO_id LIKE '%$keyword_go%') AND (c.signature_accession LIKE '%$keyword_interpro%' OR c.signature_description LIKE '%$keyword_interpro%' OR c.IPR_code LIKE '%$keyword_interpro%' OR c.IPR_name LIKE '%$keyword_interpro%' OR c.GO_id LIKE '%$keyword_interpro%' OR c.pathway LIKE '%$keyword_interpro%') LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];

		$sdb = new DB;
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%') GROUP BY go_id";
		$sdb->query($ssql);
		$GO_term = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".HighLight($srow['go_id'], $keyword_go)."] ".HighLight($srow['go_name'], $keyword_go)."</p>";
		}

		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_interpro)." ] ".HighLight($srow['IPR_name'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (GO_id LIKE '%$keyword_interpro%' OR GO_id LIKE '%$keyword_go%') GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GOIDH = HighLight($srow['GO_id'], $keyword_go);
			$GOIDH = HighLight($GOIDH, $keyword_interpro);
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".$GOIDH."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['pathway'], $keyword_interpro)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".HighLight($gid, $keyword_id)."</a>";
		$genedescription = HighLight($row['description'], $keyword_desc);
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$GO_term,
			$IPR
		);
		$i++;
	}
}
if ( $type == "idenin" ) {
	if ( $keyword_id."xx" != "xx" && $keyword_desc."xx" == "xx" ) {
		$gene_fliter = " a.name LIKE '%$keyword_id%' ";
	} else if ( $keyword_desc."xx" != "xx" && $keyword_id."xx" == "xx" ) {
		$gene_fliter = " a.description LIKE '%$keyword_desc%' ";
	} else {
		$gene_fliter = " a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' ";
	}
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, enzyme_information b, interpro c WHERE a.type='gene' AND a.name=b.gene_id AND a.name=c.gene_id AND ".$gene_fliter." AND (b.enzyme_code LIKE '%$keyword_enzyme%' OR b.enzyme_name LIKE '%$keyword_enzyme%') AND (c.signature_accession LIKE '%$keyword_interpro%' OR c.signature_description LIKE '%$keyword_interpro%' OR c.IPR_code LIKE '%$keyword_interpro%' OR c.IPR_name LIKE '%$keyword_interpro%' OR c.GO_id LIKE '%$keyword_interpro%' OR c.pathway LIKE '%$keyword_interpro%') LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];

		$sdb = new DB;
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%') GROUP BY enzyme_code";
		$sdb->query($ssql);
		$enzyme = "";
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".HighLight($srow['enzyme_code'], $keyword_enzyme)."] ".HighLight($srow['enzyme_name'], $keyword_enzyme)."</p>";
		}

		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_interpro)." ] ".HighLight($srow['IPR_name'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND GO_id LIKE '%$keyword_interpro%' GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['GO_id'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['pathway'], $keyword_interpro)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".HighLight($gid, $keyword_id)."</a>";
		$genedescription = HighLight($row['description'], $keyword_desc);
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$enzyme,
			$IPR
		);
		$i++;
	}
}
if ( $type == "goenin" ) {
	$sql = "SELECT DISTINCT c.gene_id FROM go_information a, enzyme_information b, interpro c WHERE a.gene_id=c.gene_id AND b.gene_id=c.gene_id AND (a.go_id LIKE '%$keyword_go%' OR a.go_name LIKE '$%keyword_go%' OR c.GO_id LIKE '$%keyword_go%') AND (b.enzyme_code LIKE '%$keyword_enzyme%' OR b.enzyme_name LIKE '%$keyword_enzyme%') AND (c.signature_accession LIKE '%$keyword_interpro%' OR c.signature_description LIKE '%$keyword_interpro%' OR c.IPR_code LIKE '%$keyword_interpro%' OR c.IPR_name LIKE '%$keyword_interpro%' OR c.GO_id LIKE '%$keyword_interpro%' OR c.pathway LIKE '%$keyword_interpro%') LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['gene_id'];
		$sdb = new DB;

		$GO_term = "";
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%') GROUP BY go_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".HighLight($srow['go_id'], $keyword_go)."] ".HighLight($srow['go_name'], $keyword_go)."</p>";
		}

		$enzyme = "";
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%') GROUP BY enzyme_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".HighLight($srow['enzyme_code'], $keyword_enzyme)."] ".HighLight($srow['enzyme_name'], $keyword_enzyme)."</p>";
		}

		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_interpro)." ] ".HighLight($srow['IPR_name'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (GO_id LIKE '%$keyword_interpro%' OR GO_id LIKE '%$keyword_go%') GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GOIDH = HighLight($srow['GO_id'], $keyword_go);
			$GOIDH = HighLight($GOIDH, $keyword_interpro);
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".$GOIDH."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['pathway'], $keyword_interpro)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".$gid."</a>";
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$GO_term,
			$enzyme,
			$IPR
		);
		$i++;
	}
}
if ( $type == "idgoenin" ) {
	$sql = "SELECT DISTINCT a.name, a.description FROM gene_features a, go_information b, enzyme_information c, interpro d WHERE a.name=b.gene_id AND a.name=c.gene_id AND a.name=d.gene_id AND a.name LIKE '%$keyword_id%' AND a.description LIKE '%$keyword_desc%' AND (b.go_id LIKE '%$keyword_go%' OR b.go_name LIKE '%$keyword_go%' OR d.GO_id LIKE '%$keyword_go%') AND (c.enzyme_code LIKE '%$keyword_enzyme%' OR c.enzyme_name LIKE '%$keyword_enzyme%') AND (d.signature_description LIKE '%$keyword_interpro%' OR d.signature_accession LIKE '%$keyword_interpro%' OR d.IPR_code LIKE '%$keyword_interpro%' OR d.IPR_name LIKE '%$keyword_interpro%' OR d.GO_id LIKE '%$keyword_interpro%' OR d.pathway LIKE '%$keyword_interpro%') LIMIT $start, $limit";
	$db->query($sql);
	$i = 0;
	while ( $row = $db->fetch_assoc() ) {
		$gid = $row['name'];
		$sdb = new DB;

		$GO_term = "";
		$ssql = "SELECT go_id, go_name FROM go_information WHERE gene_id='$gid' AND (go_id LIKE '%$keyword_go%' OR go_name LIKE '%$keyword_go%') GROUP BY go_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GO_term .= "<p>[".HighLight($srow['go_id'], $keyword_go)."] ".HighLight($srow['go_name'], $keyword_go)."</p>";
		}

		$enzyme = "";
		$ssql = "SELECT enzyme_code, enzyme_name FROM enzyme_information WHERE gene_id='$gid' AND (enzyme_code LIKE '%$keyword_enzyme%' OR enzyme_name LIKE '%$keyword_enzyme%') GROUP BY enzyme_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$enzyme .= "<p>[".HighLight($srow['enzyme_code'], $keyword_enzyme)."] ".HighLight($srow['enzyme_name'], $keyword_enzyme)."</p>";
		}

		$IPR = "";
		$ssql = "SELECT IPR_code, IPR_name FROM interpro WHERE gene_id='$gid' AND (IPR_code LIKE '%$keyword_interpro%' OR IPR_name LIKE '%$keyword_interpro%') GROUP BY IPR_code";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".HighLight($srow['IPR_code'], $keyword_interpro)." ] ".HighLight($srow['IPR_name'], $keyword_interpro)."</p>";
		}
		$ssql = "SELECT IPR_code, GO_id FROM interpro WHERE gene_id='$gid' AND (GO_id LIKE '%$keyword_interpro%' OR GO_id LIKE '%$keyword_go%') GROUP BY GO_id";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$GOIDH = HighLight($srow['GO_id'], $keyword_go);
			$GOIDH = HighLight($GOIDH, $keyword_interpro);
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".$GOIDH."</p>";
		}
		$ssql = "SELECT IPR_code, pathway FROM interpro WHERE gene_id='$gid' AND pathway LIKE '%$keyword_interpro%' GROUP BY pathway";
		$sdb->query($ssql);
		while ( $srow = $sdb->fetch_assoc() ) {
			$IPR .= "<p>[ ".$srow['IPR_code']." ] ".HighLight($srow['pathway'], $keyword_interpro)."</p>";
		}

		$geneid = "<a href='genepage.php?gid=".$gid."' target='_blank'>".HighLight($gid, $keyword_id)."</a>";
		$genedescription = HighLight($row['description'], $keyword_desc);
		$responce->rows[$i]['id'] = $gid;
		$responce->rows[$i]['cell'] = array(
			$geneid,
			$genedescription,
			$GO_term,
			$enzyme,
			$IPR
		);
		$i++;		
	}
}

echo json_encode($responce);
?>