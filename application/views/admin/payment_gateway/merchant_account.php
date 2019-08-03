<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
		<div class="grid_container">
			<div class="grid_12">
				<div class="widget_wrap">
					<div class="widget_top">
						<span class="h_icon list"></span>
						<h6><?php if ($this->lang->line('merchant_account') != '') echo stripslashes($this->lang->line('merchant_account')); else echo 'Merchant Account'; ?></h6>
					</div>
					<div class="widget_content">
					<?php 
						$attributes = array('class' => 'form_container left_label', 'id' => 'adduser_form', 'enctype' => 'multipart/form-data');
						echo form_open_multipart('admin/payment_gateway/insertMerchant',$attributes) 
					?>
	 						<ul>
	 							<li>
								<div class="form_grid_12">
									<label class="field_title" for="full_name"><?php if ($this->lang->line('braintree_supported_currency') != '') echo stripslashes($this->lang->line('braintree_supported_currency')); else echo 'BrainTree Supported Currency code'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<select name="currency_code">
										<?php foreach($braintree_currency as $data)  {?>
											<option value="<?php echo $data; ?>"><?php echo strtoupper($data); ?></option>
										<?php }?>
										</select>
									</div>
								</div>
								</li>
								<li>
								<div class="form_grid_12">
									<label class="field_title" for="user_name"><?php if ($this->lang->line('brain_merchant_account_id') != '') echo stripslashes($this->lang->line('brain_merchant_account_id')); else echo 'BrainTree Merchant Account Id'; ?> <span class="req">*</span></label>
									<div class="form_input">
										<input name="merchant_account" id="merchant_account" type="text" tabindex="2" class="required large tipTop" >
									</div>
								</div>
								</li>
								
								<li>
								<div class="form_grid_12">
									<div class="form_input">
										<button type="submit" class="btn_small btn_blue" tabindex="9"><span><?php if ($this->lang->line('admin_common_submit') != '') echo stripslashes($this->lang->line('admin_common_submit')); else echo 'Submit'; ?></span></button>
									</div>
								</div>
								</li>
							</ul>
						</form>
					</div>
				</div>
				<br/><br/><br/>
                    <div class="widget_top">
                        <span class="h_icon blocks_images"></span>
                        <h6><?php if ($this->lang->line('merchant_account_list') != '') echo stripslashes($this->lang->line('merchant_account_list')); else echo 'Merchant Account List'; ?></h6>
                    </div>
                    <table class="display display_tbl" id="payment_tbl">
                        <thead>
                            <tr>
                                <th class="tip_top" title="">
									Currency Code
                                </th>
								<th class="tip_top" title="">
									BrainTree Merchant Account Id
                                </th>
								
                                <th class="tip_top" title="">
                                   Action
                                </th>
                            </tr>
                        </thead>
                        <tbody>						
                        <?php 
						if($merchant_account->num_rows() > 0) {
						foreach($merchant_account->result() as $row) {?>
						<tr>
						 <td class="center">
						 <?php echo strtoupper($row->currency_code); ?>
						 </td>
						 <td class="center">
						 <?php echo $row->merchant_account; ?>
						 </td>
						<td class="center">
						<span><a class="action-icons c-delete" href="javascript:confirm_delete('admin/payment_gateway/delete_account_permanently/<?php echo $row->_id;?>')" title="<?php if ($this->lang->line('admin_common_delete') != '') echo stripslashes($this->lang->line('admin_common_delete')); else echo 'Delete'; ?>"><?php if ($this->lang->line('admin_subadmin_delete') != '') echo stripslashes($this->lang->line('admin_subadmin_delete')); else echo 'Delete'; ?></a></span>
						</td>
						</tr>
						<?php } }?>

                        </tbody>
                        <tfoot>
                            <tr>
                                <th class="tip_top" title="">
									Currency Code
                                </th>
								<th class="tip_top" title="">
									BrainTree Merchant Account Id
                                </th>
								
                                <th class="tip_top" title="">
                                   Action
                                </th>
                            </tr>
                        </tfoot>
                    </table>
			</div>
		</div>
		<span class="clear"></span>
	</div>
	
</div>
<?php 
$this->load->view('admin/templates/footer.php');
?>