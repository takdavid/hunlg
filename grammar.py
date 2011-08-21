#!/usr/bin/python
# coding=UTF-8
# vim: set fileencoding=UTF-8

import re
import copy

class Phonology :

    phonocode = {
        u'a' : 'A--1-',
        u'á' : 'A--2-',
        u'e' : 'A-I1-',
        u'é' : 'A-I2t',
        u'i' : '--I1t',
        u'í' : '--I2t',
        u'u' : '-U-1-',
        u'ú' : '-U-2-',
        u'ü' : '-UI2-',
        u'ű' : '-UI2-',
        u'o' : 'AU-1-',
        u'ó' : 'AU-2-',
        u'ö' : 'AUI1-',
        u'ő' : 'AUI2-',
    }

    @staticmethod
    def getPropagatedX(X_pattern, ortho, t_pattern) :
        X_re = re.compile(X_pattern)
        t_re = re.compile(t_pattern)
        is_propagated = None
        for chr in ortho :
            if chr in Phonology.phonocode :
                phc = Phonology.phonocode[chr]
                if t_re.match(phc) :
                    if is_propagated is not None :
                        continue
                if X_re.match(phc) :
                    is_propagated = True
                else :
                    is_propagated = False
        return is_propagated

    @staticmethod
    def getPropagatedI(ortho) :
        return Phonology.getPropagatedX('^..I', ortho, '^....t')

    @staticmethod
    def getPropagatedU(ortho) :
        return Phonology.getPropagatedX('^.U.', ortho, '^....t')

    @staticmethod
    def needSuffixI(ortho) :
        return Phonology.getPropagatedI(ortho)

    @staticmethod
    def needSuffixU(ortho) :
        return Phonology.getPropagatedU(ortho)

    vtmr_map = {
        u'á' : u'a',
        u'é' : u'e',
        u'í' : u'i',
        u'ó' : u'o',
        u'ő' : u'ö',
        u'ú' : u'u',
        u'ű' : u'ü',
    };

    amny_map = {
        u'a' : u'á',
        u'e' : u'é',
    };

    # magánhangzó-rövidülések
    @staticmethod
    def doMR(ortho) :
        return Phonology.tr(ortho, Phonology.vtmr_map);

    # utolsó alsó magánhangzó nyúlása
    @staticmethod
    def doAMNY(ortho) :
        return ortho[0:-1]+Phonology.tr(ortho[-1:], Phonology.amny_map);

    @staticmethod
    def tr(ortho, map) :
        str = ''
        for chr in ortho :
            if chr in map :
                str += map[chr]
            else :
                str += chr
        return str

    vowelmaps = {
            '--,O' : { u'A' : u'a', u'Á' : u'á', u'E' : u'o', u'O' : u'o', u'Ó' : u'ó', u'U' : u'u', u'Ú' : u'ú', u'V' : u'a', u'W' : u'o'},
            'U-,O' : { u'A' : u'a', u'Á' : u'á', u'E' : u'o', u'O' : u'o', u'Ó' : u'ó', u'U' : u'u', u'Ú' : u'ú', u'V' : u'a', u'W' : u'o'},
            '-I,O' : { u'A' : u'e', u'Á' : u'é', u'E' : u'e', u'O' : u'e', u'Ó' : u'ő', u'U' : u'ü', u'Ú' : u'ű', u'V' : u'e', u'W' : u'e'},
            'UI,O' : { u'A' : u'e', u'Á' : u'é', u'E' : u'e', u'O' : u'ö', u'Ó' : u'ő', u'U' : u'ü', u'Ú' : u'ű', u'V' : u'e', u'W' : u'e'},
            '--,-' : { u'A' : u'a', u'Á' : u'á', u'E' : u'o', u'O' : u'o', u'Ó' : u'ó', u'U' : u'u', u'Ú' : u'ú', u'V' : u'o', u'W' : u'o'},
            'U-,-' : { u'A' : u'a', u'Á' : u'á', u'E' : u'o', u'O' : u'o', u'Ó' : u'ó', u'U' : u'u', u'Ú' : u'ú', u'V' : u'o', u'W' : u'o'},
            '-I,-' : { u'A' : u'e', u'Á' : u'é', u'E' : u'e', u'O' : u'e', u'Ó' : u'ő', u'U' : u'ü', u'Ú' : u'ű', u'V' : u'e', u'W' : u'e'},
            'UI,-' : { u'A' : u'e', u'Á' : u'é', u'E' : u'ö', u'O' : u'ö', u'Ó' : u'ő', u'U' : u'ü', u'Ú' : u'ű', u'V' : u'ö', u'W' : u'e'},
    };

    @staticmethod
    def interpolateVowels(phonocode, string) :
        if phonocode in Phonology.vowelmaps :
            return Phonology.tr(string, Phonology.vowelmaps[phonocode]);
        else :
            return string

    @staticmethod
    def isOpening(string) :
        return False

    @staticmethod
    def needSuffixPhonocode(string) :
        return \
                ('U' if Phonology.needSuffixU(string) else '-') + \
                ('I' if Phonology.needSuffixI(string) else '-') + \
                (',O' if Phonology.isOpening(string) else ',-')

    skeletoncode = {
        'a' : 'V',
        'á' : 'V',
        'e' : 'V',
        'é' : 'V',
        'i' : 'V',
        'í' : 'V',
        'u' : 'V',
        'ú' : 'V',
        'ü' : 'V',
        'ű' : 'V',
        'o' : 'V',
        'ó' : 'V',
        'ö' : 'V',
        'ő' : 'V',
        'ddzs' : 'C',
        'ccs' : 'C',
        'ddz' : 'C',
        'dzs' : 'C',
        'ggy' : 'C',
        'lly' : 'C',
        'nny' : 'C',
        'ssz' : 'C',
        'tty' : 'C',
        'zzs' : 'C',
        'bb' : 'C',
        'cc' : 'C',
        'cs' : 'C',
        'dd' : 'C',
        'dz' : 'C',
        'ff' : 'C',
        'gg' : 'C',
        'gy' : 'C',
        'hh' : 'C',
        'jj' : 'C',
        'kk' : 'C',
        'll' : 'C',
        'ly' : 'C',
        'mm' : 'C',
        'nn' : 'C',
        'ny' : 'C',
        'pp' : 'C',
        'qq' : 'C',
        'rr' : 'C',
        'ss' : 'C',
        'sz' : 'C',
        'tt' : 'C',
        'ty' : 'C',
        'vv' : 'C',
        'ww' : 'C',
        'xx' : 'C',
        'zs' : 'C',
        'zz' : 'C',
        'b' : 'C',
        'c' : 'C',
        'd' : 'C',
        'f' : 'C',
        'g' : 'C',
        'h' : 'C',
        'j' : 'C',
        'k' : 'C',
        'l' : 'C',
        'm' : 'C',
        'n' : 'C',
        'p' : 'C',
        'q' : 'C',
        'r' : 'C',
        's' : 'C',
        't' : 'C',
        'v' : 'C',
        'w' : 'C',
        'x' : 'C',
        'z' : 'C',
    };

    @staticmethod
    def isVowel(char) :
        return (char in Phonology.skeletoncode) and (Phonology.skeletoncode[char] == 'V')

    consonant_regex = r'(ddzs|ccs|ddz|dzs|ggy|lly|nny|ssz|tty|zzs|bb|cc|cs|dd|dz|ff|gg|gy|h|hh|jj|k|kk|ll|ly|mm|nn|ny|pp|qq|rr|ss|sz|tt|ty|vv|ww|xx|zs|zz|b|c|d|f|g|j|l|m|n|p|q|r|s|t|v|w|x|z)$'

    @staticmethod
    def getLastConsonant(ortho) :
        regex = re.compile(Phonology.consonant_regex)
        match = regex.search(ortho)
        if match :
            return match.group(1)
        return None

    double_consonants = {
        'ddzs' : 'ddzs',
        'ccs' : 'ccs',
        'ddz' : 'ddz',
        'dzs' : 'ddzs',
        'ggy' : 'ggy',
        'lly' : 'lly',
        'nny' : 'nny',
        'ssz' : 'ssz',
        'tty' : 'tty',
        'zzs' : 'zzs',
        'bb' : 'bb',
        'cc' : 'cc',
        'cs' : 'ccs',
        'dd' : 'dd',
        'dz' : 'ddz',
        'ff' : 'ff',
        'gg' : 'gg',
        'gy' : 'ggy',
        'hh' : 'hh',
        'jj' : 'jj',
        'kk' : 'kk',
        'll' : 'll',
        'ly' : 'lly',
        'mm' : 'mm',
        'nn' : 'nn',
        'ny' : 'nny',
        'pp' : 'pp',
        'qq' : 'qq',
        'rr' : 'rr',
        'ss' : 'ss',
        'sz' : 'ssz',
        'tt' : 'tt',
        'ty' : 'tty',
        'vv' : 'vv',
        'ww' : 'ww',
        'xx' : 'xx',
        'zs' : 'zzs',
        'zz' : 'zz',
        'b' : 'bb',
        'c' : 'cc',
        'd' : 'dd',
        'f' : 'ff',
        'g' : 'gg',
        'h' : 'hh',
        'j' : 'jj',
        'k' : 'kk',
        'l' : 'll',
        'm' : 'mm',
        'n' : 'nn',
        'p' : 'pp',
        'q' : 'qq',
        'r' : 'rr',
        's' : 'ss',
        't' : 'tt',
        'v' : 'vv',
        'w' : 'ww',
        'x' : 'xx',
        'z' : 'zz',
    };

    @staticmethod
    def doubleConsonant(cons) :
        if cons in Phonology.double_consonants :
            return Phonology.double_consonants[cons]
        else :
            return None

    @staticmethod
    def doDoubleLastConsonant(ortho) :
        cons = Phonology.getLastConsonant(ortho)
        if cons :
            return ortho[0:-len(cons)]+Phonology.doubleConsonant(cons);
        else :
            return ortho

    @staticmethod
    def canAssimilate(left_ortho, right_ortho, char) :
        return not Phonology.isVowel(left_ortho[-1:]) and right_ortho[0:len(char)] == char

    is_affrikate = {
        'dz' : True,
        'ddz' : True,
        'dzs' : True,
        'ddzs' : True,
        'c' : True,
        'cc' : True,
        'cs' : True,
        'ccs' : True,
    };

    is_sybyl = {
        's' : True,
        'ss' : True,
        'sz' : True,
        'ssz' : True,
        'z' : True,
        'zz' : True,
        'zs' : True,
        'zzs' : True,
    };

    @staticmethod
    def isAffrikate(cons) :
        return (cons in Phonology.is_affrikate) and (Phonology.is_affrikate[cons])

    @staticmethod
    def isSybyl(cons) :
        return (cons in Phonology.is_sybyl) and (Phonology.is_sybyl[cons])

