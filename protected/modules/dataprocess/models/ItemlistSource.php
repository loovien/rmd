<?php
/**
 * FileName: ItemlistSource.php
 * Description: Itemlist资源我呢件
 * Author: Bigpao
 * Email: bigpao.luo@gmail.com
 * HomePage: 
 * Version: 0.0.1
 * LastChange: 2015-04-03 09:47:24
 * History:
 */

class ItemlistSource extends CSVSource
{
    /**
     * itemlist schema config path
     */
    protected $_schemaPath = "data/schemaItemlist.php";

    /**
     * 根据用户条件合并成的tag分割符号
     */
    const TAG_SEPARATE = "#";

    /**
     * 对itemlist数据进行分类
     * @return array $result
     */
    public function category()
    {
        /* 容器 */
        $categoryItems = array();
        if(empty($this -> _validData))
            throw new Exception('please download csv and read csv first');
        foreach($this -> _validData as $key => $value)
        {
            $result = $this -> itemFilter($value);
            $categoryItems[$result['key']][] = $result['value'];
        }
        return $categoryItems;
    }

    /**
     * 根据用户自定义条件分类
     * @param array $item 单行csv记录的数组
     * @return array $result
     */
    protected function itemFilter($item)
    {
        $itemlistCategoryConfig = Yii::app() -> params['itemlistCategory'];
        $condtions = array();
        $conditionKey = '';
        foreach($itemlistCategoryConfig['fields'] as $key => $value)
        {
            // 取出该商品的类别值做为分类的键名
            if(isset($value['conditions']))
            {
                $conditionKey .= call_user_func(array($this,$value['mapper']),array('conditions' => $value['conditions'],'value' => $item[$key])) . self::TAG_SEPARATE;
            }
            else
            {
                if(empty($item[$key])) continue;
                $conditionKey .= $item[$key] . self::TAG_SEPARATE;
            }
        }

        $conditions['key'] = trim($conditionKey,self::TAG_SEPARATE);
        $conditions['value'] = $item;
        return $conditions;
    }

    /**
     * 根据价格定义该商品在那个区间
     * @param array $args 区间参数和需要比较的值
     * @return string 返回区间的值
     */
    public function priceRange($args)
    {
        foreach($args['conditions'] as $key => $value)
        {
            if($args['value'] <= $value)
                return $key;
            else
            {
                unset($args['conditions'][$key]);
                return $key = $this -> priceRange($args);
            }
        }
    }


    /**
     * 根据价格定义该商品在那个区间
     * @param array $args 区间参数和需要比较的值
     * @return string 返回区间的值
     */
    public function trimSpace($args)
    {
        var_dump($args);
    }
}