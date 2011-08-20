<?php

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);

mb_internal_encoding('UTF-8');

class Phonology
{
    /** 
     * [-A]
     * [-U]
     * [-I]
     * [12] short or long
     * [-t] is transparent for I
     * ,[-O] is_opening
     */
    public static $phonocode = array(
        'a' => 'A--1-',
        'á' => 'A--2-',
        'e' => 'A-I1-',
        'é' => 'A-I2t',
        'i' => '--I1t',
        'í' => '--I2t',
        'u' => '-U-1-',
        'ú' => '-U-2-',
        'ü' => '-UI2-',
        'ű' => '-UI2-',
        'o' => 'AU-1-',
        'ó' => 'AU-2-',
        'ö' => 'AUI1-',
        'ő' => 'AUI2-',
    );

    public static function mb_chars($string)
    {
        $chars = array();
        for ($i = 0, $len = mb_strlen($string); $i < $len; $i++)
            $chars[] = mb_substr($string, $i, 1);
        return $chars;
    }

    public static function getPropagatedX($X_pattern, $ortho, $t_pattern=NULL)
    {
        $is_propagated = NULL;
        foreach (self::mb_chars($ortho) as $chr)
        {
            $phonocode = @ self::$phonocode[$chr];
            if (!$phonocode)
                continue;
            if ($t_pattern && preg_match($t_pattern, $phonocode)) // transparent
                if (!is_null($is_propagated)) // already set
                    continue;
            $is_propagated = (bool) preg_match($X_pattern, $phonocode);
        }
        return $is_propagated;
    }

    /** Kerekségi harmónia
     */
    public static function needSuffixU($ortho)
    {
        return self::getPropagatedU($ortho);
    }

    /** Elölségi harmónia
     */
    public static function needSuffixI($ortho)
    {
        return self::getPropagatedI($ortho);
    }

    public static function getPropagatedU($ortho)
    {
        return self::getPropagatedX('/^.U/', $ortho);
    }

    public static function getPropagatedI($ortho)
    {
        return self::getPropagatedX('/^..I/', $ortho, '/^....t/');
    }

    public static $vtmr_map = array(
        'á' => 'a',
        'é' => 'e',
        'í' => 'i',
        'ó' => 'o',
        'ő' => 'ö',
        'ú' => 'u',
        'ű' => 'ü',
    );

    public static $amny_map = array(
        'a' => 'á',
        'e' => 'é',
    );

    // magánhangzó-rövidülések
    public static function doMR($ortho)
    {
        return self::tr($ortho, self::$vtmr_map);
    }

    // utolsó alsó magánhangzó nyúlása
    public static function doAMNY($ortho)
    {
        return mb_substr($ortho, 0, -1).self::tr(mb_substr($ortho, -1, 1), self::$amny_map);
    }

    public static function tr($ortho, $map)
    {
        $string = '';
        foreach (self::mb_chars($ortho) as $chr)
        {
            if (isset($map[$chr]))
                $string .= $map[$chr];
            else
                $string .= $chr;
        }
        return $string;
    }

    public static $vowelmaps = array(
            '--,O' => array( 'A' => 'a', 'Á' => 'á', 'E' => 'o', 'O' => 'o', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'V' => 'a', 'W' => 'o'),
            'U-,O' => array( 'A' => 'a', 'Á' => 'á', 'E' => 'o', 'O' => 'o', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'V' => 'a', 'W' => 'o'),
            '-I,O' => array( 'A' => 'e', 'Á' => 'é', 'E' => 'e', 'O' => 'e', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'V' => 'e', 'W' => 'e'),
            'UI,O' => array( 'A' => 'e', 'Á' => 'é', 'E' => 'e', 'O' => 'ö', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'V' => 'e', 'W' => 'e'),
            '--,-' => array( 'A' => 'a', 'Á' => 'á', 'E' => 'o', 'O' => 'o', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'V' => 'o', 'W' => 'o'),
            'U-,-' => array( 'A' => 'a', 'Á' => 'á', 'E' => 'o', 'O' => 'o', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'V' => 'o', 'W' => 'o'),
            '-I,-' => array( 'A' => 'e', 'Á' => 'é', 'E' => 'e', 'O' => 'e', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'V' => 'e', 'W' => 'e'),
            'UI,-' => array( 'A' => 'e', 'Á' => 'é', 'E' => 'ö', 'O' => 'ö', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'V' => 'ö', 'W' => 'e'),
    );

    public static function interpolateVowels($phonocode, $string)
    {
        return self::tr($string, self::$vowelmaps[$phonocode]);
    }

    public static $skeletoncode = array(
        'a' => 'V',
        'á' => 'V',
        'e' => 'V',
        'é' => 'V',
        'i' => 'V',
        'í' => 'V',
        'u' => 'V',
        'ú' => 'V',
        'ü' => 'V',
        'ű' => 'V',
        'o' => 'V',
        'ó' => 'V',
        'ö' => 'V',
        'ő' => 'V',
        'ddzs' => 'C',
        'ccs' => 'C',
        'ddz' => 'C',
        'dzs' => 'C',
        'ggy' => 'C',
        'lly' => 'C',
        'nny' => 'C',
        'ssz' => 'C',
        'tty' => 'C',
        'zzs' => 'C',
        'bb' => 'C',
        'cc' => 'C',
        'cs' => 'C',
        'dd' => 'C',
        'dz' => 'C',
        'ff' => 'C',
        'gg' => 'C',
        'gy' => 'C',
        'hh' => 'C',
        'jj' => 'C',
        'kk' => 'C',
        'll' => 'C',
        'ly' => 'C',
        'mm' => 'C',
        'nn' => 'C',
        'ny' => 'C',
        'pp' => 'C',
        'qq' => 'C',
        'rr' => 'C',
        'ss' => 'C',
        'sz' => 'C',
        'tt' => 'C',
        'ty' => 'C',
        'vv' => 'C',
        'ww' => 'C',
        'xx' => 'C',
        'zs' => 'C',
        'zz' => 'C',
        'b' => 'C',
        'c' => 'C',
        'd' => 'C',
        'f' => 'C',
        'g' => 'C',
        'h' => 'C',
        'j' => 'C',
        'k' => 'C',
        'l' => 'C',
        'm' => 'C',
        'n' => 'C',
        'p' => 'C',
        'q' => 'C',
        'r' => 'C',
        's' => 'C',
        't' => 'C',
        'v' => 'C',
        'w' => 'C',
        'x' => 'C',
        'z' => 'C',
    );

    public static function isVowel($chr)
    {
        return self::$skeletoncode[$chr] === 'V';
    }

    public static $consonant_regex = '/(ddzs|ccs|ddz|dzs|ggy|lly|nny|ssz|tty|zzs|bb|cc|cs|dd|dz|ff|gg|gy|h|hh|jj|k|kk|ll|ly|mm|nn|ny|pp|qq|rr|ss|sz|tt|ty|vv|ww|xx|zs|zz|b|c|d|f|g|j|l|m|n|p|q|r|s|t|v|w|x|z)$/';

    public static function getLastConsonant($ortho)
    {
        if (preg_match(self::$consonant_regex, $ortho, $match))
            return $match[1];
        return NULL;
    }

    public static $double_consonants = array(
        'ddzs' => 'ddzs',
        'ccs' => 'ccs',
        'ddz' => 'ddz',
        'dzs' => 'ddzs',
        'ggy' => 'ggy',
        'lly' => 'lly',
        'nny' => 'nny',
        'ssz' => 'ssz',
        'tty' => 'tty',
        'zzs' => 'zzs',
        'bb' => 'bb',
        'cc' => 'cc',
        'cs' => 'ccs',
        'dd' => 'dd',
        'dz' => 'ddz',
        'ff' => 'ff',
        'gg' => 'gg',
        'gy' => 'ggy',
        'hh' => 'hh',
        'jj' => 'jj',
        'kk' => 'kk',
        'll' => 'll',
        'ly' => 'lly',
        'mm' => 'mm',
        'nn' => 'nn',
        'ny' => 'nny',
        'pp' => 'pp',
        'qq' => 'qq',
        'rr' => 'rr',
        'ss' => 'ss',
        'sz' => 'ssz',
        'tt' => 'tt',
        'ty' => 'tty',
        'vv' => 'vv',
        'ww' => 'ww',
        'xx' => 'xx',
        'zs' => 'zzs',
        'zz' => 'zz',
        'b' => 'bb',
        'c' => 'cc',
        'd' => 'dd',
        'f' => 'ff',
        'g' => 'gg',
        'h' => 'hh',
        'j' => 'jj',
        'k' => 'kk',
        'l' => 'll',
        'm' => 'mm',
        'n' => 'nn',
        'p' => 'pp',
        'q' => 'qq',
        'r' => 'rr',
        's' => 'ss',
        't' => 'tt',
        'v' => 'vv',
        'w' => 'ww',
        'x' => 'xx',
        'z' => 'zz',
    );

