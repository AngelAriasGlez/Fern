<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=$self->getTitle()?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="all,follow">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">

    <!-- Google fonts - Poppins -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Poppins:300,400,700">

    <!-- Tweaks for older IEs--><!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script><![endif]-->

    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote-bs4.css" rel="stylesheet">

    <style>
        <?include(__DIR__.'/assets/css/style.default.css');?>
        <?include(__DIR__.'/assets/css/custom.css');?>
        <?=$self->getStyles()?>
    </style>
</head>
<body>
<div class="page">
    <!-- Main Navbar-->
    <header class="header">
        <nav class="navbar">
            <!-- Search Box-->
            <div class="search-box">
                <button class="dismiss"><i class="icon-close"></i></button>
                <form id="searchForm" action="#" role="search">
                    <input type="search" placeholder="What are you looking for..." class="form-control">
                </form>
            </div>
            <div class="container-fluid">
                <div class="navbar-holder d-flex align-items-center justify-content-between">
                    <!-- Navbar Header-->
                    <div class="navbar-header">
                        <?$atitle = explode(' ', \fw\Globals::getInstance()['ADMIN_TITLE']);?>
                        <!-- Navbar Brand --><a href="<?=URL?>" class="navbar-brand">
                            <div class="brand-text brand-big"><span><?=$atitle[0]?></span><strong><?=$atitle[1]?></strong></div>
                            <div class="brand-text brand-small"><strong><?=$atitle[0][0]?><?=$atitle[1][0]?></strong></div></a>
                        <!-- Toggle Button--><a id="toggle-btn" href="#" class="menu-btn active"><span></span><span></span><span></span></a>
                    </div>
                    <!-- Navbar Menu -->

                </div>
            </div>
        </nav>
    </header>
    <div class="page-content d-flex align-items-stretch">
        <!-- Side Navbar -->
        <nav class="side-navbar">
            <!-- Sidebar Header-->
            <div class="sidebar-header d-flex align-items-center">

            </div>
            <!-- Sidebar Navidation Menus--><span class="heading">General</span>
            <ul class="list-unstyled">
                <?
                $menu = \fw\Globals::getInstance()['ADMIN_MENU'];
                if(isset($menu))
                    foreach($menu as $m):
                ?>
                <li><a href="<?=URL.$m['link']?>"> <i class="fa fa-<?=$m['icon']?>" aria-hidden="true"></i><?=$m['label']?></a></li>
                <?endforeach;?>
            </ul>
        </nav>
        <div class="content-inner">

            <?=$self->getContent()?>


        </div>
    </div>
</div>
<!-- Javascript files-->
<script src="https://code.jquery.com/jquery-3.2.1.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.3/js/bootstrap.min.js"></script>

<script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.8/summernote-bs4.js"></script>

<!-- Main File-->
<script>
    <?include(__DIR__.'/assets/js/front.js');?>


    $item = $('nav.side-navbar li a').filter(function(){
        return $(this).prop('href').indexOf(location.pathname) != -1;
    });
    $item.parent().addClass("active");


    $('textarea').summernote({
            toolbar: [
                // [groupName, [list of button]]
                ['para', ['ul', 'ol', 'paragraph']],
                ['style', ['bold', 'italic', 'underline', 'clear']],
                ['table', ['table']],
                ['insert', ['link', 'picture']],
                ['misc', ['codeview']]

            ],
            height: 350,
            enterHtml: '<br>'
        });
    <?/*$.extend($.summernote.plugins, {
        'brenter': function (context) {
            this.events = {
                'summernote.enter': function (we, e) {
                    // insert 2 br tags (if only one br tag is inserted the cursor won't go to the next line)
                    document.execCommand('insertHTML', false, '<br><br>');
                    e.preventDefault();
                }
            };
        }
    }*/?>

</script>



<?=$self->getScripts()?>

</body>
</html>