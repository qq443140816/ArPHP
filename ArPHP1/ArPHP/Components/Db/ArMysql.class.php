<?php
/**
 * class Db default class PDO
 *
 * @author assnr <ycassnr@gmail.com>
 */

/**
 * abstract Db class.
 */
class ArDbMysql extends ArDb
{

    public $driverName = __CLASS__;
    public $lastSql = '';
    public $lastInsertId = '';
    public $allowGuessConditionOperator = true;

    protected $options = array(
            'columns' => '*',
            'table' => '',
            'join' => '',
            'where' => '',
            'group' => '',
            'having' => '',
            'order' => '',
            'limit' => '',
            'union' => '',
            'comment' => '',
        );

    static public function init($config = array(), $class = __CLASS__)
    {
        self::$config = $config;

        $defaultDbconfig = self::$config['read']['default'];

        if (empty(self::$readConnections['default']))
            self::$readConnections['default'] = new self($defaultDbconfig);

        return self::$readConnections['default'];

    }

    private function query($sql = '')
    {
        static $i = array();
        if (empty($sql))
            $sql = $this->buildSelectSql();

        $sqlCmd = strtoupper(substr($sql, 0, 6));

        if(in_array($sqlCmd, array('UPDATE', 'DELETE')) && stripos($sql, 'where') === false)
            throw new ArDbException('no WHERE condition in SQL(UPDATE, DELETE) to be executed! please make sure it\'s safe', 42005);

        $this->lastSql = $sql;
        $this->_pdoStatement = $this->_pdo->query($sql);
        $i[] = $this->_pdoStatement;
        // if (count($i) == 2)
            // var_dump($i[0] ,$i[1]);
        return $this->_pdoStatement;

    }

    public function getColumns()
    {
        $table = $this->options['table'];

        $sql = 'show columns from ' . $table;

        $ret = $this->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $columns = array();

        foreach ($ret as $value) :
            $columns[] = $value['Field'];
        endforeach;

        return $columns;

    }

    public function count()
    {
        $result = $this->select(array('COUNT(\'*\') as t'))->queryRow();
        if (empty($result))
            $total = 0;
        else
            $total = (int)$result['t'];
        return $total;

    }

    public function queryRow()
    {
        $this->limit(1);
        return $this->query()->fetch(PDO::FETCH_ASSOC);

    }

    public function queryAll()
    {
        return $this->query()->fetchAll(PDO::FETCH_ASSOC);

    }

    public function setSource($source)
    {
        $this->options['source'] = $source;
        return $this;

    }

    public function insert(array $data = array(), $checkData = false)
    {
        if (ArModel::model($this->options['source'])->insertCheck($data)) :

            $data = ArModel::model($this->options['source'])->formatData($data);

            if (!empty($data)) :

                if ($checkData)
                    $data = arComp('format.format')->filterKey($this->getColumns(), $data);

                $this->data($data);

            endif;

            $sql = $this->bulidInsertSql();
            $this->exec($sql);
            return $this->lastInsertId = $this->_pdo->lastInsertId();

        endif;

        return false;

    }

    public function update(array $data = array())
    {
        if (!empty($data))
            $this->columns($data);

        $sql = $this->bulidUpdateSql();
        return $this->exec($sql);

    }

    public function delete()
    {
        $sql = $this->buildDeleteSql();
        if (!preg_match('/ WHERE /i', $sql))
            throw new ArDbException('bad sql condition , where must to be infix');
        return $this->exec($sql);

    }

    private function exec($sql)
    {
        $this->lastSql = $sql;
        return $this->_pdo->exec($sql);

    }

    protected function quote($data)
    {
        if (is_array( $data ) || is_object( $data )) {
            $return = array ();
            foreach ( $data as $k => $v ) {
                $return [$k] = $this->quote ( $v );
            }
            return $return;
        } else {
            $data = $this->_pdo->quote ( $data );
            if (false === $data)
                $data = "''";
            return $data;
        }

    }

    public function select($fields = '')
    {
        if(is_string($fields) && strpos($fields, ',')) {
            $fields = explode(',', $fields);
        }
        if(is_array($fields)) {
            $array   =  array();
            foreach ($fields as $key=>$field){
                if(!is_numeric($key))
                    $array[] =  $this->quoteObj($key).' AS '.$this->quoteObj($field);
                else
                    $array[] =  $this->quoteObj($field);
            }
            $fieldsStr = implode(',', $array);
        } elseif (is_string($fields) && !empty($fields)) {
            $fieldsStr = $this->quoteObj($fields);
        } else {
            $fieldsStr = '*';
        }

        $this->options['columns'] = $fieldsStr;
        return $this;

    }

    public function table($table)
    {
        $this->options['table'] = $this->quoteObj($this->currentConfig['prefix'] . $table);
        return $this;

    }

