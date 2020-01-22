<!-- Modal -->
<style>
	#translateModal {}
	#translateModal .modal-dialog { width: 460px; }
	#translateModal .modal-dialog .modal-content { border-radius: 0; }
	#translateModal .modal-dialog .modal-content .modal-header {background-color: #4cb781; color: #f5f5f5; padding: 0;}
	#translateModal .modal-dialog .modal-content .modal-header .modal-title { font-size: 18px; padding: 6px; }
	#translateModal .modal-dialog .modal-content .modal-header button.close { font-size: 22px; font-weight: normal; color: #f5f5f5; opacity: 1; position: absolute; right: 12px; top: 8px; }
	#translateModal .modal-dialog .modal-content .modal-body { padding: 15px; }
	#translateModal .modal-dialog .modal-content .modal-footer { margin: 0; padding: 0 15px 15px 0;}
	#translateModal .modal-dialog .modal-content .modal-footer button { background-color: #4cb781; background-image: none; border: 0 none; border-radius: 0; color: #f5f5f5; font-size: 10px; padding: 10px; width: 120px; }
	#translateModal .basic_table { border: medium none; margin: 0;}
	#translateModal .basic_table thead {}
	#translateModal .basic_table thead tr {}
	#translateModal .basic_table thead tr th {background-color: transparent; border: medium none; color: black; text-align: center; font-family: Bebas Neue; font-size: 16px; padding: 0 0 7px; }
	#translateModal .basic_table tbody {}
	#translateModal .basic_table tbody tr {}
	#translateModal .basic_table tbody tr td { text-align: center; background-color: #f5f5f5; }
	#translateModal .basic_table tbody tr td.itemname { text-align: left; text-indent: 20px; background-color: transparent; }
</style>
<div class="modal fade" id="translateModal" tabindex="-1" role="dialog" aria-labelledby="translateModalLabel" aria-hidden="true">
	<div class="modal-dialog" style="margin: 10% auto 0;" >
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
				<h4 class="modal-title" id="translateModalLabel"></h4>
			</div>
			<div class="modal-body">
				<div class="table-responsive" style="">
					<table class="table table-bordered basic_table">
				  		<thead>
				  			<tr>
				  				<th>&nbsp;</th>
				  				<th width="120px">TRANSLATED</th>
				  				<th width="120px">READ PROOFED</th>
				  			</tr>
				  		</thead>
				  		<tbody>
				  			<tr><td class="itemname">Translated Words</td><td></td><td></td></tr>
				  			<tr><td class="itemname">Translated Strings</td><td></td><td></td></tr>
				  			<tr><td class="itemname">Total Empty Strings</td><td colspan="2"></td></tr>
				  			<tr><td class="itemname">Total Strings</td><td colspan="2"></td></tr>
				  		</tbody>
				  	</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">OK</button>
			</div>
		</div>
	</div>
</div>
