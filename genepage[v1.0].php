<?php 
// Orchidstra3 Website dev 1.0
// Auther: Jay Yu
// Update: 2018/07/11
// Note: 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Orchidstra</title>
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico">
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<style>
		.gene-info th, .gene-info td { width:33%; }
		.features { margin: 8px auto 32px; width: 600px; }
		.features td { text-align: center; }
		.td-mid { text-align: center; }
		.interpro-box { 
			border: 1px #575757 solid; 
			padding: 4px;
			margin: 5px 0;
		}
		.interpro-table th {
			text-align: right;
			background-color: #575757;
			color: #FFF;
		}
		.seq-download a {
			margin-right: 32px;
		}
		.seq-download {
			margin-bottom: 16px;
		}
	</style>
</head>
<body>
<?php 
	require_once("functions/db.php");
	if ( isset($_GET['gid']) && $_GET['gid']."xx" != "xx" ) { $gene_id = $_GET['gid']; } 
	else { header("Location: http://tagrc.org/orchidstra2/genesearch.php"); }
	if ( substr_count(strtoupper($gene_id), strtoupper('scaffold') ) !== 0 ) {
		header("Location: http://tagrc.org/orchidstra2/scfpage.php?scf=$gene_id");
	} else {
		$db = new DB;
		$db->query("SELECT * FROM gene_features WHERE name='$gene_id'");
		$row = $db->fetch_assoc();
		if ( $row['type'] == 'mRNA' ) {
			$gene_id = $row['parent'];
		}
		$db->query("SELECT * FROM gene_features WHERE name='$gene_id' and type='gene'");
		if ( $db->num_rows() == 0 ) {
			header("Location: http://tagrc.org/orchidstra2/genesearch.php");
		}
		$row = $db->fetch_assoc();
?>
<div class="container">
	<div class="main-contain">
		<div style="width: 100%; background-color: #e1f7d5; padding-bottom: 15px; margin-bottom: 10px;">
			<table border="0" cellspacing="0" width="100%">
				<tr>
					<td align="right"><img src="logo.png" style="margin-top: 10px;"></td>
					<td align="center" valign="bottom"><h1 style="font-family: Georgia, Verdana, Geneva, sans-serif; color: #000"><i>Phalaenopsis aphrodite</i> Genome Resources</h1></td>
				</tr>
			</table>
			<div style="width:100%;height:15px;background: rgb(180,221,180); /* Old browsers */
background: -moz-linear-gradient(left,  rgba(180,221,180,1) 0%, rgba(131,199,131,1) 17%, rg
ba(82,177,82,1) 33%, rgba(0,138,0,1) 67%, rgba(0,87,0,1) 83%, rgba(0,36,0,1) 100%); /* FF3.6+ */
background: -webkit-gradient(linear, left top, right top, color-stop(0%,rgba(180,221,180,1)), color-stop(17%,rgba(131,199,131,1)), color-stop(33%,rgba(82,177,82,1)), color-stop(67%,rgba(0,138,0,1)), color-stop(83%,rgba(0,87,0,1)), color-stop(100%,rgba(0,36,0,1))); /* Chrome,Safari4+ */
background: -webkit-linear-gradient(left,  rgba(180,221,180,1) 0%,rgba(131,199,131,1) 17%,rgba(82,177,82,1) 33%,rgba(0,138,0,1) 67%,rgba(0,87,0,1) 83%,rgba(0,36,0,1) 100%); /* Chrome10+,Safari5.1+ */
background: -o-linear-gradient(left,  rgba(180,221,180,1) 0%,rgba(131,199,131,1) 17%,rgba(82,177,82,1) 33%,rgba(0,138,0,1) 67%,rgba(0,87,0,1) 83%,rgba(0,36,0,1) 100%); /* Opera 11.10+ */
background: -ms-linear-gradient(left,  rgba(180,221,180,1) 0%,rgba(131,199,131,1) 17%,rgba(82,177,82,1) 33%,rgba(0,138,0,1) 67%,rgba(0,87,0,1) 83%,rgba(0,36,0,1) 100%); /* IE10+ */
background: linear-gradient(to right,  rgba(180,221,180,1) 0%,rgba(131,199,131,1) 17%,rgba(82,177,82,1) 33%,rgba(0,138,0,1) 67%,rgba(0,87,0,1) 83%,rgba(0,36,0,1) 100%); /* W3C */
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#b4ddb4', endColorstr='#002400',GradientType=1 ); /* IE6-9 */">&nbsp;</div>
		</div>
		<h1 align="center">Gene <?php echo $gene_id; ?></h1>
		<h2>Information</h2>
		<hr />
		<table border="0" class="th-left gene-info">
			<tr>
				<th colspan="2">ID</th><th>Species</th>
			</tr>
			<tr>
				<td colspan="2"><?php echo $gene_id; ?></td>
				<td><i>Phalaenopsis aphrodite</i></td>
			</tr>
			<tr>
				<th colspan="3">Description</th>
			</tr>
			<tr>
				<td colspan="3"><?php echo $row['description']; ?></td>
			</tr>
			<tr>
				<th>Location</th><th>Length</th><th>Strand</th>
			</tr>
			<tr>
				<td><?php echo "<a href='scfpage.php?scf=".$row['location_scaffold']."' target='_blank'>".$row['location_scaffold']."</a>: ".$row['location_start']."...".$row['location_end']; ?></td>
				<td>
				<?php 
					if ( (int)$row['location_end'] > (int)$row['location_start'] ) {
						echo number_format((int)$row['location_end']-(int)$row['location_start']+1);
					} else {
						echo number_format((int)$row['location_start']-(int)$row['location_end']+1);
					}
				?>
				</td>
				<td style="padding-left: 24px"><?php if ( $row['strand'] == '-' ) { echo "─"; } else { echo $row['strand']; } ?></td>
			</tr>
		</table>
		<?php 
			$db->query("SELECT * FROM GMTC WHERE pagm='$gene_id'");
			if ( $db->num_rows() > 0 ) {
		?>
		<h2 id="relative">Orchidstra 2.0 Transcriptome Shotgun Assembly Sequences</h2>
		<hr />
		<?php 
				while ( $row = $db->fetch_assoc()) {
					echo "<a href='http://orchidstra2.abrc.sinica.edu.tw/orchidstra2/orchidannopage.php?id=".$row['patc']."' target='_blank'>".$row['patc']."</a><br />";
				}
			}
		?>
		<h2 id="GO">Gene Ontology</h2>
		<hr />
		<p><strong>Source:</strong> Blast2GO</p>
		<?php 
			$db->query("SELECT * FROM go_information WHERE gene_id='$gene_id'");
			if ( $db->num_rows() > 0 ) {
		?>
		<table border="1" class="gene-info td-mid">
			<tr>
				<th>Function Type</th><th>GO ID</th><th>GO Name</th>
			</tr>
		<?php 
				while ( $row = $db->fetch_assoc() ) {
					echo "<tr><td>";
					if ( $row['function_type'] == "C" ) { echo "Cellular Component"; }
					if ( $row['function_type'] == "F" ) { echo "Molecular Function"; }
					if ( $row['function_type'] == "P" ) { echo "Biological Process"; }
					echo "</td><td>";
					echo "<a href='http://amigo.geneontology.org/amigo/term/".$row['go_id']."' target='_blank'>".$row['go_id']."</a>";
					echo "</td><td>".$row['go_name']."</td></tr>";
				}
		?>
		</table>
		<?php 
			} else { echo "None predicted."; }
		?>
		<h2 id="enzyme">Enzyme</h2>
		<hr />
		<p><strong>Source:</strong> Blast2GO</p>
		<?php 
			$db->query("SELECT * FROM enzyme_information WHERE gene_id='$gene_id'");
			if ( $db->num_rows() > 0 ) {
		?>
		<table border="1" class="td-mid">
			<tr>
				<th>Enzyme Code</th><th>Enzyme Name</th>
			</tr>
		<?php 
				while ( $row = $db->fetch_assoc() ) {
					echo "<tr><td>".$row['enzyme_code']."</td>";
					echo "<td>".$row['enzyme_name']."</td></tr>";
				}
		?>
		</table>
		<?php 
			} else { echo "None predicted."; }
		?>
		<h2>InterPro Analysis</h2>
		<?php 
			$db->query("SELECT * FROM interpro WHERE gene_id='$gene_id'");
			if ( $db->num_rows() > 0 ) {
				while ( $row = $db->fetch_assoc() ) {
		?>
		<div class="interpro-box">
			<table border="0" class="interpro-table">
				<tr>
					<th width="140px">Analysis Type</th>
					<td width="110px"><?php echo $row['analysis']; ?></td>
					<th widht="90px">Accession</th>
					<td width="90px"><?php echo $row['signature_accession']; ?></td>
					<th width="90px">Location</th>
					<td width="90px"><?php echo $row['start_location']."...".$row['stop_location']; ?></td>
					<th width="140px">Score / E-value</th>
					<td width="128px"><?php echo $row['score_evalue']; ?></td>
				</tr>
				<tr>
					<th>Description</th>
					<td colspan='7'>
					<?php 
						if ( $row['signature_description']."xx" != "xx" ) {
							echo $row['signature_description'];
						} else {
							echo "N/A";
						}
					?>
					</td>
				</tr>
				<tr>
					<th>IPR Accession</th>
					<td>
					<?php 
						if ( $row['IPR_code']."xx" != "xx" ) {
							echo $row['IPR_code'];
						} else {
							echo "N/A";
						}
					?>
					</td>
					<th>IPR Name</th>
					<td colspan='5'>
					<?php 
						if ( $row['IPR_name']."xx" != "xx" ) {
							echo $row['IPR_name'];
						} else {
							echo "N/A";
						}
					?>
					</td>
				</tr>
				<tr>
					<th>GO ID</th>
					<td colspan='7'>
					<?php 
						if ( $row['GO_id']."xx" != "xx" ) {
							echo $row['GO_id'];
						} else {
							echo "N/A";
						}
					?>
					</td>
				</tr>
				<tr>
					<th>Pathway</th>
					<td colspan='7'>
					<?php 
						if ( $row['pathway']."xx" != "xx" ) {
							echo $row['pathway'];
						} else {
							echo "N/A";
						}
					?>
					</td>
				</tr>
			</table>
		</div>
		<?php 
				}
			} else {
				echo "N/A";
			}
		?>
		<h2>Related Features</h2>
		<hr />
		<?php 
			$db->query("SELECT * FROM gene_features WHERE parent='$gene_id'");
			if ( $db->num_rows() > 0 ) {
				while ( $row = $db->fetch_assoc() ) {
		?>
			<h3 id="<?php echo $row['name']; ?>"><?php echo $row['name']; ?></h3>
			<table border="0" class="th-left">
				<tr>
					<th>ID</th><th>Type</th><th>Location</th><th>Length</th><th>Strand</th><th>Source</th>
				</tr>
				<tr>
					<td><?php echo $row['name']; ?></td>
					<td><?php echo $row['type']; ?></td>
					<td><?php echo $row['location_scaffold'].": ".$row['location_start']."...".$row['location_end']; ?></td>
					<td>
					<?php 
						if ( $row['type'] == "mRNA" ) {
							echo number_format($row['length']);
						} else {
							if ( (int)$row['location_end'] > (int)$row['location_start'] ) {
								echo number_format((int)$row['location_end']-(int)$row['location_start']+1);
							} else {
								echo number_format((int)$row['location_start']-(int)$row['location_end']+1);
							}
						}
					?>
					</td>
					<td style="padding-left: 24px"><?php if ( $row['strand'] == '-' ) { echo "─"; } else { echo $row['strand']; } ?></td>
					<td><?php echo $row['source']; ?></td>
				</tr>
			</table>
		<?php
					$parent = $row['name'];
					$sdb = new DB;
					$sdb->query("SELECT * FROM gene_features WHERE parent='$parent'");
					if ( $sdb->num_rows() > 0 ) {
		?>
			<table border="1" class="features">
				<tr>
					<th>Type</th><th>Location</th><th>Length</th><th>Strand</th><th>Source</th>
				</tr>
		<?php 
						while ( $srow = $sdb->fetch_assoc() ) {
							echo "<tr>";
							echo "<td>".$srow['type']."</td>";
							echo "<td>".$srow['location_scaffold'].": ".$srow['location_start']."...".$srow['location_end']."</td>";
							echo "<td>";
							if ( (int)$srow['location_end'] > (int)$srow['location_start'] ) {
								echo number_format((int)$srow['location_end']-(int)$srow['location_start']+1);
							} else {
								echo number_format((int)$srow['location_start']-(int)$srow['location_end']+1);
							}
							echo "</td>";
							echo "<td>";
							if ( $srow['strand'] == "-" ) {
								echo "─";
							} else {
								echo $srow['strand'];
							}
							echo "</td>";
							echo "<td>".$srow['source']."</td>";
							echo "</tr>";
						}
		?>
			</table>
		<?php 
					}
					$fdb = new DB;
					$fdb->query("SELECT * FROM mrna_sequence WHERE name='$parent'");
					if ( $fdb->num_rows() > 0 ) {
						$frow = $fdb->fetch_assoc();
						echo "<h3>mRNA Sequence</h3>";
						echo "<div class='fasta'>";
						echo "<div class='seq-download'>";
						echo "<a href='nucl_sequences/nucl_".$frow['name'].".fa' download>Download mRNA sequence</a>";
						echo "<a href='gene_wup2k_sequences/nucl_up2k_".$gene_id.".fa' download>Download gene with 2kb upstream sequence</a>";
						echo "</div>";
						echo "<p>>".$frow['title']."</p>";
						$fasta = $frow['nucl_seq'];
						$fastaA = str_split($fasta, 10);
						for ( $i=0; $i<count($fastaA); $i++ ) {
							echo $fastaA[$i];
							if ( ($i+1)%7 == 0 ) { echo "<br />"; }
							else { echo " "; }
						}
						echo "</div>";
						echo "<h3>Predicted Protein Sequence</h3>";
						echo "<div class='fasta'>";
						echo "<div class='seq-download'>";
						echo "<a href='prot_sequences/prot_".$frow['name'].".fa' download>Download protein sequence</a>";
						echo "</div>";
						echo "<p>>".$frow['name']."</p>";
						$prot = $frow['prot_seq'];
						$protA = str_split($prot, 10);
						for ( $i=0; $i<count($protA); $i++ ) {
							echo $protA[$i];
							if ( ($i+1)%7 == 0 ) { echo "<br />"; }
							else { echo " "; }
						}
						echo "</div>";
					}
				}
			} else { "No features."; }
		}
		?>
	</div>
</div>
</body>
</html>