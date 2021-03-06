<?php
/**
 * Menu(菜单管理)
 */
class NavAction extends AdminbaseAction {
	
	
	protected $nav;
	protected $navcat;
	
	function _initialize() {
		parent::_initialize();
		$this->nav = new NavModel();
		$this->navcat = new NavCatModel();
	}
	
	
	/**
	 *  显示菜单
	 */
	public function index() {
		
		if(empty($_REQUEST['cid'])){
			$navcat=$this->navcat->find();
			$cid=$navcat['navcid'];
		}else{
			$cid=$_REQUEST['cid'];
		}
		
		$result = $this->nav->where("cid=$cid")->order(array("listorder" => "ASC"))->select();
		import("Tree");
		$tree = new Tree();
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ', '&nbsp;&nbsp;&nbsp;├─ ', '&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		foreach ($result as $r) {
			$r['str_manage'] = '<a href="' . U("nav/add", array("parentid" => $r['id'],"cid"=>$r['cid'])) . '">添加子菜单</a> | <a href="' . U("nav/edit", array("id" => $r['id'],"parentid"=>$r['parentid'],"cid"=>$r['cid'])) . '">修改</a> | <a class="J_ajax_del" href="' . U("nav/delete", array("id" => $r['id'])) . '">删除</a> ';
			$r['status'] = $r['status'] ? "显示" : "不显示";
			$array[] = $r;
		}
	
		$tree->init($array);
		$str = "<tr>
				<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input'></td>
				<td>\$id</td>
				<td >\$spacer\$label</td>
			    <td>\$status</td>
				<td>\$str_manage</td>
			</tr>";
		$categorys = $tree->get_tree(0, $str);
		$this->assign("categorys", $categorys);
		
		$cats=$this->navcat->select();
		$this->assign("navcats",$cats);
		$this->assign("navcid",$cid);
		
		$this->display();
	}
	
	/**
	 *  添加
	 */
	public function add() {
		if (IS_POST) {
			
			if ($this->nav->create()) {
				$result=$this->nav->add();
				if ($result) {
					$this->success("添加成功！", U("nav/index"));
					$parentid=empty($_POST['parentid'])?"0":$_POST['parentid'];
					if(empty($parentid)){
						$data['path']="0";
					}else{
						$parent=$this->nav->where("id=$parentid")->find();
						
						$data['path']=$parent[path]."-$result";
					}
					$data['id']=$result;
					$this->nav->save($data);
				} else {
					$this->error("添加失败！");
				}
			} else {
				$this->error($this->nav->getError());
			}
		} else {
			$cid=$_REQUEST['cid'];
			$result = $this->nav->where("cid=$cid")->order(array("listorder" => "ASC"))->select();
			import("Tree");
			$tree = new Tree();
			$tree->icon = array('&nbsp;│ ', '&nbsp;├─ ', '&nbsp;└─ ');
			$tree->nbsp = '&nbsp;';
			$parentid=$this->_get("parentid");
			foreach ($result as $r) {
				$r['str_manage'] = '<a href="' . U("Menu/add", array("parentid" => $r['id'], "menuid" => $_GET['menuid'])) . '">添加子菜单</a> | <a href="' . U("Menu/edit", array("id" => $r['id'], "menuid" => $_GET['menuid'])) . '">修改</a> | <a class="J_ajax_del" href="' . U("Menu/delete", array("id" => $r['id'], "menuid" => $this->_get("menuid"))) . '">删除</a> ';
				$r['status'] = $r['status'] ? "显示" : "不显示";
				$r['selected'] = $r['id']==$parentid?"selected":"";
				$array[] = $r;
			}
			
			$tree->init($array);
			$str = "<tr>
				<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input'></td>
				<td>\$id</td>
				<td >\$spacer\$label</td>
			    <td>\$status</td>
				<td>\$str_manage</td>
			</tr>";
			$str="<option value='\$id' \$selected>\$spacer\$label</option>";
			$nav_trees = $tree->get_tree(0, $str);
			$this->assign("nav_trees", $nav_trees);
			
			
			$cats=$this->navcat->select();
			$this->assign("navcats",$cats);
			$this->assign("navcid",$cid);
			$this->display();
		}
	}
	
	
	
	/**
	 *  编辑
	 */
	public function edit() {
		if (IS_POST) {
			$parentid=empty($_POST['parentid'])?"0":$_POST['parentid'];
			if(empty($parentid)){
				$_POST['path']="0";
			}else{
				$parent=$this->nav->where("id=$parentid")->find();
			
				$_POST['path']=$parent[path]."-".$_POST['id'];
			}
			if ($this->nav->create()) {
				if ($this->nav->save($_POST)) {
					$this->success("保存成功！", U("nav/index"));
				} else {
					$this->error("保存失败！");
				}
			} else {
				$this->error($this->nav->getError());
			}
		} else {
			$cid=$_REQUEST['cid'];
			$id=$this->_get("id");
			$result = $this->nav->where("cid=$cid and id!=$id")->order(array("listorder" => "ASC"))->select();
			import("Tree");
			$tree = new Tree();
			$tree->icon = array('&nbsp;│ ', '&nbsp;├─ ', '&nbsp;└─ ');
			$tree->nbsp = '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
			$parentid=$this->_get("parentid");
			foreach ($result as $r) {
				$r['str_manage'] = '<a href="' . U("Menu/add", array("parentid" => $r['id'], "menuid" => $_GET['menuid'])) . '">添加子菜单</a> | <a href="' . U("Menu/edit", array("id" => $r['id'], "menuid" => $_GET['menuid'])) . '">修改</a> | <a class="J_ajax_del" href="' . U("Menu/delete", array("id" => $r['id'], "menuid" => $this->_get("menuid"))) . '">删除</a> ';
				$r['status'] = $r['status'] ? "显示" : "不显示";
				$r['selected'] = $r['id']==$parentid?"selected":"";
				$array[] = $r;
			}
				
			$tree->init($array);
			$str = "<tr>
				<td><input name='listorders[\$id]' type='text' size='3' value='\$listorder' class='input'></td>
				<td>\$id</td>
				<td >\$spacer\$label</td>
			    <td>\$status</td>
				<td>\$str_manage</td>
			</tr>";
			$str="<option value='\$id' \$selected>\$spacer\$label</option>";
			$nav_trees = $tree->get_tree(0, $str);
			$this->assign("nav_trees", $nav_trees);
				
				
			$cats=$this->navcat->select();
			$this->assign("navcats",$cats);
			
			
			
			$nav=$this->nav->where("id=$id")->find();
			$this->assign($nav);
			$this->assign("navcid",$cid);
			$this->display();
		}
	}
	
	/**
	 * 排序
	 */
	public function listorders() {
		$status = parent::listorders($this->nav);
		if ($status) {
			$this->success("排序更新成功！");
		} else {
			$this->error("排序更新失败！");
		}
	}
	
	/**
	 *  删除
	 */
	public function delete() {
		$id = (int) $this->_get("id");
		$count = $this->nav->where(array("parentid" => $id))->count();
		if ($count > 0) {
			$this->error("该菜单下还有子菜单，无法删除！");
		}
		if ($this->nav->delete($id)) {
			$this->success("删除菜单成功！");
		} else {
			$this->error("删除失败！");
		}
	}
	
}