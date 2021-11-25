<script>
var oTable;

$(document).ready(function() {
	oTable = $('#itemlisttbl').dataTable( {
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "iDisplayLength": 50,
		"aLengthMenu": [[10,18, 25, 50, 100, -1], [10,18, 25, 50, 100, "All"]],
                "bLengthChange": true,
                "bFilter": true,
                "bSort": true,
                "bInfo": true,
		/*
                "sDom": '<"H"Tlpf>rt<"F"ip>',
                "oTableTools": {
                        "sSwfPath": "swf/copy_cvs_xls_pdf.swf"
                },
		 */
		"aoColumnDefs": [
			{ "sWidth": "60px", "aTargets":  [ 0 ] },
                        { "sWidth": "120px", "aTargets": [ 1 ] },//机架
                        { "sWidth": "120px", "aTargets": [ 2 ] },//标签
                        { "sWidth": "100px", "aTargets": [ 3 ] },//IP地址
                        { "sWidth": "150px", "aTargets": [ 4 ] },//串口
                        { "sWidth": "60px", "aTargets":  [ 5 ] },//用户
                        { "sWidth": "200px", "aTargets": [ 6 ] },//用户
                        { "sWidth": "50px", "aTargets":  [ 7 ] },//状态
			{ "sWidth": "120px", "aTargets": [ 8 ] },//型号
                        { "sWidth": "200px", "aTargets": [ 9 ] }//资产编号
		],
		"order": [[ 3, 'asc' ]],
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": "php/datatables_listitems_ajax.php",
		"sScrollX": "100%",
		"sScrollXInner": "100%",
		"bScrollCollapse": true,
	} );

	jQuery.fn.dataTableExt.oSort['title-numeric-asc']  = function(a,b) {
		var x = a.match(/title="*(-?[0-9]+)/)[1];
		var y = b.match(/title="*(-?[0-9]+)/)[1];
		x = parseFloat( x );
		y = parseFloat( y );
		return ((x < y) ? -1 : ((x > y) ?  1 : 0));
	};

	jQuery.fn.dataTableExt.oSort['title-numeric-desc'] = function(a,b) {
		var x = a.match(/title="*(-?[0-9]+)/)[1];
		var y = b.match(/title="*(-?[0-9]+)/)[1];
		x = parseFloat( x );
		y = parseFloat( y );
		return ((x < y) ?  1 : ((x > y) ? -1 : 0));
	};

/*
       	new FixedColumns( oTable, {
 		"iLeftColumns": 1,
		"iLeftWidth": 70
 	} );
*/

    $('input.column_filter').keyup(function () {
		oTable.fnFilter( this.value, $(this).parents('tr').attr('data-column') );

    } );

	var thArray=[];
	//var thArray_txts="";
	$('.colhead').each(function(i){
		var txt=$(this).text();
		if (txt)
		{
			thArray.push(txt);
			//thArray_txts = thArray_txts . "\n\n" . txt 
			console.log(txt);
		}
	})
        //file_put_contents("/tmp/thArray.txt",thArray_txts."\n\n");

	$('#colfiltertbl td.col_filt_name').each(function( index ) {
		var colidx=$(this).parents('tr').attr('data-column');
		$(this).text(thArray[colidx])
		//console.log($(this).parents('tr').attr('data-column'));
	});

    $('#togglefilter').click(function() {
		$('#colfiltertbl').toggle();
	});
} );
</script>

<h1>
<?php te("Items");?> <a title='Old Interface' style='font-size:0.5em' href="?action=listitems2">2</a>
<a title='<?php te("Add new item");?>' href='<?php echo $scriptname;?>?action=edititem&amp;id=new'><img border=0 src='images/add.png'></a>
<button style='margin-left:15px;font-weight:normal' class='filterbtn' id='togglefilter' style='font-weight:normal;font-size:1em'><?te("Filter")?></button>
</h1>



<table id='colfiltertbl' style='display:none'>
<tr>
<td style='vertical-align:top'>
	<table>
		<?php
		for ($i1=0;$i1<=9;$i1+=2) {
		?>
		<tr id="filter_col_<?=$i1?>" data-column="<?=$i1?>">
			<td class='col_filt_name'>Name</td>
			<td align="center"><input type="text" class="column_filter"></td>
		</tr>
		<?php
		}
		?>
	</table>
</td>

<td style='vertical-align:top'>
	<table>
		<?php
		for ($i2=1;$i2<=10;$i2+=2) {
		?>
		<tr id="filter_col_<?=$i2?>" data-column="<?=$i2?>">
			<td class='col_filt_name'>Name</td>
			<td align="center"><input type="text" class="column_filter"></td>
		</tr>
		<?php
		}
		?>
	</table>
</td>

</tr>
</table>


<table id='itemlisttbl' class="display">
<thead>
	<tr>
	<th class='colhead'><?php te("ID");?></th>
	<th class='colhead'><?php te("Rack");?></th>
	<th class='colhead'><?php te("Label");?></th>
	<th class='colhead'><?php te("IPv4");?></th>
	<th class='colhead'><?php te("RemAdmIP");?></th>
	<th class='colhead'><?php te("User");?></th>
	<th class='colhead'><?php te("Tags");?></th>
	<th class='colhead'><?php te("Status");?></th>
	<th class='colhead'><?php te("Item Model");?></th>
	<th class='colhead'><?php te("S/N");?></th>
<!--
	<th class='colhead'><?php te("Borrow User");?></th>
	<th class='colhead'><?php te("Date of Borrow");?></th>
	<th class='colhead'><?php te("Date of Give back");?></th>
--!>
	</tr>
</thead>
<tbody>
	<tr> <td colspan="10" class="dataTables_empty"><?php te("Loading data from server");?></td> </tr>
</tbody>
</table>



