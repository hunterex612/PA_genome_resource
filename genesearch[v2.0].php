<?php 
// Orchidstra3 Website dev 2.0
// Auther: Jay Yu
// Update: 2018/07/25
// Note: 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Orchidstra</title>
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<style>
	.search-box table th { text-align: right; }
	input[type=text] { width: 400px; }
	input[type=submit] { padding: 5px; }
	</style>
</head>
<body>
<div class="container">
	<div class="main-contain">
		<h1 align="center">Search <i>Phalaenopsis aphrodite</i> Gene</h1>
		<hr />
		<form id="gene_search" method="POST" action="genesearchresult.php" accept-charset="UTF-8" enctype="multipart/form-data">
			<div class="search-box">
				<table border="0">
					<tr>
						<th>ID</th>
						<td><input type="text" name="keyword_id" id="keyword_id" maxlength="1300"> <input type="file" name="ids_file" id="ids_file"></td>
					</tr>
					<tr>
						<th></th>
						<td><span class="notice">Please enter full ID. e.g. PAXXG000020. <br />
												 You can enter multiple ID with ';;'. e.g. PAXXG000020;;PAXXG000025;;PAXXG000030<br />
												 You can upload .txt files. Please puts a ID in one line.<br />
												 Suggest put less than 5000 IDs at once.</span>
						</td>
					</tr>
					<tr>
						<th>Description</th>
						<td><input type="text" name="keyword_desc" id="keyword_desc" maxlength="200"></td>
					</tr>
					<tr>
						<th></th>
						<td><span class="notice">Please enter the keyword of gene description. e.g. kinase<br />
												 You can use ';;' to connect multiple keywords. e.g. putative;;nuclease</span></td>
					</tr>
					<tr>
						<th>GO</th>
						<td><input type="text" name="keyword_go" id="keyword_go" maxlength="150"></td>
					</tr>
					<tr>
						<th></th>
						<td><span class="notice">Please enter GO term or GO name. e.g. GO:0003677 or DNA binding<br />
												 You can enter multiple GO term with ';;'. e.g. GO:0003677;;GO:0005634<br />
												 You can connect multiple keywords of GO name with ';;'. e.g. DNA;;binding</span></td>
					</tr>
					<tr>
						<th>Enzyme</th>
						<td><input type="text" name="keyword_enzyme" id="keyword_enzyme" maxlength="150"></td>
					</tr>
					<tr>
						<th></th>
						<td><span class="notice">Please enter enzyme code or enzyme name. e.g. EC:3.6.1.3 or Adenosinetriphosphatase<br />
												 You can enter multiple enzyme code with ';;'. e.g. EC:3.6.1.3;;EC:5.99.1<br />
												 You can connect multiple keywords of enzyme name with ';;'. e.g. DNA;;ATP</span></td>
					</tr>
					<tr>
						<th>InterPro Analysis</th>
						<td><input type="text" name="keyword_interpro" id="keyword_interpro" maxlength="200"></td>
					</tr>
					<tr>
						<th></th>
						<td><span class="notice">Please enter signature ID or signature name. e.g. PF02518 or ATPase<br />
												 Or enter IPR code or IPR name. e.g. IPR003594 or Histidine kinase-like<br />
												 Or enter GO term or pathway ID. e.g. GO:0003677 or 00061+6.4.1.2<br />
												 You can connect multiple IDs, codes, names, or GO terms with ';;'.<br />
												 e.g. PF02518;;SSF54211<br />
												 e.g. kinase;;ATPase<br />
												 e.g. IPR003594;;IPR001241<br />
												 e.g. GO:0003677;;GO:0005524<br />
												 e.g. 00061+6.4.1.2;;00253+6.4.1.2</span></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><input type="submit" id="submit" value="Submit Query"></td>
					</tr>
				</table>
			</div>
		</form>
	</div>
</div>
</body>
</html>