    public static function doubleConsonant($ortho)
    {
        assert('$ortho === self::getLastConsonant($ortho)');
        return self::$double_consonants[$ortho];
    }

    public static function doDoubleLastConsonant($ortho)
    {
        $cons = self::getLastConsonant($ortho);
        if ($cons)
            $ortho = mb_substr($ortho, 0, -mb_strlen($cons)).self::doubleConsonant($cons);
        return $ortho;
    }

    public static function canAssimilate($left_ortho, $right_ortho, $char)
    {
        return (!self::isVowel(mb_substr($left_ortho, -1, 1)) && mb_substr($right_ortho, 0, mb_strlen($char)) === $char);
    }

    public static $is_affrikate = array(
        'dz' => true,
        'ddz' => true,
        'dzs' => true,
        'ddzs' => true,
        'c' => true,
        'cc' => true,
        'cs' => true,
        'ccs' => true,
    );

    public static $is_sybyl = array(
        's' => true,
        'ss' => true,
        'sz' => true,
        'ssz' => true,
        'z' => true,
        'zz' => true,
        'zs' => true,
        'zzs' => true,
    );

    public static function isAffrikate($cons)
    {
        return (bool) self::$is_affrikate[$cons];
    }

    public static function isSybyl($cons)
    {
        return (bool) self::$is_sybyl[$cons];
    }

}

interface iWordformMorphology
{

    public function & appendSuffix(Suffixum & $suffix);
    public function onBeforeSuffixation(& $suffix);
}

interface iWordformPhonology
{
    public function isLastVowel();
    public function isVTMR();
    public function isBTMR();
    public function isOpening();
    public function isAMNYLeft();
    public function isAMNYRight();
    public function isAlternating();
    public function needSuffixU();
    public function needSuffixI();
    public function needSuffixPhonocode();
    public function doAssimilate($char);
}

class Wordform implements iWordformMorphology, iWordformPhonology
{
    public $lemma = '';
    public $ortho = '';
    public $is_vtmr = false; 
    public $is_btmr = false; 
    public $is_opening = false; 
    public $is_amny = NULL;
    public $is_alternating = false; 
    public $needSuffixI = NULL;

    public function __construct($lemma=NULL, $ortho=NULL)
    {
        $this->lemma = $lemma;
        $this->ortho = $ortho ? $ortho : $lemma;
    }

    // Helpers {{{

    public function __toString()
    {
        return $this->ortho;
    }

    public function & cloneAs($class)
    {
        $clone = new $class($this->lemma, $this->ortho);
        foreach ($this as $key => $val)
            if (substr($key, 0, 1) !== '_')
                $clone->$key = $val;
        return $clone;
    }

    // }}}

    // iWordformMorphology {{{

    public function & appendSuffix(Suffixum & $suffix)
    {
        $_input_class = $suffix->getInputClass();
        if (!($this instanceof $_input_class))
            throw new Exception('This "'.$this->ortho.'" is a '.get_class($this).' while suffix "'.$suffix->ortho.'" wants '.$suffix->getInputClass());
        $stem = & $this->cloneAs($suffix->getOutputClass());
        $affix = clone $suffix;
        $stem->onBeforeSuffixation($affix);
        $affix->onBeforeSuffixed($stem);
        $interfix_ortho = $affix->getInterfix($stem);
        $stem->ortho .= $interfix_ortho.$affix->ortho;
        $affix->onAfterSuffixed($stem);
        return $stem;
    }

    public function onBeforeSuffixation(& $suffix)
    {
        if ($this->isAlternating() && $suffix->isAlternating())
            $this->ortho = $this->lemma2;
        if ($this->isVTMR() && $suffix->isVTMR())
            $this->ortho = Phonology::doMR($this->ortho);
        if ($this->isBTMR() && $suffix->isBTMR())
            $this->ortho = Phonology::doMR($this->ortho);
        if ($this->isAMNYLeft() && $suffix->isAMNYRight())
            $this->ortho = Phonology::doAMNY($this->ortho);
    }

    // }}}

    // iWordformPhonology {{{

    public function isLastVowel()
    {
        return Phonology::isVowel(mb_substr($this->ortho, -1, 1));
    }

    public function isVTMR()
    {
        return $this->is_vtmr;
    }

    public function isBTMR()
    {
        return $this->is_btmr;
    }

    public function isOpening()
    {
        return $this->is_opening;
    }

    public function isAMNYLeft()
    {
        if (!is_null($this->is_amny))
            return $this->is_amny;
        $last = mb_substr($this->ortho, -1, 1);
        if ($last === 'a' || $last === 'e')
            return true;
        return false;
    }

    public function isAMNYRight()
    {
        if (!is_null($this->is_amny))
            return $this->is_amny;
    }

    public function isAlternating()
    {
        return $this->is_alternating;
    }

    public function needSuffixU()
    {
        return Phonology::needSuffixU($this->ortho);
    }

    public function needSuffixI()
    {
        if (!is_null($this->needSuffixI))
            return $this->needSuffixI;
        return Phonology::needSuffixI($this->lemma);
    }

    public function needSuffixPhonocode()
    {
        return 
            ($this->needSuffixU() ? 'U' : '-') . 
            ($this->needSuffixI() ? 'I' : '-') .
            ($this->isOpening() ? ',O' : ',-') ;
    }

    // @todo move 'tesz+j' => 'tegy' here
    public function doAssimilate($char)
    {
        $this->ortho = Phonology::doDoubleLastConsonant($this->ortho);
    }

    // }}}

}

/*
    Affix:
        Adfix:
            prefix
            suffix
        infix Minnesota + flippin > Minneflippinsota
        interfix speed + meter > speed-o-meter
        duplifix teeny > teeny-weeny
        transfix ktb > kiteb
        simulfix mouse > mice
        suprafix !produce (noun) > pro!duce (verb)
        disfix tipasli > tipli

    prefix+prefix+st<infix>em-suffix-suffix
     */

interface iSuffixumMorphology
{
    public function hasOptionalInterfix();
    public function getOptionalInterfix();
    public function getNonOptionalSuffix();
    public function getInterfix(& $stem);
    public function onBeforeSuffixed(& $stem);
    public function onAfterSuffixed(& $stem);
}

interface iSuffixumPhonology
{
    public function onAssimilated($char, $ortho);
}

/**
 * @todo create a NomenSuffixum descendant and move down things (e.g. hasonul-v)
 */
class Suffixum extends Wordform implements iSuffixumMorphology, iSuffixumPhonology
{

    public $_input_class = 'Nomen';
    public $_output_class = 'Nomen';
    public $stop_jaje = false;

    public function getInputClass()
    {
        return $this->_input_class;
    }

    public function getOutputClass()
    {
        return $this->_output_class;
    }

    // iWordformPhonology {{{

    public function isAMNYRight()
    {
        if (!is_null($this->is_amny))
            return $this->is_amny;
        return true;
    }

    // }}}

    // iSuffixumMorphology {{{

    public function hasOptionalInterfix()
    {
        return (mb_substr($this->lemma, 0, 1) === '_');
    }

    public function getOptionalInterfix()
    {
        if ($this->hasOptionalInterfix())
            return mb_substr($this->lemma, 1, 1);
        else
            return '';
    }

    public function getNonOptionalSuffix()
    {
        if ($this->hasOptionalInterfix())
            return mb_substr($this->lemma, 2);
        else
            return $this->lemma;
    }

