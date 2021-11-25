<?php 
//serve XHR to display a list of items with a particular TAG id (on edittag page)
require("../init.php");

$tagid=$_GET['tagid'];
if (!is_numeric($tagid)) {
  echo "invalid tagid ($tagid)";exit;

}



$sql="SELECT items.id, users.userdesc || ' ' || items.label || ' [' || itemtypes.typedesc || ', ID:' || items.id || ']' as txt ".
     "FROM users,items,itemtypes WHERE ".
     " users.id=items.userid AND items.itemtypeid=itemtypes.id AND ".
     " itemtypes.id != 1 AND itemtypes.id != 2 AND ".
     " items.id IN (SELECT itemid from tag2item where tagid = '$tagid')";
//file_put_contents("/tmp/tag2item_ajaxlist.txt",$sql."\n\n");
$sthi=db_execute($dbh,$sql);
$ri=$sthi->fetchAll(PDO::FETCH_ASSOC);
$nitems=count($ri);
$institems="";
for ($i=0;$i<$nitems;$i++) {
  $x=($i+1).": ".$ri[$i]['txt'];
  if ($i%2) $bcolor="#D9E3F6"; else $bcolor="#ffffff";
  $institems.="\t<div style='margin:0;padding:0;background-color:$bcolor'>".
	      "<a href='?action=edititem&amp;id={$ri[$i]['id']}'>$x</a></div>\n";
}

echo "<h3>".t('Associated Items')." (".tagid2name($tagid).")</h3>";
echo $institems;

?>
