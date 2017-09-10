<?php
namespace fw\Data\Helper;
use fw\Data\Query;
use fw\Data;
use fw\Helper;
use fw\Template;

class Pagination extends Helper {
    private $TotalItems;
    private $CurrentPage;
    private $ResultsPerPage;
    private $TotalPages;
    private $ParameterName = 'Page';
    public function __construct(\fw\Data\Query $query, $resultsPerPage = 20, $currentPage = null){

        $this->setTempalte(new Template(__DIR__.'/Pagination.tpl'));

        if($currentPage == null) $currentPage = intval(@$_REQUEST[$this->ParameterName]);

        $this->CurrentPage = $currentPage;
        $this->ResultsPerPage = $resultsPerPage;


        /*foreach($query->getRepository()->getPrimaryKeys() as $k){
            $query->group($k);
        }*/
        $this->TotalItems = $query->count();
        $this->TotalPages = ceil($this->TotalItems/$this->ResultsPerPage);


        $query->limit($resultsPerPage, $currentPage*$resultsPerPage);



    }

    public function getTotalItems(){
        return $this->TotalItems;
    }
    public function getCurrentPage(){
        return $this->CurrentPage;
    }
    public function getResultsPerPage(){
        return $this->ResultsPerPage;
    }
    public function getTotalPages(){
        return $this->TotalPages;
    }
    public function getParameterName(){
        return $this->ParameterName;
    }
    public function isLast(){
        return $this->CurrentPage >= ($this->TotalPages - 1);
    }
    public function isFirst(){
        return $this->CurrentPage <= 0;
    }


}
?>