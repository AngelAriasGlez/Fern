
<?

$self->getBaseTemplate()->setTitle($self->title);

?>




<section class="forms">
    <div class="container-fluid">
        <div class="row">
              <!-- Form Elements -->
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Detalle (<?=$self->title?>)</h3>
                    </div>
                    <div class="card-body">
                        <?=$self->BodyHook?>

                        <div  id="print-content">
                            <dl class="row">
                                <?foreach($self->Columns as $c):?>
                                <dt class="col-sm-3"><?=$self->onViewName($c, $self->item)?></dt>
                                <dd class="col-sm-9"><?=$self->onViewValue($c, $self->item)?></dd>
                                <?endforeach?>
                            </dl>
                        </div>
                        <a href="javascript:print()" class="btn btn-secondary" style="margin-right:5px;"><i class="fa fa-print"></i> Imprimir</a>

                        <?
                        $keys = [];
                        foreach($self->item->getRepository()->getPrimaryKeys() as $k){$keys[$k] = $self->item->$k;}
                        $params = implode('&', array_map(function ($v, $k) { return $k.'='.$v; }, $keys, array_keys($keys)))
                        ?>
                        <a href="edit?<?=$params?>" class="btn btn-primary" style="margin-right:5px;"><i class="fa fa-pencil"></i> Editar</a>
                        <a href="delete?<?=$params?>" onclick="return confirm('Estas seguro?')" class="btn btn-danger" style="margin-right:5px;"><i class="fa fa-trash"></i> Borrar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>

    function print()
    {
        var mywindow = window.open('', 'PRINT', 'height=400,width=600');

        mywindow.document.write('<html><head><title>' + document.title  + '</title>');
        mywindow.document.write('</head><body ><h1>' + document.title  + '</h1>');
        mywindow.document.write(document.getElementById('print-content').innerHTML);
        mywindow.document.write('</body></html>');

        mywindow.document.close(); // necessary for IE >= 10
        mywindow.focus(); // necessary for IE >= 10*/

        mywindow.print();
        mywindow.close();

        return true;
    }
</script>


