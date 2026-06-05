<?php
	if (count($query) > 0) {
		$cnt = 1;
		foreach ($query as $va) {
	?>

			<tr>
				<td><?php echo $cnt; ?></td>
				<td><?php echo $va->email; ?></td>
				<td><?php echo $va->mobile; ?></td>
				
				<td><?php echo Common::convertMDY($va->date); ?></td>
				<td><?php $status = '';
				
				if($va->every_month !=''){ 
					if($va->every_month ==1){ $status = "Every Month";}
					else if($va->every_month ==12){ $status = 'Every Year';}
						else{ $status = $va->every_month.' Months';}
					}else{ $status = 'On Date';}echo $status; ?></td>
				<td style="white-space:break-spaces"><?php echo $va->notes; ?></td>
				<td><?php echo Common::convertMDY($va->created_date); ?></td>
			</tr>
		<?php $cnt++;
		}
	}
	if (count($query) == 0) { ?>
		<tr>
			<td colspan="7"> Data not found</td>
		</tr>
	<?php } ?>