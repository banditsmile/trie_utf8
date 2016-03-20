<?php
use PHPTrie\Trie;
require_once('../PHPTrie/Trie.php');
function make_trie($words_file=''){
    $words_file =  $words_file ? $words_file : './words.list';
    /*$trie_object_file = '../cache/trie.object';
    if(($trie_string = file_get_contents($trie_object_file))
        && ($trie = unserialize($trie_string))
        && (get_class($trie) == 'Trie')){
        return $trie;
    }*/
    $trie = new Trie(null,true);
    //$data = "假钞QQ,假钞,假钞人民币假人民币,假钞,发票,短信群发,指纹套制作方法,指纹套,指纹套,野战,毛泽东的人肉政权,毛泽东的激情,迷魂药,炸药,独立,中共十八大,十八大,毛泽东,佐藤江梨花,脱衣舞女,玉女白菊花,制服的挑逗,奴隶战士,佐藤友里,处女杀手,李克强,佐々木,仲村桃,齐天大性,佐藤美纪,毛泽东玄机,情色电影,处女的幻觉,流亡,自由中国,河蟹,茉莉花效应,茉莉花,茉莉花散步,蝴蝶,激情,制服,玉女,色情,风流,欲望,情色,脱衣,成人,性爱,强奸,饭岛,三陪,放荡,处女,人妻,色欲,换妻,偷情,变态,裸体,青楼,情欲,肉体,侮辱,桃色,痴汉,日本,台湾,援交,三级,无码,金瓶,白石,佐藤,裸聊,巨乳,铃木,相奸,一夜情,爱欲,美少女,制服狩,淫,西藏,拉萨,艳遇,性感,政治,潘金莲,少女的青春,聊斋艳谭,一本道,佐藤弘美,讨伐,全裸,河原崎家,爆乳,情色美眉娱乐网,透视,奴隶,性福,邪教,兽性,希崎,城管,鬼畜,女王,桐谷,肉欲,赤裸,神韵,魔法少女,聊斋,杀人,桐岛,劳教,奴隶游戏,haodizhi,真相,陵辱,友崎,樱井莉,吉泽,67.159.2.80,处女思春,女教师,门,脱衣舞娘,玉女盟,佐藤穗乃花,淫荡,处女爱欲,黑火药,和谐,一党,触手,少女,佐藤愛莉,痴漢,義母,彼女,少女穴,Tokyo";
    /*$data = "李洪志,李克强";

    $data = explode(',',$data);
    $data = array_flip(array_flip($data));
    sort($data);*/
    $data = file($words_file);
    $data = array_map(function($item){
        $item = trim($item);
        return $item;
    },$data);
    $data = array_filter($data);
    $data = array_flip(array_flip($data));
    sort($data);
    file_put_contents('./words.list',implode(PHP_EOL,$data));
    foreach($data as $buffer){
        $trie->add(trim( $buffer),$buffer,true);
    }
    $trie_object = serialize($trie);
    file_put_contents('./trie.object',$trie_object);
    return $trie;
}


function test_search($word,$trie=null){
    $elapse_begin = microtime(true);
    $ret = $trie->search($word);
    $elapse_end = microtime(true);
    if($ret){
        echo "Search  {$word} suc ".$ret."in " . ($elapse_end - $elapse_begin) . PHP_EOL;
    }else{
        var_dump($ret);
        echo "Search {$word} fail... ".$ret."in " . ($elapse_end - $elapse_begin) .PHP_EOL;
    }

}
function object2array($object) {
    $ref = new ReflectionClass($object);
    $props = $ref->getProperties();
    $arr = array();


    foreach ($props as $prop) {
        $prop->setAccessible(true);
        $arr[$prop->getName()] = $prop->getValue($object);
        $prop->setAccessible(false);
    }
    return $arr;
};

$trie = make_trie("./words.list");
//print_r(object2array($trie));
file_put_contents('./f.txt',print_r($trie,true));
test_search('李洪志',$trie);
test_search('我是,李,洪,志',$trie);
test_search('我是，李★!@洪#$%^志',$trie);
test_search('我是*李*洪*志',$trie);
test_search('我是李洪志',$trie);
test_search('我是李李洪志',$trie);
test_search('我是李洪志师傅',$trie);
test_search('金正恩娶妻',$trie);
test_search('abc',$trie);
test_search('法轮功',$trie);
test_search('你好',$trie);
test_search('War游侠',$trie);
test_search('nbc',$trie);
test_search('杉山ケイ',$trie);
test_search('柳川ナナ',$trie);
test_search('假钞QQ',$trie);

//$trie->add("key", 10);
