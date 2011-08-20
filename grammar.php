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
    );

    public static function isVowel($chr)
    {
        return (isset(self::$skeletoncode[$chr]) && self::$skeletoncode[$chr] === 'V');
    }

    public static function getPropagatedX($X_pattern, $t_pattern, $ortho)
    {
        $len = mb_strlen($ortho);
        $is_propagated = NULL;
        for ($i = 0; $i < $len; $i++)
        {
            $chr = mb_substr($ortho, $i, 1);
            $phonocode = @ self::$phonocode[$chr];
            if (!$phonocode)
                continue;
            if ($t_pattern)
            {
                $is_t = (bool) preg_match($t_pattern, $phonocode);
                if (!$is_t || is_null($is_propagated))
                    $is_propagated = (bool) preg_match($X_pattern, $phonocode);
            }
            else
                $is_propagated = (bool) preg_match($X_pattern, $phonocode);
        }
        return $is_propagated;
    }

    /** Kerekségi harmónia
     */
    public static function needSuffixU($ortho)
    {
        $A = self::getPropagatedA($ortho);
        $U = self::getPropagatedU($ortho);
        $I = self::getPropagatedI($ortho);
        if ($A && $I && $U)
            return true;
        if ($A && $I && !$U)
            return false;
        if ($A && !$I && $U)
            return true;
        if ($A && !$I && !$U)
            return false;
        return $U;
    }

    /** Elölségi harmónia
     */
    public static function needSuffixI($ortho)
    {
        return self::getPropagatedI($ortho);
    }

    public static function getPropagatedA($ortho)
    {
        return self::getPropagatedX('/^A/', '', $ortho);
    }

    public static function getPropagatedU($ortho)
    {
        return self::getPropagatedX('/^.U/', '', $ortho);
    }

    public static function getPropagatedI($ortho)
    {
        return self::getPropagatedX('/^..I/', '/^....t/', $ortho);
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
        $len = mb_strlen($ortho);
        for ($i = 0; $i < $len; $i++)
        {
            $chr = mb_substr($ortho, $i, 1);
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

    public static function interpolateVowels(& $stem, $string)
    {
        $vowelmap = self::$vowelmaps[$stem->needSuffixPhonocode()];
        $suffix = self::tr($string, $vowelmap);
        return $suffix;
    }

    public static $consonant_regex = '/(ddzs|ccs|ddz|dzs|ggy|lly|nny|ssz|tty|zzs|bb|cc|cs|dd|dz|ff|gg|gy|h|hh|jj|k|kk|ll|ly|mm|nn|ny|pp|qq|rr|ss|sz|tt|ty|vv|ww|xx|zs|zz|b|c|d|f|g|j|l|m|n|p|q|r|s|t|v|w|x|z)$/';

    public static function getLastConsonant($ortho)
    {
        if (preg_match(self::$consonant_regex, $ortho, $match))
            return $match[1];
        return NULL;
    }

    public static function doubleConsonant($ortho)
    {
        assert('$ortho === self::getLastConsonant($ortho)');
        $len = mb_strlen($ortho);
        if ($len === 1)
            return $ortho.$ortho;
        elseif ($len === 2)
        {
            $c0 = mb_substr($ortho, 0, 1);
            $c1 = mb_substr($ortho, 1, 1);
            if ($c0 === $c1)
                return $ortho;
            else
                return $c0.$ortho;
        }
        elseif ($len === 3 && $ortho === 'dzs')
            return 'ddzs';
        else
            return $ortho;
    }

    public static function isAffrikate($cons)
    {
        return in_array($cons, array('dz', 'ddz', 'dzs', 'ddzs', 'c', 'cc', 'cs', 'ccs'));
    }

    public static function isSybyl($cons)
    {
        return in_array($cons, array('s', 'ss', 'sz', 'ssz', 'z', 'zz', 'zs', 'zzs'));
    }

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
}

interface iWordformMorphology
{

    public function & appendSuffix(Suffixum & $suffix);
    public function onBeforeSuffixation(& $suffix);
}

class Wordform implements iWordformPhonology, iWordformMorphology
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
            $clone->$key = $val;
        return $clone;
    }

    // }}}

    // iWordformMorphology {{{

    public function & appendSuffix(Suffixum & $suffix)
    {
        $input_class = $suffix->getInputClass();
        if (!($this instanceof $input_class))
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

    // }}}

}

