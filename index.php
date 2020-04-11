<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<title>WikiRoundTheWorld</title>
	<link rel="stylesheet" href="https://unpkg.com/purecss@1.0.0/build/pure-min.css">

	<style type='text/css'>
	body {
		padding:2em;
		margin:auto;
	}
	</style>
</head>

<body>

<div class="pure-g">
	<div class="pure-u-1">
	<?php
		$lang = $_GET["lang"] ?? "en";
		print "Ideas of articles for <strong>https://".$lang.".wikipedia.org</strong>. <br/>Change version: ";
		print "<form method='get'>";
		$versions = ["af", "als", "am", "an", "ar", "arz", "ast", "az", "azb", "ba", "bar", "bat-smg", "be", "be-tarask", "bg", "bn", "bpy", "br", "bs", "bug", "ca", "ce", "ceb", "ckb", "cs", "cv", "cy", "da", "de", "el", "en", "eo", "es", "et", "eu", "fa", "fi", "fo", "fr", "fy", "ga", "gd", "gl", "gu", "he", "hi", "hr", "hsb", "ht", "hu", "hy", "ia", "id", "io", "ilo", "is", "it", "ja", "jv", "ka", "kk", "kn", "ko", "ku", "ky", "la", "lb", "li", "lmo", "lt", "lv", "mai", "map-bms", "mg", "min", "mk", "ml", "mn", "mr", "mrj", "ms", "my", "mzn", "nap", "nds", "ne", "new", "nl", "nn", "no", "oc", "or", "os", "pa", "pl", "pms", "pnb", "pt", "qu", "ro", "ru", "sa", "sah", "scn", "sco", "sh", "si", "simple", "sk", "sl", "sq", "sr", "su", "sv", "sw", "ta", "te", "tg", "th", "tl", "tr", "tt", "uk", "ur", "uz", "vec", "vi", "vo", "wa", "war", "yi", "yo", "zh", "zh-min-nan", "zh-yue"];
		print "<select name='lang'>";
		foreach ($versions as $version) {
			print "<option value='".$version."'>".$version."</option>";
		}
		print "</select>";
		print "<input type='submit'/>";
		print "</form>";
	?>
	</div>
    <div class="pure-u-1">
		<table class='pure-table'>
		<tr><th rowspan=2>Pays</th><th colspan=2>Born</th><th colspan=2>Dead</th><th rowspan=2>Linked to</th></tr>
		<tr><th>Men</th><th>Women</th><th>Men</th><th>Women</th></tr>
		<?php
		$fh = fopen("pays.csv", "r");
		$counter = 0;
		while ( ($line = fgets($fh)) && ($counter < 200) ) {
		$data = preg_split("#\t#", $line);
		$counter++;
		print "<tr><td>";
		?>
		<?php
		print rawurldecode($data[0])."</td>";
		print "<td>";
		print "<a href='#' class='view viewBornMale' q-data='".$data[1]."'>voir</a>";
		print "</td>";
		print "<td>";
		print "<a href='#' class='view viewBornFemale' q-data='".$data[1]."'>voir</a>";
		print "</td>";
		print "<td>";
		print "<a href='#' class='view viewDeadMale' q-data='".$data[1]."'>voir</a>";
		print "</td>";		
		print "<td>";
		print "<a href='#' class='view viewDeadFemale' q-data='".$data[1]."'>voir</a>";
		print "</td>";		
		print "<td>";
		print "<a href='#' class='view viewP17' q-data='".$data[1]."'>voir</a>";
		print "</td>";
		print "</tr>";
		}
		?>
		</table>	

	</div>
</div>
<script src="https://code.jquery.com/jquery-3.2.1.min.js"
integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4="
crossorigin="anonymous"></script>
<script type='text/javascript'>

$(document).ready(
	function() {
		$("table").on("click", ".removeLink", function() {
			$(this).closest("tr").remove();
		});

		$(".view").click(function() {
			$(".rowResult").remove();
			var string = "<tr class='rowResult'><td colspan='3'><a href='#' class='removeLink'>{X}</a><div class='result'><img src='ajax-loader.gif'/></div></td></tr>";
			$jq_element = $(string);
			$(this).parent().parent().after($jq_element);

			var iframe = "";
			if ($(this).hasClass("viewBornMale")) {
				iframe = loadBorn($(this).attr("q-data"), "Q6581097");
			}
			if ($(this).hasClass("viewBornFemale")) {
				iframe = loadBorn($(this).attr("q-data"), "Q6581072");
			}
			if ($(this).hasClass("viewDeadMale")) {
				iframe = loadDead($(this).attr("q-data"), "Q6581097");
			}
			if ($(this).hasClass("viewDeadFemale")) {
				iframe = loadDead($(this).attr("q-data"), "Q6581072");
			}
			if ($(this).hasClass("viewP17")) {
				iframe = loadP17($(this).attr("q-data"));
			}

			$jq_element.find(".result").html(iframe);
		});

	}
);

