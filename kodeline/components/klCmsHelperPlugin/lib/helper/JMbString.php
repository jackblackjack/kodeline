<?php
class JMbString
{
    private $string;
    private $encoding;

    public function __construct($string, $encoding = 'UTF-8') {
        $this->string = $string;
        $this->encoding = $encoding;
    }

    public function __toString() {
        return (string)$this->string;
    }

    /**
     * Длинна строки в символах
     * @return int
     */
    public function Length() {
        return mb_strlen($this->string, $this->encoding);
    }

    /**
     * Размер данных в байтах
     * @return int
     */
    public function Size() {
        return strlen($this->string);
    }

    /**
     * Возвращает кодировку строки
     * @return string
     */
    public function getEncoding() {
        return $this->encoding;
    }

    /**
     * Изменяет кодировку строки
     * @param $encoding
     */
    public function setEncoding($encoding) {
        $this->string = mb_convert_encoding($this->string, $encoding, $this->encoding);
        $this->encoding = $encoding;
    }

    /**
     * Перечисляет поддерживаемые кодировки
     * @return array
     */
    public static function SupportEncodings() {
        return mb_list_encodings();
    }

    /**
     * Извлечение символа по индексу
     * @param int $i
     * @return string
     */
    public function GetChar($i) {
        return mb_substr($this->string, $i, 1, $this->encoding);
    }

    /**
     * Устанавливает символ по индексу
     * @param int $i
     * @param string $char
     */
   public  function SetChar($i, $char) {
        $this->string = mb_substr($this->string, 0, $i, $this->encoding)
            .mb_substr($char, 0, 1, $this->encoding) //Защита на случай если передадут строку
            .mb_substr($this->string, $i+1, $this->Length()-($i+1), $this->encoding);
    }

    /**
     * Удаляет символ с индексом
     * @param int $i
     */
    public function UnSetChar($i){
        $this->string = mb_substr($this->string, 0, $i, $this->encoding).mb_substr($this->string, $i+1, $this->Length()-($i+1), $this->encoding);
    }

    public function UCFirst() {
        $this->SetChar(0, mb_strtoupper($this->GetChar(0), $this->encoding));
        return $this->string;
    }

    public function UCWords() {
        return $this->string = mb_convert_case($this->string, MB_CASE_TITLE, $this->encoding);
    }
}