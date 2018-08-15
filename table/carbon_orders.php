<?php
namespace Table;


use CarbonPHP\Database;
use CarbonPHP\Entities;
use CarbonPHP\Interfaces\iRest;

class carbon_orders extends Entities implements iRest
{
    const PRIMARY = [
    
    ];

    const COLUMNS = [
    'order_id','order_session','order_total','order_items','order_start','order_costumer','order_server','order_finish','order_chef','order_notes','order_tip',
    ];

    const VALIDATION = [];

    const BINARY = [
    'order_id','order_costumer','order_server','order_chef',
    ];

    /**
     * @param array $return
     * @param string|null $primary
     * @param array $argv
     * @return bool
     */
    public static function Get(array &$return, string $primary = null, array $argv) : bool
    {
        $get = isset($argv['select']) ? $argv['select'] : self::COLUMNS;
        $where = isset($argv['where']) ? $argv['where'] : [];

        $group = $sql = '';

        if (isset($argv['pagination'])) {
            if (!empty($argv['pagination']) && !is_array($argv['pagination'])) {
                $argv['pagination'] = json_decode($argv['pagination'], true);
            }
            if (isset($argv['pagination']['limit']) && $argv['pagination']['limit'] != null) {
                $limit = ' LIMIT ' . $argv['pagination']['limit'];
            } else {
                $limit = '';
            }

            $order = '';
            if (!empty($limit)) {

                 $order = ' ORDER BY ';

                if (isset($argv['pagination']['order']) && $argv['pagination']['order'] != null) {
                    if (is_array($argv['pagination']['order'])) {
                        foreach ($argv['pagination']['order'] as $item => $sort) {
                            $order .= $item .' '. $sort;
                        }
                    } else {
                        $order .= $argv['pagination']['order'];
                    }
                } else {
                    $order .= self::PRIMARY[0] . ' ASC';
                }
            }
            $limit = $order .' '. $limit;
        } else {
            $limit = ' ORDER BY ' . self::PRIMARY[0] . ' ASC LIMIT 100';
        }

        foreach($get as $key => $column){
            if (!empty($sql)) {
                $sql .= ', ';
                $group .= ', ';
            }
            if (in_array($column, self::BINARY)) {
                $sql .= "HEX($column) as $column";
                $group .= "$column";
            } else {
                $sql .= $column;
                $group .= $column;
            }
        }

        if (isset($argv['aggregate']) && (is_array($argv['aggregate']) || $argv['aggregate'] = json_decode($argv['aggregate'], true))) {
            foreach($argv['aggregate'] as $key => $value){
                switch ($key){
                    case 'count':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "COUNT($value) AS count ";
                        break;
                    case 'AVG':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "AVG($value) AS avg ";
                        break;
                    case 'MIN':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "MIN($value) AS min ";
                        break;
                    case 'MAX':
                        if (!empty($sql)) {
                            $sql .= ', ';
                        }
                        $sql .= "MAX($value) AS max ";
                        break;
                }
            }
        }

        $sql = 'SELECT ' .  $sql . ' FROM RootPrerogative.carbon_orders';

        $pdo = Database::database();

        if (empty($primary)) {
            if (!empty($where)) {
                $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                    $sql = '(';
                    foreach ($set as $column => $value) {
                        if (is_array($value)) {
                            $sql .= $build_where($value, $join === 'AND' ? 'OR' : 'AND');
                        } else {
                            if (in_array($column, self::BINARY)) {
                                $sql .= "($column = UNHEX(" . $pdo->quote($value) . ")) $join ";
                            } else {
                                $sql .= "($column = " . $pdo->quote($value) . ") $join ";
                            }
                        }
                    }
                    return substr($sql, 0, strlen($sql) - (strlen($join) + 1)) . ')';
                };
                $sql .= ' WHERE ' . $build_where($where);
            }
        } 

        if (isset($argv['aggregate'])) {
            $sql .= ' GROUP BY ' . $group . ' ';
        }

