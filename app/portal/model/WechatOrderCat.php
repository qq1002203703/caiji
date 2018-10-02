<?php
namespace app\portal\model;
class WechatOrderCat extends \core\Model
{
    public $table='wechat_order_cat';
	public $primaryKey = 'id';
	/*关系映射，每个映射最多有6个选项，最少需要提供前面4个选项，后面2个选项可以不提供
	* 第一项 string：self::HAS_ONE，self::HAS_MANY，self::BELONGS_TO中的一种
	* 第二项 string：数据表名
	* 第三项 string：对应本 $this->primaryKey 的字段名
	* 第四项 string：module name 模块名
	* 第五项 string：如果提供此项，会把此项作为一个属性保存到后面的对象中，传递$this作为它的值
	* 第六项 array：键名是本类中的一个方法名，值是方法的参数（多个参数用数组表示）；求影射表的值前会先执行这个方法
	*/
	public $relations = [
		'wechat_order'=>[self::HAS_MANY, 'wechat_order', 'order_id','wechat','',['func'=>'args']],
		'tags' => array(self::HAS_MANY, 'post_tag', 'post_id','portal'),
        'comments' => array(self::HAS_MANY, 'comment', 'post_id','portal'),
        'author' => array(self::BELONGS_TO, 'user', 'user_id','portal'),
        'category' => array(self::BELONGS_TO, 'category', 'category_id','portal'),
	];
}