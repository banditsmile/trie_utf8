<?php
/*
Copyright (c) 2009, Francisco Facioni
All rights reserved.
Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
    * Redistributions of source code must retain the above copyright
      notice, this list of conditions and the following disclaimer.
    * Redistributions in binary form must reproduce the above copyright
      notice, this list of conditions and the following disclaimer in the
      documentation and/or other materials provided with the distribution.
    * The names of its contributors may not be used to endorse or promote products
      derived from this software without specific prior written permission.
THIS SOFTWARE IS PROVIDED BY Francisco Facioni ''AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL Francisco Facioni BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/
/**
 * Class Trie
 *
 * @package PHPTrie
 */
class Trie
{
    private $trie = array();
    private $value = null;
    static $print = false;
    /**
     * Trie constructor
     *
     * @param mixed $value This is for internal use
     */
    public function __construct($value = null)
    {
        $this->value = $value;
    }
    /**
     * Add value to the trie
     *
     * @param string $string    The key
     * @param mixed  $value     The value
     * @param bool   $overWrite Overwrite existing value
     */
    public function add($string, $value, $overWrite=true)
    {
        if ($string === "") {
            if (is_null($this->value) || $overWrite) {
                $this->value = $value;
            }
            return;
        }
        foreach ($this->trie as $prefix => $trie) {
            $prefix = (string)$prefix;
            $prefix_array = Trie::str_split_unicode($prefix,1);
            $prefixLength = mb_strlen($prefix);
            $head = mb_substr($string,0,$prefixLength);
            $head_array = Trie::str_split_unicode($head,1);
            $headLength = mb_strlen($head);
            $equals = true;
            $equalPrefix = "";
            for ($i= 0;$i<$prefixLength;++$i) {
                //Split
                if ($i >= $headLength) {
                    $equalTrie = new Trie($value);
                    $this->trie[$equalPrefix] = $equalTrie;
                    $equalTrie->trie[mb_substr($prefix,$i)] = $trie;
                    unset($this->trie[$prefix]);
                    return;
                } elseif ($prefix_array[$i] != $head_array[$i]) {
                    if ($i > 0) {
                        $equalTrie = new Trie();
                        $this->trie[$equalPrefix] = $equalTrie;
                        $equalTrie->trie[mb_substr($prefix,$i)] = $trie;
                        $equalTrie->trie[mb_substr($string,$i)] = new Trie($value);
                        unset($this->trie[$prefix]);
                        return;
                    }
                    $equals = false;
                    break;
                }
                $equalPrefix .= $head_array[$i];
            }
            if ($equals) {
                $trie->add(mb_substr($string,$prefixLength),$value,$overWrite);
                return;
            }
        }
        $this->trie[$string] = new Trie($value);
    }
    /**
     * Search the Trie with a string
     *
     * @param $string The string search
     *
     * @param $user_call boolean is the method call by user
     * @return mixed The value
     */
    public function search($string,$user_call=true)
    {
        if (empty($string)) {
            return $this->value;
        }
        foreach ($this->trie as $prefix => $trie) {
            $prefix = (string)$prefix;
            $prefixLength = mb_strlen($prefix);

            //add by bandit
            $working_string = $string;
            if($user_call ) {
                if(($start = mb_stripos($string,$prefix)) !==false){
                    $working_string = mb_substr($string, $start);
                }else{
                    continue;
                }

            }

            $head = mb_substr($working_string,0,$prefixLength);
            if ($head === $prefix) {
                $ret =  $trie->search(mb_substr($working_string,$prefixLength),false);

                //add by bandit
                if($ret){return $ret;}
            }
        }
        return $this->value;
    }
    /**
     * Search with multiple keys
     *
     * @param array  $array     The array of keys
     * @param string $delimeter
     *
     * @return mixed The value
     */
    public function searchMultiple(array $array, $delimeter=' ')
    {
        $size = count($array);
        $value = null;
        for ($j=0;$j<$size;++$j) {
            $trie = $this;
            $delim = '';
            $key = '';
            for ($i=$j;$i<$size;++$i) {
                $key .= $delim.$array[$i];
                $ret = $trie->searchTrie($key);
                if (is_null($ret)) {
                    break;
                }
                $trie = $ret[1];
                $key = $ret[0];
                $delim = $delimeter;
                if (!is_null($trie->value)) {
                    $value = $trie->value;
                }
            }
            if (!is_null($value)) {
                return $value;
            }
        }
        return null;
    }
    private function searchTrie($string)
    {
        if (empty($string)) {
            return array($string,$this);
        }
        $stringLength = mb_strlen($string);
        foreach ($this->trie as $prefix => $trie) {
            $prefix = (string)$prefix;
            $prefixLength = mb_strlen($prefix);
            if ($prefixLength > $stringLength) {
                $prefix = mb_substr($prefix,0,$stringLength);
                if ($prefix === $string) {
                    return array($string,$this);
                }
            }
            $head = mb_substr($string,0,$prefixLength);
            if ($head === $prefix) {
                return $trie->searchTrie(mb_substr($string,$prefixLength));
            }
        }
        return null;
    }
    public static function __set_state($state)
    {
        $t = new self;
        $t->trie = $state['trie'];
        $t->value = $state['value'];
        return $t;
    }

    public static function str_split_unicode($str, $l = 0){
        return preg_split('/(.{'.$l.'})/us', $str, -1, PREG_SPLIT_NO_EMPTY|PREG_SPLIT_DELIM_CAPTURE);
    }

}