function loadBorn(Q, Qgender) {
	var sparql = 'PREFIX schema: <http://schema.org/>';
	sparql += '';
	sparql += 'SELECT DISTINCT ?item ?itemLabel ?itemDescription ?linkcount WHERE {';
	sparql += '  { ?item wdt:P19 wd:' + Q + '. }';
	sparql += '  UNION';
	sparql += '  {';
	sparql += '    ?item wdt:P19 ?pob.';
	sparql += '    ?pob wdt:P131* wd:' + Q + '.';
	sparql += '  }';
	sparql += '  ?item wikibase:sitelinks ?linkcount.';
	sparql += '  OPTIONAL {';
	sparql += '    ?sitelinklang schema:about ?item.';
	sparql += '    ?sitelinklang schema:inLanguage "<?php echo $lang; ?>".';
	sparql += '  }';
	sparql += '  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],<?php echo $lang; ?>,en". }';
	sparql += '  ?item wdt:P31 wd:Q5.';
	sparql += '  ?item wdt:P21 wd:' + Qgender + '.';
	sparql += '  FILTER(!BOUND(?sitelinklang))';
	sparql += '}';
	sparql += 'ORDER BY DESC(?linkcount)';
	sparql += 'LIMIT 10';

	iframeSrc = "https://query.wikidata.org/embed.html#" + encodeURI(sparql);

	var iframe = '<iframe   style="width: 90vw; height: 50vh; border: none;" referrerpolicy="origin" sandbox="allow-scripts allow-same-origin allow-popups" src="' + iframeSrc + '"></iframe>';
	return iframe;
} 

function loadDead(Q, Qgender) {
	var sparql = 'PREFIX schema: <http://schema.org/>';
	sparql += '';
	sparql += 'SELECT DISTINCT ?item ?itemLabel ?itemDescription ?linkcount WHERE {';
	sparql += '  { ?item wdt:P19 wd:' + Q + '. }';
	sparql += '  UNION';
	sparql += '  {';
	sparql += '    ?item wdt:P20 ?pob.';
	sparql += '    ?pob wdt:P131* wd:' + Q + '.';
	sparql += '  }';
	sparql += '  ?item wikibase:sitelinks ?linkcount.';
	sparql += '  OPTIONAL {';
	sparql += '    ?sitelinkfr schema:about ?item.';
	sparql += '    ?sitelinkfr schema:inLanguage "<?php echo $lang; ?>".';
	sparql += '  }';
	sparql += '  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],<?php echo $lang; ?>,en". }';
	sparql += '  ?item wdt:P31 wd:Q5.';
	sparql += '  ?item wdt:P21 wd:' + Qgender + '.';
	sparql += '  FILTER(!BOUND(?sitelinkfr))';
	sparql += '}';
	sparql += 'ORDER BY DESC(?linkcount)';
	sparql += 'LIMIT 10';

	iframeSrc = "https://query.wikidata.org/embed.html#" + encodeURI(sparql);

	var iframe = '<iframe   style="width: 40vw; height: 50vh; border: none;" referrerpolicy="origin" sandbox="allow-scripts allow-same-origin allow-popups" src="' + iframeSrc + '"></iframe>';
	return iframe;
} 

function loadP17(Q) {
	var sparql = 'PREFIX schema: <http://schema.org/>';
	sparql += '';
	sparql += 'SELECT DISTINCT ?item ?itemLabel ?itemDescription ?linkcount WHERE {';
	sparql += '  { ?item wdt:P17 wd:' + Q + '. }';
	sparql += '  ';
	sparql += '  ?item wikibase:sitelinks ?linkcount.';
	sparql += '  OPTIONAL {';
	sparql += '    ?sitelinkfr schema:about ?item.';
	sparql += '    ?sitelinkfr schema:inLanguage "<?php echo $lang; ?>".';
	sparql += '  }';
	sparql += '  SERVICE wikibase:label { bd:serviceParam wikibase:language "[AUTO_LANGUAGE],<?php echo $lang; ?>,en". }';
	sparql += '  FILTER(!BOUND(?sitelinkfr))';
	sparql += '}';
	sparql += 'ORDER BY DESC(?linkcount)';
	sparql += 'LIMIT 10';
	var iframe = '<iframe style="width: 80vw; height: 50vh; border: none;" src="https://query.wikidata.org/embed.html#PREFIX%20schema%3A%20%3Chttp%3A%2F%2Fschema.org%2F%3E%0A%0ASELECT%20DISTINCT%20%3Fitem%20%3FitemLabel%20%3FitemDescription%20%3Flinkcount%20WHERE%20%7B%0A%20%20%3Fitem%20wdt%3AP17%20wd%3A' + Q + '.%20%0A%20%20%0A%20%20%3Fitem%20wikibase%3Asitelinks%20%3Flinkcount.%0A%20%20OPTIONAL%20%7B%0A%20%20%20%20%3Fsitelinkfr%20schema%3Aabout%20%3Fitem.%0A%20%20%20%20%3Fsitelinkfr%20schema%3AinLanguage%20%22fr%22.%0A%20%20%7D%0A%20%20SERVICE%20wikibase%3Alabel%20%7B%20bd%3AserviceParam%20wikibase%3Alanguage%20%22%5BAUTO_LANGUAGE%5D%2Cfr%2Cen%22.%20%7D%0A%20%20FILTER%28%21BOUND%28%3Fsitelinkfr%29%29%0A%7D%0AORDER%20BY%20DESC%28%3Flinkcount%29%0ALIMIT%2010" referrerpolicy="origin" sandbox="allow-scripts allow-same-origin allow-popups" ></iframe>';
	return iframe;
}


</script>
</body>
</html>