    public static $invalid_suffix_regex_list = array(
        '/[bcdfghkmptvw],t/',
        '/[bcdfghklmnpqrstvwyz],[dkmn]/',
        '/[bcdfghklmnpqrstvwyz]{2,},[bcdfghklmnpqrstvwyz]/',
        '/[lrsy],t.+/', // @see barnulástok, hoteltek
    );

    public function isValidSuffixConcatenation($ortho_stem, $ortho_suffix)
    {
        $string = "$ortho_stem,$ortho_suffix";
        foreach (self::$invalid_suffix_regex_list as $regex)
        {
            if (preg_match($regex, $string))
            {
                return false;
            }
        }
        return true;
    }

    // kötőhang
    public function getInterfix(& $stem)
    {
        $interfix = '';
        if ($this->hasOptionalInterfix())
        {
            $_interfix = $this->getOptionalInterfix();
            $_interfix = Phonology::interpolateVowels($stem->needSuffixPhonocode(), $_interfix);
            if ($stem->isOpening() && !$stem->isLastVowel())
            {
                $interfix = $_interfix;
            }
            elseif ($stem->isLastVowel())
            {
                $interfix = '';
            }
            elseif ($this instanceof PossessiveSuffixum)
            {
                $interfix = $_interfix;
            }
            else
            {
                if ($this->isValidSuffixConcatenation($stem->ortho, $this->ortho))
                {
                    $interfix = '';
                }
                else
                {
                    $interfix = $_interfix;
                }
            }
        }
        return $interfix;
    }

    public function onAssimilated($char, $ortho)
    {
        $ortho = mb_substr($ortho, 1);
        return $ortho;
    }

    public function onBeforeSuffixed(& $stem)
    {
        $ortho = $this->getNonOptionalSuffix();
        if (Phonology::canAssimilate($stem->ortho, $ortho, $char = 'v'))
        {
            $stem->doAssimilate($char);
            $ortho = $this->onAssimilated($char, $ortho);
        }
        $ortho = Phonology::interpolateVowels($stem->needSuffixPhonocode(), $ortho);
        $this->ortho = $ortho;
    }

    public function onAfterSuffixed(& $stem)
    {
        if ($stem instanceof Nomen && $this->stop_jaje)
            $stem->is_jaje = false;
    }

}

interface iNumPers
{
    public function & makeNumPers($numero = 1, $person = 3);
    public function getNumero();
    public function getPerson();
}

class PossessiveSuffixum extends Suffixum implements iNumPers
{

    public $_input_class = 'iPossessable';
    public $_output_class = 'Nomen';
    public $numero = 1;
    public $person = 3;
    public $possessed_numero = 1;

    public static $suffixmap = array(
        1 => array(
            1 => array(1 => '_Vm', 2 => '_Vd', 3 => 'A'),
            3 => array(1 => '_Unk', 2 => '_VtEk', 3 => 'Uk'),
        ),
        3 => array(
            1 => array(1 => '_Aim', 2 => '_Aid', 3 => '_Ai'),
            3 => array(1 => '_Aink', 2 => '_AitWk', 3 => '_Aik'),
        ),
    );

    public function & makeNumPers($numero = 1, $person = 3, $possessed_numero = 1)
    {
        $suffixcode = self::$suffixmap[$possessed_numero][$numero][$person];
        $obj = new PossessiveSuffixum($suffixcode);
        $obj->numero = $numero;
        $obj->person = $person;
        $obj->possessed_numero = $possessed_numero;
        $obj->is_opening = true;
        $obj->is_vtmr = true;
        $obj->is_alternating = true;
        return $obj;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getPerson()
    {
        return $this->person;
    }

    public function onBeforeSuffixed(& $stem)
    {
        assert('$stem instanceof iPossessable');
        parent::onBeforeSuffixed($stem);
        // birtokos A/jA
        if ($this->person === 3 && $this->possessed_numero === 1 && $stem->isJaje())
            $this->ortho = 'j'.$this->ortho;
    }

    public function onAfterSuffixed(& $stem)
    {
        $stem->is_opening = true;
        $this->numero = $stem->numero;
    }

}

/** 
 * Birtokjel
 */
class PossessorSuffixum extends Suffixum
{
    public $_input_class = 'iPossessable';
    public $_output_class = 'Nomen';
    public $numero = 1;
    public $person = 3;
    public $possessed_numero = 1;

    public static function makePossessor($possessed_numero = 1)
    {
        $suffixcode = ($possessed_numero === 1) ? 'é' : 'éi';
        $obj = new PossessorSuffixum($suffixcode);
        $obj->possessed_numero = $possessed_numero;
        $obj->person = 3;
        $obj->is_opening = true;
        $obj->is_vtmr = false;
        return $obj;
    }

    public function onAfterSuffixed(& $stem)
    {
        $stem->is_opening = true;
        $this->numero = $stem->numero;
    }

}

class PostpositionSuffixum extends Suffixum
{

    // @todo
    public $_input_class = 'Nomen';
    public $_output_class = 'Nomen';

    public static $paradigm = array(
        0 => array(0 => ''),
        1 => array(1 => '_Am', 2 => '_Ad', 3 => 'A'),
        3 => array(1 => '_Unk', 2 => '_AtWk', 3 => 'Uk'),
    );

    public function __construct($numero=0, $person=0)
    {
        assert('isset(self::$paradigm[$numero][$person])');
        $this->lemma = self::$paradigm[$numero][$person];
        $this->numero = $numero;
        $this->person = $person;
    }

    public function onBeforeSuffixed(& $stem)
    {
        if ($stem->isLastVowel() && $this->numero === 1 && $this->person === 3)
            $this->lemma = ''; // LingVar: 'j'.$this->lemma; // alája
        if ($stem->isLastVowel() && $this->numero === 3 && $this->person === 3)
            $this->lemma = 'j'.$this->lemma;
        parent::onBeforeSuffixed($stem);
    }

}

interface iVerbal
{
    public function getCase();
    public function setCase($code);
    public function matchCase($regex);
}

class VerbalHelper
{

    public function getCase(& $that)
    {
        return $that->numero.$that->person.$that->mood.($that->tense < 0 ? (10+$that->tense) : $that->tense).$that->definite;
    }

    public function setCase(& $that, $code)
    {
        $that->numero   = (int) $code{0};
        $that->person   = (int) $code{1};
        $that->mood     = (int) $code{2};
        $that->tense    = (int) $code{3};
        $that->definite = (int) $code{4};
    }

    public function matchCase(& $that, $regex)
    {
        return (bool) preg_match("/^$regex$/", $that->getCase());
    }

}

abstract class aVerbalSuffixum extends Suffixum implements iVerbal
{

    public $_input_class = 'Verbum';
    public $_output_class = 'Verbum';

    public $mood = NULL;
    public $tense = NULL;
    public $definite = NULL;
    public $numero = NULL;
    public $person = NULL;

    public static $paradigm = array();

    /**
     * @param $numero = 1 egyes szám, 3 többes szám
     * @param $person = 1 első személy, 2 második személy, 3 harmadik személy
     * @param $mood = 1 kijelentő, 2 feltételes, 3 felszólító
     * @param $tense = -1 múlt, 0 jelen, 1 jövő
     * @param $definite = 0 alanyi, 3 tárgyas, 2 lAk
     */
    public function __construct($numero, $person, $mood=1, $tense=0, $definite=0)
    {
        $this->mood = $mood;
        $this->tense = $tense;
        $this->definite = $definite;
        $this->numero = $numero;
        $this->person = $person;
    }

    public function onAfterSuffixed(& $stem)
    {
        $stem->mood = $this->mood;
        $stem->tense = $this->tense;
        $stem->definite = $this->definite;
        $stem->numero = $this->numero;
        $stem->person = $this->person;
    }

    public function getCase()
    {
        return VerbalHelper::getCase($this);
    }

    public function setCase($code)
    {
        return VerbalHelper::setCase($this, $code);
    }

    public function matchCase($regex)
    {
        return VerbalHelper::matchCase($this, $regex);
    }

}

class VerbalSuffixum1 extends aVerbalSuffixum
{

    public function __construct($numero, $person, $mood=1, $tense=0, $definite=0)
    {
        parent::__construct($numero, $person, $mood, $tense, $definite);
        if ($this->tense === -1) // múlt idő jele
            $this->lemma = $this->ortho = 't';
        if ($this->mood === 2 && $this->tense === 0) // feltételes mód jele
            $this->lemma = $this->ortho = 'n';
        if ($this->mood === 3) // felszólító mód jele
            $this->lemma = $this->ortho = 'j';
    }

