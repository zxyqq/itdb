<!-- Spiros Ioannou 2009 , sivann _at_ gmail.com -->
<SCRIPT LANGUAGE="JavaScript"> 

  $(document).ready(function() {
    $('input#invoicefilter').quicksearch('table#invoicelisttbl tbody tr');
    $('input#itemsfilter').quicksearch('table#itemslisttbl tbody tr');
    $('input#softfilter').quicksearch('table#softwarelisttbl tbody tr');
    $('input#contrfilter').quicksearch('table#contrlisttbl tbody tr');

    $("#tabs").tabs();
    $("#tabs").show();

    $("#locationid").change(function() {
      var locationid=$(this).val();
      var locareaid=$('#locareaid').val();
      var dataString = 'locationid='+ locationid;//+'&locareaid='+'<?php echo $locareaid?>';
      //var dataString2 = 'locationid='+ locationid+'&locareaid='+locareaid;

      $.ajax ({
	  type: "POST",
	  url: "php/locarea_options_ajax.php",
	  data: dataString,
	  cache: false,
	  success: function(html) {
	    $("#locareaid").html(html);
	  }
      });

      $.ajax ({
	  type: "POST",
	  url: "php/racks_perlocarea_ajax.php",
	  data: dataString,
	  cache: false,
	  success: function(html) {
	    $("#rackid").html(html);
	  }
      });



    });

  });

</SCRIPT>

<?php 

if (!isset($initok)) {echo "do not run this script directly";exit;}


if ($id!="new") {
    //get current item data
    $id=$_GET['id'];
    $sql="SELECT * FROM items WHERE id='$id'";
    $sth=db_execute($dbh,$sql);
    $item=$sth->fetchAll(PDO::FETCH_ASSOC);
}


$sql="SELECT * FROM itemtypes order by typedesc";
$sth=$dbh->query($sql);
$itypes=$sth->fetchAll(PDO::FETCH_ASSOC);

for ($i=0;$i<count($itypes);$i++) {
    $typeid2name[$itypes[$i]['id']]=$itypes[$i]['typedesc'];
}

$sql="SELECT * FROM users order by userdesc asc";
$sth=$dbh->query($sql);
$userlist=$sth->fetchAll(PDO::FETCH_ASSOC);

$sql="SELECT * FROM locations order by name";
$sth=$dbh->query($sql);
$locations=$sth->fetchAll(PDO::FETCH_ASSOC);



//$sql="SELECT * FROM racks"; $sth=$dbh->query($sql); $racks=$sth->fetchAll(PDO::FETCH_ASSOC);

$sql="SELECT id,title,type FROM agents order by title";
$sth=db_execute($dbh,$sql);
while ($r=$sth->fetch(PDO::FETCH_ASSOC)) $agents[$r['id']]=$r;

$sql="SELECT * FROM statustypes";
$sth=$dbh->query($sql);
$statustypes=$sth->fetchAll(PDO::FETCH_ASSOC);



$sql="SELECT items.* from items,itemtypes where ".
    " (itemtypes.typedesc like '%switch%' or itemtypes.typedesc like '%router%' ) ".
    " and itemtypes.id=items.itemtypeid ";
$sth=$dbh->query($sql);
$netitems=$sth->fetchAll(PDO::FETCH_ASSOC);


//change displayed form items in input fields
if ($id=="new") {
    $caption=t("Add New Item");
    foreach ($formvars as $formvar){
        $$formvar="";
    }
    $d="";
    //$mend="";
}
//if editing, fill in form with data from supplied item id
else if ($action=="edititem") {
    $caption=t("Item Data")." ($id)";
    foreach ($formvars as $formvar){
        $$formvar=$item[0][$formvar];
    }
    //seconds from 1970
    $d=strlen($item[0]['purchasedate'])?date($dateparam,$item[0]['purchasedate']):"";
}
?>

<h1><?php echo $caption?></h1>
<?php echo $disperr;?>

<!-- our error errcontainer -->
<div class='errcontainer ui-state-error ui-corner-all' style='padding: 0 .7em;width:700px;margin-bottom:3px;'>
	<p><span class='ui-icon ui-icon-alert' style='float: left; margin-right: .3em;'></span>
	<h4><?php te("There are errors in your form submission, please see below for details");?>.</h4>
	<ol>
		<li><label for="itemtypeid" class="error"><?php te("Please select item type");?></label></li>
		<li><label for="ispart" class="error"><?php te("Please specify if this item is a part of another");?></label></li>
		<li><label for="rackmountable" class="error"><?php te("Please check if this item can be rackmounted");?></label></li>
		<li><label for="manufacturerid" class="error"><?php te("Manufacturer is missing");?></label></li>
		<li><label for="model" class="error"><?php te("Specify model");?></label></li>
		<li><label for="userid" class="error"><?php te("Specify user responsible for this item");?></label></li>
	</ol>