/** Valid nominal cases in hungarian.
 */
interface NominalCases
{
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
interface VirtualNominalCases
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
interface VirtualTemporalCases
{
    public function & makeAntessivus();
    public function & makeTemporalis(); // -kor
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

class Suffixum extends Wordform implements iSuffixumMorphology
{

    public $input_class = 'Nomen';
    public $output_class = 'Nomen';
    public $stop_jaje = false;

    public function getInputClass()
    {
        return $this->input_class;
    }

    public function getOutputClass()
    {
        return $this->output_class;
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
        '/[bcdfghklmnprstvwyz],[kmn]/',
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
            $_interfix = Phonology::interpolateVowels($stem, $_interfix);
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

    public function hasonul(& $stem, $ortho, $char)
    {
        if (mb_substr($ortho, 0, 1) !== $char)
            return $ortho;
        if ($stem->isLastVowel())
            return $ortho;
        $cons = Phonology::getLastConsonant($stem->ortho);
        $stem->ortho = mb_substr($stem->ortho, 0, -mb_strlen($cons));
        $ortho = Phonology::doubleConsonant($cons).mb_substr($ortho, 1);
        return $ortho;
    }

    public function onBeforeSuffixed(& $stem)
    {
        $ortho = $this->getNonOptionalSuffix();
        $ortho = $this->hasonul($stem, $ortho, 'v');
        $ortho = Phonology::interpolateVowels($stem, $ortho);
        $this->ortho = $ortho;
    }

    public function onAfterSuffixed(& $stem)
    {
        if ($stem instanceof Nomen && $this->stop_jaje)
            $stem->is_jaje = false;
    }

}

interface PersNum
{
    public function & makeNumPers($numero = 1, $person = 3);
}

class PossessiveSuffixum extends Suffixum implements PersNum
{

    public $input_class = 'iPossessable';
    public $output_class = 'Nomen';
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
    public $input_class = 'iPossessable';
    public $output_class = 'Nomen';
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

    public $input_class = 'Verbum';
    public $output_class = 'Verbum';

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
    public function __construct($numero, $person, $mood, $tense, $definite)
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

    public function __construct($numero, $person, $mood, $tense, $definite)
    {
        parent::__construct($numero, $person, $mood, $tense, $definite);
        if ($this->tense === -1) // múlt idő jele
            $this->lemma = $this->ortho = 't';
        if ($this->mood === 2 && $this->tense === 0)
            $this->lemma = $this->ortho = 'n';
        if ($this->mood === 3)
            $this->lemma = $this->ortho = 'j';
    }

    public function onBeforeSuffixed(& $stem)
    {
        if ($this->tense === -1) // múlt idő jele
        {
            if ($stem->needVtt($this)) // Vtt
                $this->ortho =  Phonology::interpolateVowels($stem, 'Vtt');
            elseif ($stem->needTT()) // tt
                $this->ortho = 'tt';
            else // t
                $this->ortho = 't';
        }
        if ($this->mood === 2 && $this->tense === 0)
        {
            if ($stem->isSZV)
                $this->ortho = 'nn';
            else
                $this->ortho = 'n';
        }
        if ($this->mood === 3)
        {
            $last = Phonology::getLastConsonant($stem->ortho);
            if ($stem->isSZV)
            {
                // @fixme
                if ($stem->lemma === 'hisz')
                    $this->ortho = 'ggy';
                else
                    $this->ortho = 'gy';
            }
            else
            {
                // V(t+j) => ss
                // zörej+t+j => zörejhez hasonul
                //
                // ha t-re végződik, levágjuk a t-t,
                // az új utolsó ha V, akkor ss,
                // ha zörej, akkor hasonul,
                // egyébként s
                if (Phonology::isSybyl($last))
                {
                    $this->ortho = $this->hasonul($stem, 'j', 'j');
                }
                if ($last === 't' || $last === 'tt')
                {
                    $stem->ortho = mb_substr($stem->ortho, 0, -mb_strlen($last));
                    if ($stem->isLastVowel())
                    {
                        $this->ortho = 'ss';
                    }
                    elseif (
                        Phonology::isAffrikate(Phonology::getLastConsonant($stem->ortho))
                        || Phonology::isSybyl(Phonology::getLastConsonant($stem->ortho))
                    )
                    {
                        $this->ortho = $this->hasonul($stem, 'j', 'j');
                    }
                    else
                    {
                        $stem->ortho .= 't';
                        $this->ortho = 's';
                    }
                }
            }
        }
        if (!$this->isValidSuffixConcatenation($stem->ortho, $this->ortho))
            $this->ortho = Phonology::interpolateVowels($stem, 'A').$this->ortho;
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

    public function __construct($numero, $person, $mood, $tense, $definite)
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
            $interfix = Phonology::interpolateVowels($stem, $interfix);
        }
        return $interfix;
    }

