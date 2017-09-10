<?php
namespace fw\Controller;
class Crud extends \fw\Controller {
    protected $ObjectName;
    public function __construct($objectName)
    {
        $this->ObjectName = $objectName;
        $this->BodyHook = $this->onBodyHook();

    }
    protected function onBodyHook(){
        return '';
    }

    public function getObjectName(){
        return  $this->ObjectName;
    }
    protected function getRepository(){
        return call_user_func(array($this->ObjectName, 'getRepository'));;
    }
    public function defaultAction(){
        return $this->onDefault();
    }
    protected function onDefault(){
        return new \fw\Redirect(strtolower($this->ObjectName).'/list');
    }

    public function createAction(){
        return $this->onCreate();
    }
    protected function onCreate(){
        return $this->_form(new $this->ObjectName());
    }


    public function editAction(){
        return $this->onEdit();
    }
    protected function onEdit(){
        return $this->_form($this->getRepository()->findByPk($_REQUEST));
    }

    private function _form($obj){

        $form = new \fw\Form($this->ObjectName);
        $form->addFieldGroup(new \fw\FormFieldGroup('common', $obj->getFormFields()));
        $form->addSubmitButton();

        $form->bind($obj);
        if($form->isSubmitted() && $form->validate() && $this->onFormIsSubmittedAndValid($obj)){
            $obj->save();
            return $this->onObjectSave($obj);
        }


        $this->form = $form;
        return self::View(__DIR__.'/CrudForm.tpl');
    }
    protected function onObjectSave($obj){
        return new \fw\Redirect('../'.strtolower($this->ObjectName).'/list');
    }
    protected function onFormIsSubmittedAndValid($obj){
        return true;
    }

    public function deleteAction(){
        return $this->onDelete();
    }
    protected function onDelete(){
        $item = $this->getRepository()->findByPk($_REQUEST);
        if($item) $item->delete();
        return new \fw\Redirect('../'.strtolower($this->ObjectName).'/list');
    }

    public function viewAction(){
        return $this->onView();
    }
    protected function onView(){
        $this->item = $this->getRepository()->findByPk($_REQUEST);
        $this->title = $this->onViewPageTitle($this->item);
        $this->Columns = $this->onViewColumns();
        return self::View(__DIR__ . '/CrudView.tpl');
    }
    protected function onViewColumns(){
        $structure = $this->getRepository()->getStructure();
        return array_keys($structure);
    }


    public function listAction()
    {
        return $this->onList();
    }

    protected function onList(){
        $this->repo = $this->getRepository();
        $this->Columns = $this->onListColumns();
        $query = $this->onListQuery();
        $this->items = $query->exec();
        return self::View(__DIR__ . '/CrudList.tpl');
    }

    protected function onCreateListQuery(){
        return $this->getRepository()->query();
    }

    protected function onListQuery(){
        $query = $this->onCreateListQuery();
        $this->pagination = new \fw\Data\Helper\Pagination($query);
        $this->search = new \fw\Data\Helper\Search($query);
        return $query;
    }
    protected function onListColumns(){
        $structure = $this->getRepository()->getStructure();
        return array_keys($structure);
    }
    public function onListHeader($name){
        return $name;
    }
    public function onListCell($name, $item){
        return substr(strip_tags($item->$name), 0, 100);
    }

    public function onViewValue($name, $item){
        return strip_tags($item->$name);
    }
    public function onViewName($name, $item){
        return $name;
    }
    public function onViewPageTitle($item){
        $title = $item->getRepository()->getName().' ';
        foreach($item->getRepository()->getPrimaryKeys() as $k){
            $title .= $item->$k;
        }
        return $title;
    }

}