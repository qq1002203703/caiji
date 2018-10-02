<?php
/**
 * 网站专业定制：网站、微信公众号、小程序等一站式开发
 *
 * QQ 46502166
 *
 * @author: LaoYang
 * @email: 46502166@qq.com
 * @link:  http://dahulu.cc
 * @date: 2018/6/9
 * @time: 10:39
 */
// ------------------------------------------------------------------------

namespace core;
use core\lib\tree\Node;

/**
* 通用树类
* 代码大部分来自：https://packagist.org/packages/bluem/tree ，小部分后来添加和修改
*/
class Tree implements \JsonSerializable
{
	/**
	* 树结构格式化成字符串时用到
	* @var array
	*/
	public $icon = ['│', '├─', '└─'];
	/**
	* 树结构格式化成字符串时用到
	* @var string
	*/
    public $nbsp = '&nbsp;';
	
	/**
	* 当前节点的样式
	* @var string
	*/
	public $curentStyle='curent';
    /**
     * API version (will always be in sync with first digit of release version number).
     *
     * @var int
     */
    const API = 2;
    /**
     * @var int|float|string
     */
    protected $rootId = 0;
    /**
     * @var string
     */
    protected $idKey = 'id';
    /**
     * @var string
     */
    protected $parentKey = 'parent';
    /**
     * @var Node[]
     */
    protected $nodes;
    /**
     * @param array|\Traversable $data    The data for the tree (iterable)
     * @param array              $options 0 or more of the following keys: "rootId" (ID of the root node, defaults to 0), "id"
     *                                    (name of the ID field / array key, defaults to "id"), "parent", (name of the parent
     *                                    ID field / array key, defaults to "parent")
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function __construct($data = [], array $options = [])
    {
        $options = array_change_key_case($options, CASE_LOWER);
        if (isset($options['rootid'])) {
            if (!is_scalar($options['rootid'])) {
                throw new \Exception('设置项 “rootid” 必须是一个标量');
            }
            $this->rootId = $options['rootid'];
        }
        if (!empty($options['id'])) {
            if (!is_string($options['id'])) {
                throw new \InvalidArgumentException('设置项 “id” 必须是一个字符串');
            }
            $this->idKey = $options['id'];
        }
        if (!empty($options['parent'])) {
            if (!is_string($options['parent'])) {
                throw new \InvalidArgumentException('设置项 “parent” 必须是一个字符串');
            }
            $this->parentKey = $options['parent'];
        }
        $this->build($data);
    }
    /**
     * @param array $data
     *
     * @throws \RuntimeException
     */
    public function rebuildWithData(array $data)
    {
        $this->build($data);
    }
    /**
     * Returns a flat, sorted array of all node objects in the tree.
     * 返回所有节点
	 * @param $is_all2array bool:返回数组的二级数组类型由这个参数确定，如果为true时，二级数组也是数组；为fals时，二级数组是Node类型
     *
     * @return Node[] Nodes, sorted as if the tree was hierarchical,
     *                i.e.: the first level 1 item, then the children of
     *                the first level 1 item (and their children), then
     *                the second level 1 item and so on.
     */
    public function getNodes(): array
    {
		$nodes_tmp=$this->nodes[$this->rootId]->getDescendants();
        return array_values($nodes_tmp);
    }
    /**
     * Returns a single node from the tree, identified by its ID.
     *
     * @param int|string $id Node ID
     *
     * @throws \InvalidArgumentException
     *
     * @return Node
     */
    public function getNodeById($id): Node
    {
        if (empty($this->nodes[$id])) {
            throw new \InvalidArgumentException("无效的节点id：{$id}");
        }
        return $this->nodes[$id];
    }
    /**
     * Returns an array of all nodes in the root level.
	 * 返回所有顶级父节点
	 * @param bool $is_toarray :返回数组的二级数组类型由这个参数确定，如果为true时，二级数组也是数组；为fals时，二级数组是Node类型
     *
     * @return array,Node[] Nodes in the correct order
     */
    public function getRootNodes($is_toarray=false): array
    {
		$nodes=$this->nodes[$this->rootId]->getChildren();
		if($is_toarray){
			if($nodes && is_array($nodes)){
				foreach($nodes as $k => $v){
					$nodes[$k]=$v->toArray();
				}
			}
		}
        return $nodes;
    }
    /**
     * Returns the first node for which a specific property's values of all ancestors
     * and the node are equal to the values in the given argument.
     *
     * Example: If nodes have property "name", and on the root level there is a node with
     * name "A" which has a child with name "B" which has a child which has node "C", you
     * would get the latter one by invoking getNodeByValuePath('name', ['A', 'B', 'C']).
     * Comparison is case-sensitive and type-safe.
     *
     * @param string $name
     * @param array  $search
     *
     * @return Node|null
     */
    public function getNodeByValuePath($name, array $search)
    {
        $findNested = function (array $nodes, array $tokens) use ($name, &$findNested) {
            $token = array_shift($tokens);
            foreach ($nodes as $node) {
                $nodeName = $node->get($name);
                if ($nodeName === $token) {
                    // Match
                    if (\count($tokens)) {
                        // Search next level
                        return $findNested($node->getChildren(), $tokens);
                    }
                    // We found the node we were looking for
                    return $node;
                }
            }
            return null;
        };
        return $findNested($this->getRootNodes(), $search);
    }
    /**
     * Core method for creating the tree.
     *
     * @param array|\Traversable $data The data from which to generate the tree
     *
     * @throws \RuntimeException
     */
    private function build($data)
    {
        if (!\is_array($data) && !($data instanceof \Traversable)) {
			//Data must be an iterable (array or implement Traversable)
            throw new \RuntimeException('传入的参数类型不正确，必须是一个可迭代的数据类型 (数组或可遍历的对象)');
        }
        $this->nodes = [];
        $children = [];
        // Create the root node
        $this->nodes[$this->rootId] = $this->createNode($this->rootId, null, []);
        foreach ($data as $row) {
            if ($row instanceof \Iterator) {
                $row = iterator_to_array($row);
            }
            $this->nodes[$row[$this->idKey]] = $this->createNode(
                $row[$this->idKey],
                $row[$this->parentKey],
                $row
            );
            if (empty($children[$row[$this->parentKey]])) {
                $children[$row[$this->parentKey]] = [$row[$this->idKey]];
            } else {
                $children[$row[$this->parentKey]][] = $row[$this->idKey];
            }
        }
        foreach ($children as $pid => $childIds) {
            foreach ($childIds as $id) {
                if ((string) $pid === (string) $id) {
                    throw new \RuntimeException(
                        //"Node with ID $id references its own ID as parent ID"
						"节点(ID:{$id}) 跟它的父ID相同了"
                    );
                }
                if (isset($this->nodes[$pid])) {
                    $this->nodes[$pid]->addChild($this->nodes[$id]);
                } else {
                    throw new \RuntimeException(
                        //"Node with ID $id points to non-existent parent with ID $pid"
						"节点(ID:{$id})指向一个不存在的父ID(pid:{$pid})"
                    );
                }
            }
        }
    }


