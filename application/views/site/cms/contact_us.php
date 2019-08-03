<?php
$this->load->view('site/templates/common_header');
$this->load->view('site/templates/cms_header');
?> 
<div class="cms_base_div">
    <div class="container-new cms-container">
        <section>
            <div class="inner-bans" style="background: transparent url('images/bans.png') repeat scroll 0% 0%;">
                <div class="container-new">
                    <?php if ($pageDetail['page_title'] != '') { ?>
                        <h1 class="text-center"><?php echo $pageDetail['page_title']; ?></h1>
                    <?php } ?>
                </div>
            </div>                            
        </section>

        <section>
            <div class="contact">
                <div class="container-new">
                    <div class="col-md-6 contact_left">
						<form class="contct-form cms_contact_form" name="cms_contact_form" id="cms_contact_form" action="site/cms/send_contact_mail" method="post" enctype="multipart/form-data">
                            <ul>
                                <li class="col-md-12">
                                    <label><?php if ($this->lang->line('cms_name') != '') echo stripslashes($this->lang->line('cms_name')); else echo 'Name'; ?></label>
                                    <input type="text" id="user_name" name="user_name" class="form-control required" placeholder="<?php if ($this->lang->line('cms_name') != '') echo stripslashes($this->lang->line('cms_name')); else echo 'Name'; ?>">
                                </li>

                                <li class="col-md-12">
                                    <label><?php if ($this->lang->line('cms_email') != '') echo stripslashes($this->lang->line('cms_email')); else echo 'Email'; ?></label>
                                    <input type="text" id="user_email" name="user_email" class="form-control required" placeholder="<?php if ($this->lang->line('cms_email') != '') echo stripslashes($this->lang->line('cms_email')); else echo 'Email'; ?>">
                                </li>


                                <li class="col-md-12">
                                    <label><?php if ($this->lang->line('cms_address') != '') echo stripslashes($this->lang->line('cms_address')); else echo 'Address'; ?></label>
                                    <input type="text" id="user_address" name="user_address" class="form-control required" placeholder="<?php if ($this->lang->line('cms_address') != '') echo stripslashes($this->lang->line('cms_address')); else echo 'Address'; ?>">
                                </li>

                                <li class="col-md-12">
                                    <input type="text" id="user_address1" name="user_address1" class="form-control" placeholder="<?php if ($this->lang->line('cms_address') != '') echo stripslashes($this->lang->line('cms_address')); else echo 'Address'; ?> 2">
                                </li>

                                <li class="col-md-5">
                                    <label><?php if ($this->lang->line('cms_city') != '') echo stripslashes($this->lang->line('cms_city')); else echo 'City'; ?></label>
                                    <input type="text" id="city"  name="city" class="form-control required" placeholder="<?php if ($this->lang->line('cms_city') != '') echo stripslashes($this->lang->line('cms_city')); else echo 'City'; ?>">
                                </li>



                                <li class="col-md-5">
                                    <label><?php if ($this->lang->line('cms_state') != '') echo stripslashes($this->lang->line('cms_state')); else echo 'State'; ?></label>
                                    <input type="text" id="state" name="state" class="form-control required" placeholder="<?php if ($this->lang->line('cms_state') != '') echo stripslashes($this->lang->line('cms_state')); else echo 'State'; ?>">
                                </li> 

                                <li class="col-md-5">
                                    <label><?php if ($this->lang->line('cms_zip') != '') echo stripslashes($this->lang->line('cms_zip')); else echo 'ZIP Code'; ?></label>
                                    <input type="text" id="zipcode" name="zipcode" class="form-control required" placeholder="<?php if ($this->lang->line('cms_zip') != '') echo stripslashes($this->lang->line('cms_zip')); else echo 'ZIP Code'; ?>">
                                </li>

                                <li class="col-md-5">
                                    <label><?php if ($this->lang->line('cms_phone') != '') echo stripslashes($this->lang->line('cms_phone')); else echo 'Phone Number'; ?></label>
                                    <input type="text" id="mobile" name="mobile" class="form-control required" placeholder="<?php if ($this->lang->line('cms_phone') != '') echo stripslashes($this->lang->line('cms_phone')); else echo 'Phone Number'; ?>">
                                </li>

                                <li class="col-md-12">
                                    <label><?php if ($this->lang->line('cms_message') != '') echo stripslashes($this->lang->line('cms_message')); else echo 'Message'; ?></label>
                                    <textarea id="message" name="message" class="form-control required" rows="3" placeholder="<?php if ($this->lang->line('cms_message') != '') echo stripslashes($this->lang->line('cms_message')); else echo 'Message'; ?>"></textarea>
                                </li>
                                <li class="col-md-12">
                                    <input class="btn btn-default contact_submit_btn" type="submit" value="<?php if ($this->lang->line('cms_submit') != '') echo stripslashes($this->lang->line('cms_submit')); else echo 'Submit'; ?>">
                                </li>
                            </ul>
	
                        </form>
                    </div>

                    <div class="col-md-5 contact_right">
                        <?php echo $pageDetail['description']; ?>
                        <?php echo $pageDetail['css_descrip']; ?>
                    </div>

                </div>
            </div>
        </section>
    </div>
</div>

 <script>
	$(document).ready(function () {
		$("#cms_contact_form").validate();
	});
</script>


<?php
$this->load->view('site/templates/footer');
?> 		