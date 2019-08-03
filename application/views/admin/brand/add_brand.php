<?php
$this->load->view('admin/templates/header.php');
?>
<div id="content">
    <div class="grid_container">
        <div class="grid_12">
            <div class="widget_wrap">
                <div class="widget_top">
                    <span class="h_icon list"></span>
                    <h6><?php if ($this->lang->line('admin_make_and_model_add_new_maker_add_new_brand') != '') echo stripslashes($this->lang->line('admin_make_and_model_add_new_maker_add_new_brand')); else echo 'Add New Brand'; ?></h6>
                    <div id="widget_tab">
                    </div>
                </div>
                <div class="widget_content">
                    <form class="form_container left_label" action="admin/brand/insertBrand" id="addbrand_form" method="post" enctype="multipart/form-data">
                        <div>
                            <ul>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="brand_name"><?php if ($this->lang->line('admin_make_and_model_add_new_maker_brand_name') != '') echo stripslashes($this->lang->line('admin_make_and_model_add_new_maker_brand_name')); else echo 'Brand Name'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <input name="brand_name" id="brand_name" type="text" tabindex="2" class="required large tipTop" title="<?php if ($this->lang->line('make_model_enter_brandname') != '') echo stripslashes($this->lang->line('make_model_enter_brandname')); else echo 'Please enter the brandname'; ?>"/>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="category_name"><?php if ($this->lang->line('admin_make_and_model_marker_list_brand_logo') != '') echo stripslashes($this->lang->line('admin_make_and_model_marker_list_brand_logo')); else echo 'Brand Logo'; ?> </label>
                                        <div class="form_input">
                                            <input name="brand_logo" id="brand_logo" type="file" tabindex="2" class="large tipTop" title="<?php if ($this->lang->line('make_model_upload_brandlogo') != '') echo stripslashes($this->lang->line('make_model_upload_brandlogo')); else echo 'Please upload Brand Logo'; ?>"/>
                                            <img src="images/ajax-loader/ajax-loader.gif" id="loadedImg" style="width:10px;display:none;" />
                                            <div class="error" id="ErrNotify"><?php if ($this->lang->line('admin_make_and_model_model_pixels') != '') echo stripslashes($this->lang->line('admin_make_and_model_model_pixels')); else echo 'Note: Minimum Logo size 75 X 42 Pixels'; ?></div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <label class="field_title" for="admin_name"><?php if ($this->lang->line('admin_subadmin_status') != '') echo stripslashes($this->lang->line('admin_subadmin_status')); else echo 'Status'; ?> <span class="req">*</span></label>
                                        <div class="form_input">
                                            <div class="active_inactive">
                                                <input type="checkbox" tabindex="7" name="status" checked="checked" id="active_inactive_active" class="active_inactive"/>
                                            </div>
                                        </div>
                                    </div>
                                </li>

                                <li>
                                    <div class="form_grid_12">
                                        <div class="form_input">
                                            <input type="hidden" name="brand_id" id="brand_id" value=""  />
                                            <button type="submit" class="btn_small btn_blue" tabindex="15"><span><?php if ($this->lang->line('admin_subadmin_submit') != '') echo stripslashes($this->lang->line('admin_subadmin_submit')); else echo 'Submit'; ?></span></button>
                                        </div>
                                    </div>
                                </li>

                            </ul>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
    <span class="clear"></span>
</div>

<script>
    $(document).ready(function () {
        $("#brand_logo").change(function (e) {
            e.preventDefault();
            var formData = new FormData($(this).parents('form')[0]);
            $.ajax({
                beforeSend: function () {
                    $("#loadedImg").css("display", "block");
                },
                url: 'admin/brand/ajax_check_brand_logo',
                type: 'POST',
                xhr: function () {
                    var myXhr = $.ajaxSettings.xhr();
                    return myXhr;
                },
                success: function (data) {
                    $("#loadedImg").css("display", "none");
                    if (data == 'Success') {
                        $('#ErrNotify').html('Success').css("color", "green");
                        return true;
                    } else {
                        document.getElementById("brand_logo").value = '';
                        $('#ErrNotify').html('Upload Image Too Small. Please Upload Image Size More than or Equalto 75 X 42 .').css("color", "red");
                        return false;
                    }
                },
                data: formData,
                cache: false,
                contentType: false,
                processData: false
            });
            return false;
        });
    });
</script>
<?php
$this->load->view('admin/templates/footer.php');
?>