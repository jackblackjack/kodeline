<?php
/**
 * Угадыватель строк.
 * 
 * Пытается определить что за строка была изначально,
 * когда слово шифруется по правилу:
 * 1 буква изначального слова = одна из множества букв в конечном слове
 * 
 * TODO: 
 * Если понадобится эта игрушка то ее стоит переписать как статический класс,
 * пока она написана как простой класс, а переписать все руки не дойдут.
 *
 * @package     yaMorphyPlugin
 * @subpackage  guesser
 * @link        http://savepearlharbor.com/?p=171977
 * @category    morphology
 * @author      chugarev@gmail.com
 * @version     $Id$
 */
class yaMorphyGuesser
{
  /**
   * Экземпляр класса словаря.
   * 
   * @var yaMorphyMorphologist
   */
  private static $glossary = null;

  /**
   * Кодировка, относительно которой идет поиск.
   * 
   * @var string
   */
  private static $codepage = null;

  /**
   * Максимальная длинна слова для подбора.
   * 
   * @var integer
   */
  const MAX_GUESS_WORD_LENGTH = 10;

  private $word;  
  private $text;   // Обрабатываемое слово  
  public  $phrase; // Обрабатываемая фраза  
  public  $stack = array();   // Слоты возвращаемых слов  
  public  $trash = array();   // Слоты дополнительных слов  
  private $stackIndex = 0;  // Текущий номер слота  
  const maxSolveTrash  = 10;  // Максимальное количество подстановок  
  const defaultPhrase  = "сасу сефыче та тшисифе сфот";

  // Массив начальный, на текущий момент содержит только массив кириллицы   
  private $map_init = array(  // Кирилица   
    array(      "а", "б", "в", "г", "д", "е", "ё", "ж", "з", "и", "й", "к", "л", "м", "н", "о",         "п", "р", "с", "т", "у", "ф", "х", "ц", "ч", "ш", "щ", "ъ", "ы", "ь", "э", "ю", "я")  );  // Массив перевода, на текущий момент содержит только массив кириллицы  
  private $map_sudo = array(  // Кириллица     
  array(       array("е", "э"), "-", "з", "-", "-", "о", "-", "щ", "ш", array("а", "ю"), "ъ", "ц",         "-", "-", "-", "и", array("д", "ж"), "-", array("б", "т", "ф"), array("г", "н", "х"),         "я", "л", "м", "п", array("к", "р"), array("в", "с"), array("й", "ч"), "-", array("у", "ь"), "-",         "-", "ы", "ё")  );    
  
  /**
   * Пытается определить изначальное форму строки
   * по правилам описанным в массивах.
   */
  public static function try($locale, $codepage, $string)
  {
    self::$codepage = $codepage;
    self::$glossary = yaMorphyToolkit::getGlossary($locale, $codepage);

    $guessed = self::guessString($string);
    var_dump($guessed); die;
  }

  // Получение кода соотнесенного символа (с=б, а=у и т.п.) 
  protected function indexbychar($char, $map_index) {
    for ($index = 0; $index < count($this->map_init[$map_index]); $index++) {
      if ($this->map_init[$map_index][$index] == $char) return $index;
    }
    return -1;
  }



  // Посимвольное сравнение полученного и начального слова protected 
  function str_compare($sample, $value)  {       
  for ($i = 0; $i < strlen($sample); $i++) {          
  if ($sample[$i] == $value[$i]) return false;      }       return true; }   

  // Определение слова в словаре, если слово есть, то словарь вернет массив 
  protected function str_dict($solve) {
    $lemma = $this->morph->lemmatize(mb_strtoupper($solve, "windows-1251"));
    return is_array($lemma);
  }   

  protected function str_safe($value) {
    return trim(stripslashes(htmlspecialchars($value, ENT_NOQUOTES)));
  }

  // Запись найденного слова в основной слот 
  protected function stack_push($value) {
    if (!isset($this->stack[$this->stackIndex]))
    {
      $this->stack[$this->stackIndex] = array();
    }
    array_push($this->stack[$this->stackIndex], $value);
  }   

  // Запись найденного слова в дополнительный слот 
  protected function trash_push($value) {
    if (!isset($this->trash[$this->stackIndex])) {
      $this->trash[$this->stackIndex] = array();
    }

    array_push($this->trash[$this->stackIndex], $value);
  }   

  // Перебор букв слова 
  protected function bruteforceWord($word, $charindex, $sudoindex)
  {      
    // Если все буквы слова обработаны - то выход
    if (!isset($solve[$charindex])) return false;

    // Цикл по буквам слова, в рекурсии - цикл от последней обработанной буквы
    for ($solveindex = $charindex; $solveindex < strlen($solve); $solveindex++)
    {
      // Цикл по доступным массивам перевода          
      for ($map = 0; $map < count($this->map_sudo); $map++)
      {
        // Не перебирать букву, если она уже заменена
        if ($solve[$charindex] != $this->word[$charindex]) continue;

        // Поиск позиции сотнесенной буквы                
        $subindex = $this->indexbychar($solve[$charindex], $map);
        if ($subindex == -1) continue;

        // Цикл по соотнесенным буквам, в рекурсии - цикл от последней соотнесенной буквы
        for ($index = $sudoindex; $index < count($this->map_sudo[$map][$subindex]); $index++)
        {
          // Получение нового слова для отображения
          $solve[$charindex] = $this->map_sudo[$map][$subindex][$index];

          // Если слово не содержит начальных букв и имеется в словаре, то запись в стек слов
          if ($this->str_compare($this->word, $solve)) {
            if ($this->str_dict($solve))
            {
              $this->stack_push($solve);
            }
            else {
              if (count(@$this->trash[$this->stackIndex]) < self::maxSolveTrash)
              {
                $this->trash_push($solve);
              }
            }
          }

          // Переход к следующей букве слова в рекурсии
          $this->bruteforce($solve, $solveindex + 1, 0);
        }
      }
    }
  }  

  // Перевод одного слова 
  public function guessWord($word)
  {
    $glossary = yaMorphyToolkit::getGlossary('Ru_RU', 'utf-8');
    $word = self::$glossary->prepareString($word);

    $this->stackIndex++;
    $this->stack_push("");      
    $this->word = $solve;         

    if (self::MAX_GUESS_WORD_LENGTH > mb_strlen($word, self::$codepage)) {
      return $word;
    }
    else {
      return self::bruteforceWord($word, 0, 0);
    }
  }  
  
  // Перевод строки public 
  private static function guessString($string)
  {
    // Разбитие строки на слова.
    if (! preg_match_all("/[a-zа-я]+/ms", $string, $words)) return false;

    $_ =& $words;

    foreach($words[0] as $word) {
      self::guessWord($word);
    }
  } 
}