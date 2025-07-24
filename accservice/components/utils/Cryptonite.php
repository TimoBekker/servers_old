<?php  // Обратимое Шифрование Строк

namespace app\components\utils;

class Cryptonite
{
    private static $keyString = 'aknIEml92kjls';

    public static function xorEncrypt( $InputString, $KeyString )
    {
        $KeyStringLength = mb_strlen( $KeyString );
        $InputStringLength = mb_strlen( $InputString );
        for ( $i = 0; $i < $InputStringLength; $i++ ) {
            // Если входная строка длиннее строки-ключа
            $rPos = $i % $KeyStringLength;
            // Побитовый XOR ASCII-кодов символов
            $r = ord( $InputString[$i] ) ^ ord( $KeyString[$rPos] );
            // Записываем результат - символ, соответствующий полученному ASCII-коду
            $InputString[$i] = chr($r);
        }
         return $InputString;
    }

    public static function encodePassword( $InputString )
    {
         $str = self::xorEncrypt( base64_encode($InputString), self::$keyString );
         return $str;
    }

    public static function decodePassword( $InputString )
    {
        $str = base64_decode(self::xorEncrypt( $InputString, self::$keyString ));
        return $str;
    }
}

// function br()
// {
//     echo '<br/>';
// }
// $arrTestStrings[] = 'Hello World!';
// $arrTestStrings[] = 'Mудя ло2347 АУЫ sdjow Д а пошел ты на ухо Пиздоблюхер тупой !"№;:;**_"__":*!*%( 0927460 lKN ывл оДЛВОёЁ ёё~~~ ``..,';
// print_r($arrTestStrings[0]);
// br();
// print_r(Cryptonite::encrypt($arrTestStrings[0]));
// br();
// print_r(mb_strlen(Cryptonite::encrypt($arrTestStrings[0])));
// Cryptonite::decrypt( Cryptonite::encrypt($arrTestStrings[0]) );
// br();
// print_r($arrTestStrings[0]);