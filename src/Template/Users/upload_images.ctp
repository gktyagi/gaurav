<?php
/**
  * @var \App\View\AppView $this
  */
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <?= $this->Form->create(null,['type'=>'file','id'=>'imageUploadForm']) ?>
    <fieldset>
	<div id="image_div">
        <input type="file" name="images[]" />
	</div>
	<div style="background-color: #4CAF50; /* Green */  border: none;color: white;  padding: 15px 32px;  text-align: center;text-decoration: none; display: inline-block;font-size: 16px;" onclick="add_more_button()"> Add MORE IMAGE</div>
    </fieldset>
    <?= $this->Form->button(__('Submit')) ?>
    <?= $this->Form->end() ?>

</div>
<script>
function add_more_button(){
	var imghtml = '<input type="file" name="images[]" />';
	$('#image_div').append(imghtml);
}

$(document).ready(function (e) {
    $('#imageUploadForm').on('submit',(function(e) {
        e.preventDefault();
        var formData = new FormData(this);

        $.ajax({
            type:'POST',
            url: 'uploadImages',
            data:formData,
            cache:false,
            contentType: false,
            processData: false,
            success:function(data){
                console.log("success");
				alert(data);
            },
            error: function(data){
                console.log("error");
                console.log(data);
            }
        });
		return false;
    }))
	});
</script>
