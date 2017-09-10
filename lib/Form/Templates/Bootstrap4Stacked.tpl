
<?if($field instanceof \fw\FormFieldMultiLang):?>
<div class="form-group">
    <div class="col-sm-11">
        <label class="col-sm-3 form-control-label" for="<?=$field->getName()?>"><?=$field->getLabel()?></label>

    <?
    $fname = $field->getName();
    $ferror = $field->getError();
    $hint = $field->getHint();
    $langs = $field->getLanguages();
    $langs = array_combine($langs, $langs);
    //$name = $field->getName();
    //$errors = array();
    foreach($field->getFields() as $field):?>

        <script>

            $('#<?=$fname?>-lang-selector').change(function(){
                <?=$fname?>_lang_sel($(this).val());
            });
            $( document ).ready(function(){
                <?=$fname?>_lang_sel($('#<?=$fname?>-lang-selector').val());
            });
            function <?=$fname?>_lang_sel(lang){
                $('.<?=$fname?>-lang-cont').hide();
                $('#<?=$fname?>-'+lang+'-cont').show();
            }
        </script>

        <div class="<?=$fname?>-lang-cont" id="<?=$fname.'-'.$field->getHtmlField(0)->getAttr('data-lang')?>-cont">
        <?include('Bootstrap4.tpl');?>
        </div>
    <?endforeach;?>


        <small id="" class="help-block-none form-text text-muted"><?=$hint?></small>
        <?php if(isset($ferror)):?>
            <div class="invalid-feedback" style="display:block;"><?=$ferror?></div>
        <?endif;?>
    </div>
    <div class="col-sm-1">
        <?=(new \fw\HtmlFormField($fname.'-lang-selector', \fw\HtmlFormField::SELECT))->setValue($langs)->addClass('form-control')->addClass(isset($ferror)?'is-invalid':'')?>
    </div>
</div>
<?elseif($field instanceof \fw\FormFieldHidden):?>
    <?=$field->getHtmlField()?>
<?else:?>
    <div class="form-group">
        <label class="form-control-label" for="<?=$field->getName()?>"><?=$field->getLabel()?></label>

            <? include('Bootstrap4.tpl');?>

    </div>
<?endif;?>
