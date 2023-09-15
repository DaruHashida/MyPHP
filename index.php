
<?php
/*
a text straightener that removes extra spaces and line breaks, prepares a line of recognised text for machine translation,
 and saves this translation as the original text with Russian underlining.

The straightened text appears in the input box so that it can be easily copied ctrl+A
*/
$file_path = "SOME_PATH_TO_FILE_WHERE_WILL_BE_TEXT_WITH_RUSSIAN_UNDERLINING";
$latin_long=0;
if (array_key_exists('latinfilelong',$_POST))
{
$latin_long = $_POST['latinfilelong'];}
?>
<h1 style="text-align: center"> ВЫПРЯМИТЕЛЬ ТЕКСТА, РАСПОЗНАННОГО СО СКАНА, ДЛЯ ВСТАВКИ В ПЕРЕВОДЧИК</h1>
<div id="translate_field" style="text-align: center">
    <form action="<?=$_SERVER['PHP_SELF']?>" method="post"  style="display: inline-block">
        <?php
        if (array_key_exists('text',$_POST))
        {   $divider = "\n".str_repeat('_',73)."\n";
            $string = str_replace(["\n","\r"],' ',$_POST['text']);
            $string = preg_replace('/-\s+/','',$string);
            $string = preg_replace('/(?<=[A-Z])\./', '', $string);
            $string = preg_replace('/(\.|\!|\?)(?!\s*[A-Z,А-Я])(?!\s*$)/', '',$string);
            $string = str_replace(["«", "»"],' ',$string);
            $string= str_replace('Ibid.', 'Ibid', $string);
            $demarker=0;
            for ($l=0; $l<mb_strlen($string); $l++)
            {
                if ($string[$l]!= mb_convert_encoding($string[$l], 'ISO-8859-1'))
                {
                    $demarker+=1;
                }
            }
            if ($demarker>intdiv(mb_strlen($string),10))
            {   $rfp = fopen($file_path,'r+');
                file_put_contents($file_path, $string, FILE_APPEND);
                file_put_contents($file_path,$divider, FILE_APPEND);
                $fp = fopen($file_path,'r+');
                fseek($fp, 0,SEEK_END);
                $position = ftell($fp);
                rewind($fp);
                fseek($fp, $position-$latin_long);
                $stroke = fread($fp, filesize($file_path));
                rewind($fp);
                fseek($fp, $position-$latin_long);
                fwrite($fp,'', filesize($file_path));
                fclose($fp);
                fclose($rfp);
                $delim_of_arr = "/[\.\?\!]+/";
                $latin_arr= preg_split($delim_of_arr, $stroke);
                $russian_arr = preg_split($delim_of_arr, $string);
                for ($i=0; $i<count($latin_arr);$i++)
                {
                    $latin_arr[$i] =  $latin_arr[$i]."\n".$russian_arr[$i]."\n====\n";
                }
                $final_string = implode("\n",$latin_arr);
                file_put_contents($file_path, $final_string,FILE_APPEND);
                file_put_contents($file_path,$divider, FILE_APPEND);
                $latin_long=0;
                /*echo($_POST['text']);*/
            }
            else {
                file_put_contents($file_path, $string, FILE_APPEND);
                $latin_long += mb_strlen($string);
            }
        }
        ?>
        <div>
            <textarea name="text" id="text" rows="5" cols="100"><?=$string??''?></textarea>
        </div>
        <div>
            <input type="hidden" name="latinfilelong" value="<?=$latin_long?>">
        </div>
        <div>
            <button type="submit" name="">Добавить</button>
        </div>
    </form>
</div>



