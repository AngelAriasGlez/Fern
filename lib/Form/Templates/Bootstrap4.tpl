<?php
$htmlField = $field->getHtmlField();

$val = $field->getValue();


$error = $field->getError();
if ($error) $htmlField->addClass('is-invalid');
//if (!is_null($field->getValue()) && !($field instanceof \fw\FormFieldSubmit)) $htmlField->addClass('is-valid');

if($val && !(($field instanceof \fw\FormFieldPassword) || $field instanceof \fw\FormFieldPasswordRepeat)) {
    if ($htmlField->getTagName() == \fw\HtmlFormField::SELECT) {
        $htmlField->selected($val);
    }else if(!($field instanceof \fw\FormFieldRadio)){
        $field->setValue($val);
    }
}else{
    $htmlField->removeClass('is-valid');
    //echo $f->getAttr('class');
}

if($field instanceof \fw\FormFieldSubmit) {
    $htmlField->addClass('btn btn-primary');
}
?>

<?if($field instanceof \fw\FormFieldRadio):?>

            <?foreach($field->getHtmlFields(false) as $k=>$sf):?>
                <div class="i-checks">
                    <?if($val == $sf->getValue()) $sf->checked();?>
                    <?=$sf->addClass('radio-template')->setId($sf->getId().'_'.$k)->setAttr('data-toggle', 'tooltip')->setAttr('title', $sf->getAttr('data-label'))?>

                    <label for="<?=$sf->getId()?>"><?=$sf->getAttr('data-label')?></label>
                </div>
            <?endforeach;?>
            <small id="" class="help-block-none form-text text-muted"><?=$field->getHint()?></small>
            <?php if(isset($error)):?>
                <div class="invalid-feedback" style="display:block;"><?=$error?></div>
            <?endif;?>


<?elseif($field instanceof \fw\FormFieldFile):?>

    <?
    $finfo = new finfo(FILEINFO_MIME);
    $file = $field->getValue();
    $mime = $finfo->buffer($file);
    $info = '';
    if($mime){
        $info = explode(';', $mime)[0].', ';
    }
    $info .= (mb_strlen($file, '8bit')/1000).'KB';

    $field->setValue(null);
    $id=$field->getName();
    ?>
    <div style="display:flex">
            <?foreach($field->getHtmlFields() as $k=>$sf):?>
                <?=$sf->addClass("form-control")?>
            <?endforeach;?>
            <small id="" class="help-block-none form-text text-muted"><?=$field->getHint()?></small>
            <?php if(isset($error)):?>
                <div class="invalid-feedback"><?=$error?></div>
            <?endif;?>

    <style>
        .img<?=$id?> i{display:none;}
        .img<?=$id?>:hover i{display:inline-block;}
    </style>
        <?if($file):?>
    <div class="img<?=$id?>" style="position:relative;height:44px;min-width: 85px;background:#eee;margin-left:20px;cursor:pointer;padding:5px;"
         onclick="
                 var id = 'h_<?=$id?>';
                 var i = document.getElementById(id);
                 if(!i){
                 i =  document.createElement('input');
                 i.id = 'h_<?= $id ?>';
                 i.name = '<?= $id ?>';
                 i.type='hidden';
                 i.value = '';
                 this.parentNode.appendChild(i);
                 }
                 this.style.display = 'none';
                 "
    >
            <span id="mime-<?=$id?>"><?=$info?></span>
        <i class="fa fa-times" aria-hidden="true" style="position:absolute;top:calc(50% - 8px); left:calc(50% - 6px);color:red;"></i>
    </div>
        <?endif;?>
    </div>