    public function onBeforeSuffixed(& $stem)
    {

        if ($this->tense === -1) // múlt idő jele
        {
            if ($stem->needVtt($this)) // Vtt
                $this->ortho =  Phonology::interpolateVowels($stem->needSuffixPhonocode(), 'Vtt');
            elseif ($stem->needTT()) // tt
                $this->ortho = 'tt';
            else // t
                $this->ortho = 't';
        }

        if ($this->mood === 2 && $this->tense === 0) // feltételes mód jele
        {
            if ($stem->needNN())
                $this->ortho = 'nn';
            else
                $this->ortho = 'n';
        }

        if ($this->mood === 3) // felszólító mód jele
        {
            $last = Phonology::getLastConsonant($stem->ortho);
            if ($stem->needJggy())
                $this->ortho = 'ggy';
            elseif ($stem->needJgy())
                $this->ortho = 'gy';
            elseif ($stem->needJss())
                $this->ortho = 'ss';
            elseif ($stem->needJs())
                $this->ortho = 's';
            elseif ($stem->needJAssim())
            {
                $ortho = $char = 'j';
                if (Phonology::canAssimilate($stem->ortho, $ortho, $char))
                {
                    $stem->doAssimilate($char);
                    $ortho = $this->onAssimilated($char, $ortho);
                }
                $this->ortho = $ortho;
            }
            else
                $this->ortho = 'j';
        }

    }

    public function getInterfix(& $stem)
    {
        if (!$this->isValidSuffixConcatenation($stem->ortho, $this->ortho))
            return Phonology::interpolateVowels($stem->needSuffixPhonocode(), 'A');
    }

    public static $invalid_suffix_regex_list = array(
        '/lt,n/',
    );

    public function isValidSuffixConcatenation($ortho_stem, $ortho_suffix)
    {
        $string = "$ortho_stem,$ortho_suffix";
        foreach (self::$invalid_suffix_regex_list as $regex)
            if (preg_match($regex, $string))
                return false;
        return true;
    }

    public function onAfterSuffixed(& $stem)
    {
        parent::onAfterSuffixed($stem);
        if ($this->mood === 3)
            $stem->is_opening = true;
    }

}

class VerbalSuffixum2 extends aVerbalSuffixum
{

    /**
     * $paradigm[$mood][$tense][$definite][$numero][$person]
     * $mood = 1 kijelentő, 2 feltételes, 3 felszólító
     * $tense = -1 múlt, 0 jelen, 1 jövő
     * $definite = 0 alanyi, 3 tárgyas, 2 lAk
     * $numero = 1 egyes szám, 3 többes szám
     * $person = 1 első személy, 2 második személy, 3 harmadik személy
     */
    public static $paradigm = array(
        1 => array(
            0 => array(
                0 => array(
                    1 => array(1 => 'Vk', 2 => '_Asz|Vl', 3 => ''),
                    3 => array(1 => 'Unk', 2 => '_VtVk', 3 => '_AnAk'),
                ),
                2 => array(
                    1 => array(1 => 'lAk'),
                ),
                3 => array(
                    1 => array(1 => 'Vm', 2 => 'Vd', 3 => 'ja|i'),
                    3 => array(1 => 'jUk', 2 => 'jÁtVk|itek', 3 => 'jÁk|ik'),
                ),
            ),
            -1 => array(
                0 => array(
                    1 => array(1 => 'Am', 2 => 'Ál', 3 => ''),
                    3 => array(1 => 'Unk', 2 => '_AtWk', 3 => 'Ak'),
                ),
                2 => array(
                    1 => array(1 => 'AlAk'),
                ),
                3 => array(
                    1 => array(1 => 'Am', 2 => 'Ad', 3 => 'A'),
                    3 => array(1 => 'Uk', 2 => 'ÁtWk', 3 => 'Ák'),
                ),
            ),
        ),
        2 => array(
            0 => array(
                0 => array(
                    1 => array(1 => 'ék', 2 => 'Ál', 3 => 'A'),
                    3 => array(1 => 'Ánk', 2 => 'ÁtWk', 3 => 'ÁnAk'),
                ),
                2 => array(
                    1 => array(1 => 'ÁlAk'),
                ),
                3 => array(
                    1 => array(1 => 'Ám', 2 => 'Ád', 3 => 'Á'),
                    3 => array(1 => 'Ánk', 2 => 'ÁtWk', 3 => 'Ák'),
                ),
            ),
        ),
        3 => array(
            0 => array(
                0 => array(
                    1 => array(1 => 'Ak', 2 => 'Ál', 3 => 'On'),
                    3 => array(1 => 'Unk', 2 => 'AtWk', 3 => 'AnAk'),
                ),
                2 => array(
                    1 => array(1 => 'AlAk'),
                ),
                3 => array(
                    1 => array(1 => 'Am', 2 => 'Ad', 3 => 'A'),
                    3 => array(1 => 'Uk', 2 => 'ÁtWk', 3 => 'Ák'),
                ),
            ),
        ),
    );

    public function __construct($numero, $person, $mood=1, $tense=0, $definite=0)
    {
        assert('isset(self::$paradigm[$mood][$tense][$definite][$numero][$person])');
        parent::__construct($numero, $person, $mood, $tense, $definite);
        $this->lemma = $this->ortho = self::$paradigm[$mood][$tense][$definite][$numero][$person];
    }

    public static $invalid_suffix_regex_list = array(
        '/[dlstz]t,[nt]/',
        '/t,tt/',
        '/lt,sz/',
        '/lsz,[nt]/',
    );

    public function isValidSuffixConcatenation($ortho_stem, $ortho_suffix)
    {
        $string = "$ortho_stem,$ortho_suffix";
        foreach (self::$invalid_suffix_regex_list as $regex)
            if (preg_match($regex, $string))
                return false;
        return true;
    }

    public function getInterfix(& $stem)
    {
        $interfix = '';
        if ($this->hasOptionalInterfix() && !$this->isValidSuffixConcatenation($stem->ortho, $this->ortho))
        {
            $interfix .= $this->getOptionalInterfix();
            $interfix = Phonology::interpolateVowels($stem->needSuffixPhonocode(), $interfix);
        }
        return $interfix;
    }

    public function onBeforeSuffixed(& $stem)
    {
        $lemma = $this->lemma;
        if (strpos($lemma, '|'))
        {
            $alters = explode('|', $lemma);
            // Vl/Asz
            if ($this->matchCase('12100') && $stem->isLastAffrSyb())
                $i = 1;
            elseif ($this->matchCase('(13|32|33)103') && $stem->needSuffixI())
                $i = 1;
            else
                $i = 0;
            $lemma = $alters[$i];
        }
        $this->lemma = $lemma;

        $ortho = $this->getNonOptionalSuffix();
        if (!$stem->isLastT())
        {
            if (Phonology::canAssimilate($stem->ortho, $ortho, $char = 'j'))
            {
                $stem->doAssimilate($char);
                $ortho = $this->onAssimilated($char, $ortho);
            }
        }
        $ortho = Phonology::interpolateVowels($stem->needSuffixPhonocode(), $ortho);

        if ($stem->ikes && $this->matchCase('13100'))
            $ortho = 'ik';

        $this->ortho = $ortho;
    }

}

class InfinitiveSuffixum extends aVerbalSuffixum
{

    public static $paradigm = array(
        1 => array(
            0 => array(
                0 => array(
                    0 => array(0 => 'ni'),
                    1 => array(1 => 'nVm', 2 => 'nVd', 3 => 'niA'),
                    3 => array(1 => 'nUnk', 2 => 'nVtVk', 3 => 'niUk'),
                ),
            ),
        ),
    );

    public function __construct($numero, $person, $mood=1, $tense=0, $definite=0)
    {
        assert('isset(self::$paradigm[$mood][$tense][$definite][$numero][$person])');
        $this->lemma = self::$paradigm[$mood][$tense][$definite][$numero][$person];
    }