class iWordformMorphology :

    def appendSuffix(self, suffix) : pass 
    def onBeforeSuffixation(self, suffix) : pass

class iWordformPhonology :

    def isLastVowel(self) : pass
    def isVTMR(self) : pass
    def isBTMR(self) : pass 
    def isOpening(self) : pass 
    def isAMNYLeft(self) : pass 
    def isAMNYRight(self) : pass 
    def isAlternating(self) : pass 
    def needSuffixU(self) : pass 
    def needSuffixI(self) : pass 
    def needSuffixPhonocode(self) : pass 
    def doAssimilate(self, char) : pass

class Wordform (iWordformMorphology, iWordformPhonology) :

    lemma = ''
    ortho = ''
    is_vtmr = False
    is_btmr = False
    is_opening = False
    is_amny = None
    is_alternating = False
    needSuffixI = None

    def __init__(self, lemma=None, ortho=None) :
        self.lemma = lemma;
        self.ortho = ortho if ortho else lemma;

    def __repr__(self) :
        return self.ortho

    def cloneAs(self, className) :
        clone = className(self.lemma, self.ortho)
        for key in self.__dict__ :
            setattr(clone, key, self.__dict__[key])
        return clone

    def appendSuffix(self, suffix) :
        stem = self.cloneAs(Wordform)
        stem.ortho += suffix.ortho
        return stem

class Wordform1 (Wordform) : 

    def appendSuffix(self, suffix) :
        # @todo check input class
        stem = self.cloneAs(Wordform)
        output_class = Wordform
        affix = copy.copy(suffix)
        stem.onBeforeSuffixation(affix)
        affix.onBeforeSuffixed(stem)
        interfix_ortho = affix.getInterfix(stem)
        stem.ortho += suffix.ortho
        affix.onAfterSuffixed(stem)
        return stem

