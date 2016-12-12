<?php
/**
 * 一致性哈希的实现
 * hash 采用 md5
 * 使用虚拟节点
 * @author: phachon@163.com
 * Time: 17:12
 */
class Consistenthash {

	/**
	 * 真实节点集合
	 * @var array
	 */
	protected $_nodes = [];

	/**
	 * 虚拟节点集合
	 * @var array
	 */
	protected $_virtualNodes = [];

	/**
	 * 虚拟节点的个数
	 * @var int
	 */
	protected $_virtualNumber = 32;

	/**
	 * instance
	 * @var null
	 */
	protected static $_instance = NULL;

	/**
	 * @return Consistenthash|null
	 */
	public static function instance() {
		if(self::$_instance === NULL) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * hash 方法
	 * @param string $key
	 * @return string
	 */
	public function _hash($key) {
		return md5($key);
	}

	/**
	 * 增加单个节点
	 * @param string $node
	 * @return object
	 */
	public function addNode($node) {

		$hash = $this->_hash($node);

		$this->_nodes[$hash] = $node;

		for($i = 1; $i <= $this->_virtualNumber; $i++) {
			$virtualNode = $node. '#' .$i;
			$hashValue = $this->_hash($virtualNode);
			$this->_virtualNodes[$hashValue] = $node;
		}

		return $this;
	}

	/**
	 * 增加多个节点
	 * @param array $nodes
	 * @return object
	 */
	public function addNodes(array $nodes) {
		foreach ($nodes as $node) {
			$this->addNode($node);
		}

		return $this;
	}

	/**
	 * 移除单个节点
	 * @param string $node
	 * @return object
	 */
	public function removeNode($node) {

		$hash = $this->_hash($node);

		if(!isset($this->_nodes[$hash])) {
			return $this;
		}

		if(isset($this->_nodes[$hash])) {
			unset($this->_nodes[$hash]);
		}

		for($i = 1; $i <= $this->_virtualNumber; $i++) {
			$virtualNode = $node. '#' .$i;
			$hashValue = $this->_hash($virtualNode);
			if(isset($this->_virtualNodes[$hashValue])) {
				unset($this->_virtualNodes[$hashValue]);
			}
		}

		return $this;
	}

	/**
	 * 移除多个节点
	 * @param array $nodes
	 * @return object
	 */
	public function removeNodes(array $nodes) {
		foreach ($nodes as $node) {
			$this->removeNode($node);
		}

		return $this;
	}

	/**
	 * 根据 key 找到单个节点
	 * @param $key
	 * @return mixed
	 */
	public function getNode($key) {

		$hashKey = $this->_hash($key);
		//升序排列
		ksort($this->_virtualNodes);

		$findNode = '';
		foreach($this->_virtualNodes as $hashNode => $node) {
			if($hashNode > $hashKey) {
				$findNode = $node;
				break;
			}
		}

		if(!$findNode) {
			$findNode = $this->_virtualNodes[0];
		}

		return $findNode;
	}

	/**
	 * 获取所有的真实节点集合
	 */
	public function getAllNodes() {
		return $this->_nodes;
	}

	/**
	 * 获取所有的虚拟节点集合
	 */
	public function getAllVirtualNodes() {
		return $this->_virtualNodes;
	}
}

//测试
$instance = Consistenthash::instance()->addNodes(['server1', 'server2', 'server3']);
echo $instance->getNode('key1')."\r\n";
echo $instance->getNode('key2')."\r\n";
echo $instance->getNode('key3')."\r\n";
echo $instance->getNode('key4')."\r\n";
echo $instance->getNode('key5')."\r\n";
$instance->addNode('server4');
echo '----------------添加一个节点----------------'."\r\n";
echo $instance->getNode('key1')."\r\n";
echo $instance->getNode('key2')."\r\n";
echo $instance->getNode('key3')."\r\n";
echo $instance->getNode('key4')."\r\n";
echo $instance->getNode('key5')."\r\n";
echo $instance->getNode('key6')."\r\n";
echo $instance->getNode('key7')."\r\n";
echo $instance->getNode('key8')."\r\n";
$instance->addNode('server5');
echo '--------------添加一个节点------------------'."\r\n";
echo $instance->getNode('key1')."\r\n";
echo $instance->getNode('key2')."\r\n";
echo $instance->getNode('key3')."\r\n";
echo $instance->getNode('key4')."\r\n";
echo $instance->getNode('key5')."\r\n";
echo $instance->getNode('key6')."\r\n";
echo $instance->getNode('key7')."\r\n";
echo $instance->getNode('key8')."\r\n";
echo '--------------移除一个节点------------------'."\r\n";
$instance->removeNodes(['server1']);
echo $instance->getNode('key1')."\r\n";
echo $instance->getNode('key2')."\r\n";
echo $instance->getNode('key3')."\r\n";
echo $instance->getNode('key4')."\r\n";
echo $instance->getNode('key5')."\r\n";
echo $instance->getNode('key6')."\r\n";
echo $instance->getNode('key7')."\r\n";
echo $instance->getNode('key8')."\r\n";