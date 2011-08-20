<?php

assert_options(ASSERT_ACTIVE, 1);
assert_options(ASSERT_WARNING, 1);

mb_internal_encoding("UTF-8");

/*
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

class Phonology
{
    /** 
     * [-A]
     * [-U]
     * [-I]
     * [12]
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

    public static function getPropagatedX($X_pattern, $t_pattern, $actual)
    {
        $len = mb_strlen($actual);
        $is_propagated = NULL;
        for ($i = 0; $i < $len; $i++)
        {
            $chr = mb_substr($actual, $i, 1);
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
    public static function needSuffixU($actual)
    {
        $A = self::getPropagatedA($actual);
        $U = self::getPropagatedU($actual);
        $I = self::getPropagatedI($actual);
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
    public static function needSuffixI($actual)
    {
        return self::getPropagatedI($actual);
    }

    public static function getPropagatedA($actual)
    {
        return self::getPropagatedX('/^A/', '', $actual);
    }

    public static function getPropagatedU($actual)
    {
        return self::getPropagatedX('/^.U/', '', $actual);
    }

    public static function getPropagatedI($actual)
    {
        return self::getPropagatedX('/^..I/', '/^....t/', $actual);
    }

    public static function doMR($actual)
    {
        $vtmr_map = array(
            'á' => 'a',
            'é' => 'e',
            'í' => 'i',
            'ó' => 'o',
            'ő' => 'ö',
            'ú' => 'u',
            'ű' => 'ü',
        );
        return self::tr($actual, $vtmr_map);
    }

    public static function tr($actual, $map)
    {
        $string = '';
        $len = mb_strlen($actual);
        for ($i = 0; $i < $len; $i++)
        {
            $chr = mb_substr($actual, $i, 1);
            if (isset($map[$chr]))
                $string .= $map[$chr];
            else
                $string .= $chr;
        }
        return $string;
    }

    public static function addSuffix(& $nomen, & $suffix)
    {
        $suffixcode = (string) $suffix;

        if (mb_substr($suffixcode, 0, 1) === 'v')
        {
            $last = self::getLastCons($nomen->actual);
            $suffixcode = $last.mb_substr($suffixcode, 1);
        }
        
        $stem = $nomen->actual;
        if ($nomen->isVTMR() && $suffix->isVTMR())
            $stem = self::doMR($nomen->actual);
        //print $nomen->lemma.' => $nomen->isBTMR()='.(int) $nomen->isBTMR().' $suffix->isBTMR()='.(int) $suffix->isBTMR()."\n";
        if ($nomen->isBTMR() && $suffix->isBTMR())
            $stem = self::doMR($nomen->actual);

        $vowelmaps = array(
            true => array(
                '--' => array( 'A' => 'a', 'Á' => 'á', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'O' => 'o', 'V' => 'a'),
                'U-' => array( 'A' => 'a', 'Á' => 'á', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'O' => 'o', 'V' => 'a'),
                '-I' => array( 'A' => 'e', 'Á' => 'é', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'O' => 'e', 'V' => 'e'),
                'UI' => array( 'A' => 'e', 'Á' => 'é', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'O' => 'ö', 'V' => 'e'),
            ),
            false => array(
                '--' => array( 'A' => 'a', 'Á' => 'á', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'O' => 'o', 'V' => 'o'),
                'U-' => array( 'A' => 'a', 'Á' => 'á', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'O' => 'o', 'V' => 'a'),
                '-I' => array( 'A' => 'e', 'Á' => 'é', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'O' => 'e', 'V' => 'e'),
                'UI' => array( 'A' => 'e', 'Á' => 'é', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'O' => 'ö', 'V' => 'ö'),
            ),
        );
        $is_opening = $nomen->isOpening();
        $nomen_phonocode = (Phonology::needSuffixU($nomen->lemma) ? 'U' : '-') . (Phonology::needSuffixI($nomen->lemma) ? 'I' : '-');
        //print "\n".'lemma='.$nomen->lemma.' opening='.(int) $is_opening.' phonocode='.$nomen_phonocode;
        $vowelmap = $vowelmaps[$is_opening][$nomen_phonocode];
        $suffix = '';
        $len = mb_strlen($suffixcode);
        for ($i = 0; $i < $len; $i++)
        {
            $chr = mb_substr($suffixcode, $i, 1);
            if (isset($vowelmap[$chr]))
                $suffix .= $vowelmap[$chr];
            else
                $suffix .= $chr;
        }
        //print "suffix/1=$suffix ";

        // kötőhang
        $chr0 = mb_substr($suffix, 0, 1);
        if ($chr0 === '_')
        {
            $suffix_vowel = mb_substr($suffix, 1, 1);
            $suffix_stem = mb_substr($suffix, 2);
            if ($nomen->isOpening())
                $suffix = $suffix_vowel.$suffix_stem;
            elseif (self::isValidSuffix($stem, $suffix_stem))
                $suffix = $suffix_stem;
            else
                $suffix = $suffix_vowel.$suffix_stem;
        }

        return $stem.$suffix;
    }

    public static function isValidSuffix($stem, $suffix)
    {
        $invalid_list = array('dt', 'kt', 'tt', 'kk', 'rk', 'tk', 'zk');
        $ending = mb_substr($stem, -1, 1) . $suffix;
        if (in_array($ending, $invalid_list))
            return false;
        $ending = mb_substr($stem, -2, 2) . $suffix;
        if (in_array($ending, $invalid_list))
            return false;
        return true;
    }

    public static function getLastCons($actual)
    {
        $last = mb_substr($actual, -1, 1);
        if ($last === 'y')
            $last = mb_substr($actual, -2, 2);
        return $last;
    }

    public static function getVowSeq($actual)
    {
        $vowelmap = array(
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
        $vowseq = '';
        $len = mb_strlen($actual);
        for ($i = 0; $i < $len; $i++)
        {
            $chr = mb_substr($actual, $i, 1);
            if (isset($vowelmap[$chr]))
                $vowseq .= $vowelmap[$chr];
        }
        return $vowseq;
    }

    public static function getVow($actual)
    {
        $vowseq = self::getVowSeq($actual);
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
    public $actual = '';
    public $vow = '';

    public function __construct($lemma, $actual=NULL)
    {
        $this->lemma = $lemma;
        $this->actual = $actual ? $actual : $lemma;
        $this->vow = Phonology::getVow($this->actual);
    }

    public function __toString()
    {
        return $this->actual;
    }

    public function & appendSuffix($suffix)
    {
        $clone = clone $this;
        $clone->actual = Phonology::addSuffix($clone, $suffix);
        return $clone;
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

class Suffixum extends Wordform
{
    public $is_vtmr = false; 
    public $is_btmr = false; 
    public $is_opening = false; 

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

}

class Nomen extends Wordform implements NominalCases, VirtualNominalCases
{

    public $case = 'Nominativus';
    public $numero = 1;
    public $person = 3;

    public $is_vtmr = false; 
    public $is_btmr = false; 
    public $is_opening = false; 

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

    public function & makePlural()
    {
        $clone = $this->makeNominativus();
        if ($this->isPlural())
            return $clone;
        $clone->numero = 3;
        return $clone->appendSuffix(parseSuffixum('_Vk'));
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
        $clone = & $this->makeNominativus()->appendSuffix(parseSuffixum('_Vt'));
        $clone->case = 'Accusativus';
        return $clone;
    }

    public function & makeCausalisFinalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('CausalisFinalis', parseSuffixum('ért'));
    }

    public function & makeDativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Dativus', parseSuffixum('nAk'));
    }

    public function & makeInstrumentalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Instrumentalis', parseSuffixum('vAl'));
    }

    public function & makeTranslativusFactivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('TranslativusFactivus', parseSuffixum('vÁ'));
    }

    public function & makeFormativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Formativus', parseSuffixum('ként'));
    }

    public function & makeEssivusFormalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('EssivusFormalis', parseSuffixum('Ul'));
    }

    public function & makeIllativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Illativus', parseSuffixum('bA'));
    }

    public function & makeInessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Inessivus', parseSuffixum('bAn'));
    }

    public function & makeElativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Elativus', parseSuffixum('bÓl'));
    }

    public function & makeSublativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Sublativus', parseSuffixum('rA'));
    }

    public function & makeSuperessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Superessivus', parseSuffixum('On'));
    }

    public function & makeDelativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Delativus', parseSuffixum('rÓl'));
    }

    public function & makeAllativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Allativus', parseSuffixum('hOz'));
    }

    public function & makeAdessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Adessivus', parseSuffixum('nÁl'));
    }

    public function & makeAblativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Ablativus', parseSuffixum('tÓl'));
    }

    public function & makeTerminativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Terminativus', parseSuffixum('ig'));
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
        $num = $this->arg->numero;
        $pers = $this->arg->person;
        $suffix = $map[$vow][$num][$pers];
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
        $arg = $this->arg->appendSuffix(parseSuffixum($this->suffixcode));
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

function parseNP($string)
{
    $obj = new Nomen($string);
    // full list
    $vtmr_list = array(
        'híd', 'ín', 'nyíl', 'víz',
        'szűz', 'tűz', 'fű', 'nyű',
        'kút', 'lúd', 'nyúl', 'rúd', 'úr', 'út', 'szú',
        'cső', 'kő', 'tő',
        'ló',
        'kéz', 'réz', 'mész', 'ész', 'szén', 'név', 'légy', 'ég', 'jég', 'hét', 'tér', 'dér', 'ér', 'bél', 'nyél', 'fél', 'szél', 'dél', 'tél', 'lé',
        'nyár', 'sár',
        'egér', 'szekér', 'tenyér', 'kenyér', 'levél', 'fedél', 'fenék', 'kerék', 'cserép', 'szemét', 'elég', 'veréb', 'nehéz', 'tehén', 'derék',
        'gyökér', 'kötél', 'közép',
        'fazék',
        'madár', 'szamár', 'agár', 'kanál', 'darázs', 'parázs',
        'bogár', 'kosár', 'mocsár', 'mozsár', 'pohár', 'fonál',
        'sugár', 'sudár',
    );
    $obj->is_vtmr = in_array($string, $vtmr_list, true);

    // @todo tő és toldalék elkülönítése: mít|osz, ennek konstruálásakor legyen lemma=mít, és legyen a "nominális toldalék" osz, képzéskor pedig nem a nominálisból, hanem a lemmából képezzünk. (?)
    // @todo not full list
    $btmr_list = array(
        'aktív',
        'vízió',
        'miniatűr',
        'úr',
        'fúzió',
        'téma',
        'szláv',
        'privát',

        'náció',
        'analízis',
        'mítosz',
        'motívum',
        'stílus',
        'kultúra',
        'múzeum',
        'pasztőröz',
        'periódus',
        'paródia',
        'kódex',
        'filozófia',
        'história',
        'prémium',
        'szintézis',
        'hérosz',
        'matéria',
        'klérus',
        'május',
        'banális',
        'elegáns',
    );
    $obj->is_btmr = in_array($string, $btmr_list, true);

    // @todo not full list
    $opening_list = array('út', 'nyár', 'ház', 'tűz', 'víz', 'föld');
    // not opening e.g.: gáz bűz rés
    $obj->is_opening = in_array($string, $opening_list, true);
    return $obj;
}

function parseSuffixum($string)
{
    $obj = new Suffixum($string);

    // @todo not full list
    $vtmr_list = array(
        '_Vk', // többesjel
        '_Vt', // tárgyrag
        // birtokos személyragok
        'As', // melléknévképző
        'Az', // igeképző
        'cskA', // kicsinyítő képző
    );
    $obj->is_vtmr = in_array($string, $vtmr_list, true);

    // @todo not full list
    $btmr_list = array(
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
    $obj->is_btmr = in_array($string, $btmr_list, true);

    // @todo is full list?
    $opening_list = array(
        '_Vk', // többesjel
        // birtokos személyjelek
        '_Vbb', // középfok jele 
        // múlt idő jele 
        // felszólító j 
    );
    $obj->is_opening = in_array($string, $opening_list, true);
    return $obj;
}

// vtmr verbs, not full list: ir-at sziv-attyú tür-elem bün-tet szur-ony buj-kál huz-at usz-oda szöv-és vag-dal

?>
