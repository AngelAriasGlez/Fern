<?php /*
	<div class="form-group row">
		<label for="p-title" class="col-sm-3 form-control-label"><span class="required">*</span><?=$field->getLabel()?></label>
		<div class="col-sm-9">
			<?=$field->getFields()[0]->addClass("form-control")?>
		</div>
		<div class="offset-sm-3 col-sm-9">
			<div class="form-control-feedback"></div>
	  		<div class="form-text text-muted"><?=$field->getText()?></div>
		</div>
	</div>
	*/?>




			<label for="name"><?=$field->getLabel()?></label>
			<?=$field->getFields()[0]->addClass("form-control")?>