</div>

<div id="tabs">
  <ul>
    <li><a href="#tab1"><span><?php te("Item Data");?></span></a></li>
  </ul>

<div id="tab1" class="tab_content">

  <form class='frm1' enctype='multipart/form-data' method=post name='additmfrm' id='mainform'>

  <table border='0' class=tbl1 >
  <tr>
  <td class='tdtop'>
    <table border='0' class=tbl2>
    <tr><td colspan=2><h3><?php te("Intrinsic Properties");?></h3></td></tr>

    <tr>
    <td class='tdt'><?php te("Item Type");?>:<sup class='red'>*</sup></td>
    <td title='<?php te("Populate list from the Item Types menu");?>'>

<?php 
echo "\n<select class='mandatory' validate='required:true' name='itemtypeid'>\n";
echo "<option value=''>Select</option>\n";
for ($i=0;$i<count($itypes);$i++) {
    $dbid=$itypes[$i]['id']; $itype=$itypes[$i]['typedesc']; $s="";
    if ($itemtypeid=="$dbid") $s=" SELECTED ";
    echo "<option $s title='id=$dbid' value='$dbid'>$itype</option>\n";
}
?>
      </select>
      </td>
      </tr>


    <?php 
    //manufacturer
    ?>

      <tr> <td class='tdt'><?php te("Model");?><sup class='red'>*</sup>:</td><td><input type=text validate='required:true' class='mandatory' value="<?php echo $model?>" name='model'></td> </tr>

        <!-- 用warrinfo来代替systemID -->

      <tr> <td class='tdt'><?php te("S/N");?><sup class='red'>*</sup>:</td><td><input type=text validate='required:true' class='mandatory' value='<?php echo $sn?>' name='sn'></td> </tr>

      <tr> <td class='tdt'><?php te("Comments");?>:</td><td> <textarea wrap='soft' class=tarea1  name='comments'><?php echo $comments?></textarea></td> </tr>

    <tr> <td class='tdt'><?php te("Label");?>:</td><td title='<?php te("show also this text on printable labels");?>'><input type='text' value="<?php echo $label?>" name='label'></td> </tr>
      </table>
    </td>

    <td class='tdtop'>

      <table border='0' class=tbl2><!-- Usage -->
      <tr><td colspan=2 ><h3><?php te("Usage");?></h3></td></tr>

      <tr>

      <?php 
      //status
      ?>
	<td class='tdt'><?php te("Status");?><sup class='red'>*</sup>:</td>
	<td>
	<select validate='required:true' class='mandatory'  name='status'>

      <?php 
      for ($i=0;$i<count($statustypes);$i++) {
	$dbid=$statustypes[$i]['id']; $itype=$statustypes[$i]['statusdesc']; $s="";
	if ($status==$dbid) $s=" SELECTED ";
	echo "<option $s value='$dbid'>$itype</option>\n";
      }
      ?>
      </select>
	</td>
	</tr>


      <?php 
      //user
      ?>

      <tr>
      <td class='tdt'><?php te("User");?><sup class='red'>*</sup>:</td><td title='<?php te("User responsible for this item");?>'>
      <select validate='required:true' class='mandatory' name='userid'>
      <option value=''><?php te("Select User");?></option>
      <?php 
        //$userid=38;
      for ($i=0;$i<count($userlist);$i++) {
	$dbid=$userlist[$i]['id']; $itype=$userlist[$i]['userdesc']; $s="";
	if ($userid==$dbid) $s=" SELECTED ";
	//echo "<option $s value='$dbid'>".sprintf("%02d",$dbid)."-$itype</option>\n";
	echo "<option $s value='$dbid'>$itype</option>\n";
      }
      ?>

      </select>
      </td>
      </tr>

      <tr>
      <?php 
      //location
      ?>
      <td class='tdt' class='tdt'><?php te("Location");?><sup class='red'>*</sup>:</td>
      <td>
	<select id='locationid' name='locationid'>
	<option value=''><?php te("Select");?></option>
	<?php 
	foreach ($locations  as $key=>$location ) {
	  $dbid=$location['id']; 
	  $itype=$location['name'].", Floor:".$location['floor'];

      if (is_numeric($location['floor']))
          $itype=$location['name'].", ".t("Floor").":".$location['floor'];
      else
          $itype=$location['name'];
	  $s="";
	  if (($locationid=="$dbid")) $s=" SELECTED "; 
	  echo "    <option $s value='$dbid'>$itype</option>\n";
	}
	?>
	</select>

      </td>
      </tr>

      <tr>
      <?php 
      //area
      if (is_numeric($locationid)) {
	$sql="SELECT * FROM locareas WHERE locationid=$locationid order by areaname";
	$sth=$dbh->query($sql);
	$locareas=$sth->fetchAll(PDO::FETCH_ASSOC);
      } 
      else 
	$locareas=array();
      ?>
      <td class='tdt' class='tdt'><?php te("Area/Room");?><sup class='red'>*</sup>:</td>
      <td>
	<select id='locareaid' name='locareaid'>
	  <option value=''><?php te("Select");?></option>
	  <?php 
	  foreach ($locareas  as $key=>$locarea ) {
	    $dbid=$locarea['id']; 
	    $itype=$locarea['areaname'];
	    $s="";
	    if (($locareaid=="$dbid")) $s=" SELECTED "; 
	    echo "    <option $s value='$dbid'>$itype</option>\n";
	  }
	  ?>
     

	</select>

      </td>
      </tr>



      <tr>
      <?php 
      //rackid
      echo "\n<td class='tdt' class='tdt'>";
      if (is_numeric($rackid)) 
	//echo "<a alt='View' title='".t("view rack")."' href='$scriptname?action=viewrack&amp;id=$rackid&amp;highlightid=$id'><img height=12 src='images/eye.png'></a> ";
	echo "<a id=viewrack alt='View' title='".t("view rack")."' href='$scriptname?action=viewrack&amp;id=$rackid&amp;highlightid=$id&amp;nomenu=1'><img height=12 src='images/eye.png'></a> ";
	echo "<a alt='Edit' title='".t("edit rack")."' href='$scriptname?action=editrack&amp;id=$rackid&amp;highlightid=$id'><img src='images/edit.png'></a> ";
      ?>

      <script type="text/javascript"> 
	$('a#viewrack').popupWindow({ 
	  centerScreen:1,
	  height:800, 
	  scrollbars:1,
	  width:700, 
	  windowName:'viewrack', 
	}); 
      </script>
      Rack<sup class='red'>*</sup>:</td>
      <td>
      <select validate='required:true' class='mandatory' id='rackid' name='rackid'>
      <option value=''><?php te("Select");?></option>
      <?php 
      if (is_numeric($locationid)) {
	$sql="SELECT * FROM racks WHERE locationid=$locationid order by label,id";
	$sth=$dbh->query($sql);
	$racks=$sth->fetchAll(PDO::FETCH_ASSOC);
      } 
      else 
	$racks=array();

      for ($i=0;$i<count($racks);$i++) {
	$dbid=$racks[$i]['id']; 
	$itype=$racks[$i]['label'].",".$racks[$i]['usize']."U ". $racks[$i]['model'];
	$s="";
	if ($rackid=="$dbid") $s=" SELECTED ";
	echo "<option $s value='$dbid'>$dbid:$itype</option>\n";
      }
      ?>
      </select></td>
      </tr>

      <tr>

      <?php 
      //rackposition
      ?>
      <td class='tdt' class='tdt'><?php te("Rack Pos. (topmost)");?>:</td><td>

      <select name='rackposition' title='Rack Row'  style='width:40%'>
      <option value=''><?php te("Select");?></option>
      <?php 
      for ($i=1;$i<51;$i++) {
	$s="";
	if ($rackposition=="$i") $s=" SELECTED ";
	echo "<option $s value='$i'>$i</option>\n";
      }
      ?>
      </select>

      <?php 
	$s="";$s6="";$s3="";$s4="";$s2="";$s1="";$s7="";
	$x="s$rackposdepth";
	$$x="SELECTED";
      ?>
      <select name='rackposdepth'  style='width:40%' title='<?php te("Depth of rack occupation. (F)ront, (M)iddle, (B)ack");?>'>
      <option <?php echo $s6?> value='6'>FM-</option>
      <option <?php echo $s3?> value='3'>-MB</option>
      <option <?php echo $s4?> value='4'>F--</option>
      <option <?php echo $s2?> value='2'>-M-</option>
      <option <?php echo $s1?> value='1'>--B</option>
      <option <?php echo $s7?> value='7'>FMB</option>
      </select>

      </td>
      </tr>

      </table><!--/usage-->


    </td>
    <td class='tdtop'>


    </td>

  <?php 
  //Associated files
  //
    $f=itemid2files($id,$dbh);
    $flnk=showfiles($f);

  ?>
    </tr>

    <td class='tdtop' colspan=1>
      <table border='0' class=tbl2> <!-- 3-Network -->
      <tr><td colspan=1 ><h3><?php te("Network");?></h3></td></tr>
      <tr> <td class='tdt'>IPv4:</td><td><input type=text size=15 value='<?php echo $ipv4?>' name='ipv4'></td> </tr>
      <tr> <td class='tdt'><?php te("Rem.Adm.IP");?>:</td><td title='<?php te("Remote Administration IP");?>'><input type=text size=15 value='<?php echo $remadmip?>' name='remadmip'></td> </tr>
      </table>
    </td>

    <!-- tags -->
    <td class='tdtop' colspan=1>
      <h3>Tags <span title='Changes are saved immediately.<br>Removing tags removes associations not Tags. Use the "Tags" menu for that.' style='font-weight:normal;font-size:70%'>(<a class="edit-tags" href="">edit tags</a>)</span></h3>
      
      <?php 
      echo showtags("item",$id);
      ?>
      <script>
	ajaxtagscript="php/tag2item_ajaxedit.php?id=<?php echo $id?>";
	<?php 
	require_once('js/jquery.tag.front.js');
	?>
      </script>
	      <br>
	      <div style='clear:both;height:20px;'></div>
	      <div style='font-style:italic' id='result'></div>
    </td>


  </table>

    <?php
     
    if ($id!="new") {

      //check for different status of linked items
      $sql="SELECT items.id,items.status,statustypes.statusdesc FROM ".
	   " items,statustypes where (items.id =$id or items.id in ".
	   " (select itemid1 from itemlink where itemid2=$id union select itemid2 from itemlink where itemid1=$id)) ".
	   " AND items.status=statustypes.id";
      $sth=db_execute($dbh,$sql);
      while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
	if ($status!=$r['status'])
	  $warr.= t("<li><b>status</b> of this item  different from status of associated item").
	  " <a href='$scriptname?action=edititem&amp;id={$r['id']}'>{$r['id']}</a> ".
	  "({$r['statusdesc']})</li>";
      }

      //check for different location of linked items
      $sql="SELECT items.id,items.locationid,locations.name FROM ".
           "items,locations where (items.id =$id or items.id IN ".
	   "(select itemid1 from itemlink where itemid2=$id union select itemid2 from itemlink where itemid1=$id)) ".
	   "AND items.locationid=locations.id";
      $sth=db_execute($dbh,$sql);
      while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
	if ($locationid!=$r['locationid'])
	  $warr.= t("<li><b>location</b> of this item  different from location of associated item").
	  " <a href='$scriptname?action=edititem&amp;id={$r['id']}'>{$r['id']}</a> ".
	  "({$r['name']})</li>";
      }

      //check for different user of linked items
      $sql="SELECT items.id,items.userid,users.username FROM ".
           "items,users where (items.id =$id or items.id IN ".
	   "(select itemid1 from itemlink where itemid2=$id union select itemid2 from itemlink where itemid1=$id)) ".
	   "AND items.userid=users.id";
      $sth=db_execute($dbh,$sql);
      while ($r=$sth->fetch(PDO::FETCH_ASSOC)) {
	if ($userid!=$r['userid'])
	  $warr.= t("<li><b>user</b> of this item  different from user of associated item").
	  " <a href='$scriptname?action=edititem&amp;id={$r['id']}'>{$r['id']}</a> ".
	  "({$r['username']})</li>";
      }


      if (strlen($warr)) {
	echo "<div class='ui-state-highlight ui-corner-all' style='text-align:left;'>
	       <p>
	       <span style='float: left; margin-right: .3em;margin-top:2px;' class='ui-icon ui-icon-notice'></span>
	       </p>
	       <h4>Warning:</h4>
	       <ol>
	       $warr
	       </ol>
	      </div>
	";
      }
    }
    ?>
       
   
</div> <!--tab1-->




</div><!-- tab container -->


<table><!-- save buttons -->
<tr>
<td style='text-align: center' colspan=1><button type="submit"><img src="images/save.png" alt="Save" > <?php te("Save");?></button></td>
<?php 
if ($id!="new") {
  echo "\n<td style='text-align: center' ><button type='button' onclick='javascript:delconfirm2(\"Item {$_GET['id']}\",\"$scriptname?action=$action&amp;delid={$_GET['id']}\");'>".
       "<img title='Delete' src='images/delete.png' border=0>".t("Delete")."</button></td>\n";

  echo "\n<td style='text-align: center' ><button type='button' onclick='javascript:cloneconfirm(\"Item {$_GET['id']}\",\"$scriptname?action=$action&amp;cloneid={$_GET['id']}\");'>".
       "<img  src='images/copy.png' border=0>". t("Clone")."</button></td>\n";
} 
else 
  echo "\n<td>&nbsp;</td>";
?>
 
</tr>
</table>

<input type=hidden name=action value='<?php echo $_GET["action"]?>'>
</form>


