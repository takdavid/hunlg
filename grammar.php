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

    public static function doMR($ortho)
    {
        return self::tr($ortho, self::$vtmr_map);
    }

    public static function doAMNY($ortho)
    {
        $last = mb_substr($ortho, -1, 1);
        if (isset(self::$amny_map[$last]))
            $ortho = mb_substr($ortho, 0, -1).self::$amny_map[$last];
        return $ortho;
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
        true => array(
            '--' => array( 'A' => 'a', 'Á' => 'á', 'E' => 'a', 'O' => 'o', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'V' => 'a'),
            'U-' => array( 'A' => 'a', 'Á' => 'á', 'E' => 'a', 'O' => 'o', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'V' => 'a'),
            '-I' => array( 'A' => 'e', 'Á' => 'é', 'E' => 'e', 'O' => 'e', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'V' => 'e'),
            'UI' => array( 'A' => 'e', 'Á' => 'é', 'E' => 'e', 'O' => 'ö', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'V' => 'e'),
        ),
        false => array(
            '--' => array( 'A' => 'a', 'Á' => 'á', 'E' => 'o', 'O' => 'o', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'V' => 'o'),
            'U-' => array( 'A' => 'a', 'Á' => 'á', 'E' => 'o', 'O' => 'o', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'V' => 'a'),
            '-I' => array( 'A' => 'e', 'Á' => 'é', 'E' => 'e', 'O' => 'e', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'V' => 'e'),
            'UI' => array( 'A' => 'e', 'Á' => 'é', 'E' => 'ö', 'O' => 'ö', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'V' => 'ö'),
        ),
    );

    public static function addSuffix(& $nomen, & $suffix)
    {
        $suffixcode = (string) $suffix;

        if (mb_substr($suffixcode, 0, 1) === 'v')
        {
            $last = self::getLastCons($nomen->ortho);
            $suffixcode = $last.mb_substr($suffixcode, 1);
        }
        
        $stem = $nomen->ortho;
        if ($nomen->isVTMR() && $suffix->isVTMR())
            $stem = self::doMR($nomen->ortho);
        //print $nomen->lemma.' => $nomen->isBTMR()='.(int) $nomen->isBTMR().' $suffix->isBTMR()='.(int) $suffix->isBTMR()."\n";
        if ($nomen->isBTMR() && $suffix->isBTMR())
            $stem = self::doMR($nomen->ortho);
        //print 'isAMNYLeft='.(int) $nomen->isAMNYLeft().' isAMNYRight='.(int) $suffix->isAMNYRight();
        if ($nomen->isAMNYLeft() && $suffix->isAMNYRight())
            $stem = self::doAMNY($nomen->ortho);

        //if ($suffix instanceof BirtokosSuffixum)
        ////    print 'nomen '.$nomen->ortho.' is '.get_class($nomen).' ';

        $is_opening = $nomen->isOpening();
        // @todo nemcsak a birtokoshoz, hanem a további toldalékokra is terjed az I ha needBirtokosSuffixI() volt korábban, ld. oxigéneteket!
        $nomen_phonocode = 
            (Phonology::needSuffixU($nomen->lemma) ? 'U' : '-') . 
            ((Phonology::needSuffixI($nomen->lemma) || ($suffix instanceof BirtokosSuffixum && $nomen->needBirtokosSuffixI())) ? 'I' : '-') ;
        //print "\n".'lemma='.$nomen->lemma.' opening='.(int) $is_opening.' phonocode='.$nomen_phonocode;
        $vowelmap = self::$vowelmaps[$is_opening][$nomen_phonocode];

        $_suffix = '';
        $len = mb_strlen($suffixcode);
        for ($i = 0; $i < $len; $i++)
        {
            $chr = mb_substr($suffixcode, $i, 1);
            if (isset($vowelmap[$chr]))
                $_suffix .= $vowelmap[$chr];
            else
                $_suffix .= $chr;
        }
        //print "suffix/1=$_suffix ";

        //print "stem=$stem ";

        // birtokos A/jA
        if ($suffix instanceof BirtokosSuffixum && $suffix->person === 3 && $nomen instanceof Nomen && $nomen->isJaje())
            $_suffix = "j$_suffix";

        // kötőhang
        $chr0 = mb_substr($_suffix, 0, 1);
        if ($chr0 === '_')
        {
            $suffix_vowel = mb_substr($_suffix, 1, 1);
            $suffix_stem = mb_substr($_suffix, 2);
            if ($nomen->isOpening())
                $_suffix = $suffix_vowel.$suffix_stem;
            elseif ($suffix instanceof BirtokosSuffixum && !self::isVowel(mb_substr($stem, -1, 1)))
                $_suffix = $suffix_vowel.$suffix_stem;
            elseif (self::isValidSuffix($stem, $suffix_stem))
                $_suffix = $suffix_stem;
            else
                $_suffix = $suffix_vowel.$suffix_stem;
        }

        //print "\n";

        return $stem.$_suffix;
    }

    public static $invalid_suffix_regex_list = array(
        '/d,t/', '/k,t/', '/t,t/', '/k,k/', '/r,k/', '/t,k/', '/z,k/', 
        '/s,t.+/', // @see barnulástok
        '/r,t.+/',
    );

    public static function isValidSuffix($stem, $suffix)
    {
        $string = "$stem,$suffix";
        foreach (self::$invalid_suffix_regex_list as $regex)
            if (preg_match($regex, $string))
                return false;
        return true;
    }

    public static function getLastCons($ortho)
    {
        $last = mb_substr($ortho, -1, 1);
        if ($last === 'y')
            $last = mb_substr($ortho, -2, 2);
        return $last;
    }

    public static $vowelmap_hl = array(
        'a' => 'l',
        'á' => '0',
        'e' => 'h',
        'é' => 'h',
        'i' => '0',
        'í' => '0',
        'o' => 'l',
        'ó' => 'l',
        'ö' => 'h',
        'ő' => 'h',
        'u' => 'l',
        'ú' => 'l',
        'ü' => 'h',
        'ű' => 'h',
    );

    public static function getVowSeq($ortho)
    {
        $vowseq = '';
        $len = mb_strlen($ortho);
        for ($i = 0; $i < $len; $i++)
        {
            $chr = mb_substr($ortho, $i, 1);
            if (isset(self::$vowelmap_hl[$chr]))
                $vowseq .= self::$vowelmap_hl[$chr];
        }
        return $vowseq;
    }

    public static function getVow($ortho)
    {
        $vowseq = self::getVowSeq($ortho);
        if (preg_match('/h+$/', $vowseq))
            return 'high';
        if (preg_match('/l+$/', $vowseq))
            return 'low';
        if (preg_match('/0+$/', $vowseq))
            return 'opening';
    }

}

