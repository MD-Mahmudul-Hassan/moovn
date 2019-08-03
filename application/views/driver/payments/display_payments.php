<?php
$this->load->view('driver/templates/header.php');
?>


<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon blocks_images"></span>
						<h6><?php echo $heading?></h6>
					</div>
					<div class="widget_content">
						<table class="display display_tbl" id="driver_payments_tbl">
						<thead>
						<tr>
						
					<?php 
					if($this->lang->line('dash_click_sort') != '') $dash_click_sort = stripslashes($this->lang->line('dash_click_sort')); else  $dash_click_sort = 'Click to sort';
					?>
						
							<th class="center" title="<?php 
					echo $dash_click_sort;
					?>">
								<?php 
						if($this->lang->line('dash_S_No') != '') echo stripslashes($this->lang->line('dash_S_No')); else  echo 'S.No';
						?>
							</th>
							<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
								<?php 
						if($this->lang->line('dash_invoice_id') != '') echo stripslashes($this->lang->line('dash_invoice_id')); else  echo 'Invoice ID';
						?>
							</th>
							<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
								<?php 
						if($this->lang->line('dash_bill_date') != '') echo stripslashes($this->lang->line('dash_bill_date')); else  echo 'Bill Date';
						?>
							</th>
							<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
								 <?php 
						if($this->lang->line('dash_bill_period_from') != '') echo stripslashes($this->lang->line('dash_bill_period_from')); else  echo 'Bill Period From';
						?>
							</th>
							<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
								<?php 
						if($this->lang->line('dash_bill_period_to') != '') echo stripslashes($this->lang->line('dash_bill_period_to')); else  echo 'Bill Period To';
						?>
							</th>
							<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
								<?php 
						if($this->lang->line('dash_total_trips') != '') echo stripslashes($this->lang->line('dash_total_trips')); else  echo 'Total Trips';
						?>
							</th>
							<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
								<?php 
						if($this->lang->line('dash_total_earnings') != '') echo stripslashes($this->lang->line('dash_total_earnings')); else  echo 'Total Earnings';
						?>
							</th>
							<th class="tip_top" title="<?php echo $dash_click_sort;?>">
								<?php 
						if($this->lang->line('dash_total_tips') != '') echo stripslashes($this->lang->line('dash_total_tips')); else  echo 'Total Tips';
						?>
							</th>
							<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
								<?php 
						if($this->lang->line('dash_site_commission') != '') echo stripslashes($this->lang->line('dash_site_commission')); else  echo 'Site Commission';
						?>
							</th>
							<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
								<?php 
						if($this->lang->line('dash_your_earnings') != '') echo stripslashes($this->lang->line('dash_your_earnings')); else  echo 'Your Earnings';
						?>
							</th>
							<th>
								 <?php 
						if($this->lang->line('dash_action') != '') echo stripslashes($this->lang->line('dash_action')); else  echo 'Action';
						?>
							</th>
						</tr>
						</thead>
						<tbody>
						<?php 
						$i=0;
						if ($billings->num_rows() > 0){ 
							foreach ($billings->result() as $row){ $i++;
						?>
						<tr>
							<td class="center tr_select ">
								<?php echo $i;?>
							</td>
							<td class="center">
								<?php  if(isset($row->invoice_id)) echo $row->invoice_id;?>
							</td>
							<td class="center">
								<?php  if(isset($row->bill_date)) echo date("j M Y",$row->bill_date->sec);?>
							</td>
							<td class="center">
								<?php  if(isset($row->bill_from)) echo date("j M Y",$row->bill_from->sec);?>
							</td>
							<td class="center">
								<?php  if(isset($row->bill_to)) echo date("j M Y",$row->bill_to->sec);?>
							</td>
							<td class="center">
								<?php  if(isset($row->total_rides)) echo $row->total_rides;?>
							</td>
							<td class="center">
								<?php  echo number_format(($row->couponamount+$row->total_revenue),2);?>
							</td>
							
							<td class="center">
								<?php  if(isset($row->total_tips))  echo number_format($row->total_tips,2); else echo '0.00';  ?>
							</td>
							
							<td class="center">
								<?php  echo number_format($row->site_earnings,2);?>
							</td>
							<td class="center">
								<?php  echo number_format($row->driver_earnings,2);?>
							</td>							
							<td class="center">
								<ul class="action_list"><li style="width:100%;"><a class="p_edit tipTop" href="driver/payments/payment_summary/<?php echo $row->invoice_id;?>"><?php 
						if($this->lang->line('dash_view_details') != '') echo stripslashes($this->lang->line('dash_view_details')); else  echo 'View Details';
						?></a></li></ul>
							</td>
						</tr>
						<?php 
							}
						}
						?>
						</tbody>
						<tfoot>
							<tr>
								<th class="center" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_S_No') != '') echo stripslashes($this->lang->line('dash_S_No')); else  echo 'S.No';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_invoice_id') != '') echo stripslashes($this->lang->line('dash_invoice_id')); else  echo 'Invoice ID';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_bill_date') != '') echo stripslashes($this->lang->line('dash_bill_date')); else  echo 'Bill Date';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									 <?php 
							if($this->lang->line('dash_bill_period_from') != '') echo stripslashes($this->lang->line('dash_bill_period_from')); else  echo 'Bill Period From';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_bill_period_to') != '') echo stripslashes($this->lang->line('dash_bill_period_to')); else  echo 'Bill Period To';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_total_trips') != '') echo stripslashes($this->lang->line('dash_total_trips')); else  echo 'Total Trips';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_total_earnings') != '') echo stripslashes($this->lang->line('dash_total_earnings')); else  echo 'Total Earnings';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_total_tips') != '') echo stripslashes($this->lang->line('dash_total_tips')); else  echo 'Total Tips';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_site_commission') != '') echo stripslashes($this->lang->line('dash_site_commission')); else  echo 'Site Commission';
							?>
								</th>
								<th class="tip_top" title="<?php 
					echo $dash_click_sort;
					?>">
									<?php 
							if($this->lang->line('dash_your_earnings') != '') echo stripslashes($this->lang->line('dash_your_earnings')); else  echo 'Your Earnings';
							?>
								</th>
								<th>
									 <?php 
							if($this->lang->line('dash_action') != '') echo stripslashes($this->lang->line('dash_action')); else  echo 'Action';
							?>
								</th>
							</tr>						
						</tfoot>
						</table>
					</div>
				</div>
			</div>
			
		</div>
		<span class="clear"></span>
	</div>
</div>


<?php 
$this->load->view('driver/templates/footer.php');
?>