<?elseif($field instanceof \fw\FormFieldPhoto):?>
    <?
    $photo = $field->getValue();
    $field->setValue(null);
    ?>
    <?$id=$field->getName()?>
            <div style="display:flex;">
                <div style="flex:1">
                    <?=$field->getHtmlField()->addClass("form-control")?>
                    <small id="" class="help-block-none form-text text-muted"><?=$field->getHint()?></small>
                    <?php if(isset($error)):?>
                        <div class="invalid-feedback"><?=$error?></div>
                    <?endif;?>
                </div>
                <style>
                    .img<?=$id?> i{display:none;}
                    .img<?=$id?>:hover i{display:inline-block;}
                </style>
                <div class="img<?=$id?>" style="position:relative;height:44px;min-width: 85px;background:#eee;margin-left:20px;cursor:pointer;"
                     onclick="
                        var id = 'h_<?=$id?>';
                        var i = document.getElementById(id);
                        if(!i){
                        i =  document.createElement('input');
                        i.id = 'h_<?= $id ?>';
                        i.name = '<?= $id ?>';
                        i.type='hidden';
                        i.value = '';
                        this.parentNode.appendChild(i);
                        }
                        var z = document.getElementById('img-<?=$id?>');
                        if(z)z.style.display = 'none';
                        "
                    >
                    <?if($photo):?>
                        <img src="data:image/jpeg;charset=utf-8;base64, <?=base64_encode($photo)?>" style="height:44px;" id="img-<?=$id?>">
                    <?endif;?>
                    <i class="fa fa-times" aria-hidden="true" style="position:absolute;top:calc(50% - 8px); left:calc(50% - 6px);color:red;"></i>
                </div>
            </div>
<?elseif($field instanceof \fw\Form\Field\MultiPhoto):?>
    <?
    $id=$field->getName();
    $photos = $field->getValue();
    ?>
    <div class="row">
            <style>
                .photo-item{
                    cursor:pointer;
                    min-height: 140px;
                    margin-bottom:20px;
                }
                .photo-item:hover{
                    opacity:0.5;
                }
                .photo-item:hover i, .photo-item.add i{
                    display:inline-block !important;
                }

                .photo-item i{
                    display:none;
                    font-size:40px;
                    top:calc(50% - 20px);
                    left:calc(50% - 15px);
                    position: absolute;
                    color:#fff;
                }
                .photo{
                    background: #eee;
                    background-size: cover;
                    background-position: center;
                    height: 100%;
                }

            </style>
            <script>
                function removePhoto(obj, pk, id){
                    if(pk === null) {
                        $(obj).remove();
                    }else{
                        var i =  $('<input name="'+id+'['+pk+']" type="hidden" value=""/>');
                        $(obj).empty();
                        $(obj).append(i);
                        $(obj).hide();
                    }
                }

                function addPhoto(obj, id){
                    var th = $(obj);
                    var tpl  = $('<div class="col-sm-3 photo-item" onclick="removePhoto(this, null, null);"><input type="file" name="'+id+'[]" style="display:none;"><div class="photo"></div><i class="fa fa-trash-o"></i></div>');
                    var file = tpl.find('input');
                    file.click();
                    file.change(function(){
                        //console.log($(this).val());
                        var reader = new FileReader();
                        reader.onload = function (e) {
                            tpl.children('.photo').css('background-image', 'url("' + e.target.result + '")');
                        };
                        reader.readAsDataURL(file.get(0).files[0]);
                        th.parent().children('.photo-item.add').before(tpl);
                    });

                    //console.log(tpl);

                };
            </script>
            <?if(count($photos))foreach($photos as $pk=>$photo): ?>

                <div class="col-sm-3 photo-item remove" onclick="removePhoto(this, '<?=$pk?>', '<?=$id?>');">
                    <input type="hidden" name="<?=$id?>[<?=$pk?>]" value="<?=$pk?>">
                    <div class="photo" style="background-image: url(<?=(new \fw\Media($photo, 'image/jpeg'))->toBase64()?>)"></div>
                    <i class="fa fa-trash-o"></i>
                </div>
            <?endforeach;?>
            <div class="col-sm-3 photo-item add" onclick="addPhoto(this, '<?=$id?>');">
                <div class="photo"></div>
                <i class="fa fa-plus" aria-hidden="true"></i>
            </div>

            <div class="col-lg-12">
                <small id="" class="help-block-none form-text text-muted"><?=$field->getHint()?></small>
                <?php if(isset($error)):?>
                    <div class="invalid-feedback" style="display: block"><?=$error?></div>
                <?endif;?>
            </div>
    </div>

<?else:?>
            <?=$htmlField->addClass("form-control")?>
            <small id="" class="help-block-none form-text text-muted"><?=$field->getHint()?></small>
            <?php if(isset($error)):?>
                <div class="invalid-feedback"><?=$error?></div>
            <?endif;?>

<?endif;?>
