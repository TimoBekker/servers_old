<?php
namespace app\components\utils;

use yii\base\BaseObject;

/**
* Класс для хранения различных Общедоступных в коде подпрограмм
*/

class Utils extends BaseObject
{
	public static function array_unique_callback(array $arr, callable $callback, $clearempty = false, $strict = false) {
		return array_filter(
			$arr,
			function ($item) use ($strict, $callback, $clearempty) {
				static $haystack = array();
				$needle = $callback($item);
				if (in_array($needle, $haystack, $strict) || empty($needle) && $clearempty) {
					return false;
				}
				else {
					$haystack[] = $needle;
					return true;
				}
			}
		);
	}

	/*
		Транслитерация
    */
    public static function transliteration($str)
    {
        // ГОСТ 7.79B
        $transliteration = array(
            'А' => 'A', 'а' => 'a',
            'Б' => 'B', 'б' => 'b',
            'В' => 'V', 'в' => 'v',
            'Г' => 'G', 'г' => 'g',
            'Д' => 'D', 'д' => 'd',
            'Е' => 'E', 'е' => 'e',
            'Ё' => 'Yo', 'ё' => 'yo',
            'Ж' => 'Zh', 'ж' => 'zh',
            'З' => 'Z', 'з' => 'z',
            'И' => 'I', 'и' => 'i',
            'Й' => 'J', 'й' => 'j',
            'К' => 'K', 'к' => 'k',
            'Л' => 'L', 'л' => 'l',
            'М' => 'M', 'м' => 'm',
            'Н' => "N", 'н' => 'n',
            'О' => 'O', 'о' => 'o',
            'П' => 'P', 'п' => 'p',
            'Р' => 'R', 'р' => 'r',
            'С' => 'S', 'с' => 's',
            'Т' => 'T', 'т' => 't',
            'У' => 'U', 'у' => 'u',
            'Ф' => 'F', 'ф' => 'f',
            'Х' => 'H', 'х' => 'h',
            'Ц' => 'Cz', 'ц' => 'cz',
            'Ч' => 'Ch', 'ч' => 'ch',
            'Ш' => 'Sh', 'ш' => 'sh',
            'Щ' => 'Shh', 'щ' => 'shh',
            'Ъ' => 'ʺ', 'ъ' => 'ʺ',
            'Ы' => 'Y`', 'ы' => 'y`',
            'Ь' => '', 'ь' => '',
            'Э' => 'E`', 'э' => 'e`',
            'Ю' => 'Yu', 'ю' => 'yu',
            'Я' => 'Ya', 'я' => 'ya',
            '№' => '#', 'Ӏ' => '‡',
            '’' => '`', 'ˮ' => '¨',
        );

        $str = strtr($str, $transliteration);
        // потом заменяем все ненужное
        $str = mb_strtolower($str, 'UTF-8');
        $str = preg_replace('/[^0-9a-z\-]/', '', $str);
        $str = preg_replace('|([-]+)|s', '-', $str);
        $str = trim($str, '-');

        return $str;
    }

    /*
        Проверка возможности действия пользователя для переданных в массиве разрешений
        Если какой-то из передаваемых элементов в массиве имеет разрешение, то возвращается true
    */
    public static function userCanIn(array $items)
    {
        foreach ($items as $value) {
            if ( \Yii::$app->user->can($value) )
                return true;
        }
        return false;
    }

    /*
        Рекурсивное удаление директории
    */
    // рекурсивная функция
    function remove_folder($folder) {
        if (!is_dir($folder)) return;
        // получаем все файлы из папки
        if ($files = glob($folder . "/*")) {
            // удаляем по одному
            foreach($files as $file) {
                if (is_dir($file)) {
                    // если попалась папка,
                    // то удаляем ее
                    remove_folder($file);
                } else {
                    // если попался файл
                    unlink($file);
                }
            }
        }
        // удаляем пустую папку
        rmdir($folder);
    }

    /* Удаление файла
    */
    public function deleteFile($file_name)
    {
        if ( is_readable( $file_name ) ){
            return unlink( $file_name );
        } else {
           throw new \Exception("Удаляемый файл документации не существует или нет к нему доступа");
        }
    }

    // возврат ip маски в двузначном виде // deprecated
    public static function maskToBin($mask)
    {
        $components = explode('.', $mask);
        $sum = 0;
        foreach ($components as $component) {
            $binComponent = decbin($component);
            for ($i = 0; $i < strlen($binComponent); $i++) {
                $sum += $binComponent{$i};
            }
        }
        return "/ $sum";
    }

    // возвращает адрес маски вида 255.255.255.255 в виде числа от 0 до 32
    // маска должна быть задана в правильном формате
    // int in(0,32)
    public static function maskToDec($mask) : int
    {
        $components = explode('.', $mask);
        $sum = 0;
        foreach ($components as $component) {
            $binComponent = decbin($component);
            for ($i = 0; $i < strlen($binComponent); $i++) {
                $sum += $binComponent{$i};
            }
        }
        return $sum;
    }

    // return string '255.255.255.255' | false
    public static function lenToMask(int $len) {
        if ($len > 32 || $len < 0) return false;
        $tmp = '';
        $count = 32;
        while ($count--) {
            if ($len > 0) {
                $tmp .= '1';
                $len--;
                continue;
            }
            $tmp .= '0';
        };
        $arr = str_split($tmp, 8);
        foreach ($arr as &$val) {
            $val = bindec($val);
        }
        $res = join('.', $arr);
        return $res;
    }

    // return 'int' | 'float' | false
    public static function getRealNumType(string $str)
    {
        // if ($str === '' || preg_match('/^\D/', ltrim($str,'.'))) {
        //     return false;
        // }
        if (!is_numeric($str)) return false;
        $filtered = floatval($str);
        if (round($filtered) !== $filtered) {
            return 'float';
        } else if (round($filtered) === $filtered) {
            return 'int';
        } else {
            return false;
        }
    }

    // Вернуть последний компонент из пространства имен
    public static function basename($str) {
        $arr = explode("\\", $str);
        return end($arr);
    }

    public static function convertModelToArray($models) {
        if (is_array($models))
            $arrayMode = TRUE;
        else {
            $models = array($models);
            $arrayMode = FALSE;
        }
        $result = array();
        foreach ($models as $model) {
            if (is_null($model)) {
                break;
            }
            $attributes = $model->getAttributes(null, [
                "id","password_hash","password_reset_token","created_at","updated_at","status"
            ]);
            $relations = array();
            foreach ($model->getRelatedRecords() as $key => $related) {
                if ($model->isRelationPopulated($key)) {
                    $relations[$key] = self::convertModelToArray($model->$key);
                }
            }
            $all = array_merge($attributes, $relations);
            if ($arrayMode)
                array_push($result, $all);
            else
                $result = $all;
        }
        return $result;
    }
}