<?php
$self->getBaseTemplate()->setTitle ("Listado");

?>



<section class="forms">
    <div class="container-fluid">
        <div class="row">
            <!-- Form Elements -->
            <div class="col-lg-12">


                <div class="card">
 
                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4">Listado (<?=$self->pagination->getTotalItems()?>)</h3>
                    </div>
                    <div class="card-body">
                        <?=$self->BodyHook?>
                        
                        <div class="row">
                            <div class="col-lg-4" style="margin-bottom:20px;">

                                <?=$self->search?>

                            </div>
                            <div class="col-lg-8" style="text-align:right;margin-bottom:20px;" >
                                <a id="login" href="../<?=strtolower($self->getObjectName())?>/create" class="btn btn-primary"><i class="fa fa-plus" aria-hidden="true"></i> Crear</a>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <form method="post">
                        <table class="table table-striped table-hover">
                            <thead>
                            <tr>
                                <th></th>
                                <?foreach($self->Columns as $k=>$v):?>
                                <th><?=$self->onListHeader($v)?></th>
                                <?endforeach;?>

                            </tr>
                            </thead>
                            <tbody>
                            <?foreach(@$self->items as $item):?>
                            <tr>
                                <?
                                $keys = [];
                                foreach($self->repo->getPrimaryKeys() as $k){$keys[$k] = $item->$k;}
                                $params = implode('&', array_map(function ($v, $k) { return $k.'='.$v; }, $keys, array_keys($keys)))
                                ?>

                                <td>
                                    <a href="view?<?=$params?>" class="btn btn-xs btn-default"><i class="fa fa-eye"></i></a>
                                    <a href="edit?<?=$params?>" class="btn btn-xs btn-default"><i class="fa fa-pencil"></i></a>
                                    <a href="delete?<?=$params?>"  onclick="return confirm('Estas seguro?')" class="btn btn-xs btn-default"><i class="fa fa-trash"></i></a>
                                </td>

                                <?foreach($self->Columns as $k=>$v):?>
                                    <td><?=$self->onListCell($v, $item)?></td>
                                <?endforeach;?>

                            </tr>
                            <?endforeach?>
                            </tbody>
                        </table>
                        </div>
                        <div style="margin-left:10px;"><?=$self->pagination?></div>
                    </div>
                </div>



            </div>
        </div>
    </div>
</section>

<style>
    table{
        display:table;
        max-width:100%;
        width: 100%;

    }

    td a.btn{
        color: #777 !important;
    }
    .btn-xs {
        padding: 0.20rem 0.5rem;
        font-size: 0.8rem;
        line-height: 1.5;
        border-radius: 0.2rem;
        min-width:40px;
    }

    td a.btn{
        margin-right: 4px !important;
    }
    .page-link{
        min-width: 40px !important;
        text-align: center;
    }

    td, th { overflow:hidden;white-space:nowrap  }
</style>

<script>

</script>