    public function onBeforeSuffixed(& $stem)
    {
        $lemma = $this->lemma;
        if ($stem->isSZV)
            $lemma = 'n'.$lemma;
        if (!$this->isValidSuffixConcatenation($stem->ortho, $lemma))
        {
            $lemma = 'A'.$lemma;
            $stem->is_opening = true;
        }
        $this->ortho = Phonology::interpolateVowels($stem->needSuffixPhonocode(), $lemma);
    }

    public static $invalid_suffix_regex_list = array(
        '/lt,n/',
    );

    public function isValidSuffixConcatenation($ortho_stem, $ortho_suffix)
    {
        $string = "$ortho_stem,$ortho_suffix";
        foreach (self::$invalid_suffix_regex_list as $regex)
            if (preg_match($regex, $string))
                return false;
        return true;
    }

}

class Verbum extends Wordform implements iVerbal
{
    public $mood = NULL;
    public $tense = NULL;
    public $definite = NULL;
    public $numero = NULL;
    public $person = NULL;

    public $isPlusV = false;
    public $isSZV = false;
    public $isSZDV = false;
    public $ikes = false;

    // Szótári alak
    public function & getCitationForm()
    {
        return $this->conjugate(1, 3, 1, 0, 0);
    }

    public function & conjugate($numero, $person, $mood, $tense, $definite)
    {
        // 'ragoztam volna': morfológiai szó
        if ($mood === 2 && $tense === -1)
        {
            $clone1 = & $this->appendSuffix(new VerbalSuffixum1($numero, $person, 1, $tense, $definite));
            $clone2 = & $clone1->appendSuffix(new VerbalSuffixum2($numero, $person, 1, $tense, $definite));
            $morphoword = new MorphoWord(new Wordform('volna'), $clone2);
            return $morphoword;
        }
        else
        {
            $clone1 = & $this->appendSuffix(new VerbalSuffixum1($numero, $person, $mood, $tense, $definite));
            $clone2 = & $clone1->appendSuffix(new VerbalSuffixum2($numero, $person, $mood, $tense, $definite));
            return $clone2;
        }
    }

    public function onBeforeSuffixation(& $suffix)
    {

        if ($this->isPlusV && $suffix->matchCase('..10.') && $suffix instanceof VerbalSuffixum2)
        {
            // @fixme this is a bit too complex. store the results in the lexicon instead?
            if (
                $suffix->matchCase('.1..0')
                || (
                    $suffix->matchCase('....3') 
                    && !($this->needSuffixI() && $suffix->matchCase('31...'))
                    && !(!$this->needSuffixI() && $suffix->matchCase('13...|3....'))
                )
            )
            {
                $this->ortho = $this->lemma2;
            }
        }

        if ($this->isSZV && $suffix instanceof VerbalSuffixum1)
        {
            if ($suffix->matchCase('..[23]..|...9.'))
                $this->ortho = $this->lemma2;
            //if ($suffix->matchCase('..20.')) // nn
            //if ($suffix->matchCase('..3..')) // gy, ggy
        }

        if ($this->isSZV && $suffix instanceof InfinitiveSuffixum)
        {
            $this->ortho = $this->lemma2;
        }

        if ($this->isSZDV && $suffix instanceof VerbalSuffixum1)
        {
            if ($suffix->matchCase('..[23]..|...9.'))
                $this->ortho = $this->lemma2;
        }

        if ($this->isSZDV && $suffix instanceof InfinitiveSuffixum)
        {
            $this->ortho = $this->lemma2;
        }

        // @fixme
        if ($this->lemma === 'alsz' && $suffix instanceof VerbalSuffixum2)
        {
            if ($suffix->matchCase('(13|3.)103'))
                $this->ortho = 'alusz';
        }

        // @fixme
        if ($this->lemma === 'isz' && $suffix instanceof VerbalSuffixum1)
        {
            if ($suffix->matchCase('13190'))
            {
                $this->ortho = 'iv';
            }
        }

        // @fixme
        if ($this->lemma === 'esz' && $suffix instanceof VerbalSuffixum1)
        {
            if ($suffix->matchCase('13190'))
            {
                $this->ortho = 'ev';
            }
        }

    }

    public function needNN()
    {
        return $this->isSZV;
    }

    public function needTT()
    {
        return ($this->isPlusV || $this->isSZV);
    }

    // ha V+t, akkor ss,
    public function needJss()
    {
        $last = Phonology::getLastConsonant($this->ortho);
        $last1 = Phonology::getLastConsonant(mb_substr($this->ortho, 0, -mb_strlen($last)));
        return Phonology::isVowel($last1) && ($last === 't' || $last === 'tt');
    }

    public function isLastAffrSyb()
    {
        $char = Phonology::getLastConsonant($this->ortho);
        return Phonology::isAffrikate($char) || Phonology::isSybyl($char);
    }

    public function isLastT()
    {
        $char = Phonology::getLastConsonant($this->ortho);
        return ($char === 't' || $char === 'tt');
    }

    public function needJs()
    {
        $last = Phonology::getLastConsonant($this->ortho);
        $last1 = Phonology::getLastConsonant(mb_substr($this->ortho, 0, -mb_strlen($last)));
        return 
            !(Phonology::isVowel($last1) || Phonology::isAffrikate($last1) || Phonology::isSybyl($last1))
            && $this->isLastT();
    }

    public function needJgy()
    {
        return $this->isSZV && !($this->lemma === 'hisz');
    }

    public function needJggy()
    {
        return $this->lemma === 'hisz';
    }

    // zörej+j => zörejhez hasonul
    // ha zörej+t, akkor hasonul,
    public function needJAssim()
    {
        $last = Phonology::getLastConsonant($this->ortho);
        if (Phonology::isSybyl($last))
            return true;
        $last1 = Phonology::getLastConsonant(mb_substr($this->ortho, 0, -mb_strlen($last)));
        return 
            (Phonology::isAffrikate($last1) || Phonology::isSybyl($last1))
            && $this->isLastT();
    }

    /**
     * @todo gAt, _tAt, hAt => +Vtt
     * CC (C!=t) => ingadozók
     */
    public function needVtt(& $suffix)
    {
        $cons = Phonology::getLastConsonant($this->lemma);
        if ($cons === 't')
            return true;
        $arr = array('m', 'v', 'r', 's', 'ss', 't', 'tt', 'z', 'zz', 'zs', 'zzs');
        if (in_array($cons, $arr))
        {
            if ($suffix->matchCase('13..0'))
                return true;
            else
                return false;
        }
        if ($this->lemma === 'isz')
        {
            if ($suffix->matchCase('13190'))
            {
                return true;
            }
        }
        if ($this->lemma === 'esz')
        {
            if ($suffix->matchCase('13190'))
            {
                return true;
            }
        }
        return false;
    }

    public function getCase()
    {
        return VerbalHelper::getCase($this);
    }

    public function setCase($code)
    {
        return VerbalHelper::setCase($this, $code);
    }

    public function matchCase($regex)
    {
        return VerbalHelper::matchCase($this, $regex);
    }

    public function & makeInfinitive($numero=0, $person=0)
    {
        $suffix = new InfinitiveSuffixum($numero, $person);
        return $this->appendSuffix($suffix);
    }

    // tAt Műveltető
    public function & makeCausative() { }

    // gerund, -Ás
    public function & makeVerbalNoun() { }

    // -Ó
    // Ott / t
    // AndÓ
    public function & makeParticiple($tense) { }

    // -hAt
    public function & makeModal() {  }

    // Igekötő
    public function & addParticle($particle) { }

    public function & addAuxiliary($aux) { }

