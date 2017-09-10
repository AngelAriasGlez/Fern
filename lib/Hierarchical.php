<?php
/**
 * Created by PhpStorm.
 * User: angel
 * Date: 15/05/2018
 * Time: 12:25
 */

namespace fw;


class Hierarchical extends \fw\Data\Record
{

    /**
     * @primary
     * @autoincrement
     */
    protected $Id;
    protected $Lft;
    protected $Rgt;



    public function getBreadcrumb($showSelf = false){
        $n = get_class($this);

        $con = 'AND parent.id != node.id';
        if($showSelf) $con = '';
        $parents = $this->getRepository()->findBySql("SELECT *
				FROM $n AS node,
				$n AS parent
				WHERE node.lft BETWEEN parent.lft AND parent.rgt
				AND node.id = '$this->Id'
				$con
				ORDER BY parent.lft
				");

        $bread = array();
        if(count($parents)){
            foreach($parents as $p){
                $bread[] = $p;
            }

        }

        return $bread;

    }

    public function getParent(){
        $n = get_class($this);
        $parent = $this->getRepository()->findBySql("SELECT parent.*
			FROM $n AS node,
			        $n AS parent
			WHERE node.lft BETWEEN parent.lft AND parent.rgt
			        AND node.id = '$this->Id'
			ORDER BY parent.Lft DESC
			LIMIT 1,1
		");
        if(count($parent)){
            return $parent[0];
        }else{
            return null;
        }
    }
    public function getChilds(){
        $n = get_class($this);
        return $this->getRepository()->findBySql("SELECT node.*, (COUNT(parent.Id) - (sub_tree.depth + 1)) AS depth
			FROM $n AS node,
			        $n AS parent,
			        $n AS sub_parent,
			        (
			                SELECT node.Id, (COUNT(parent.Id) - 1) AS depth
			                FROM $n AS node,
			                        $n AS parent
			                WHERE node.lft BETWEEN parent.lft AND parent.rgt
			                        AND node.id = '$this->Id'
			                GROUP BY node.Id
			                ORDER BY node.lft
			        )AS sub_tree
			WHERE node.lft BETWEEN parent.lft AND parent.rgt
			        AND node.lft BETWEEN sub_parent.lft AND sub_parent.rgt
			        AND sub_parent.Id = sub_tree.Id
			GROUP BY node.Id
			HAVING depth = 1
			ORDER BY node.lft DESC;");
    }





    public function add(Hierarchical $parent){
        $db = $this->getRepository()->getDbContext();
        $n = get_class($this);
        $db->exec("LOCK TABLE $n WRITE;");
        if($res = $db->query("SELECT @myLeft := lft FROM $n WHERE Id = '".$parent->Id."';")->fetch()){
            $db->exec("UPDATE $n SET Rgt = Rgt + 2 WHERE Rgt > @myLeft;");
            $db->exec("UPDATE $n SET Lft = Lft + 2 WHERE Lft > @myLeft;");
        }
        $db->exec("UNLOCK TABLES;");
        $this->Lft = $res[0] + 1;
        $this->Rgt = $res[0] + 2;
        $this->save();
    }

    public function delete(){

        $db = $this->getRepository()->getDb();
        $n = get_class($this);
        $query = "
			LOCK TABLE $n WRITE;
		
			SELECT @myLeft := Lft, @myRight := Rgt, @myWidth := Rgt - Lft + 1 FROM $n WHERE Id = '".$this->Id."';

			DELETE FROM $n WHERE Lft BETWEEN @myLeft AND @myRight;

			UPDATE $n SET Lgt = Rgt - @myWidth WHERE Rgt > @myRight;
			UPDATE $n SET Lft = Lft - @myWidth WHERE Lft > @myRight;

		";
        $res = $db->exec($query);

        $db->exec("UNLOCK TABLES;");
        return $res;
    }


    function getFields(){
        /*$n = get_class($this);
        return CategoryField::getRepository()->findWhere("Category IN
					(SELECT parent.id
						FROM Category AS node,
						        Category AS parent
						WHERE node.lft BETWEEN parent.lft AND parent.rgt
						        AND node.id = '$this->Id'
						ORDER BY parent.lft)
				");*/
    }


}