    public function join($table, $cond)
    {
        return $this->joinInternal('JOIN', $table, $cond);

    }

    public function leftJoin($table, $cond)
    {
        return $this->joinInternal('LEFT JOIN', $table, $cond);

    }

    public function rightJoin($table, $cond)
    {
        return $this->joinInternal('RIGHT JOIN', $table, $cond);

    }

    protected function joinInternal($join, $table, $cond)
    {
        $table = $this->quoteObj($table);
        $this->options['join'] .= " $join $table ";
        if (is_string($cond)
        && (strpos($cond, '=') === false && strpos($cond, '<') === false && strpos($cond, '>') === false))
        {
            $column = $this->quoteObj($cond);
            $this->options['join'] .= " USING ($column) ";
        } else {
            $cond = $this->buildCondition($cond);
            $this->options['join'] .= " ON $cond ";
        }
        return $this;
    }

    public function quoteObj($objName) {
        if (is_array ( $objName )) {
            $return = array ();
            foreach ( $objName as $k => $v ) {
                $return[] = $this->quoteObj($v);
            }
            return $return;
        } else {
            $v = trim($objName);
            $v = str_replace('`', '', $v);
            $v = preg_replace('# +AS +| +#i', ' ', $v);
            $v = explode(' ', $v);
            foreach($v as $k_1=>$v_1) {
                $v_1 = trim($v_1);
                if($v_1 == '')
                {
                    unset($v[$k_1]);
                    continue;
                }
                if(strpos($v_1, '.'))
                {
                    $v_1 = explode('.', $v_1);
                    foreach($v_1 as $k_2 => $v_2)
                    {
                        $v_1[$k_2] = '`'.trim($v_2).'`';
                    }
                    $v[$k_1] = implode('.', $v_1);
                }
                elseif (preg_match('#\(.+\)#', $v_1)) {
                    $v[$k_1] = $v_1;
                }
                else
                {
                   $v[$k_1] = '`'.$v_1.'`';
                }
            }
            $v = implode(' AS ', $v);
            return $v;
        }
    }


    public function group($group)
    {
        $this->options['group'] = empty($group) ? '' : ' GROUP BY ' . $group;
        return $this;

    }

    public function having($having)
    {
        $this->options['having'] = empty($having) ? '' : ' HAVING ' . $having;
        return $this;

    }

    public function where($conditions = '')
    {
        $conStr = $this->buildCondition($conditions);
        $this->options['where'] = empty($conStr) ? '' : ' WHERE ' . $conStr;
        return $this;

    }

    public function order($order)
    {
        $this->options['order'] = empty($order) ? '' : ' ORDER BY ' . $order;
        return $this;

    }

    public function limit($limit)
    {
        $this->options['limit'] = empty($limit) ? '' : ' LIMIT ' . $limit;
        return $this;

    }

    public function union($union)
    {

    }

    public function columns($data)
    {
        $setStr = '';
        if (is_string($data)) :
            $setStr = $data;
        elseif (is_array($data)) :
            foreach ($data as $key => $val) :
                $set[] = $this->quoteObj($key) . '=' . $this->quote($val);
            endforeach;
            $setStr = implode(',', $set);
        endif;
        $this->options['set'] = ' SET ' . $setStr;

        return $this;

    }

    public function data(array $data)
    {
        $values  =  $fields    = array();
        foreach ($data as $key => $val) {
            if(is_scalar($val) || is_null($val)) {
                $fields[] = $this->quoteObj($key);
                $values[] = $this->quote($val);
            }
        }
        $this->options['data'] = '(' . implode($fields, ',') . ') VALUES (' . implode($values, ',') . ')';
        return $this;
    }