    // auxiliaries : kell kellene kéne muszáj szabad tilos fog tud szokott
}

/** Valid nominal cases in hungarian.
 */
interface iNominalCases
{
    public function & makeCase($case);
    public function & makeNominativus();
    public function & makeAccusativus();
    public function & makeCausalisFinalis();
    public function & makeDativus();
    public function & makeInstrumentalis();
    public function & makeTranslativusFactivus();
    public function & makeFormativus();
    public function & makeEssivusFormalis();
    public function & makeIllativus();
    public function & makeInessivus();
    public function & makeElativus();
    public function & makeSublativus();
    public function & makeSuperessivus();
    public function & makeDelativus();
    public function & makeAllativus();
    public function & makeAdessivus();
    public function & makeAblativus();
    public function & makeTerminativus();
}

/** Invalid (virtual) nominal cases in hungarian.
 */
interface iVirtualNominalCases
{
    public function & makeGenitivus();
    public function & makeCausalis();
    //public function & makeExessivus();
    public function & makePerlativus();
    public function & makeProlativus();
    public function & makeVialis();
    public function & makeSubessivus();
    public function & makeProsecutivus();
    //public function & makeApudessivus();
    //public function & makeInitiativus();
    //public function & makeEgressivus();
}

/** Invalid (virtual) temporal cases in hungarian.
 */
interface iVirtualTemporalCases
{
    public function & makeAntessivus();
    public function & makeTemporalis(); // -kor
}

interface iPossessable
{
    public function isJaje();
}

class Nomen extends Wordform implements iPossessable, iNominalCases, iVirtualNominalCases, iNumPers
{

    public $case = 'Nominativus';
    public $numero = 1;
    public $person = 3;

    public $is_jaje = NULL;
    public $lemma2 = '';

    public function __construct($lemma, $ortho=NULL)
    {
        parent::__construct($lemma, $ortho);
        $this->lemma2 = $lemma;
    }

    public function & makeNumPers($numero = 1, $person = 3)
    {
        $this->numero = $numero;
        $this->person = $person;
    }

    public function getNumero()
    {
        return $this->numero;
    }

    public function getPerson()
    {
        return $this->person;
    }

    /** Hint.
     */
    public function isJaje()
    {
        if (!is_null($this->is_jaje))
        {
            return $this->is_jaje; // már adott lexikonban
        }
        if ($this->isLastVowel())
        {
            return true; // mindig
        }
        if (preg_match('/(s|sz|z|zs|c|cs|dzs|gy|j|ny|ty|tor|ter|er|um)$/', $this->ortho))
        {
            return false; // általában
        }
        if (preg_match('/[bcdfghjklmnprstvxz]{2,}$/', $this->ortho))
        {
            return true; // általában
        }
        return false; // egyébként általában nem
    }

    /** Nomen is alternating only if not yet inflexed.
     */
    public function isAlternating()
    {
        return $this->is_alternating && ($this->lemma === $this->ortho);
    }

    public function isAMNYRight()
    {
        return false;
    }

    public function & makePlural()
    {
        //$clone = $this->makeNominativus();
        $clone = clone $this;
        if ($this->isPlural())
            return $clone;
        $clone->numero = 3;
        return $clone->appendSuffix(GFactory::parseSuffixum('_Vk'));
    }

    public function isSingular()
    {
        return ($this->numero == 1);
    }

    public function isPlural()
    {
        return ($this->numero > 1);
    }

    // cases helpers {{{

    public function & _makeCaseFromNominativusWithSuffix($case, & $suffix)
    {
        if ($this->isNominativus())
            $clone = $this->appendSuffix($suffix);
        else
            $clone = $this->makeNominativus()->appendSuffix($suffix);
        $clone->case = $case;
        return $clone;
    }

    public function isNominativus()
    {
        return ($this->case === 'Nominativus');
    }

    public function isAccusativus()
    {
        return ($this->case === 'Accusativus');
    }

    // }}}

    // interface iNominalCases {{{

    public function & makeCase($case)
    {
        $method = "make$case";
        if (method_exists($this, $method))
            return $this->$method();
        else
            throw Exception("No such case: $case");
    }

    public function & makeNominativus()
    {
        $clone = clone $this;
        $clone->case = 'Nominativus';
        if ($this->isPlural())
            $clone = $clone->makePlural();
        return $clone;
    }

    public function & makeAccusativus()
    {
        $clone = & $this->makeNominativus()->appendSuffix(GFactory::parseSuffixum('_Vt'));
        $clone->case = 'Accusativus';
        return $clone;
        return $this->_makeCaseFromNominativusWithSuffix('Accusativus', GFactory::parseSuffixum('_Vt'));
    }

    public function & makeCausalisFinalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('CausalisFinalis', GFactory::parseSuffixum('ért'));
    }

    public function & makeDativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Dativus', GFactory::parseSuffixum('nAk'));
    }

    public function & makeInstrumentalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Instrumentalis', GFactory::parseSuffixum('vAl'));
    }

    public function & makeTranslativusFactivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('TranslativusFactivus', GFactory::parseSuffixum('vÁ'));
    }

    public function & makeFormativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Formativus', GFactory::parseSuffixum('ként'));
    }

    public function & makeEssivusFormalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('EssivusFormalis', GFactory::parseSuffixum('Ul'));
    }

    public function & makeIllativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Illativus', GFactory::parseSuffixum('bA'));
    }

    public function & makeInessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Inessivus', GFactory::parseSuffixum('bAn'));
    }

    public function & makeElativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Elativus', GFactory::parseSuffixum('bÓl'));
    }

    public function & makeSublativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Sublativus', GFactory::parseSuffixum('rA'));
    }

    public function & makeSuperessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Superessivus', GFactory::parseSuffixum('_On'));
    }

    public function & makeDelativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Delativus', GFactory::parseSuffixum('rÓl'));
    }

    public function & makeAllativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Allativus', GFactory::parseSuffixum('hOz'));
    }

    public function & makeAdessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Adessivus', GFactory::parseSuffixum('nÁl'));
    }

    public function & makeAblativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Ablativus', GFactory::parseSuffixum('tÓl'));
    }

    public function & makeTerminativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Terminativus', GFactory::parseSuffixum('ig'));
    }

    // helpers {{{

    public function & _makeCaseWithNU($case, & $head)
    {
        $ADVP = new ADVP_NU($head, $this->makeNominativus());
        $ADVP->case = $case;
        return $ADVP;
    }

    public function & _makeCaseWithHRHSZ($case, & $head, & $suffix)
    {
        $ADVP = new ADVP_HRHSZ($head, $this->makeNominativus(), $suffix);
        $ADVP->case = $case;
        return $ADVP;
    }

    public function & _makeCaseWithHNHSZ($case, & $head)
    {
        $ADVP = new ADVP_HNHSZ($head, $this->makeNominativus());
        $ADVP->case = $case;
        return $ADVP;
    }

    // }}}

    // interface iVirtualNominalCases {{{

    public function & makeGenitivus()
    {
        return $this->makeDativus();
    }

    public function & makeCausalis()
    {
        return $this->_makeCaseWithNU('Causalis', GFactory::parseNP('miatt'));
    }

    // @skip public function & makeExessivus();

    public function & makePerlativus()
    {
        return $this->_makeCaseWithHRHSZ('Perlativus', GFactory::parseNP('keresztül'), GFactory::parseSuffixum('On'));
    }

    public function & makeProlativus()
    {
        return $this->_makeCaseWithHRHSZ('Perlativus', GFactory::parseNP('át'), GFactory::parseSuffixum('On'));
    }

    public function & makeVialis()
    {
        return $this->makeProlativus();
    }

    public function & makeSubessivus()
    {
        return $this->_makeCaseWithNU('Perlativus', GFactory::parseNP('alatt'));
    }

    public function & makeProsecutivus()
    {
        return $this->_makeCaseWithHNHSZ('Prosecutivus', GFactory::parseNP('mentén'));
    }

    //public function & makeApudessivus();
    //public function & makeInitiativus();
    //public function & makeEgressivus();

    //public function & makeAntessivus();
    public function & makeTemporalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Temporalis', GFactory::parseSuffixum('kor'));
    }

    // }}}

}

class AdjSuffixum extends Suffixum
{

    public $_input_class = 'Adj';
    public $_output_class = 'Adj';

}

class Adj extends Nomen
{

    public function & makeComparativus()
    {
        if (!($this->case === "Nominativus"))
            throw new Exception('Adj '.__METHOD__.'() needs Nominativus');
        $bb = & GFactory::parseSuffixum('_Vbb')->cloneAs('AdjSuffixum');
        $A = & $this->appendSuffix($bb);
        $A->case = 'Comparativus';
        return $A;
    }

    public function & makeSuperlativus()
    {
        if (!($this->case === "Nominativus"))
            throw new Exception('Adj '.__METHOD__.'() needs Nominativus');
        $A = & $this->makeComparativus();
        // @todo prependPrefix()
        $A->ortho = 'leg'.$A->ortho;
        $A->case = 'Superlativus';
        return $A;
    }

