<nav aria-label="Page navigation">
    <ul class="pagination">
        <li class="page-item <?if($self->isFirst()):?>disabled<?endif;?>"><a class="page-link" href="<?=\fw\Url::current()->replaceQuery([$self->getParameterName()=>$self->getCurrentPage()-1])?>" aria-label="Previous">
                <span aria-hidden="true">&laquo;</span>
                <span class="sr-only">Previous</span>
            </a>
        </li>
        <?for($i=0;$i < $self->getTotalPages(); $i++):?>
            <li class="page-item <?if($self->getCurrentPage() == $i):?>active<?endif;?>"><a class="page-link" href="<?=\fw\Url::current()->replaceQuery([$self->getParameterName()=>$i])?>"><?=$i+1?></a></li>
        <?endfor;?>
        <li class="page-item <?if($self->isLast()):?>disabled<?endif;?>">
            <a class="page-link" href="<?=\fw\Url::current()->replaceQuery([$self->getParameterName()=>$self->getCurrentPage()+1])?>" aria-label="Next">
                <span aria-hidden="true">&raquo;</span>
                <span class="sr-only">Next</span>
            </a>
        </li>
    </ul>
</nav>