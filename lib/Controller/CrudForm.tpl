
<?
$title = 'Nuevo';
$self->getBaseTemplate()->setTitle($title);

?>

<!-- Page Header-->
<header class="page-header">
    <div class="container-fluid">
        <h2 class="no-margin-bottom"><?=$title?></h2>
    </div>
</header>


<section class="forms">
    <div class="container-fluid">
        <div class="row">
              <!-- Form Elements -->
            <div class="col-lg-12">
                <div class="card">

                    <div class="card-header d-flex align-items-center">
                        <h3 class="h4"><?=$title?></h3>
                    </div>
                    <div class="card-body">
                        <?=$self->BodyHook?>

                        <?=$self->form?>

                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>


</script>


