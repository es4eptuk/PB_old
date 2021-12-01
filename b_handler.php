<?php
include 'include/class.inc.php';

define('REST_KEY', 'ywR5djLVUy');

global $bitrixForm;

//ВРЕМЕННО
$no_lid = [
    "лицо" => ["face","ubuso","oju","פּנים","ubuso","wyneb","khuôn","mặt","yuz","چہرہ","обличчя","yüz","ใบหน้า","ముఖం","முகம்","рӯ","mukha","ansikte","uso","rupina","cara","wajiga","obraz","tvár","මුහුණ","چهرو","kumeso","sefahleho","лице","aghaidh","fofoga","лицо","față","ਚਿਹਰਾ","rosto","Twarz","صورت","مخ","nkhope","ansikt","अनुहार","မျက်နှာ","царай","нүүр","चेहरा","kanohi","wiċċ","മുഖം","muka","face","лице","Gesiicht","veidas","seja","faciem","ໃບຫນ້າ","бет","rû","얼굴","មុខ","бет","ಮುಖ","pasuryan","面","viso","aghaidh","wajah","ihu","andlit","arc","muag","ntsej","चेहरा","פָּנִים","alo","fuska","figi","ચહેરો","πρόσωπο","Gesicht","სახე","cara","gesicht","visage","kasvot","nägu","vizaĝo","gezicht","ansigt","tvář","lice","faccia","面對","面对","nawong","cara","лице","lice","মুখ","твар","aurpegia","üz","դեմք","وجه","ፊት","fytyrë","gesig"],
    "внешность" => ["aspekto","species","aparans","katon","hitsura","panagway","ahua","penampilan","fijery","penampilan","maonekedwe","bayyanar","muonekano","muuqaalka","ponahalo ea","irisi","anya","ukubukeka","voorkoms","ظاهر","המראה","مظهر","外観","tsos","दिखावट","ظہور","paydo bo'lish","görünüm","ప్రదర్శన","தோற்றம்","การปรากฏ","пайдоиш","පෙනුම","उपस्थिति","Гадаад өнгө байдал","देखावा","കാഴ്ച","ຮູບລັກສະນະ","រូបរាង","외관","外形","келбеті","ನೋಟವನ್ನು","દેખાવ","გამოჩენა","xuất hiện","အသွင်အပြင်","চেহারা","հայտնվելը","görünüş","välimus","utseende","vzhled","izgled","apparence","ulkomuoto","зовнішність","videz","vzhľad","изглед","apariție","aparência","wygląd","utseende","Aussehen","dehra","изглед","išvaizda","izskats","aparença","aspetto","apariencia","útlit","cuma","אויסזען","udseende","εμφάνιση","verschijning","aspecto","megjelenés","ymddangosiad","izgled","вид","знешнасць","itxura","appearance","	shfaqje"],
    "личность" => ["personeco","personality","pèsonalite","pribadine","pagkatao","personalidad","tuakiri","personaliti","toetra","kepribadian","umunthu","hali","utu","shakhsiyadda","botho","eniyan","ụdị onye","ubuntu","persoonlikheid","شخصیت","אישי","شخصية","個性","cwm pwm","व्यक्तित्व","شخصیت","shaxsiyat","kişilik","వ్యక్తిత్వం","ஆளுமை","บุคลิกภาพ","шахсият","පෞරුෂත්වය","व्यक्तित्व","хувийн","व्यक्तिमत्व","വ്യക്തിത്വം","ບຸກຄະລິກກະ","បុគ្គលិកលក្ខណៈ","성격","个性","個性","ವ್ಯಕ್ತಿತ್ವದ","жеке адам","વ્યક્તિત્વ","პიროვნება","nhân cách","ပုဂ္ဂိုလ်","ব্যক্তিত্ব","անհատականություն","şəxsiyyət","isiksus","personlighet","osobnost","osoba","personnalité","persoonallisuus","особистість","osebnost","osobnosť","личност","personalitate","personalidade","osobowość","personlighet","persönlichkeit","personalità","личност","asmenybė","personība","personalitat","personalità","personalidad","persónuleiki","pearsantacht","פּערזענלעכקייט","personlighed","προσωπικότητα","persoonlijkheid","personalidade","személyiség","personoliaeth","ličnost","индивидуалност","асобу","nortasuna","personality","personalitet"],
    "доп" => ["facial","person","identity","лица","voice","rights"]
];
$no_lid_bool = false;
//

//$log = date('Y-m-d H:i:s') . ' ' . print_r($_POST, true);
//file_put_contents(__DIR__ . '/log.txt', $log . PHP_EOL, FILE_APPEND);

$rest_key = (isset($_GET['rest_key'])) ? $_GET['rest_key'] : '';

if ($rest_key == REST_KEY) {
    $post = [];
    if (isset($_POST)) {
        foreach ($_POST as $name => $value) {
            $post[$name] = $value;

            //ВРЕМЕННО
            if ($no_lid_bool === false) {
                foreach ($no_lid as $w) {
                    foreach ($w as $w0) {
                        if (stripos($value, $w0) !== false) {
                            $no_lid_bool = true;
                        }
                    }
                }
            }
            //

        }
    }

    $key = (array_key_exists('formname', $post)) ? $post['formname'] : 0;
    $b_form = $bitrixForm->get_info_form_by_key($key);

    if ($b_form) {

        //ВРЕМЕННО
        if ($no_lid_bool === false) {
            $result = $bitrixForm->action($b_form['id'], $post);
        } else {
            $result = 1;
        }
        //

        if ($result) {
            echo 'Ok';
            //echo json_encode(['status' => true], JSON_UNESCAPED_UNICODE);
        } else {
            echo 'Error';
            //echo json_encode(['status' => false, 'error' => $error], JSON_UNESCAPED_UNICODE);
        }
    } else {
        echo 'Not found form';
        //echo json_encode(['status' => false, 'error' => 'Not found form'], JSON_UNESCAPED_UNICODE);
    }
} else {
    echo 'Access is denied';
    //echo json_encode(['status' => false, 'error' => 'Access is denied'], JSON_UNESCAPED_UNICODE);
}