    public function onBeforeSuffixed(& $stem)
    {
        $lemma = $this->lemma;
        if (strpos($lemma, '|'))
        {
            $alters = explode('|', $lemma);
            $i = 0;
            // Vl/Asz
            if (
                $this->matchCase('12100')
                && (
                    Phonology::isAffrikate(Phonology::getLastConsonant($stem->ortho))
                    || Phonology::isSybyl(Phonology::getLastConsonant($stem->ortho))
                )
            )
            {
                $i = 1;
            }
            if (
                $this->matchCase('(13|32|33)103')
                && $stem->needSuffixI()
            )
            {
                    $i = 1;
            }
            $lemma = $alters[$i];
        }
        $this->lemma = $lemma;

        $ortho = $this->getNonOptionalSuffix();
        $last = Phonology::getLastConsonant($stem->ortho);
        if ($last !== 't')
            $ortho = $this->hasonul($stem, $ortho, 'j');
        $ortho = Phonology::interpolateVowels($stem, $ortho);

        if ($stem->ikes && $this->matchCase('13100'))
            $ortho = 'ik';

        $this->ortho = $ortho;
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
        if ($this->isPlusV && $suffix->matchCase('..10.'))
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
        }

        if ($this->isSZDV && $suffix instanceof VerbalSuffixum1)
        {
            if ($suffix->matchCase('..[23]..|...9.'))
                $this->ortho = $this->lemma2;
        }

        // @fixme
        if ($this->lemma === 'alsz')
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

    public function needTT()
    {
        return ($this->isPlusV || $this->isSZV);
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

    public function & makeInfinitive($numero=NULL, $person=NULL) { }

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

interface iPossessable
{
    public function isJaje();
}

class Nomen extends Wordform implements iPossessable, NominalCases, VirtualNominalCases
{

    public $case = 'Nominativus';
    public $numero = 1;

    public $is_jaje = NULL;
    public $lemma2 = '';

    public function __construct($lemma, $ortho=NULL)
    {
        parent::__construct($lemma, $ortho);
        $this->lemma2 = $lemma;
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

    // interface NominalCases {{{

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

    // interface VirtualNominalCases {{{

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

/*
 * @todo Képzők
 *
 * N -> N
 * (V)s s os as es ös
 * né né
 * kA ka ke
 * (V)cskA cska cske ocska acska ecske öcske
 * féle féle
 *
 * N -> ADJ
 * i i
 * (V)s s os es ös as ás és
 * (j)Ú ú ű jú jű
 * ...
 *
 * V -> N
 * Ás ás és
 * Ó ó ő
 * V -> ADJ
 * Ós ós ős
 * (A)tlAn tlan tlen atlan etlen
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
 * (V)gAt gat get ogat eget öget
 * (t)At at et tat tet
 * (t)Atik 
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
            list($input_class, $output_class) = self::$suffixum_classes[$string];
        else
        {
            $input_class = 'Nomen';
            $output_class = 'Nomen';
        }
        $obj->input_class = $input_class;
        $obj->output_class = $output_class;
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

    public static function parseV($string)
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

}

?>
