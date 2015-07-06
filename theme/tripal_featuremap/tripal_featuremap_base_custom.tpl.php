<?php

$featuremap  = $variables['node']->featuremap;

// expand featuremap to include the organism
$featuremap = tripal_core_expand_chado_vars($featuremap,'table','featuremap_organism');
$organisms = $featuremap->featuremap_organism;
if (is_array($organisms)) {
	foreach ($organisms AS $org) {
		$organism = $org->organism_id;
		if ($organism->nid) {	$display_organism .= "<a href=\"/node/$organism->nid\" target=\"_blank\">";	}
		$display_organism .= "$organism->genus $organism->species";
		if ($organism->nid) {	$display_organism .= "</a>";	}
		$display_organism .= "<br>";
	}
} else {
	$organism = $organisms->organism_id;
	if ($organism->nid) {	$display_organism = "<a href=\"/node/$organism->nid\" target=\"_blank\">";	}
	$display_organism .= "$organism->genus $organism->species";
	if ($organism->nid) {	$display_organism .= "</a>";	}
	if (!trim($display_organism)) {
		$display_organism = "N/A";
	}
}
// expand featuremap to include the properties.
$featuremap = tripal_core_expand_chado_vars($featuremap,'table','featuremapprop');
$featuremapprop = $featuremap->featuremapprop;

// expand featuremap to include stockprop so we can find out the population size 
$featuremap = tripal_core_expand_chado_vars($featuremap, 'table', 'featuremap_stock');
$featuremap = tripal_core_expand_chado_vars($featuremap, 'table', 'stockprop');
$stockprop =$featuremap->featuremap_stock->stock_id->stockprop;
$pop_size = NULL;
if ($stockprop) {
	foreach ($stockprop AS $prop) {
		if ($prop->type_id->name == 'population_size') {
			$pop_size = $prop->value;
		}
	}
}

// expand featuremap to include stock parents
$featuremap = tripal_core_expand_chado_vars($featuremap, 'table', 'stock_relationship');
$parents = $featuremap->featuremap_stock->stock_id->stock_relationship->object_id;
$maternal = NULL;
$paternal = NULL;
if ($parents) {
	foreach($parents AS $parent) {
		if ($parent->type_id->name == 'is_a_maternal_parent_of') {
			$maternal = $parent->subject_id;
		} else if ($parent->type_id->name == 'is_a_paternal_parent_of') {
			$paternal = $parent->subject_id;
		}
	}
}

// expand featuremap to include pubs 
$featuremap = tripal_core_expand_chado_vars($featuremap, 'table', 'featuremap_pub');
$pubs = $featuremap->featuremap_pub;

// expand featuremap to include contacts
$featuremap = tripal_core_expand_chado_vars($featuremap, 'table', 'featuremap_contact');
$contacts = $featuremap->featuremap_contact;

?>

<div id="tripal_featuremap-base-box" class="tripal_featuremap-info-box tripal-info-box">
  <div class="tripal_featuremap-info-box-title tripal-info-box-title">Map Details</div>
  <div class="tripal_featuremap-info-box-desc tripal-info-box-desc"></div>

   <table id="tripal_featuremap-base-table" class="tripal_featuremap-table tripal-table tripal-table-vert">
      <tr class="tripal_featuremap-table-even-row tripal-table-even-row">
        <th width="40%">Name</th>
        <td>
        <?php print $featuremap->name; ?>
        <?php if ($featuremap->cmap_url) {print " [<a href=\"" . $featuremap->cmap_url . "&ref_map_accs=-1\" target=\"_blank\">View in CMap</a>]";}?>
        </td>
      </tr>
<?php 
// Define function to get table row class
function featuremapGetTableRowClass($counter) {
	if ($counter % 2 == 1) {
		$class = "tripal_featuremap-table-even-row tripal-table-even-row";
	} else {
		$class = "tripal_featuremap-table-odd-row tripal-table-odd-row";
	}
	return $class;
}

// Print Species
$class = "";
$counter = 0;
$class = featuremapGetTableRowClass($counter);
print "<tr class=\"" . $class ."\">";
print "<th nowrap>Species</th><td>";
print $display_organism;
print "</td></tr>";
$counter ++;

// Print featuremapprop
foreach ($featuremapprop AS $prop) {
   $class = featuremapGetTableRowClass($counter);
   print "<tr class=\"" . $class ."\">";
   print "<th nowrap>". str_replace("_", " ", ucfirst($prop->type_id->name)) . "</th><td>$prop->value</td></tr>";
	$counter ++;
}

// Print mapunit
$class = featuremapGetTableRowClass($counter);
$counter ++;
print "<tr class=\"" . $class ."\">";
print "<th nowrap>Map unit</th><td>" . $featuremap->unittype_id->name . "</td></tr>";

// Print Maternal parents
if ($maternal) {
   $class = featuremapGetTableRowClass($counter);
   $counter ++;
   print "<tr class=\"" . $class ."\">";
   print "<th nowrap>Maternal parent</th><td><a href=\"/node/$maternal->nid\">". $maternal->uniquename . "</a></td></tr>";
}

// Print Paternal parents
if ($paternal){
	$class = featuremapGetTableRowClass($counter);
	$counter ++;
	print "<tr class=\"" . $class ."\">";
	print "<th nowrap>Paternal parent</th><td><a href=\"/node/$paternal->nid\">". $paternal->uniquename . "</a></td></tr>";
}

// Print Population size
if ($pop_size) {
	$class = featuremapGetTableRowClass($counter);
	$counter ++;
	print "<tr class=\"" . $class ."\">";
	print "<th nowrap>Population size</th><td>$pop_size</td></tr>";
}

// Print # of Loci
$num_loci = $featuremap->num_loci;
if ($num_loci){
	$class = featuremapGetTableRowClass($counter);
	$counter ++;
	print "<tr class=\"" . $class ."\">";
	print "<th nowrap>Number of loci</th><td>$num_loci</td></tr>";
}

// Print # of Linkage group
$num_lg = $featuremap->num_lg;
if ($num_lg){
	$class = featuremapGetTableRowClass($counter);
	$counter ++;
	print "<tr class=\"" . $class ."\">";
	print "<th nowrap>Number of linkage groups</th><td>$num_lg</td></tr>";
}

// Print Publications
$class = featuremapGetTableRowClass($counter);
$counter ++;
print "<tr class=\"" . $class ."\">";
print "<th nowrap>Publication</th><td>";
if (is_array($pubs)) {
	 print "[<a class=\"tripal_featuremap_toc_item\" href=\"#tripal_featuremap-pub-box\">view all " . count($pubs) . "</a>]<br>";
} else {
  print "[<a class=\"tripal_featuremap_toc_item\" href=\"#tripal_featuremap-pub-box\">view</a>]<br>";
}

// Print Contact
$class = featuremapGetTableRowClass($counter);
$counter ++;
print "<tr class=\"" . $class ."\">";
print "<th nowrap>Contact</th><td>";

if (is_array($contacts)) {
   foreach ($contacts AS $contact) {
	   print "<a class=\"tripal_featuremap_toc_item\" href=\"#tripal_featuremap-contact-box\">" . $contact->contact_id->name . "</a><br>";
   }
} else {
   $cont = $contacts->contact_id->name;
   if ($cont) {
      print "<a class=\"tripal_featuremap_toc_item\" href=\"#tripal_featuremap-contact-box\">" . $contacts->contact_id->name . "</a><br>";
   }else {
      print "N/A";
   }
   
}
?>
   </table>
</div>