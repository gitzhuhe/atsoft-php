<?php
echo "<?php\n";
?>
namespace <?php echo $moudel; ?>\entity;


use AtSoft\SingPHP\Common\Entity;
use AtSoft\SingPHP\validate\Validate;

class <?php echo $name; ?>Entity extends Entity
{
<?php
if (is_array($column)) {
?>
    public $field = [<?php
    foreach ($column as $value) {
       echo '\''.$value['Field'].'\',';
    }
    ?>];
    public $fieldDescription = [<?php
        foreach ($column as $value) {
            if(!in_array($value['Field'],['uid','inputtime','updatetime','display'])){
                echo "\n\t\t\t";
                echo '[';
                echo '\'key\'=>\''.$value['Field'].'\',';
                echo '\'description\'=>\''.$value['Comment'].'\',';
                echo '],';
            }
        }
    ?>

    ];
    <?php

    foreach ($column as $value) {
        // var_dump($value);
        echo "\n\t";
        echo "// ";
        echo $value['Comment'];
        echo "\n\t";
        echo "private $";
        echo $value['Field'];
        echo ";";

        if($value['Key']=='PRI'){
            $PrimaryKey = $value['Field'];
        }
    }
    ?>


    private $rules = [<?php
        foreach ($column as $value) {
            if(!in_array($value['Field'],['uid','inputtime','updatetime','display'])) {
                echo "\n\t\t\t";
                echo "'" . $value['Field'] . "'=>[Validate::required,'" . $value['Field'] . "为必填',3,[";
                if($value['Key']=='PRI'){
                    echo "'get','delete','recovery',";
                }else if($value['Null']=='NO'){
                    echo "'save',";
                }
                echo "]],";
            }
        }
    ?>

    ];

    private $blackKey = ['blackKey', 'rules', 'fieldDescription','field'];
<?php

    foreach ($column as $value) {
        // var_dump($value);
        echo "\n\n\t";
        echo "public function set";
        echo ucfirst($value['Field']);
        echo ' ($value)';
        echo "\n\t";
        echo "{";
        echo "\n\t\t";
        echo '$this->';
        echo $value['Field'];
        echo " = ";
        echo '$value';
        echo ";";
        echo "\n\t";
        echo "}";
    }
    ?>



    <?php

    foreach ($column as $value) {
        // var_dump($value);
        echo "\n\n\t";
        echo "public function get";
        echo ucfirst($value['Field']);
        echo ' ()';
        echo "\n\t";
        echo "{";
        echo "\n\t\t";
        echo 'return $this->';
        echo $value['Field'];
        echo ";";
        echo "\n\t";
        echo "}";
    }

}
?>

    public function getPrimaryKey(){
        return '<?php echo $PrimaryKey;?>';
    }

    public function getPrimaryKeyValue(){
        $func = 'get'.ucfirst($this->getPrimaryKey());
        return $this->$func();
    }

    public function getRules(){
        return $this->rules;
    }

    public function getBlackKey(){
        return $this->blackKey;
    }

}