        $sql .= $limit;

        $return = self::fetch($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::COLUMNS
        */

        

        return true;
    }

    /**
    * @param array $argv
    * @return bool|mixed
    */
    public static function Post(array $argv)
    {
        $sql = 'INSERT INTO RootPrerogative.carbon_orders (order_id, order_session, order_total, order_items, order_start, order_costumer, order_server, order_finish, order_chef, order_notes, order_tip) VALUES ( UNHEX(:order_id), :order_session, :order_total, :order_items, :order_start, UNHEX(:order_costumer), UNHEX(:order_server), :order_finish, UNHEX(:order_chef), :order_notes, :order_tip)';
        $stmt = Database::database()->prepare($sql);

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

            
                $order_id = $argv['order_id'];
                $stmt->bindParam(':order_id',$order_id, 2, 16);
                    
                $order_session = isset($argv['order_session']) ? $argv['order_session'] : null;
                $stmt->bindParam(':order_session',$order_session, 2, 225);
                    
                $order_total = isset($argv['order_total']) ? $argv['order_total'] : null;
                $stmt->bindParam(':order_total',$order_total, 2, 11);
                    $stmt->bindValue(':order_items',$argv['order_items'], 2);
                    
                $order_start = isset($argv['order_start']) ? $argv['order_start'] : null;
                $stmt->bindParam(':order_start',$order_start, 2, 40);
                    
                $order_costumer = isset($argv['order_costumer']) ? $argv['order_costumer'] : null;
                $stmt->bindParam(':order_costumer',$order_costumer, 2, 16);
                    
                $order_server = isset($argv['order_server']) ? $argv['order_server'] : null;
                $stmt->bindParam(':order_server',$order_server, 2, 16);
                    
                $order_finish = isset($argv['order_finish']) ? $argv['order_finish'] : null;
                $stmt->bindParam(':order_finish',$order_finish, 2, 225);
                    
                $order_chef = isset($argv['order_chef']) ? $argv['order_chef'] : null;
                $stmt->bindParam(':order_chef',$order_chef, 2, 16);
                    
                $order_notes = isset($argv['order_notes']) ? $argv['order_notes'] : null;
                $stmt->bindParam(':order_notes',$order_notes, 2, 225);
                    $stmt->bindValue(':order_tip',isset($argv['order_tip']) ? $argv['order_tip'] : null, 2);
        

        return $stmt->execute();
    }

