<?php 
$this->load->view('site/templates/common_header');
$this->load->view('site/templates/cms_header');
?> 
<?php if(isset($pageDetail['banner_img']) && $pageDetail['use_banner'] == 'Yes') { ?>
<div class="cms_banner_contianer">
	<img src="<?php echo base_url().'images/banner/'.$pageDetail['banner_img']?>" width="100%">
</div>
<?php 
}
?>

<div class="cms_base_div">
    <div class="container-new cms-container">
        <?php if ($pageDetail['page_title'] != '') { ?>
            <h1 class="text-center"><?php echo $pageDetail['page_title']; ?></h1>
        <?php } ?>
        <?php echo $pageDetail['description']; ?>
		<?php 
		if(isset($pageDetail['css_descrip'])){ echo $pageDetail['css_descrip']; } 
		?>
    </div>
</div>
<?php
$this->load->view('site/templates/footer');
?> 		