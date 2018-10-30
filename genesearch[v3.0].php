<?php 
// Orchidstra3 Website dev 3.0
// Auther: Jay Yu
// Update: 2018/08/08
// Note: 
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8">
	<title>Orchidstra</title>
	<script src="functions/jquery-1.11.0.min.js" type="text/javascript"></script>
	<link href="css/style.css" rel="stylesheet" type="text/css" />
	<style>
	.search-box table th { text-align: right; }
	input[type=text] { width: 400px; }
	input[type=submit] { padding: 5px; }
	.notice {
		border: 1px solid #575757;
		padding: 0 16px 16px 16px;
	}
	.notice ol {
		list-style-type: decimal;
		margin-left: 16px;
	}
	</style>
	<script type="text/javascript">
		$(document).ready(function(){
			$('#keyword1').attr('placeholder', getPlacehold($('#ktype1').val()));
			$('#kweg1').text(getHint($('#ktype1').val()));
			$('#keyword2').attr('placeholder', getPlacehold($('#ktype2').val()));
			$('#kweg2').text(getHint($('#ktype2').val()));
			$('#keyword3').attr('placeholder', getPlacehold($('#ktype3').val()));
			$('#kweg3').text(getHint($('#ktype3').val()));

			$('#ktype1').change(function(){
				$('#keyword1').attr('placeholder', getPlacehold($(this).val()));
				$('#kweg1').text(getHint($(this).val()));
			})
			$('#ktype2').change(function(){
				$('#keyword2').attr('placeholder', getPlacehold($(this).val()));
				$('#kweg2').text(getHint($(this).val()));
			})
			$('#ktype3').change(function(){
				$('#keyword3').attr('placeholder', getPlacehold($(this).val()));
				$('#kweg3').text(getHint($(this).val()));
			})

			function getPlacehold(value) {
				if ( value == "de" ) {
					return "Enter the gene description keyword";
				} else if ( value == "go" ) {
					return "Enter the GO term or GO name";
				} else if ( value == "en" ) {
					return "Enter the enzyme ID or enzyme name";
				} else if ( value == "in" ) {
					return "Enter InterPro keyword or IPR code";
				}
			}
			function getHint(value) {
				if ( value == "de" ) {
					return "(e.g. MADS)";
				} else if ( value == "go" ) {
					return "(e.g. GO:0003677 or DNA binding)";
				} else if ( value == "en" ) {
					return "(e.g. EC:3.6.1.3 or ATP)";
				} else if ( value == "in" ) {
					return "(e.g. kinase or IPR003594)";
				}
			}
		})
	</script>
</head>
<body>
<div class="container">
	<div class="main-contain">
		<h1 align="center">Search <i>Phalaenopsis aphrodite</i> Gene</h1>
		<hr />
		<form id="gene_search" method="POST" action="genesearchresult3.php" accept-charset="UTF-8" enctype="multipart/form-data">
			<div class="search-box">
				<table border="0">
					<tr>
						<td colspan="3"><h2>Search with ID</h2></td>
					</tr>
					<tr>
						<td></td>
						<th>ID</th>
						<td><input type="text" name="keyword_id" id="keyword_id" maxlength="1300" placeholder="Enter the gene ID"> (e.g. PAXXG000020)</td>
					</tr>
					<tr>
						<td></td>
						<td></td>
						<td><input type="file" name="ids_file" id="ids_file"></td>
					</tr>
					<tr>
						<td colspan="3" align="center"><input type="submit" id="submit" value="Submit"></td>
					</tr>
		</form>
		<form id="gene_search2" method="POST" action="genesearchresult3.php" accept-charset="UTF-8" enctype="multipart/form-data">
					<tr>
						<td colspan="3"><hr /></td>
					</tr>
					<tr>
						<td colspan="3"><h2>Search with Filter</h2></td>
					</tr>
					<tr>
						<td></td>
						<td>
							<select name="ktype1" id="ktype1">
								<option value="de">Description</option>
								<option value="go">GO</option>
								<option value="en">Enzyme</option>
								<option value="in">InterPro Analysis</option>
							</select>
						</td>
						<td>
							<input type="text" name="keyword1" id="keyword1" maxlength="200">
							<label id="kweg1"></label>
						</td>
					</tr>
					<tr>
						<td>
							<select name="klog2" id="klog2">
								<option value="and">AND</option>
								<option value="or">OR</option>
							</select>
						</td>
						<td>
							<select name="ktype2" id="ktype2">
								<option value="de">Description</option>
								<option value="go" selected="true">GO</option>
								<option value="en">Enzyme</option>
								<option value="in">InterPro Analysis</option>
							</select>
						</td>
						<td>
							<input type="text" name="keyword2" id="keyword2" maxlength="200">
							<label id="kweg2"></label>
						</td>
					</tr>
					<tr>
						<td>
							<select name="klog3" id="klog3">
								<option value="and">AND</option>
								<option value="or">OR</option>
							</select>
						</td>
						<td>
							<select name="ktype3" id="ktype3">
								<option value="de">Description</option>
								<option value="go">GO</option>
								<option value="en" selected="true">Enzyme</option>
								<option value="in">InterPro Analysis</option>
							</select>
						</td>
						<td>
							<input type="text" name="keyword3" id="keyword3" maxlength="200">
							<label id="kweg3"></label>
						</td>
					</tr>
					<tr>
						<td colspan="3" align="center"><input type="submit" id="submit" value="Submit Query"></td>
					</tr>
				</table>
				<div class="notice">
					<h2>How to Search</h2>
					<ol>
						<li>To search with ID(s), enter the gene ID or upload a plain text file containing each ID on a separate line (less than 100 IDs).</li>
						<li>To search with Filter, enter words or phrases and choose operators (AND, OR) from dropdowns to construct queries. AND combines terms where both terms must be present. OR combines two terms where one term or the other may be present. AND has a higher precedence than OR. Searching is case-insensitive.</li>
					</ol>
				</div>
			</div>
		</form>
	</div>
</div>
</body>
</html>