class Wordform
{
    public $lemma = '';
    public $ortho = '';
    public $vow = '';
    public $is_vtmr = false; 
    public $is_btmr = false; 
    public $is_opening = false; 
    public $is_amny = NULL;

    public function __construct($lemma, $ortho=NULL)
    {
        $this->lemma = $lemma;
        $this->ortho = $ortho ? $ortho : $lemma;
        $this->vow = Phonology::getVow($this->ortho);
    }

    public function __toString()
    {
        return $this->ortho;
    }

    public function & appendSuffix($suffix)
    {
        $clone = clone $this;
        $clone->ortho = Phonology::addSuffix($clone, $suffix);
        return $clone;
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
        return true;
    }

    /** @todo use lexicon is_birtokos_i
    /** @todo move down from here
     */
    public function needBirtokosSuffixI()
    {
        if ($this->lemma === 'oxigén')
            return true;
    }

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
    public function & makeTemporalis();
}

/** @todo latin neve?
 */
interface PersNum
{
    public function & makePersNum($numero = 1, $person = 3);
}

class Suffixum extends Wordform
{
}

class BirtokosSuffixum extends Suffixum implements PersNum
{

    public $numero = 1;
    public $person = 3;

    public static $suffixmap = array(
        1 => array(1 => 'Vm', 2 => 'Vd', 3 => 'A'),
        3 => array(1 => 'Unk', 2 => '_EtEk', 3 => 'Uk'),
    );

    public function & makePersNum($numero = 1, $person = 3)
    {
        $suffixcode = self::$suffixmap[$numero][$person];
        $obj = new BirtokosSuffixum($suffixcode);
        $obj->numero = $numero;
        $obj->person = $person;
        $obj->is_opening = true;
        $obj->is_vtmr = true;
        return $obj;
    }

}

class Verbum extends Wordform
{
}

class Nomen extends Wordform implements NominalCases, VirtualNominalCases
{

    public $case = 'Nominativus';
    public $numero = 1;
    public $person = 3;

    public $is_jaje = NULL;

