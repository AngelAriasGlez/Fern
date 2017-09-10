<?php
namespace fw\Data\Helper;
use fw\Data\Query;
use fw\Data;
use fw\Helper;
use fw\Template;

class Search extends Helper{
    private $Text;
    private $ParameterName = 'Search';
    public function __construct(\fw\Data\RepoQuery $query, $searchText = null){
        $this->setTempalte(new Template(__DIR__.'/Search.tpl'));

        if($searchText == null) $searchText = @$_REQUEST[$this->ParameterName];
        if(empty($searchText)) return;
        $this->Text = $searchText;
        $havingCond = new Query\Where();

        $jcols = $query->getColumns();
        array_shift($jcols);
        foreach($jcols as $k=>$v)  $jcols[$k] = explode(' ', $v)[0];


        $tcols = $query->getRepository()->getColumns();
        foreach($tcols as $k=>$v)  $tcols[$k] = $query->getRepository()->getName().'.'.$v;


        
        $cols = $jcols + $tcols;
        $last_key = end(@array_keys($tcols));
        foreach($cols as $k=>$f) {
            $havingCond->like(explode(' ', $f)[0], "%$searchText%");
            if ($k != $last_key) $havingCond->or();
        }
        $query->having($havingCond);
        //$query->where($whereCond);


        //$query->getWhere()


    }
    public function getText(){
        return $this->Text;
    }

}
?>