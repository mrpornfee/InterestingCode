<?php
/**
 * Created by PhpStorm.
 * User: sulifer
 * Date: 2020/3/27
 * Time: 18:10
 */

//初始化
Class AutoFindRoad
{   //地图
    private static $map;
    //长---x轴
    private static $length;
    //宽---y轴
    private static $width;
    //墙
    private static $wall;
    //空路
    private static $road;
    //轨迹
    private static $gone;
    //角色
    private static $ch;
    //角色个数
    private static $chNu;
    //终点
    private static $de;
    //终点个数
    private static $deNu;
    //路径长度

    private static $roadLength=0;
    //标记是否已有起点或终点
    private static $flag = 0;

    public function __construct($length, $width)
    {
        self::$length = $length;
        self::$width = $width;
        $this->setIcon();
        for ($i = 0; $i < $width; $i++) {
            self::$map[0][$i] = self::$wall;
            self::$map[$length - 1][$i] = self::$wall;
        }
        for ($i = 0; $i < $length; $i++) {
            self::$map[$i][0] = self::$wall;
            self::$map[$i][$width - 1] = self::$wall;
        }
    }

//设置图案

    public function setIcon($wall = '卐卐', $road = '[  ]', $ch = '张三', $de = '终点', $gone = '路过')
    {
        self::$wall = $wall;
        self::$road = $road;
        self::$ch = $ch;
        self::$de = $de;
        self::$gone = $gone;
    }


//设置行列
    public function setRC($row = [], $column = [])
    {
        for ($i = 0; $i < sizeof($row); $i++) {
            if ($row[$i] > self::$width || $row[$i] < 0) {
                echo '行越界';
                exit;
            }
        }
        for ($i = 0; $i < sizeof($column); $i++) {
            if ($column[$i] > self::$length || $column[$i] < 0) {
                echo '列越界';
                exit;
            }
        }
        for ($i = 0; $i < sizeof($row); $i++) {
            for ($m = 0; $m < sizeof(self::$map[0]); $m++) {
                self::$map[$row[$i]][$m] = self::$wall;
            }
        }
        for ($j = 0; $j < sizeof($column); $j++) {
            for ($n = 0; $n < sizeof(self::$map); $n++) {
                self::$map[$n][$column[$j]] = self::$wall;
            }
        }
    }


//挡板设置
    public function setItem($a = [])
    {
        for ($i = 0; $i < sizeof($a); $i++) {
            $arr = explode(',', $a[$i]);
            self::$map[$arr[1]][$arr[0]] = self::$wall;
        }
    }


//设置起始坐标
    public function setStart($arr = [1, 1])
    {
        if (self::$chNu >= 1) {
            echo '已经有1个起点了';
            exit;
        }
        if ($arr[0] <= 0 || $arr[0] >= self::$length - 1 || $arr[1] <= 0 || $arr[1] >= self::$width - 1) {
            echo '起始坐标越界';
            exit;
        }
        if (self::$map[$arr[0]][$arr[1]] === self::$wall) {
            echo '起始位置已有墙，无法摆置';
            exit;
        }
        if (self::$flag === 1) {
            if (self::$map[$arr[0]][$arr[1]] === self::$de) {
                echo '起始位置不能与终点相重合';
                exit;
            }
        }
        self::$map[$arr[0]][$arr[1]] = self::$ch;
        self::$flag = 1;
        self::$chNu += 1;
    }

    public function setDestination($arr = [])
    {
        if (self::$deNu >= 1) {
            echo '已经有1个终点了';
            exit;
        }
        if (sizeof($arr) != 2) {
            echo '参数必须会大小为2的数组';
            exit;
        }
        if ($arr[0] <= 0 || $arr[0] >= self::$length - 1 || $arr[1] <= 0 || $arr[1] >= self::$width - 1) {
            echo '终点坐标越界';
            exit;
        }
        if (self::$map[$arr[0]][$arr[1]] === self::$wall) {
            echo '终点位置已有墙，无法摆置';
            exit;
        }
        if (self::$flag === 1) {
            if (self::$map[$arr[0]][$arr[1]] === self::$ch) {
                echo '终点不能与起始相重合';
                exit;
            }
        }
        self::$map[$arr[0]][$arr[1]] = self::$de;
        self::$flag = 1;
        self::$deNu += 1;
    }

//寻找起点
    public function findStart()
    {
        for ($i = 0; $i < self::$width - 1; $i++) {
            for ($j = 0; $j < self::$length - 1; $j++) {
                if (self::$map[$i][$j] === self::$ch)
                    return [$i, $j];
            }
        }
    }

//寻找终点
    public function findDestination($type = 1)
    {
        $res = $this->findStart();
        if (sizeof($res) == 2) {
            switch ($type) {
                case 1:
                    $this->findRDLU($res);
                    break;
                case 2:
                    $this->findDLUR($res);
                    break;
                case 3 :
                    $this->findLURD($res);
                    break;
                case 4 :
                    $this->findURDL($res);
                    break;
                default:
                    echo '暂无相关算法';
                    exit;
            }
        } else {
            echo '未找到起点';
            exit;
        }
    }

    //寻找的过程1:→↓←↑
    private function findRDLU($res)
    {
        if (self::$map[$res[0]][$res[1]] == self::$de) {
            return true;
        } else if (self::$map[$res[0]][$res[1] + 1] == self::$road || self::$map[$res[0]][$res[1] + 1] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0], $res[1] + 1]);
            return false;
        } else if (self::$map[$res[0] + 1][$res[1]] == self::$road || self::$map[$res[0] + 1][$res[1]] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0] + 1, $res[1]]);
            return false;
        } else if (self::$map[$res[0]][$res[1] - 1] == self::$road || self::$map[$res[0]][$res[1] - 1] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0], $res[1] - 1]);
            return false;
        } else if (self::$map[$res[0] - 1][$res[1]] == self::$road || self::$map[$res[0] - 1][$res[1]] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0] - 1, $res[1]]);
            return false;
        } else {
            return false;
        }
    }

    //寻找的过程1:↓←↑→
    private function findDLUR($res)
    {
        if (self::$map[$res[0]][$res[1]] == self::$de) {
            return true;
        } else if (self::$map[$res[0] + 1][$res[1]] == self::$road || self::$map[$res[0] + 1][$res[1]] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0] + 1, $res[1]]);
            return false;
        } else if (self::$map[$res[0]][$res[1] - 1] == self::$road || self::$map[$res[0]][$res[1] - 1] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0], $res[1] - 1]);
            return false;
        } else if (self::$map[$res[0] - 1][$res[1]] == self::$road || self::$map[$res[0] - 1][$res[1]] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0] - 1, $res[1]]);
            return false;
        } else if (self::$map[$res[0]][$res[1] + 1] == self::$road || self::$map[$res[0]][$res[1] + 1] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0], $res[1] + 1]);
            return false;
        } else {
            return false;
        }
    }

    //寻找的过程1:←↑→↓
    private function findLURD($res)
    {
        if (self::$map[$res[0]][$res[1]] == self::$de) {
            return true;
        } else if (self::$map[$res[0]][$res[1] - 1] == self::$road || self::$map[$res[0]][$res[1] - 1] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0], $res[1] - 1]);
            return false;
        } else if (self::$map[$res[0] - 1][$res[1]] == self::$road || self::$map[$res[0] - 1][$res[1]] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0] - 1, $res[1]]);
            return false;
        } else if (self::$map[$res[0]][$res[1] + 1] == self::$road || self::$map[$res[0]][$res[1] + 1] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0], $res[1] + 1]);
            return false;
        } else if (self::$map[$res[0] + 1][$res[1]] == self::$road || self::$map[$res[0] + 1][$res[1]] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0] + 1, $res[1]]);
            return false;
        } else {
            return false;
        }
    }

    //寻找的过程1:↑→↓←
    private function findURDL($res)
    {
        if (self::$map[$res[0]][$res[1]] == self::$de) {
            return true;
        } else if (self::$map[$res[0] - 1][$res[1]] == self::$road || self::$map[$res[0] - 1][$res[1]] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0] - 1, $res[1]]);
            return false;
        } else if (self::$map[$res[0]][$res[1] + 1] == self::$road || self::$map[$res[0]][$res[1] + 1] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0], $res[1] + 1]);
            return false;
        } else if (self::$map[$res[0] + 1][$res[1]] == self::$road || self::$map[$res[0] + 1][$res[1]] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0] + 1, $res[1]]);
            return false;
        } else if (self::$map[$res[0]][$res[1] - 1] == self::$road || self::$map[$res[0]][$res[1] - 1] == self::$de) {
            self::$map[$res[0]][$res[1]] = self::$gone;
            $this->findRDLU([$res[0], $res[1] - 1]);
            return false;
        } else {
            return false;
        }
    }
    //计算路径长度
    public function calRoadLength()
    {
        $cal = 0;
        for ($i = 1; $i < self::$width - 1; $i++) {
            for ($j = 1; $j < self::$length - 1; $j++) {
                if (self::$map[$i][$j] == self::$gone) {
                    $cal += 1;
                }

            }
        }
      self::$roadLength=$cal;
    }

//遍历
    public function makeMap()
    {
        for ($i = 0; $i < self::$width; $i++) {
            for ($j = 0; $j < self::$length; $j++) {
                if (self::$map[$i][$j] != self::$wall && self::$map[$i][$j] != self::$ch && self::$map[$i][$j] != self::$de && self::$map[$i][$j] != self::$gone) self::$map[$i][$j] = self::$road;
                echo self::$map[$i][$j] . '----';
            }
            echo $i . PHP_EOL;
            for ($j = 0; $j < self::$length; $j++) {
                echo ' |      ';
            }
            echo PHP_EOL;
        }
        for ($i = 0; $i < $j; $i++) {
            echo ' ' . $i . '      ';
        }
        $this->calRoadLength();
        echo '路径长度：'.self::$roadLength.PHP_EOL;
    }

}

/*$test = new Map(9, 9);
$test->setRC([4],[4]);
$test->setItem(['2,2', '2,6', '6,2', '6,6', '3,2', '3,6', '5,2', '5,6', '4,4']);
$test->setStart([1, 1]);
$test->setDestination([7, 7]);
$test->makeMap();
echo '============================================================================='.PHP_EOL;
$test->findDestination(2);
$test->makeMap();
*/