	/**
	* 返回节点的数组形式的树型结构
	*
	* param $node Node|null:一个实例化的Node,没有提供就返回全部树节点，如果提供只返回这部分节点的子节点（不包括自身）
	* param $curent_id string|int:当前id
	* @return array
	*/
	public function getTreeArray(Node $node=null,$curent_id=1,$level=0):array
	{
		$nodes=\is_object($node) ? $node->getChildren() : $this->getRootNodes();
		$arr=[];
		foreach($nodes as $k=>$v){
			$arr[$k]=$v->toArray();
			$arr[$k]['level']=$v->getLevel()-$level;
			$arr[$k]['curent']=($v->getId()==$curent_id)?$this->curentStyle:'';
			if($v->hasChildren()){
				$arr[$k]['children']=$this->getTreeArray($v,$curent_id,$level);
			}
		}
		return $arr;
	}

	/**
	* 返回指定id节点的数组形式的树型结构
	*
	* param $id int|string:节点id
	* param $is_self bool:是否包括自身，为true时返回自身和它包含的后代节点，如果为false只返回它的后代节点
	* param $curent_id string|int:当前id
	* @return array
	*/
	public function getTreeByid($id,$is_self=true,$curent_id=null)
	{
		$nodes=$this->getNodeByid($id);
		$curent_id=$curent_id ?? ($nodes->getId());
		$level=$nodes->getLevel()-1;
		$arr=[];
		if($is_self){
			$arr=$nodes->toArray();
			$arr['level']=$level;
			$arr['curent']=($curent_id)?'':$this->curentStyle;
			$arr['children']=$this->getTreeArray($nodes,$curent_id,$level);
		}else{
			$arr=$this->getTreeArray($nodes,$curent_id,$level-1);
		}
		return $arr;
	}



