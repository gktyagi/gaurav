<?php echo $this->Form->create(['id'=>'image_form','type' => 'file']); ?>
<?php echo  $this->Form->input('image',array('type' => 'file')); ?>

<?php echo $this->Form->submit(); ?>
<?php echo $this->Form->end(); ?>
