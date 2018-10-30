<?php 
// Orchidstra3 Website dev 1.0
// Auther: Jay Yu
// Update: 2018/07/11
// Note: 

$keyword_gene = $_POST['keyword_gene'];
$keyword_id = $_POST['keyword_id'];
$keyword_desc = $_POST['keyword_desc'];
$keyword_go = $_POST['keyword_go'];
$keyword_enzyme = $_POST['keyword_enzyme'];
$keyword_interpro = $_POST['keyword_interpro'];
$keyword_gene = trim($keyword_gene);
$keyword_id = trim($keyword_id);
$keyword_desc = trim($keyword_desc);
$keyword_go = trim($keyword_go);
$keyword_enzyme = trim($keyword_enzyme);
$keyword_interpro = trim($keyword_interpro);
if ( $keyword_gene.$keyword_id.$keyword_desc.$keyword_go.$keyword_enzyme.$keyword_interpro."xx" == "xx" ) {
	header("Location: http://tagrc.org/orchidstra2/genelist.php"); 
} else if ( $keyword_gene."xx" != "xx" ) {
	$type = "ge";
} else if ( $keyword_id.$keyword_desc."xx" != "xx" && $keyword_go.$keyword_enzyme.$keyword_interpro."xx" == "xx" ) {
	$type = "id";
} else if ( $keyword_go."xx" != "xx" && $keyword_id.$keyword_desc.$keyword_enzyme.$keyword_interpro."xx" == "xx" ) {
	$type = "go";
} else if ( $keyword_enzyme."xx" != "xx" && $keyword_id.$keyword_desc.$keyword_go.$keyword_interpro."xx" == "xx" ) {
	$type = "en";
} else if ( $keyword_interpro."xx" != "xx" && $keyword_id.$keyword_desc.$keyword_go.$keyword_enzyme."xx" == "xx" ) {
	$type = "in";
} else if ( $keyword_id.$keyword_desc.$keyword_go."xx" != "xx" && $keyword_enzyme.$keyword_interpro."xx" == "xx" ) {
	$type = "idgo";
} else if ( $keyword_id.$keyword_desc.$keyword_enzyme."xx" != "xx" && $keyword_go.$keyword_interpro."xx" == "xx" ) {
	$type = "iden";
} else if ( $keyword_id.$keyword_desc.$keyword_interpro."xx" != "xx" && $keyword_go.$keyword_enzyme."xx" == "xx" ) {
	$type = "idin";
} else if ( $keyword_go.$keyword_enzyme."xx" != "xx" && $keyword_id.$keyword_desc.$keyword_interpro."xx" == "xx" ) {
	$type = "goen";
} else if ( $keyword_go.$keyword_interpro."xx" != "xx" && $keyword_id.$keyword_desc.$keyword_enzyme."xx" == "xx" ) {
	$type = "goin";
} else if ( $keyword_enzyme.$keyword_interpro."xx" != "xx" && $keyword_id.$keyword_desc.$keyword_go."xx" == "xx" ) {
	$type = "enin";
} else if ( $keyword_id.$keyword_desc.$keyword_go.$keyword_enzyme."xx" != "xx" && $keyword_interpro."xx" == "xx" ) {
	$type = "idgoen";
} else if ( $keyword_id.$keyword_desc.$keyword_go.$keyword_interpro."xx" != "xx" && $keyword_enzyme."xx" == "xx" ) {
	$type = "idgoin";
} else if ( $keyword_id.$keyword_desc.$keyword_enzyme.$keyword_interpro."xx" != "xx" && $keyword_go."xx" == "xx" ) {
	$type = "idenin";
} else if ( $keyword_go.$keyword_enzyme.$keyword_interpro."xx" != "xx" && $keyword_id.$keyword_desc."xx" == "xx" ) {
	$type = "goenin";
} else {
	$type = "idgoenin";
}
?>
<!DOCTYPE html>
<html>
<meta charset="UTF-8">
	<title>Orchidstra</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<script src="functions/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="functions/jquery-ui.min.js" type="text/javascript"></script>
	<script src="functions/jquery.jqGrid.min.js" type="text/javascript"></script>
	<script src="functions/grid.locale-en.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" media="screen" href="functions/jquery-ui.min.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="functions/jquery-ui.structure.min.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="functions/jquery-ui.theme.min.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="functions/ui.jqgrid-5.2.1.css" />
	<style>
		.ui-jqgrid tr.jqgrow td {
		word-wrap: break-word; /* IE 5.5+ and CSS3 */
		white-space: pre-wrap; /* CSS3 */
		white-space: -moz-pre-wrap; /* Mozilla, since 1999 */
		white-space: -pre-wrap; /* Opera 4-6 */
		white-space: -o-pre-wrap; /* Opera 7 */
		overflow: hidden;
		height: auto;
		vertical-align: middle;
		padding-top: 3px;
		padding-bottom: 3px;
		font-size: 16px;
		}
		.highlight {
			background-color: #FFC000;
		}
	</style>