    /**
     * Returns a textual representation of the tree.
	 * 返回树的文本表示形式
     *
     * @return string
     */
    public function __toString()
    {
        $str = [];
        foreach ($this->getNodes() as $node) {
            $indent1st = str_repeat('  ', $node->getLevel() - 1).'- ';
            $indent = str_repeat('  ', ($node->getLevel() - 1) + 2);
            $node = (string) $node;
            $str[] = $indent1st.str_replace("\n", "$indent\n  ", $node);
        }
        return implode("\n", $str);
    }

	/**
	* 得到格式化的字符串类型的树型结构
	* 
	* param $id int|string:节点id，如果提供的是rootId,将得到全部节点的树型结构，否则得到对应节点的树型结构
	* param $is_self bool:是否包括自身，$id不是rootId时，如果本项为true时，则返回自身和它包含的后代节点，为false只返回它的后代节点
	* param $repeat_name string:需要在此节点属性名/字段名前，打上 "|--"、“--”之类的字符
	* param $output array:需要输出的节点属性名/字段名
	* param $format string:对应$output中的属性名的文本格式化输出，还可以加上'%select%'用来被后面的$select['text']格式化替换
	* param $select array:选中项，数组中要包含两个值，选中id和选中时的文本输出text
	* @return string
	*/
	public function getTree($id,$is_self=true,$repeat_name='title',$output=['id','title'],$format='<option value="%id%" title="%title%" %select%>%__repeat_content__%</option>',$select=['id'=>1,'text'=>' selected="selected"'])
	{
		$str = [];
		if($id==$this->rootId){
			$nodes=$this->getNodes();
			$level=0;
		}else{
			$node_tem=$this->getNodeByid($id);
			if($is_self){
				$nodes=$node_tem->getDescendantsAndSelf();
				$level=$node_tem->getLevel()-1;
			}else{
				$nodes=$node_tem->getDescendants();
				$level=$node_tem->getLevel();
			}
			unset($node_tem);
		}

		$search=[];
		foreach($output as $v){
			$search[]='%'.$v.'%';
		}
		$search[]='%select%';
        $search[]='%__repeat_content__%';
        foreach ($nodes as $node) {
			$now_level=$node->getLevel()-$level;
			$indent=($now_level==1)?$this->icon[1]: $this->icon[2];
			$repeat=str_repeat($this->nbsp, $now_level*2);
            $repeat_content='';
			$out=[];
			foreach($output as $k=>$v){
				$out[$k]=$node->get($v);
                if($v==$repeat_name) {
                    $repeat_content=$repeat.$indent.$out[$k] ;
                }
			}
			$out[]=($select && $select['id']==$node->get($this->idKey))?$select['text']:'';
            $out[]=$repeat_content;
			$str[]=str_replace($search,$out,$format);
        }
        return implode("\n", $str);
	}

	public function getTreeAny($nodeArray,$level,$repeat_name='title',$output=['id','title'],$format='<option value="%id%" title="%title%" %select%>%__repeat_content__%</option>',$select=['id'=>1,'text'=>' selected="selected"']){
        $str = [];
        $search=[];
        foreach($output as $v){
            $search[]='%'.$v.'%';
        }
        $search[]='%select%';
        $search[]='%__repeat_content__%';
        foreach ($nodeArray as $node) {
            $now_level=$node->getLevel()-$level;
            $indent=($now_level==1)?$this->icon[1]: $this->icon[2];
            $repeat=str_repeat($this->nbsp, $now_level*2);
            $repeat_content='';
            $out=[];
            foreach($output as $k=>$v){
                $out[$k]=$node->get($v);
                if($v==$repeat_name) {
                    $repeat_content=$repeat.$indent.$out[$k] ;
                }
            }
            $out[]=($select && $select['id']==$node->get($this->idKey))?$select['text']:'';
            $out[]=$repeat_content;
            $str[]=str_replace($search,$out,$format);
        }
        return implode("\n", $str);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return json_encode($this->getTreeArray());
    }
    /**
     * Creates and returns a node with the given properties.
     *
     * Can be overridden by subclasses to use a Node subclass for nodes.
     *
     * @param string|int $id
     * @param string|int $parent
     * @param array      $properties
     *
     * @return Node
     */
    protected function createNode($id, $parent, array $properties): Node
    {
        return new Node($id, $parent, $properties);
    }

}