    /** Hint.
     */
    public function isJaje()
    {
        if (!is_null($this->is_jaje))
            return $this->is_jaje; // már adott
        if (Phonology::isVowel(mb_substr($this->ortho, -1, 1)))
            return true; // mindig
        if (preg_match('/(s|sz|z|zs|c|cs|dzs|gy|j|ny|ty|tor|ter|er|um)$/', $this->ortho))
            return false; // általában
        if (preg_match('/[bcdfghjklmnprstvxz]{2,}$/', $this->ortho))
            return true; // általában
        return false; // unknown
    }

    public function & appendSuffix($suffix)
    {
        $clone = parent::appendSuffix($suffix);
        if ($suffix->ortho === 'sÁg')
            $clone->is_jaje = false;
        if ($suffix instanceof BirtokosSuffixum)
            $clone->is_opening = true;
        return $clone;
    }

    public function & makePlural()
    {
        $clone = $this->makeNominativus();
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
        $clone = new Nomen($this->lemma);
        $clone->vow = $this->vow;
        $clone->is_vtmr = $this->is_vtmr;
        $clone->is_opening = $this->is_opening;
        // @todo copy other fields?
        if ($this->isPlural())
            $clone = $clone->makePlural();
        return $clone;
    }

    public function & makeAccusativus()
    {
        $clone = & $this->makeNominativus()->appendSuffix(GFactory::parseSuffixum('_Vt'));
        $clone->case = 'Accusativus';
        return $clone;
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
        return $this->_makeCaseFromNominativusWithSuffix('Superessivus', GFactory::parseSuffixum('On'));
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

    public function & _makeCaseWithNU($case, $lemma)
    {
        $clone = $this->makeNominativus();
        $ADVP = new ADVP_NU($lemma, $clone);
        $ADVP->case = $case;
        return $ADVP;
    }

    public function & _makeCaseWithHRHSZ($case, $lemma, $suffix)
    {
        $clone = $this->makeNominativus();
        $ADVP = new ADVP_HRHSZ($lemma, $clone, $suffix);
        $ADVP->case = $case;
        return $ADVP;
    }

    public function & _makeCaseWithHNHSZ($case, $lemma)
    {
        $clone = $this->makeNominativus();
        $ADVP = new ADVP_HNHSZ($lemma, $clone);
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
        return $this->_makeCaseWithNU('Causalis', 'miatt');
    }

    // @skip public function & makeExessivus();

    public function & makePerlativus()
    {
        return $this->_makeCaseWithHRHSZ('Perlativus', 'keresztül', 'On');
    }

    public function & makeProlativus()
    {
        return $this->_makeCaseWithHRHSZ('Perlativus', 'át', 'On');
    }

    public function & makeVialis()
    {
        return $this->makeProlativus();
    }

    public function & makeSubessivus()
    {
        return $this->_makeCaseWithNU('Perlativus', 'alatt');
    }

    public function & makeProsecutivus()
    {
        return $this->_makeCaseWithHNHSZ('Prosecutivus', 'mentén');
    }

    //public function & makeApudessivus();
    //public function & makeInitiativus();
    //public function & makeEgressivus();

    //public function & makeAntessivus();
    //public function & makeTemporalis();

    // }}}

}

/*
 * Képzők
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

class ADVP_NU
{
    public $lemma = '';
    public $vow = ''; // @todo factor out this->base = new Wordform()
    public $arg = NULL;
    public $case = '';

    public function __construct($lemma, & $arg)
    {
        $this->lemma = $lemma;
        $this->arg = & $arg;
        $this->vow = Phonology::getVow($lemma);
    }

    public function __toString()
    {
        return (string) $this->arg . ' ' . $this->lemma;
    }

    public function & pronominalize()
    {
        $map = array(
            'low' => array(
                1 => array(1 => 'am', 2 => 'ad', 3 => 'a',),
                3 => array(1 => 'unk', 2 => 'atok', 3 => 'uk',),
            ),
            'high' => array(
                1 => array(1 => 'em', 2 => 'ed', 3 => 'e',),
                3 => array(1 => 'ünk', 2 => 'etek', 3 => 'ük',),
            ),
        );
        $vow = $this->vow;
        $numero = $this->arg->numero;
        $person = $this->arg->person;
        $suffix = $map[$vow][$numero][$person];
        return new Wordform($this->lemma, $this->lemma.$suffix);
    }

}

class ADVP_HRHSZ
{
    public $lemma = '';
    public $vow = ''; // @todo factor out this->base = new Wordform()
    public $arg = NULL;
    public $case = '';
    public $suffixcode;

    public function __construct($lemma, & $arg, $suffixcode)
    {
        $this->lemma = $lemma;
        $this->arg = & $arg;
        $this->suffixcode = $suffixcode;
        $this->vow = Phonology::getVow($lemma);
    }

    public function __toString()
    {
        $arg = $this->arg->appendSuffix(GFactory::parseSuffixum($this->suffixcode));
        return (string) $arg . ' ' . $this->lemma;
    }

    public function & pronominalize()
    {
        throw new Exception();
    }

}

class ADVP_HNHSZ
{
    public $lemma = '';
    public $vow = ''; // @todo factor out this->base = new Wordform()
    public $arg = NULL;
    public $case = '';

    public function __construct($lemma, & $arg)
    {
        $this->lemma = $lemma;
        $this->arg = & $arg;
        $this->vow = Phonology::getVow($lemma);
    }

    public function __toString()
    {
        return (string) $this->arg . ' ' . $this->lemma;
    }

    public function & pronominalize()
    {
        throw new Exception();
    }

}

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
        // @todo A kéttagúak első mghja mindig rövid - egyszerűen az egész alakot lehet rövidíteni.
        'egér', 'szekér', 'tenyér', 'kenyér', 'levél', 'fedél', 'fenék', 'kerék', 'cserép', 'szemét', 'elég', 'veréb', 'nehéz', 'tehén', 'derék',
        'gyökér', 'kötél', 'közép',
        'fazék',
        'madár', 'szamár', 'agár', 'kanál', 'darázs', 'parázs',
        'bogár', 'kosár', 'mocsár', 'mozsár', 'pohár', 'fonál',
        'sugár', 'sudár',
    );

    // @todo tő és toldalék elkülönítése: mít|osz, ennek konstruálásakor legyen lemma=mít, és legyen a "nominális toldalék" osz, képzéskor pedig nem a nominálisból, hanem a lemmából képezzünk. (?)
    // @todo not full list
    public static $N_btmr_list = array(
        'aktív', 'vízió', 'miniatűr', 'úr', 'fúzió', 'téma', 'szláv', 'privát',
        'náció', 'analízis', 'mítosz', 'motívum', 'stílus',
        'kultúra', 'múzeum', 'pasztőr', 'periódus', 'paródia',
        'kódex', 'filozófia', 'história', 'prémium', 'szintézis',
        'hérosz', 'matéria', 'klérus', 'május', 'banális',
        'elegáns',
    );

    // @todo not full list
    // not opening e.g.: gáz bűz rés
    public static $N_opening_list = array('út', 'nyár', 'ház', 'tűz', 'víz', 'föld', 'zöld', 'nyúl');

    // @todo not full list
    public static $N_jaje_list = array('nagy', 'pad', 'sárkány', 'kupec', 'kortes', 'macesz', 'trapéz', );

    public static function parseNP($string)
    {
        $obj = new Nomen($string);
        $obj->is_vtmr = in_array($string, self::$N_vtmr_list, true);
        $obj->is_btmr = in_array($string, self::$N_btmr_list, true);
        $obj->is_opening = in_array($string, self::$N_opening_list, true);
        if (in_array($string, self::$N_jaje_list, true))
            $obj->is_jaje = true;
        return $obj;
    }

    // @todo not full list
    public static $suffixum_vtmr_list = array(
        '_Vk', // többesjel
        '_Vt', // tárgyrag
        // birtokos személyragok
        'As', // melléknévképző
        'Az', // igeképző
        'cskA', // kicsinyítő képző
    );

    // @todo not full list
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

    // @todo is full list?
    public static $suffixum_opening_list = array(
        '_Vk', // többesjel
        // birtokos személyjelek
        '_Vbb', // középfok jele 
        // múlt idő jele 
        // felszólító j 
    );

    // @todo test
    public static $suffixum_not_AMNY_right_list = array(
        'kor', 'ista', 'izmus', // stb., átlátszatlan toldalékok @todo
        'szOr', 'sÁg', 'i', 'ként',
    );

    public static function parseSuffixum($string)
    {
        $obj = new Suffixum($string);
        $obj->is_vtmr = in_array($string, self::$suffixum_vtmr_list, true);
        $obj->is_btmr = in_array($string, self::$suffixum_btmr_list, true);
        $obj->is_opening = in_array($string, self::$suffixum_opening_list, true);
        $obj->is_amny = !in_array(string, self::$suffixum_not_AMNY_right_list);
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