    public function buildCondition($condition = array(), $logic = 'AND')
    {
        if( ! is_array($condition))
        {
            if (is_string($condition))
            {
                //forbid to use a CONSTANT as condition
                $count = preg_match('#\>|\<|\=| #', $condition, $logic);
                if(!$count)
                {
                    throw new ArDbException('bad sql condition: must be a valid sql condition');
                }
                $condition = explode($logic[0], $condition);
                $condition[0] = $this->quoteObj($condition[0]);
                $condition = implode($logic[0], $condition);
                return $condition;
            }

            throw new ArDbException('bad sql condition: ' . gettype($condition));
        }
        $logic = strtoupper($logic);
        $content = null;
        foreach ($condition as $k => $v)
        {
            $v_str = null;
            $v_connect = '';

            if (is_int($k))
            {
                //default logic is always 'AND'
                if ($content)
                    $content .= $logic . ' (' . $this->buildCondition($v) . ') ';
                else
                    $content = '(' . $this->buildCondition($v) . ') ';
                continue;
            }

            $k = trim($k);

            $maybe_logic = strtoupper($k);
            if (in_array($maybe_logic, array('AND', 'OR')))
            {
                if ($content)
                    $content .= $logic . ' (' . $this->buildCondition($v, $maybe_logic) . ') ';
                else
                    $content = '(' . $this->buildCondition($v, $maybe_logic) . ') ';
                continue;
            }

            $k_upper = strtoupper($k);
            //the order is important, longer fist, to make the first break correct.
            $maybe_connectors = array('>=', '<=', '<>', '!=', '>', '<', '=',
                    ' NOT BETWEEN', ' BETWEEN', 'NOT LIKE', ' LIKE', ' IS NOT', ' NOT IN', ' IS', ' IN');
            foreach ($maybe_connectors as $maybe_connector)
            {
                $l = strlen($maybe_connector);
                if (substr($k_upper, -$l) == $maybe_connector)
                {
                    $k = trim(substr($k, 0, -$l));
                    $v_connect = $maybe_connector;
                    break;
                }
            }
            if (is_null($v))
            {
                $v_str = ' NULL';
                if( $v_connect == '') {
                    $v_connect = 'IS';
                }
            }
            else if (is_array($v))
            {
                if($v_connect == ' BETWEEN') {
                    $v_str = $this->quote($v[0]) . ' AND ' . $this->quote($v[1]);
                }
                else if ( is_array($v) && ! empty($v) ) {
                    // 'key' => array(v1, v2)
                    $v_str = null;
                    foreach ($v AS $one)
                    {
                        if(is_array($one)) {
                            // (a,b) in ( (c, d), (e, f) )
                            $sub_items = '';
                            foreach($one as $sub_value) {
                                $sub_items .= ',' . $this->quote($sub_value);
                            }
                            $v_str .= ',(' . substr($sub_items, 1) . ')' ;
                        } else {
                            $v_str .= ',' . $this->quote($one);
                        }
                    }
                    $v_str = '(' . substr($v_str, 1) . ')';
                    if (empty($v_connect)) {
                        if($this->allowGuessConditionOperator === null || $this->allowGuessConditionOperator === true)
                        {
                            // if($this->allowGuessConditionOperator === null)
                                // Log::instance()->log("guessing condition operator is not allowed: use '$k IN'=>array(...)", array('type'=>E_WARNING));

                            $v_connect = 'IN';
                        }
                        else
                            throw new ArDbException("guessing condition operator is not allowed: use '$k IN'=>array(...)");
                    }
                }
                else if (empty($v)) {
                    // 'key' => array()
                    $v_str = $k;
                    $v_connect = '<>';
                }
            }
            else {
                $v_str = $this->quote($v);
            }

            if(empty($v_connect))
                $v_connect = '=';

            $quoted_k = $this->quoteObj($k);
            if ($content)
                $content .= " $logic ( $quoted_k $v_connect $v_str ) ";
            else
                $content = " ($quoted_k $v_connect $v_str) ";
        }

        return $content;

    }

    protected function buildSelectSql()
    {
         $sql   = str_replace(
            array('%TABLE%','%COLUMNS%','%JOIN%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%','%UNION%','%COMMENT%'),
            array(
                $this->options['table'],
                $this->options['columns'],
                $this->options['join'],
                $this->options['where'],
                $this->options['group'],
                $this->options['having'],
                $this->options['order'],
                $this->options['limit'],
                $this->options['union'],
                $this->options['comment']
            ),
            'SELECT %COLUMNS% FROM %TABLE%%JOIN%%WHERE%%GROUP%%HAVING%%ORDER%%LIMIT% %UNION%%COMMENT%'
        );

        return $sql;

    }

    protected function bulidUpdateSql()
    {
        $sql   = str_replace(
            array('%TABLE%','%SET%','%WHERE%','%COMMENT%'),
            array(
                $this->options['table'],
                $this->options['set'],
                $this->options['where'],
                $this->options['comment']
            ),
            'UPDATE %TABLE%%SET%%WHERE%%COMMENT%'
        );

        return $sql;

    }

    protected function bulidInsertSql()
    {
        $sql = str_replace(
            array('%TABLE%','%DATA%','%COMMENT%'),
            array(
                $this->options['table'],
                $this->options['data'],
                $this->options['comment']
            ),
            'INSERT INTO %TABLE%%DATA%%COMMENT%'
        );

        return $sql;

    }

    public function buildDeleteSql($options=array()) {
        $sql = str_replace(
            array('%TABLE%', '%WHERE%', '%COMMENT%'),
            array(
                $this->options['table'],
                $this->options['where'],
                $this->options['comment']
            ),
            'DELETE FROM %TABLE%%WHERE%%COMMENT%'
        );

        return $sql;

    }


    public function __toString()
    {
        return var_export(get_class_methods(__CLASS__), 1);

    }

}