    public function & makeSuperlativus2()
    {
        if (!($this->case === "Nominativus"))
            throw new Exception('Adj '.__METHOD__.'() needs Nominativus');
        $A = & $this->makeSuperlativus();
        $A->ortho = 'leges'.$A->ortho;
        $A->case = 'Superlativus2';
        return $A;
    }

    // @fixme english/latin name
    public function & kiemelo()
    {
        if (!($this->case === "Comparativus" || $this->case === "Superlativus" || $this->case === "Superlativus2"))
            throw new Exception('Adj '.__METHOD__.'() needs Comparativus or Superlativus or Superlativus2');
        $bb = & GFactory::parseSuffixum('ik')->cloneAs('AdjSuffixum');
        $A = & $this->appendSuffix($bb);
        $A->case = '+';
        return $A;
    }

}

abstract class HeadedExpression
{
    public $case = NULL;
    public $head = NULL;
    public $arg = NULL;

    public function __construct(& $head, & $arg)
    {
        $this->head = & $head;
        $this->arg = & $arg;
    }

    public function & pronominalize()
    {
        throw new Exception();
    }

    public function __toString()
    {
        return '[' . (string) $this->head . ' ' . $this->arg . ']';
    }

}

/** Morfológiai szó
 */
class MorphoWord extends HeadedExpression
{

    public function __toString()
    {
        return (string) $this->arg . ' ' . $this->head;
    }

}

class ADVP_NU extends HeadedExpression
{

    public function __toString()
    {
        return (string) $this->arg . ' ' . $this->head;
    }

    public function & pronominalize()
    {
        return $this->head->appendSuffix(PossessiveSuffixum::makeNumPers($this->arg->numero, 3));
    }

}

class ADVP_HRHSZ extends HeadedExpression
{
    public $suffix = NULL;

    public function __construct(& $head, & $arg, & $suffix)
    {
        parent::__construct($head, $arg);
        $this->suffix = & $suffix;
    }

    public function __toString()
    {
        return $this->arg->appendSuffix($this->suffix) . ' ' . $this->head;
    }

}

class ADVP_HNHSZ extends HeadedExpression
{

    public function __toString()
    {
        return (string) $this->arg . ' ' . $this->head;
    }

}

/**
 * @todo fának kell lennie, ld. [ágál vki [vmi ellen]]
 */
class Caseframe
{

    public $argdef = array();
    public $args = array();
    public $relorder = 'VSO12'; // standard rel order

    public function __construct(& $verb, $argdef)
    {
        $this->args['V'] = & $verb;
        $this->argdef = $argdef;
    }

    public function setArg($rel, & $arg)
    {
        $this->args[$rel] = & $arg;
    }

    /** @todo Ki mikor készíti elő az argumentumokat? Ki mikor hogyan ellenőriz?
     * 1. hívásmód: csak nominativus argumentumokat adunk meg, a teljes előkészítést elvárjuk,
     * pl. makeCaseframe1('ágál', 'valaki', 'valami') => "ágál valaki valami ellen"
     * 2. hívásmód: minden argumentumot előkészítettünk már, max ellenőrzést várunk,
     * pl. makeCaseframe1(parseV('ágál')->conjugate(3, 1, 2, -1, 0), makePronom(3, 1), new ADVP_NU('ellen', parseNP('valami')->makePlural())) => "ágáltunk volna valamik ellen"
     *
     * @todo Ha a komplementumok nem esetben vannak, hanem pl. névutósítottak (ld. ágál vmi ellen):
     * Esetleg valami action-objektumba zárhatnánk? 
     */
    public function prepareComponent($rel)
    {
        if ($rel === 'V')
        {
            $S = & $this->prepareComponent('S');
            if ($S)
                $V = & $this->args['V']->conjugate($S->getNumero(), $S->getPerson(), 1, 0, 0);
            else
                $V = & $this->args['V']->conjugate(1, 3, 1, 0, 0);
            return $V;
        }
        elseif (isset($this->args[$rel]))
        {
            if ($this->args[$rel] instanceof Nomen)
            {
                $case = $this->argdef[$rel];
                return $this->args[$rel]->makeCase($case);
            }
        }
        return $this->args[$rel];
    }

    /**
     * p. 30.
     */
    public function __toString()
    {
        $strs = array();
        foreach (str_split($this->relorder) as $rel)
        {
            $strs[] = (string) $this->prepareComponent($rel);
        }
        return implode(' ', array_filter($strs));
    }

}

/*
 * @todo Képzők
 *
 * N -> N
 * _Vs s os as es ös
 * né né
 * kA ka ke
 * _VcskA cska cske ocska acska ecske öcske
 * féle féle
 *
 * N -> ADJ
 * i i
 * _Vs s os es ös as ás és
 * _jÚ ú ű jú jű
 * ...
 *
 * V -> N
 * Ás ás és
 * Ó ó ő
 *
 * V -> ADJ
 * Ós ós ős
 * _AtlAn tlan tlen atlan etlen
 * tAlAn
 * hAtÓ ható hető
 * hAtAtlAn hatatlan hetetlen
 *
 * V -> NV
 * ni
 * Ó ó ő
 * t t
 * Vtt tt ott ett ött
 * AndÓ andó endő
 * vA va ve
 *
 * V -> V
 * _VgAt gat get ogat eget öget
 * _tAt at et tat tet
 * _tAtik 
 * ...
 */

class GFactory
{

    // full list
    public static $N_vtmr_list = array(
        'híd', 'ín', 'nyíl', 'víz',
        'szűz', 'tűz', 'fű', 'nyű',
        'kút', 'lúd', 'nyúl', 'rúd', 'úr', 'út', 'szú',
        'cső', 'kő', 'tő',
        'ló',
        'kéz', 'réz', 'mész', 'ész', 'szén', 'név', 'légy', 'ég', 'jég', 'hét', 'tér', 'dér', 'ér', 'bél', 'nyél', 'fél', 'szél', 'dél', 'tél', 'lé',
        'nyár', 'sár',
        // A kéttagúak első mghja mindig rövid - egyszerűen az egész alakot lehet rövidíteni.
        'egér', 'szekér', 'tenyér', 'kenyér', 'levél', 'fedél', 'fenék', 'kerék', 'cserép', 'szemét', 'elég', 'veréb', 'nehéz', 'tehén', 'derék',
        'gyökér', 'kötél', 'közép',
        'fazék',
        'madár', 'szamár', 'agár', 'kanál', 'darázs', 'parázs',
        'bogár', 'kosár', 'mocsár', 'mozsár', 'pohár', 'fonál',
        'sugár', 'sudár',
    );

    // @todo tő és toldalék elkülönítése: mít|osz, ennek konstruálásakor legyen lemma=mít, és legyen a "nominális toldalék" osz, képzéskor pedig nem a nominálisból, hanem a lemmából képezzünk. (?)
    // not full list
    public static $N_btmr_list = array(
        'aktív', 'vízió', 'miniatűr', 'úr', 'fúzió', 'téma', 'szláv', 'privát',
        'náció', 'analízis', 'mítosz', 'motívum', 'stílus',
        'kultúra', 'múzeum', 'pasztőr', 'periódus', 'paródia',
        'kódex', 'filozófia', 'história', 'prémium', 'szintézis',
        'hérosz', 'matéria', 'klérus', 'május', 'banális',
        'elegáns',
    );

    // not full list
    // not opening e.g.: gáz bűz rés
    public static $N_opening_list = array('út', 'nyár', 'ház', 'tűz', 'víz', 'föld', 'zöld', 'nyúl', 'híd', 'nyíl', 'bátor', 'ajak', 'kazal', 'ló', 'hó', 'fű', 'hazai', );

    // not full list
    public static $N_jaje = array(
        'nagy' => true,
        'pad' => true,
        'sárkány' => true,
        'kupec' => true,
        'kortes' => true,
        'macesz' => true,
        'trapéz' => true,
        'rassz' => true,
        'miatt' => false,
    );

