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

    public static function getLastConsonant($ortho)
    {
        $last = mb_substr($ortho, -1, 1);
        if ($last === 'y')
            $last = mb_substr($ortho, -2, 2);
        return $last;
    }

    public static $invalid_suffix_regex_list = array(
        '/[bcdfghkmptvw],t/',
        '/[bcdfghklmnprstvwyz],[kmn]/',
        '/[lrsy],t.+/', // @see barnulástok, hoteltek
    );

    public static function isValidSuffixConcatenation($ortho_stem, $ortho_suffix)
    {
        $string = "$ortho_stem,$ortho_suffix";
        foreach (self::$invalid_suffix_regex_list as $regex)
            if (preg_match($regex, $string))
                return false;
        return true;
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

    public function __construct($lemma, $ortho=NULL)
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
        $stem = $this->cloneAs($suffix->getOutputClass());
        $stem->onBeforeSuffixation($suffix);
        $affix = clone $suffix;
        $affix->onBeforeSuffixed($stem);
        $interfix_ortho = $affix->getInterfix($stem);
        $affix->onAfterSuffixed($stem);
        $stem->ortho .= $interfix_ortho.$affix->ortho;
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

    public function isValidSuffixConcatenation($ortho_stem, $ortho_suffix)
    {
        return Phonology::isValidSuffixConcatenation($ortho_stem, $ortho_suffix);
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
        if (mb_substr($ortho, 0, 1) === $char)
        {
            if ($stem->isLastVowel())
                $first = $char;
            else
                $first = Phonology::getLastConsonant($stem->ortho);
            $ortho = $first.mb_substr($ortho, 1);
        }
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

    public function onBeforeSuffixed(iPossessable & $stem)
    {
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

class VerbalSuffixum extends Suffixum
{

    public $input_class = 'Verbum';
    public $output_class = 'Verbum';

    public static $paradigm = array(
        1 => array(
            0 => array(
                0 => array(
                    1 => array(1 => 'Vk', 2 => 'Vl', 3 => ''),
                    3 => array(1 => 'Unk', 2 => '_VtVk', 3 => '_AnAk'),
                ),
                1 => array(
                    1 => array(1 => 'Vm', 2 => 'Vd', 3 => 'ja'),
                    3 => array(1 => 'jUk', 2 => 'jÁtVk', 3 => 'jÁk'),
                ),
            ),
            -1 => array(
                0 => array(
                    1 => array(1 => 'Am', 2 => 'Ál', 3 => ''),
                    3 => array(1 => 'Unk', 2 => '_AtVk', 3 => 'Ak'),
                ),
                1 => array(
                    1 => array(1 => 'Am', 2 => 'Ad', 3 => 'A'),
                    3 => array(1 => 'Uk', 2 => '_ÁtVk', 3 => 'Ák'),
                ),
            ),
        ),
        2 => array(
            0 => array(
                0 => array(
                    1 => array(1 => 'ék', 2 => 'Ál', 3 => 'A'),
                    3 => array(1 => 'Ánk', 2 => 'ÁtVk', 3 => 'ÁnAk'),
                ),
                1 => array(
                    1 => array(1 => 'Ám', 2 => 'Ád', 3 => 'Á'),
                    3 => array(1 => 'Ánk', 2 => 'ÁtVk', 3 => 'Ák'),
                ),
            ),
        ),
        3 => array(
            0 => array(
                0 => array(
                    1 => array(1 => 'Ak', 2 => 'Ál', 3 => 'On'),
                    3 => array(1 => 'Unk', 2 => 'AtOk', 3 => 'AnAk'),
                ),
                1 => array(
                    1 => array(1 => 'Am', 2 => 'Ad', 3 => 'A'),
                    3 => array(1 => 'Uk', 2 => 'ÁtVk', 3 => 'Ák'),
                ),
            ),
        ),
    );

    public static function & conjugate($numero, $person, $moode, $tense, $definite)
    {
        $suffix = new VerbalSuffixum(self::$paradigm[$moode][$tense][$definite][$numero][$person]);
        $suffix->moode = $moode;
        $suffix->tense = $tense;
        $suffix->definite = $definite;
        $suffix->numero = $numero;
        $suffix->person = $person;
        return $suffix;
    }

    public static $valid_suffix_regex_list = array(
        '/s,[nt][^nt]/',
    );

    public function isValidSuffixConcatenation($ortho_stem, $ortho_suffix)
    {
        $string = "$ortho_stem,$ortho_suffix";
        //print " isValidSuffixConcatenation($string) ";
        foreach (self::$valid_suffix_regex_list as $regex)
            if (preg_match($regex, $string))
                return true;
        return false;
    }

    public function getInterfix(& $stem)
    {
        $interfix = '';
        if ($this->moode === 1 && $this->tense === -1)
        {
            if (!$stem->isLastVowel() && $this->numero === 1 && $this->person === 3 && !$this->definite)
                return Phonology::interpolateVowels($stem, 'Vt').'t';
            $interfix = 't';
        }
        if ($this->moode === 2 && $this->tense === 0)
            $interfix = 'n';
        if ($this->moode === 3)
            $interfix = $this->hasonul($stem, 'j', 'j');
        //print "stem=$stem suffix=$this ";
        //print "hasOptionalInterfix=".(int) $this->hasOptionalInterfix();
        //print "isValidSuffixConcatenation=".(int) $this->isValidSuffixConcatenation($stem->ortho, $this->ortho);
        if ($this->hasOptionalInterfix() && !$this->isValidSuffixConcatenation($stem->ortho, $interfix.$this->ortho))
        {
            $interfix .= $this->getOptionalInterfix();
            //print "interfix/2=$interfix ";
            $interfix = Phonology::interpolateVowels($stem, $interfix);
            //print "interfix/3=$interfix ";
        }
        //print "interfix/4=$interfix ";
        return $interfix;
    }

    public function onBeforeSuffixed(& $stem)
    {
        $ortho = $this->getNonOptionalSuffix();
        $ortho = $this->hasonul($stem, $ortho, 'j');
        $ortho = Phonology::interpolateVowels($stem, $ortho);
        $this->ortho = $ortho;
    }

    public function onAfterSuffixed(& $stem)
    {
    }

}

class Verbum extends Wordform
{

    // Szótári alak
    public function & getCitationForm()
    {
        $clone = clone $this;
        $clone->conjugate(1, 3, 1, 0, 0);
        return $clone;
    }

    public function & conjugate($numero, $person, $moode, $tense, $definite)
    {
        $suffix = VerbalSuffixum::conjugate($numero, $person, $moode, $tense, $definite);
        $clone = & $this->appendSuffix($suffix);
        return $clone;
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
        // @todo alternating verbs
        //'szerez' => 'szerző',
        //'töröl' => 'törlő',
        //'becsül' => 'becsl',
        //'őriz' => 'őrz',
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

    public static function parseV($string)
    {
        $obj = new Verbum($string);
        $obj->is_btmr = in_array($string, self::$V_btmr_list, true);
        $obj->is_opening = in_array($string, self::$V_opening_list, true);
        return $obj;
    }

}

?>