    /**
    * @param array $return
    * @param string $primary
    * @param array $argv
    * @return bool
    */
    public static function Put(array &$return, string $primary, array $argv) : bool
    {
        if (empty($primary)) {
            return false;
        }

        foreach ($argv as $key => $value) {
            if (!in_array($key, self::COLUMNS)){
                unset($argv[$key]);
            }
        }

        $sql = 'UPDATE RootPrerogative.carbon_orders ';

        $sql .= ' SET ';        // my editor yells at me if I don't separate this from the above stmt

        $set = '';

        if (!empty($argv['order_id'])) {
            $set .= 'order_id=UNHEX(:order_id),';
        }
        if (!empty($argv['order_session'])) {
            $set .= 'order_session=:order_session,';
        }
        if (!empty($argv['order_total'])) {
            $set .= 'order_total=:order_total,';
        }
        if (!empty($argv['order_items'])) {
            $set .= 'order_items=:order_items,';
        }
        if (!empty($argv['order_start'])) {
            $set .= 'order_start=:order_start,';
        }
        if (!empty($argv['order_costumer'])) {
            $set .= 'order_costumer=UNHEX(:order_costumer),';
        }
        if (!empty($argv['order_server'])) {
            $set .= 'order_server=UNHEX(:order_server),';
        }
        if (!empty($argv['order_finish'])) {
            $set .= 'order_finish=:order_finish,';
        }
        if (!empty($argv['order_chef'])) {
            $set .= 'order_chef=UNHEX(:order_chef),';
        }
        if (!empty($argv['order_notes'])) {
            $set .= 'order_notes=:order_notes,';
        }
        if (!empty($argv['order_tip'])) {
            $set .= 'order_tip=:order_tip,';
        }

        if (empty($set)){
            return false;
        }

        $sql .= substr($set, 0, strlen($set)-1);

        $db = Database::database();

        

        $stmt = $db->prepare($sql);

        global $json;

        if (empty($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        if (!empty($argv['order_id'])) {
            $order_id = $argv['order_id'];
            $stmt->bindParam(':order_id',$order_id, 2, 16);
        }
        if (!empty($argv['order_session'])) {
            $order_session = $argv['order_session'];
            $stmt->bindParam(':order_session',$order_session, 2, 225);
        }
        if (!empty($argv['order_total'])) {
            $order_total = $argv['order_total'];
            $stmt->bindParam(':order_total',$order_total, 2, 11);
        }
        if (!empty($argv['order_items'])) {
            $stmt->bindValue(':order_items',$argv['order_items'], 2);
        }
        if (!empty($argv['order_start'])) {
            $order_start = $argv['order_start'];
            $stmt->bindParam(':order_start',$order_start, 2, 40);
        }
        if (!empty($argv['order_costumer'])) {
            $order_costumer = $argv['order_costumer'];
            $stmt->bindParam(':order_costumer',$order_costumer, 2, 16);
        }
        if (!empty($argv['order_server'])) {
            $order_server = $argv['order_server'];
            $stmt->bindParam(':order_server',$order_server, 2, 16);
        }
        if (!empty($argv['order_finish'])) {
            $order_finish = $argv['order_finish'];
            $stmt->bindParam(':order_finish',$order_finish, 2, 225);
        }
        if (!empty($argv['order_chef'])) {
            $order_chef = $argv['order_chef'];
            $stmt->bindParam(':order_chef',$order_chef, 2, 16);
        }
        if (!empty($argv['order_notes'])) {
            $order_notes = $argv['order_notes'];
            $stmt->bindParam(':order_notes',$order_notes, 2, 225);
        }
        if (!empty($argv['order_tip'])) {
            $stmt->bindValue(':order_tip',$argv['order_tip'], 2);
        }

        if (!$stmt->execute()){
            return false;
        }

        $return = array_merge($return, $argv);

        return true;

    }

    /**
    * @param array $remove
    * @param string|null $primary
    * @param array $argv
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv) : bool
    {
        $sql = 'DELETE FROM RootPrerogative.carbon_orders ';

        foreach($argv as $column => $constraint){
            if (!in_array($column, self::COLUMNS)){
                unset($argv[$column]);
            }
        }

        if (empty($primary)) {
            /**
            *   While useful, we've decided to disallow full
            *   table deletions through the rest api. For the
            *   n00bs and future self, "I got chu."
            */
            if (empty($argv)) {
                return false;
            }
            $pdo = self::database();

            $build_where = function (array $set, $join = 'AND') use (&$pdo, &$build_where) {
                $sql = '(';
                foreach ($set as $column => $value) {
                    if (is_array($value)) {
                        $sql .= $build_where($value, $join === 'AND' ? 'OR' : 'AND');
                    } else {
                        if (in_array($column, self::BINARY)) {
                            $sql .= "($column = UNHEX(" . $pdo->quote($value) . ")) $join ";
                        } else {
                            $sql .= "($column = " . $pdo->quote($value) . ") $join ";
                        }
                    }
                }
                return substr($sql, 0, strlen($sql) - (strlen($join) + 1)) . ')';
            };
            $sql .= ' WHERE ' . $build_where($argv);
        } 

        $remove = null;

        global $json;

        if (!isset($json['sql'])) {
            $json['sql'] = [];
        }
        $json['sql'][] = $sql;

        return self::execute($sql);
    }
}