    // is full list? latin/english name?
    public static $N_alternating_list = array(
        'ajak' => 'ajk',
        'bagoly' => 'bagly',
        'bajusz' => 'bajsz',
        'bátor' => 'bátr',
        'dolog' => 'dolg',
        'haszon' => 'haszn',
        'izom' => 'izm',
        'kazal' => 'kazl',
        'lepel' => 'lepl',
        'majom' => 'majm',
        'piszok' => 'piszk',
        'torony' => 'torny',
        'tücsök' => 'tücsk',
        'tükör' => 'tükr',
        'tülök' => 'tülk',
        'vacak' => 'vack',
        'álom' => 'álm',
        // v-vel bővülő tövek, nem teljes lista
        'ló' => 'lov',
        'fű' => 'füv',
        'hó' => 'hav',
        // hangátvetéses váltakozás, nem teljes lista
        'teher' => 'terh',
        'pehely' => 'pelyh',
        'kehely' => 'kelyh',
    );

    public static $needSuffixI = array(
        'híd' => false,
        'nyíl' => false,
        'oxigén' => true,
        'valami' => true,
        'valaki' => true,
    );

    public static function parseNP($string)
    {
        $obj = new Nomen($string);
        $obj->is_vtmr = in_array($string, self::$N_vtmr_list, true);
        $obj->is_btmr = in_array($string, self::$N_btmr_list, true);
        $obj->is_opening = in_array($string, self::$N_opening_list, true);
        if (isset(self::$N_jaje[$string]))
            $obj->is_jaje = self::$N_jaje[$string];
        if (isset(self::$needSuffixI[$string]))
            $obj->needSuffixI = self::$needSuffixI[$string];
        $obj->is_alternating = isset(self::$N_alternating_list[$string]);
        if ($obj->is_alternating)
            $obj->lemma2 = self::$N_alternating_list[$string];
        return $obj;
    }

    public static function parseADJ($string)
    {
        $obj = & GFactory::parseNP($string)->cloneAs('Adj');
        $obj->is_opening = true; // a melléknevek túlnyomó többsége nyitótővű
        return $obj;
    }

    // not full list
    public static $suffixum_vtmr_list = array(
        '_Vk', // többesjel
        '_Vt', // tárgyrag
        // birtokos személyragok
        'Vs', // melléknévképző
        'Az', // igeképző
        '_VcskA', // kicsinyítő képző
    );

    // not full list
    public static $suffixum_btmr_list = array(
        'ista',
        'izál',
        'izmus',
        'ikus',
        'atív',
        'itás',
        'ális',
        'íroz',
        'nál', // ? fuzionál
    );

    // is full list?
    public static $suffixum_opening_list = array(
        '_Vk', // többesjel
        // birtokos személyjelek
        '_Vbb', // középfok jele 
        // múlt idő jele 
        // felszólító j 
    );

    public static $suffixum_not_AMNY_right_list = array(
        'kor', 'ista', 'izmus', // stb. átlátszatlan toldalékok
        'szOr', 'sÁg', 'i', 'ként',
    );

    // is full list? latin/english name?
    public static $suffixum_alternating_list = array(
        '_Vt', // tárgyrag
        'On', // Superessivus
        '_Vk', // többesjel
        // birtokos személyragok
        'VstUl',
        'Vs', // melléknévképző
        'Vnként',
        '_VcskA', // kicsinyítő képző
    );

    public static $suffixum_classes = array(
        'Ás' => array('Verbum', 'Nomen'),
        'Ul' => array('Nomen', 'Verbum'),
    );

    public static $suffixum_stop_jaje_list = array('sÁg');

    public static function parseSuffixum($string)
    {
        $obj = new Suffixum($string);
        $obj->is_vtmr = in_array($string, self::$suffixum_vtmr_list, true);
        $obj->is_btmr = in_array($string, self::$suffixum_btmr_list, true);
        $obj->is_opening = in_array($string, self::$suffixum_opening_list, true);
        $obj->is_amny = !in_array($string, self::$suffixum_not_AMNY_right_list);
        $obj->is_alternating = in_array($string, self::$suffixum_alternating_list);
        $obj->stop_jaje = in_array($string, self::$suffixum_stop_jaje_list, true);

        if (isset(self::$suffixum_classes[$string]))
            list($_input_class, $_output_class) = self::$suffixum_classes[$string];
        else
        {
            $_input_class = 'Nomen';
            $_output_class = 'Nomen';
        }
        $obj->_input_class = $_input_class;
        $obj->_output_class = $_output_class;
        return $obj;
    }

    // vtmr verbs, not full list: 
    // ir-at sziv-attyú tür-elem bün-tet szur-ony buj-kál huz-at usz-oda szöv-és vag-dal
    public static $V_btmr_list = array(
        'ír',
        'szív',
        'tűr',
        'bűn',
        'szúr',
        'búj',
        'húz',
        'úsz',
        'sző',
        'vág',
    );

    public static $V_opening_list = array(
        'zöldül',
    );

    // full list : lő nő sző fő ró
    public static $plusV_list = array('lő' => 'löv', 'nő' => 'növ', 'sző' => 'szöv', 'fő' => 'föv', 'ró' => 'rov');

    // full list : tesz lesz vesz hisz visz eszik iszik
    // @todo -Ó -Ás : evő, evés, alvó, alvás ; -ni : enni, aludni
    public static $SZV_list = array('tesz' => 'te', 'lesz' => 'le', 'vesz' => 've', 'hisz' => 'hi', 'visz' => 'vi', 'esz' => 'e', 'isz' => 'i');

    // @todo alszik ; -Ó -Ás : alvó, alvás ; -ni : aludni
    public static $SZDV_list = array(
        // @corpus sok -kVd(ik) képzős ige
        'alsz' => array('alud', 'alv'),
        'feksz' => array('feküd', 'fekv'),
        'haragsz' => array('haragud', 'haragv'),
        'cseleksz' => array('cseleked', 'cselekv'),
        'dicseksz' => array('dicseked', 'dicsekv'),
        'töreksz' => array('töreked', 'törekv'),
        // @corpus csak deverb és denom -Vd és -kOd képzős igék között
        'öregsz' => array('öreged', 'öreged'),
        'veszeksz' => array('veszeked', 'veszeked'),
    );

    // @corpus hangkivetéses igék: vándorol/vándorlunk
    // _Vl _Vz dVkVl _Vg képzős igék többsége, pl. vándorol, céloz, haldokol, mosolyog, és még söpör, sodor
    //'szerez' => 'szerző',
    //'töröl' => 'törlő',
    //'becsül' => 'becsl',
    //'őriz' => 'őrz',

    public static $needSuffixI_verb_list = array(
        'isz' => false,
    );

    // @corpus -z képzős igék általában, sok -kVd(ik) képzős ige
    public static $ikes = array(
        'esz' => true,
        'isz' => true,
        'alsz' => true,
        'feksz' => true,
        'haragsz' => true,
        'cseleksz' => true,
        'dicseksz' => true,
        'töreksz' => true,
        'öregsz' => true,
        'veszeksz' => true,
    );

    public static function & parseV($string)
    {
        $obj = new Verbum($string);
        $obj->setCase('13100');
        $obj->is_btmr = in_array($string, self::$V_btmr_list, true);
        $obj->is_opening = in_array($string, self::$V_opening_list, true);
        if (array_key_exists($obj->lemma, self::$plusV_list))
        {
            $obj->isPlusV = true;
            $obj->lemma2 = self::$plusV_list[$obj->lemma];
        }
        if (array_key_exists($obj->lemma, self::$SZV_list))
        {
            $obj->isSZV = true;
            $obj->lemma2 = self::$SZV_list[$obj->lemma];
        }
        if (array_key_exists($obj->lemma, self::$SZDV_list))
        {
            $obj->isSZDV = true;
            $obj->lemma2 = self::$SZDV_list[$obj->lemma][0];
            $obj->lemma3 = self::$SZDV_list[$obj->lemma][1];
        }
        if (isset(self::$needSuffixI_verb_list[$string]))
            $obj->needSuffixI = self::$needSuffixI_verb_list[$string];
        if (isset(self::$ikes[$string]))
            $obj->ikes = self::$ikes[$string];
        return $obj;
    }

    public static function & createCaseframe($string, $args)
    {
        $F = new Caseframe(GFactory::parseV($string), $args);
        return $F;
    }

}

?>
