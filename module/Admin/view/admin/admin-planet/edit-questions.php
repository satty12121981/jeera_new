<?php
$title = 'Add Questions';
$this->headTitle($title);  
?> 
<section id="secondary_bar">
    <div class="breadcrumbs_container">
        <article class="breadcrumbs"><a href="<?php echo $this->url('jadmin') ?>">Admin Dashboard </a><div class="breadcrumb_divider"></div>  <a href="<?php echo $this->url('jadmin/admin-planet') ?>">Manage Planet</a><div class="breadcrumb_divider"></div><a class="current" href="javascript:void(0);">View Planet</a></article>
    </div>
</section>
<section id="main" class="column">
<?php if(isset($flashMessages) && count($flashMessages)) : ?>
<ul class="session">
    <?php foreach ($flashMessages as $msg) : ?>
    <li><?php echo $msg; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
<?php if(isset($error) && count($error)) : ?>
<ul class="error">
    <?php foreach ($error as $errormsg) : ?>
    <li><?php echo $errormsg; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
<?php if(isset($success) && count($success)) : ?>
<ul class="success">
    <?php foreach ($success as $successmsg) : ?>
    <li><?php echo $successmsg; ?></li>
    <?php endforeach; ?>
</ul>
<?php endif; ?>
    <article class="module width_full">
        <header><h3><?php echo $this->escapeHtml($title); ?></h3>
		<ul class="tabs">
				<li class="second"><a href="<?php echo $this->url('jadmin/admin-planet', array('action' => 'index')); ?>">List All</a></li>
                <li class="second"><a href="<?php echo $this->url('jadmin/admin-planet-add'); ?>">Add New Planet</a></li>
				<li><a href="<?php echo $this->url('jadmin/admin-planet-approvelist', array('action'=>'approvelist'));?>">Approve Planets added by Users</a></li>
        </ul>
		</header>		 
		<?php
			$form = $this->form;
			$form->setAttribute('action', $this->url('jadmin/admin-planet-add-questions',array('id' => $group_id)));
			$form->prepare();
			echo $this->form()->openTag($form) . PHP_EOL;        
       ?>
	   <div class="module_content">
			<fieldset>
                <label> Question</label>
                <?php echo $this->formRow($form->get('question')) . PHP_EOL; ?>
            </fieldset>
			<fieldset>
                <label> Answer Type</label>
                <?php echo $this->formRow($form->get('answer_type')) . PHP_EOL; ?>
            </fieldset>
			<div id="options_list" <?php if(empty($error)){ ?>style="display:none;" <?php } ?>>
			<fieldset>
                <label>Options</label>
                <?php echo $this->formRow($form->get('option[]')) . PHP_EOL; ?>
				<?php echo $this->formRow($form->get('option[]')) . PHP_EOL; ?>
				<?php echo $this->formRow($form->get('option[]')) . PHP_EOL; ?>
            </fieldset>
			</div>
			<footer>
            <div class="submit_link">				 
                <?php echo $this->formInput($form->get('submit')) . PHP_EOL; ?>
            </div>
			</footer>
			<?php echo $this->form()->closeTag($form) . PHP_EOL; ?>
	   </div>
    </article>
    <div class="clear"></div>
    <div class="spacer"></div>
</section>
<script>
	$(document).on("click","input:radio[name=answer_type]",function(event){
		if($('input:radio[name=answer_type]:checked').val()=='radio'||$('input:radio[name=answer_type]:checked').val()=='checkbox'){
			 $("#options_list").show();
		}else{
			$("#options_list").hide(); 
		}
	});
</script>