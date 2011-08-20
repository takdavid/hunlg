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

    public static function addSuffix($nomen, $suffixcode)
    {
        if (substr($suffixcode, 0, 1) === 'v')
        {
            $last = self::getLastCons($nomen->actual);
            $suffixcode = $last.substr($suffixcode, 1);
        }

        $vowelmaps = array(
            '--' => array( 'A' => 'a', 'Á' => 'á', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'O' => 'o', 'V' => 'a'),
            'U-' => array( 'A' => 'a', 'Á' => 'á', 'Ó' => 'ó', 'U' => 'u', 'Ú' => 'ú', 'O' => 'o', 'V' => 'o'),
            '-I' => array( 'A' => 'e', 'Á' => 'é', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'O' => 'e', 'V' => 'e'),
            'UI' => array( 'A' => 'e', 'Á' => 'é', 'Ó' => 'ő', 'U' => 'ü', 'Ú' => 'ű', 'O' => 'ö', 'V' => 'ö'),
        );
        $nomen_phonocode = (Phonology::needSuffixU($nomen->actual) ? 'U' : '-') . (Phonology::needSuffixI($nomen->actual) ? 'I' : '-');
        $vowelmap = $vowelmaps[$nomen_phonocode];
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
        return $nomen->actual.$suffix;
    }

    public static function getLastCons($actual)
    {
        $last = substr($actual, -1, 1);
        if ($last === 'y')
            $last = substr($actual, -2, 2);
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

    public function addSuffix($suffixcode)
    {
        $this->actual = Phonology::addSuffix($this, $suffixcode);
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


class Nomen extends Wordform implements NominalCases, VirtualNominalCases
{

    public $case = 'Nominativus';
    public $numero = 1;
    public $person = 3;

    public function & makePlural()
    {
        $clone = clone $this;
        if ($this->isPlural())
            return $clone;
        if ($this->isNominativus())
        {
            $clone->numero = 3;
            $clone->addSuffix('ek');
            return $clone;
        }
        if ($this->isAccusativus()) // @deprecated Always start with a makeNominativus()
        {
            $clone2 = $clone->makeNominativus()->makePlural()->makeAccusativus();
            return $clone2;
        }
        throw new Exception();
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

    public function & _makeCaseFromNominativusWithSuffix($case, $suffix)
    {
        $clone = $this->makeNominativus();
        $clone->addSuffix($suffix);
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
        $clone->vow = $this->vow; // @todo copy others?
        if ($this->isPlural())
            $clone = $clone->makePlural();
        return $clone;
    }

    public function & makeAccusativus()
    {
        if ($this->isNominativus())
        {
            $clone = clone $this;
            if ($clone->isPlural())
                $clone->addSuffix('At');
            else
                $clone->addSuffix('t');
            $clone->case = 'Accusativus';
            return $clone;
        }
        throw new Exception();
    }

    public function & makeCausalisFinalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('CausalisFinalis', 'ért');
    }

    public function & makeDativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Dativus', 'nAk');
    }

    public function & makeInstrumentalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Instrumentalis', 'vAl');
    }

    public function & makeTranslativusFactivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('TranslativusFactivus', 'vÁ');
    }

    public function & makeFormativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Formativus', 'ként');
    }

    public function & makeEssivusFormalis()
    {
        return $this->_makeCaseFromNominativusWithSuffix('EssivusFormalis', 'Ul');
    }

    public function & makeIllativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Illativus', 'bA');
    }

    public function & makeInessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Inessivus', 'bAn');
    }

    public function & makeElativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Elativus', 'bÓl');
    }

    public function & makeSublativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Sublativus', 'rA');
    }

    public function & makeSuperessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Superessivus', 'On');
    }

    public function & makeDelativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Delativus', 'rÓl');
    }

    public function & makeAllativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Allativus', 'hOz');
    }

    public function & makeAdessivus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Adessivus', 'nÁl');
    }

    public function & makeAblativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Ablativus', 'tÓl');
    }

    public function & makeTerminativus()
    {
        return $this->_makeCaseFromNominativusWithSuffix('Terminativus', 'ig');
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
        $arg = clone $this->arg;
        $arg->addSuffix($this->suffixcode);
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
    return new Nomen($string);
}

?>
