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
		<h1 align="center">Search <i>Phalaenopsis aphrodite</i></h1>
		<hr />
		<form id="gene_search" method="POST" action="genesearchresult.php" accept-charset="UTF-8">
			<div class="search-box">
				<table border="0">
					<tr>
						<th>Species</th>
						<td><i>Phalaenopsis aphrodite</i></td>
					</tr>
					<tr>
						<th>Keyword in Gene</th>
						<td><input type="text" name="keyword_gene" id="keyword_gene"></td>
					</tr>
					<tr>
						<th>Keyword in ID</th>
						<td><input type="text" name="keyword_id" id="keyword_id"></td>
					</tr>
					<tr>
						<th>Keyword in Description</th>
						<td><input type="text" name="keyword_desc" id="keyword_desc"></td>
					</tr>
					<tr>
						<th>Keyword in GO</th>
						<td><input type="text" name="keyword_go" id="keyword_go"></td>
					</tr>
					<tr>
						<th>Keyword in Enzyme</th>
						<td><input type="text" name="keyword_enzyme" id="keyword_enzyme"></td>
					</tr>
					<tr>
						<th>Keyword in InterPro Analysis</th>
						<td><input type="text" name="keyword_interpro" id="keyword_interpro"></td>
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