</head>
<body>
<div class="container">
	<div class="main-contain">
		<h1 align="center">Search Result</h1>
		<hr />
		<div style="margin-bottom: 16px;">
			<h2>Search Filter</h2>
		<?php 
			if ( $keyword_gene."xx" != "xx" ) { echo "<p><strong>Gene:</strong> ".$keyword_gene."</p>"; }
			if ( $keyword_id."xx" != "xx" ) { echo "<p><strong>ID:</strong> ".$keyword_id."</p>"; }
			if ( $keyword_desc."xx" != "xx" ) { echo "<p><strong>Description:</strong> ".$keyword_desc."</p>"; }
			if ( $keyword_go."xx" != "xx" ) { echo "<p><strong>GO:</strong> ".$keyword_go."</p>"; }
			if ( $keyword_enzyme."xx" != "xx" ) { echo "<p><strong>Enzyme:</strong> ".$keyword_enzyme."</p>"; }
			if ( $keyword_interpro."xx" != "xx" ) { echo "<p><strong>InterPro:</strong> ".$keyword_interpro."</p>"; }
		?>
		</div>
		<div style="margin: 0 auto; width: 100%">
			<div id="jqGridPager"></div>
			<table id="jqGrid" style="margin: 0 auto"></table>
		</div>
		<script>
			var type = "<?php echo $type; ?>";
			if ( type == "ge" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_gene=<?php echo $keyword_gene; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>&keyword_go=<?php echo $keyword_go; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'GO', 'Enzyme', 'InterPro'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 200, sortable: false },
							{ name: 'go', width: 170, sortable: false },
							{ name: 'enzyme', width: 170, sortable: false },
							{ name: 'interpro', width: 305, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "id" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'Gene Loaction'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 595, sortable: false },
							{ name: 'gene_location', width: 250, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});

			}
			if ( type == "go" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_go=<?php echo $keyword_go; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID', 'GO'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'go', width: 845, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "en" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID', 'Enzyme'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'enzyme', width: 845, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "in" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID', 'Analysis', 'IPR' ],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'analysis', width: 385, sortable: false },
							{ name: 'IPR', width: 460, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "idgo" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>&keyword_go=<?php echo $keyword_go; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'GO'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 350, sortable: false },
							{ name: 'go', width: 495, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "iden" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'Enzyme'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 400, sortable: false },
							{ name: 'enzyme', width: 445, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "idin" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'InterPro Analysis', 'InterPro IPR'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 200, sortable: false },
							{ name: 'analysis', width: 250, sortable: false },
							{ name: 'ipr', width: 395, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "goen" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_go=<?php echo $keyword_go; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID', 'GO', 'Enzyme'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'go', width: 435, sortable: false },
							{ name: 'enzyme', width: 410, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "goin" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_go=<?php echo $keyword_go; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID', 'GO', 'InterPro'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'go', width: 410, sortable: false },
							{ name: 'interpro', width: 435, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "enin" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID', 'Enzyme', 'InterPro'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'enzyme', width: 370, sortable: false },
							{ name: 'interpro', width: 475, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "idgoen" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>&keyword_go=<?php echo $keyword_go; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'GO', 'Enzyme'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 200, sortable: false },
							{ name: 'go', width: 325, sortable: false },
							{ name: 'enzyme', width: 320, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "idgoin" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>&keyword_go=<?php echo $keyword_go; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'GO', 'InterPro'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 200, sortable: false },
							{ name: 'go', width: 320, sortable: false },
							{ name: 'interpro', width: 325, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "idenin" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'Enzyme', 'InterPro'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 200, sortable: false },
							{ name: 'enzyme', width: 320, sortable: false },
							{ name: 'interpro', width: 325, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "goenin" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_go=<?php echo $keyword_go; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID', 'GO', 'Enzyme', 'InterPro'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'go', width: 280, sortable: false },
							{ name: 'enzyme', width: 280, sortable: false },
							{ name: 'interpro', width: 285, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
			if ( type == "idgoenin" ) {
				$(document).ready(function () {
					$("#jqGrid").jqGrid({
						url: "genesearch_list.php?type=<?php echo $type; ?>&keyword_id=<?php echo $keyword_id; ?>&keyword_desc=<?php echo $keyword_desc; ?>&keyword_go=<?php echo $keyword_go; ?>&keyword_enzyme=<?php echo $keyword_enzyme; ?>&keyword_interpro=<?php echo $keyword_interpro; ?>",
						mtype: "GET",
						datatype: "json",
						colNames: ['Gene ID','Gene Description', 'GO', 'Enzyme', 'InterPro'],
						colModel: [
							{ name: 'gene_id', width: 120, sortable: false },
							{ name: 'gene_description', width: 200, sortable: false },
							{ name: 'go', width: 170, sortable: false },
							{ name: 'enzyme', width: 170, sortable: false },
							{ name: 'interpro', width: 305, sortable: false }
						],
						viewrecords: true,
						width: 965,
						height: '100%',
						rowNum: 25,
						pager: "#jqGridPager"
					});
				});
			}
		</script>
	</div>
</div>
</body>
</html>